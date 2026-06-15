<?php

namespace App\Http\Controllers;

use App\Helpers\AuditLogger;
use App\Mail\SupervisorEvaluationInvite;
use App\Models\Classes;
use App\Models\OjtEvaluation;
use App\Models\OjtEvaluationRequest;
use App\Models\OjtEvaluationTemplate;
use App\Models\OjtEvaluationTemplateItem;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EvaluationController extends Controller
{
    public function studentIndex(Request $request)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $student = Student::where('user_id', $data->id)->first();
        $expectedSupervisorEmail = $this->getExpectedSupervisorEmail($student);
        $perPage = (int) $request->query('per_page', 5);
        $search = trim((string) $request->query('search', ''));
        $sort = trim((string) $request->query('sort', 'newest'));
        if (!in_array($perPage, [5, 10, 25, 50], true)) {
            $perPage = 5;
        }

        $requestsQuery = OjtEvaluationRequest::with(['template', 'evaluation'])
            ->where('student_id', $data->id);

        if ($search !== '') {
            $requestsQuery->where(function ($query) use ($search) {
                $query->where('supervisor_email', 'like', '%' . $search . '%')
                    ->orWhere('supervisor_name', 'like', '%' . $search . '%')
                    ->orWhere('status', 'like', '%' . $search . '%');
            });
        }

        if (!in_array($sort, ['newest', 'oldest'], true)) {
            $sort = 'newest';
        }

        $requests = $requestsQuery
            ->orderBy('id', $sort === 'oldest' ? 'asc' : 'desc')
            ->paginate($perPage)
            ->withQueryString();

        return view('students.evaluation', [
            'data' => $data,
            'student' => $student,
            'requests' => $requests,
            'expectedSupervisorEmail' => $expectedSupervisorEmail,
            'perPage' => $perPage,
            'search' => $search,
            'sort' => $sort,
        ]);
    }

    public function sendEvaluationForm(Request $request)
    {
        $request->validate([
            'supervisor_email' => 'required|email',
            'supervisor_name' => 'nullable|string|max:255',
            'confirm_email_mismatch' => 'nullable|boolean',
        ]);

        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $student = Student::where('user_id', $user->id)->first();
        if (!$student) {
            return back()->with('error', 'Student profile not found.');
        }

        $expectedSupervisorEmail = $this->getExpectedSupervisorEmail($student);
        $enteredEmail = trim((string) $request->supervisor_email);
        if (
            !empty($expectedSupervisorEmail)
            && strcasecmp($enteredEmail, $expectedSupervisorEmail) !== 0
            && !$request->boolean('confirm_email_mismatch')
        ) {
            return back()
                ->withInput()
                ->with('error', 'The entered email does not match the company/supervisor email from your submitted MOA. If this is intentional, confirm and submit again.');
        }

        $template = OjtEvaluationTemplate::where('is_active', 1)->latest('id')->first();
        if (!$template) {
            return back()->with('error', 'No active evaluation template found.');
        }

        $newRequest = OjtEvaluationRequest::create([
            'student_id' => $user->id,
            'class_id' => $student->class_id,
            'template_id' => $template->id,
            'student_num' => $student->studentNum ?? '',
            'student_name' => trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')),
            'supervisor_name' => $request->supervisor_name,
            'supervisor_email' => $request->supervisor_email,
            'token' => Str::random(64),
            'token_expires_at' => now()->addDays(14),
            'emailed_at' => now(),
            'status' => 'sent',
        ]);

        Mail::to($request->supervisor_email)->send(new SupervisorEvaluationInvite($newRequest));

        AuditLogger::log(
            'Evaluation',
            'create',
            'Student sent supervisor evaluation request',
            $user->id
        );

        return back()->with('success', 'Evaluation form link sent to supervisor.');
    }

    public function professorIndex()
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $allClassrooms = Classes::where('adviser_name', $data->full_name)->get();
        $classIds = $allClassrooms->pluck('id')->all();

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            });
        $students = $students->get();

        $studentIds = $students->pluck('id')->all();

        $requests = OjtEvaluationRequest::with(['evaluation', 'template'])
            ->whereIn('student_id', $studentIds)
            ->latest('id')
            ->get()
            ->groupBy('student_id');

        $template = OjtEvaluationTemplate::with('items')->where('is_active', 1)->latest('id')->first();

        $classStats = [];
        foreach ($allClassrooms as $room) {
            $classStudents = $students->filter(function ($student) use ($room) {
                return (string) optional($student->studentInfo)->class_id === (string) $room->id;
            });

            $submittedCount = 0;
            foreach ($classStudents as $student) {
                $latest = ($requests[$student->id] ?? collect())->first();
                if ($latest && $latest->status === 'submitted') {
                    $submittedCount++;
                }
            }

            $totalCount = $classStudents->count();
            $classStats[$room->id] = [
                'total_count' => $totalCount,
                'submitted_count' => $submittedCount,
                'pending_count' => max($totalCount - $submittedCount, 0),
            ];
        }

        $latestRequests = $students->map(function ($student) use ($requests) {
            return ($requests[$student->id] ?? collect())->first();
        })->filter();

        $totalStudents = $students->count();
        $submittedRequests = $latestRequests->where('status', 'submitted')->count();
        $sentRequests = $latestRequests->where('status', 'sent')->count();
        $openedRequests = $latestRequests->where('status', 'opened')->count();
        $expiredRequests = $latestRequests->where('status', 'expired')->count();
        $pendingEvaluations = max($totalStudents - $submittedRequests, 0);
        $classesWithPending = collect($classStats)->filter(fn ($stat) => ($stat['pending_count'] ?? 0) > 0)->count();
        $topPendingClass = $allClassrooms
            ->map(function ($room) use ($classStats) {
                return [
                    'label' => trim(($room->course ?? '') . ' ' . ($room->room ?? '')),
                    'pending' => (int) ($classStats[$room->id]['pending_count'] ?? 0),
                    'total' => (int) ($classStats[$room->id]['total_count'] ?? 0),
                    'submitted' => (int) ($classStats[$room->id]['submitted_count'] ?? 0),
                ];
            })
            ->sortByDesc('pending')
            ->first();

        $evaluationAiData = [
            'report_type' => 'professor_evaluation',
            'metrics' => [
                'total_classes' => $allClassrooms->count(),
                'total_students' => $totalStudents,
                'submitted_evaluations' => $submittedRequests,
                'pending_evaluations' => $pendingEvaluations,
                'sent_requests' => $sentRequests,
                'opened_requests' => $openedRequests,
                'expired_requests' => $expiredRequests,
                'classes_with_pending' => $classesWithPending,
                'top_pending_class' => $topPendingClass,
                'template_title' => $template->title ?? null,
                'template_version' => $template->version ?? null,
            ],
            'highlights' => [
                'Evaluation coverage includes ' . $allClassrooms->count() . ' class' . ($allClassrooms->count() === 1 ? '' : 'es') . ' and ' . $totalStudents . ' student' . ($totalStudents === 1 ? '' : 's') . '.',
                $submittedRequests . ' evaluation request' . ($submittedRequests === 1 ? '' : 's') . ' have been submitted.',
                'Active template: ' . ($template->title ?? 'No active template') . '.',
            ],
            'watchouts' => array_values(array_filter([
                $pendingEvaluations > 0 ? $pendingEvaluations . ' evaluation' . ($pendingEvaluations === 1 ? '' : 's') . ' still need follow-up.' : null,
                $expiredRequests > 0 ? $expiredRequests . ' request' . ($expiredRequests === 1 ? '' : 's') . ' have expired.' : null,
                $classesWithPending > 0 ? $classesWithPending . ' class' . ($classesWithPending === 1 ? '' : 'es') . ' still have pending evaluation submissions.' : null,
            ])),
            'actions' => [
                'Open classes with pending evaluations and resend reminders where needed.',
                'Review expired requests before exporting the evaluation report.',
                'Keep the active template updated before sending new evaluation links.',
            ],
        ];

        $perPage = (int) request()->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $allClassrooms->forPage($currentPage, $perPage)->values();

        foreach ($currentItems as $room) {
            if (isset($classStats[$room->id])) {
                foreach ($classStats[$room->id] as $key => $value) {
                    $room->{$key} = $value;
                }
            }
        }

        $classrooms = new LengthAwarePaginator(
            $currentItems,
            $allClassrooms->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('professor.evaluation', [
            'data' => $data,
            'classrooms' => $classrooms,
            'classroomsTotal' => $allClassrooms->count(),
            'students' => $students,
            'requestsByStudent' => $requests,
            'template' => $template,
            'evaluationAiData' => $evaluationAiData,
        ]);
    }

    public function professorClassList($classId)
    {
        $data = null;
        if (Session::has('loginId')) {
            $data = User::where('id', Session::get('loginId'))->first();
        }

        if (!$data) {
            return redirect('/login');
        }

        $classroom = Classes::where('id', $classId)
            ->where('adviser_name', $data->full_name)
            ->first();

        if (!$classroom) {
            abort(403);
        }

        $students = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classId) {
                $query->where('class_id', $classId);
            })
            ->get();

        $studentIds = $students->pluck('id')->all();
        $requestsByStudent = OjtEvaluationRequest::with(['evaluation'])
            ->whereIn('student_id', $studentIds)
            ->latest('id')
            ->get()
            ->groupBy('student_id');

        $perPage = (int) request()->query('per_page', 10);
        if (!in_array($perPage, [10, 25, 50], true)) {
            $perPage = 10;
        }

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $students->forPage($currentPage, $perPage)->values();

        $students = new LengthAwarePaginator(
            $currentItems,
            $students->count(),
            $perPage,
            $currentPage,
            [
                'path' => request()->url(),
                'query' => request()->query(),
            ]
        );

        return view('professor.evaluation_list', [
            'data' => $data,
            'classroom' => $classroom,
            'students' => $students,
            'requestsByStudent' => $requestsByStudent,
        ]);
    }

    public function resendEvaluationForm($requestId)
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $requestRow = OjtEvaluationRequest::where('id', $requestId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if ($requestRow->status === 'submitted') {
            return back()->with('error', 'Submitted evaluations can no longer be resent.');
        }

        $requestRow->token = Str::random(64);
        $requestRow->token_expires_at = now()->addDays(14);
        $requestRow->emailed_at = now();
        $requestRow->opened_at = null;
        $requestRow->status = 'sent';
        $requestRow->save();

        Mail::to($requestRow->supervisor_email)->send(new SupervisorEvaluationInvite($requestRow));

        AuditLogger::log(
            'Evaluation',
            'update',
            'Student resent supervisor evaluation request',
            $user->id
        );

        return back()->with('success', 'Evaluation form link resent to supervisor.');
    }

    public function cancelEvaluationForm($requestId)
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $requestRow = OjtEvaluationRequest::where('id', $requestId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if ($requestRow->status === 'submitted') {
            return back()->with('error', 'Submitted evaluations cannot be cancelled.');
        }

        $requestRow->status = 'cancelled';
        $requestRow->token = Str::random(64);
        $requestRow->token_expires_at = now();
        $requestRow->save();

        AuditLogger::log(
            'Evaluation',
            'update',
            'Student cancelled supervisor evaluation request',
            $user->id
        );

        return back()->with('success', 'Evaluation request cancelled.');
    }

    public function updateTemplate(Request $request, $templateId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'item_keys' => 'required|array|min:1',
            'item_keys.*' => 'required|string|max:255',
            'item_ids' => 'nullable|array',
            'item_ids.*' => 'nullable|integer',
            'item_labels' => 'required|array',
            'item_labels.*' => 'required|string|max:255',
            'item_sections' => 'required|array',
            'item_sections.*' => 'nullable|string|max:255',
            'item_required' => 'nullable|array',
        ]);

        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return back()->with('error', 'User not found.');
        }

        $template = OjtEvaluationTemplate::with('items')->findOrFail($templateId);

        DB::transaction(function () use ($request, $user, $template) {
            $template->is_active = 0;
            $template->updated_by_user_id = $user->id;
            $template->save();

            $newTemplate = OjtEvaluationTemplate::create([
                'title' => $request->title,
                'description' => $request->description,
                'version' => ((int) $template->version) + 1,
                'previous_template_id' => $template->id,
                'is_active' => 1,
                'created_by_user_id' => $template->created_by_user_id ?: $user->id,
                'updated_by_user_id' => $user->id,
            ]);

            $requiredSet = array_map('strval', $request->item_required ?? []);
            $displayOrder = 1;

            foreach ($request->item_keys as $index => $rowKey) {
                $itemId = $request->item_ids[$index] ?? null;
                $oldItem = null;

                if (!empty($itemId)) {
                    $oldItem = $template->items()->where('id', (int) $itemId)->where('input_type', 'rating')->first();
                }

                $section = $request->item_sections[$index] ?? ($oldItem->section ?? null);
                $label = $request->item_labels[$index] ?? ($oldItem->label ?? null);

                if (empty($label)) {
                    continue;
                }

                OjtEvaluationTemplateItem::create([
                    'template_id' => $newTemplate->id,
                    'section' => $section,
                    'label' => $label,
                    'input_type' => 'rating',
                    'display_order' => $displayOrder++,
                    'is_required' => in_array((string) $rowKey, $requiredSet, true) ? 1 : 0,
                ]);
            }

            $fixedItems = $template->items()
                ->where('input_type', '!=', 'rating')
                ->orderBy('display_order')
                ->get();

            foreach ($fixedItems as $fixedItem) {
                OjtEvaluationTemplateItem::create([
                    'template_id' => $newTemplate->id,
                    'section' => $fixedItem->section,
                    'label' => $fixedItem->label,
                    'input_type' => $fixedItem->input_type,
                    'display_order' => $displayOrder++,
                    'is_required' => $fixedItem->is_required,
                ]);
            }
        });

        AuditLogger::log(
            'Evaluation Template',
            'update',
            'Professor created a new evaluation template version',
            $user->id
        );

        return back()->with('success', 'New evaluation template version saved successfully.');
    }

    public function showSupervisorForm($token)
    {
        $requestRow = OjtEvaluationRequest::with(['template.items', 'evaluation'])->where('token', $token)->first();

        if (!$requestRow) {
            abort(404);
        }

        if ($requestRow->token_expires_at && now()->greaterThan($requestRow->token_expires_at) && $requestRow->status !== 'submitted') {
            $requestRow->status = 'expired';
            $requestRow->save();
        }

        if ($requestRow->status === 'sent') {
            $requestRow->status = 'opened';
            $requestRow->opened_at = now();
            $requestRow->save();
        }

        return view('evaluations.supervisor_form', [
            'requestRow' => $requestRow,
            'submitted' => $requestRow->status === 'submitted',
            'expired' => $requestRow->status === 'expired',
        ]);
    }

    public function reviewSupervisorForm(Request $request, $token)
    {
        $requestRow = OjtEvaluationRequest::with('template.items')->where('token', $token)->first();
        if (!$requestRow) {
            abort(404);
        }

        if ($requestRow->status === 'submitted') {
            return back()->with('error', 'This evaluation has already been submitted.');
        }

        if ($requestRow->token_expires_at && now()->greaterThan($requestRow->token_expires_at)) {
            $requestRow->status = 'expired';
            $requestRow->save();
            return back()->with('error', 'This evaluation link has expired.');
        }

        $validated = $this->validateSupervisorPayload($request, $requestRow, true);

        $signatureFile = $request->file('signature_file');
        $signatureTempPath = $signatureFile->store('evaluation-signatures/tmp', 'public');
        $signatureOriginalName = $signatureFile->getClientOriginalName();
        $signaturePreview = $this->buildSignaturePreviewDataUri($signatureTempPath);

        $responses = [];
        foreach ($requestRow->template->items as $item) {
            if ($item->input_type === 'rating') {
                $responses[] = [
                    'item_id' => $item->id,
                    'section' => $item->section,
                    'label' => $item->label,
                    'score' => (int) ($validated['rating_' . $item->id] ?? 0),
                ];
            }
        }

        return view('evaluations.supervisor_review', [
            'requestRow' => $requestRow,
            'validated' => $validated,
            'responses' => $responses,
            'signatureTempPath' => $signatureTempPath,
            'signaturePreviewDataUri' => $signaturePreview['dataUri'],
            'signaturePreviewMime' => $signaturePreview['mime'],
            'signatureOriginalName' => $signatureOriginalName,
        ]);
    }

    public function submitSupervisorForm(Request $request, $token)
    {
        $requestRow = OjtEvaluationRequest::with('template.items')->where('token', $token)->first();
        if (!$requestRow) {
            abort(404);
        }

        if ($requestRow->status === 'submitted') {
            return back()->with('error', 'This evaluation has already been submitted.');
        }

        if ($requestRow->token_expires_at && now()->greaterThan($requestRow->token_expires_at)) {
            $requestRow->status = 'expired';
            $requestRow->save();
            return back()->with('error', 'This evaluation link has expired.');
        }

        $validated = $this->validateSupervisorPayload($request, $requestRow, false);

        $signaturePath = $this->persistSignatureFromTemp($validated['signature_temp_path']);
        if (!$signaturePath) {
            return back()->with('error', 'Signature upload was not found. Please go back and upload the signature again.');
        }

        $responses = [];
        foreach ($requestRow->template->items as $item) {
            if ($item->input_type === 'rating') {
                $responses[] = [
                    'item_id' => $item->id,
                    'section' => $item->section,
                    'label' => $item->label,
                    'score' => (int) ($validated['rating_' . $item->id] ?? 0),
                ];
            }
        }

        OjtEvaluation::updateOrCreate(
            ['request_id' => $requestRow->id],
            [
                'template_id' => $requestRow->template_id,
                'supervisor_name' => $validated['supervisor_name'],
                'responses_json' => json_encode($responses),
                'comments' => $validated['comments'] ?? null,
                'signature_path' => $signaturePath,
                'submitted_at' => now(),
            ]
        );

        $requestRow->status = 'submitted';
        $requestRow->submitted_at = now();
        $requestRow->supervisor_name = $validated['supervisor_name'];
        $requestRow->save();

        AuditLogger::log(
            'Evaluation',
            'submit',
            'Supervisor submitted evaluation for student: ' . ($requestRow->student_name ?: ('Student ID ' . $requestRow->student_id)),
            $requestRow->student_id
        );

        return redirect()->route('evaluation.form.thankyou');
    }

    protected function validateSupervisorPayload(Request $request, OjtEvaluationRequest $requestRow, $isReviewStep = true)
    {
        $rules = [
            'supervisor_name' => 'required|string|max:255',
            'comments' => 'nullable|string',
        ];

        if ($isReviewStep) {
            $rules['signature_file'] = 'required|file|mimes:jpg,jpeg,png,pdf|max:4096';
        } else {
            $rules['signature_temp_path'] = 'required|string|max:255';
        }

        foreach ($requestRow->template->items as $item) {
            if ($item->input_type === 'rating') {
                $rules['rating_' . $item->id] = ($item->is_required ? 'required' : 'nullable') . '|integer|min:1|max:5';
            }
        }

        return $request->validate($rules);
    }

    protected function persistSignatureFromTemp($tempPath)
    {
        $normalizedPath = str_replace('\\', '/', (string) $tempPath);
        if (!Str::startsWith($normalizedPath, 'evaluation-signatures/tmp/')) {
            return null;
        }

        if (!Storage::disk('public')->exists($normalizedPath)) {
            return null;
        }

        $extension = pathinfo($normalizedPath, PATHINFO_EXTENSION) ?: 'png';
        $finalPath = 'evaluation-signatures/' . Str::uuid() . '.' . strtolower($extension);
        Storage::disk('public')->move($normalizedPath, $finalPath);

        return $finalPath;
    }

    protected function buildSignaturePreviewDataUri(?string $path): array
    {
        if (empty($path)) {
            return ['dataUri' => null, 'mime' => null];
        }

        $normalizedPath = str_replace('\\', '/', (string) $path);
        if (!Storage::disk('public')->exists($normalizedPath)) {
            return ['dataUri' => null, 'mime' => null];
        }

        $absolutePath = storage_path('app/public/' . $normalizedPath);
        $mime = @mime_content_type($absolutePath) ?: 'application/octet-stream';
        $content = Storage::disk('public')->get($normalizedPath);

        return [
            'dataUri' => 'data:' . $mime . ';base64,' . base64_encode($content),
            'mime' => $mime,
        ];
    }

    protected function getExpectedSupervisorEmail($student)
    {
        if (!$student) {
            return null;
        }

        $company = $student->companies()
            ->orderByDesc('companies.id')
            ->first();

        $email = trim((string) optional($company)->company_email);
        return $email !== '' ? $email : null;
    }

    public function studentShowEvaluation($requestId)
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $requestRow = OjtEvaluationRequest::with(['evaluation', 'template'])
            ->where('id', $requestId)
            ->where('student_id', $user->id)
            ->firstOrFail();

        if (!$requestRow->evaluation) {
            return back()->with('error', 'Evaluation is not submitted yet.');
        }

        $signaturePreview = $this->buildSignaturePreviewDataUri($requestRow->evaluation->signature_path);

        return view('evaluations.detail', [
            'requestRow' => $requestRow,
            'evaluation' => $requestRow->evaluation,
            'signaturePreviewDataUri' => $signaturePreview['dataUri'],
            'signaturePreviewMime' => $signaturePreview['mime'],
            'isProfessorView' => false,
            'backUrl' => '/student/evaluation',
        ]);
    }

    public function professorShowEvaluation($requestId)
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $requestRow = OjtEvaluationRequest::with(['evaluation', 'student.studentInfo', 'template'])
            ->where('id', $requestId)
            ->firstOrFail();

        $student = $requestRow->student;
        if (!$student || !$student->studentInfo) {
            abort(403);
        }

        $class = Classes::where('id', $student->studentInfo->class_id)
            ->where('adviser_name', $user->full_name)
            ->first();

        if (!$class) {
            abort(403);
        }

        if (!$requestRow->evaluation) {
            return back()->with('error', 'Evaluation is not submitted yet.');
        }

        $signaturePreview = $this->buildSignaturePreviewDataUri($requestRow->evaluation->signature_path);

        AuditLogger::log(
            'Evaluation',
            'view',
            'Professor viewed submitted evaluation for student: ' . ($requestRow->student_name ?: optional($student)->full_name),
            $user->id
        );

        return view('evaluations.detail', [
            'requestRow' => $requestRow,
            'evaluation' => $requestRow->evaluation,
            'signaturePreviewDataUri' => $signaturePreview['dataUri'],
            'signaturePreviewMime' => $signaturePreview['mime'],
            'isProfessorView' => true,
            'backUrl' => '/professor/evaluation/class/' . $class->id,
        ]);
    }

    public function professorStudentHistory($studentId)
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $student = User::with('studentInfo')
            ->where('id', $studentId)
            ->where('role', 0)
            ->firstOrFail();

        $studentClassId = optional($student->studentInfo)->class_id;
        $class = Classes::where('id', $studentClassId)
            ->where('adviser_name', $user->full_name)
            ->first();

        if (!$class) {
            abort(403);
        }

        $requests = OjtEvaluationRequest::with(['evaluation', 'template'])
            ->where('student_id', $student->id)
            ->latest('id')
            ->get();

        AuditLogger::log(
            'Evaluation',
            'view',
            'Professor viewed evaluation history for student: ' . $student->full_name,
            $user->id
        );

        return view('professor.evaluation_history', [
            'data' => $user,
            'student' => $student,
            'class' => $class,
            'requests' => $requests,
        ]);
    }

    public function exportProfessorEvaluation()
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $classIds = Classes::where('adviser_name', $user->full_name)->pluck('id')->all();
        $selectedClassId = request()->query('class_id');

        $studentsQuery = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            });

        if (!empty($selectedClassId)) {
            $studentsQuery->whereHas('studentInfo', function ($query) use ($selectedClassId) {
                $query->where('class_id', $selectedClassId);
            });
        }

        $students = $studentsQuery->get();
        $studentIds = $students->pluck('id')->all();
        $selectedClass = !empty($selectedClassId)
            ? Classes::where('id', $selectedClassId)->where('adviser_name', $user->full_name)->first()
            : null;
        $scopeLabel = $selectedClass
            ? ($selectedClass->room . ' - ' . $selectedClass->course)
            : 'All adviser classes';

        AuditLogger::log(
            'Evaluation Report',
            'export',
            'Professor exported evaluation CSV for scope: ' . $scopeLabel . ' (' . $students->count() . ' students)',
            $user->id
        );

        if (empty($studentIds)) {
            $filename = 'evaluation-monitoring-' . now()->format('Ymd-His') . '.csv';

            return Response::streamDownload(function () {
                $handle = fopen('php://output', 'w');
                fputcsv($handle, ['Student No.', 'Student Name', 'Class ID', 'Latest Status', 'Supervisor Email', 'Sent At', 'Submitted At']);
                fclose($handle);
            }, $filename, [
                'Content-Type' => 'text/csv',
            ]);
        }

        $requests = OjtEvaluationRequest::with(['evaluation'])
            ->whereIn('student_id', $studentIds)
            ->latest('id')
            ->get()
            ->groupBy('student_id');

        $filename = 'evaluation-monitoring-' . now()->format('Ymd-His') . '.csv';

        return Response::streamDownload(function () use ($students, $requests) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, ['Student No.', 'Student Name', 'Class ID', 'Latest Status', 'Supervisor Email', 'Sent At', 'Submitted At']);

            foreach ($students as $student) {
                $latest = ($requests[$student->id] ?? collect())->first();
                fputcsv($handle, [
                    optional($student->studentInfo)->studentNum ?: '',
                    $student->full_name,
                    optional($student->studentInfo)->class_id ?: '',
                    optional($latest)->status ?? 'not sent',
                    optional($latest)->supervisor_email ?? '',
                    optional(optional($latest)->emailed_at)->format('Y-m-d H:i:s') ?: '',
                    optional(optional($latest)->submitted_at)->format('Y-m-d H:i:s') ?: '',
                ]);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function printProfessorEvaluation()
    {
        $user = User::where('id', Session::get('loginId'))->first();
        if (!$user) {
            return redirect('/login');
        }

        $classrooms = Classes::where('adviser_name', $user->full_name)->get();
        $classIds = $classrooms->pluck('id')->all();
        $selectedClassId = request()->query('class_id');

        $studentsQuery = User::with('studentInfo')
            ->where('role', 0)
            ->whereHas('studentInfo', function ($query) use ($classIds) {
                $query->whereIn('class_id', $classIds);
            });

        if (!empty($selectedClassId)) {
            $studentsQuery->whereHas('studentInfo', function ($query) use ($selectedClassId) {
                $query->where('class_id', $selectedClassId);
            });
        }

        $students = $studentsQuery->get();
        $studentIds = $students->pluck('id')->all();
        $requestsByStudent = OjtEvaluationRequest::with(['evaluation'])
            ->whereIn('student_id', $studentIds)
            ->latest('id')
            ->get()
            ->groupBy('student_id');
        $selectedClass = !empty($selectedClassId)
            ? $classrooms->firstWhere('id', $selectedClassId)
            : null;
        $scopeLabel = $selectedClass
            ? ($selectedClass->room . ' - ' . $selectedClass->course)
            : 'All adviser classes';

        AuditLogger::log(
            'Evaluation Report',
            'print',
            'Professor generated evaluation print report for scope: ' . $scopeLabel . ' (' . $students->count() . ' students)',
            $user->id
        );

        return view('professor.evaluation_print', [
            'data' => $user,
            'classrooms' => $classrooms,
            'students' => $students,
            'requestsByStudent' => $requestsByStudent,
            'selectedClassId' => $selectedClassId,
            'printedAt' => now(),
        ]);
    }


    public function thankYou()
    {
        return view('evaluations.submitted');
    }
}
