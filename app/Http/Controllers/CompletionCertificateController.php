<?php

namespace App\Http\Controllers;

use App\Exceptions\UnsafeUploadException;
use App\Helpers\AuditLogger;
use App\Models\CompletionCertificate;
use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use App\Services\DocumentAccessService;
use App\Services\PrivateDocumentStorage;
use App\Services\RequirementUploadLock;
use App\Services\SecureDocumentResponse;
use App\Services\SecureUploadService;
use App\Services\UploadSecurityRecorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CompletionCertificateController extends Controller
{
    public function __construct(
        private readonly SecureUploadService $secureUploads,
        private readonly PrivateDocumentStorage $documents,
        private readonly RequirementUploadLock $uploadLock,
        private readonly DocumentAccessService $documentAccess,
        private readonly SecureDocumentResponse $documentResponse,
        private readonly UploadSecurityRecorder $securityEvidence
    ) {
    }

    public function studentIndex()
    {
        $user = $this->currentUser(0);
        $student = Student::with('professor')->where('user_id', $user->id)->first();
        $currentCertificate = CompletionCertificate::with(['professorReviewer', 'coordinatorReviewer'])
            ->where('student_user_id', $user->id)
            ->current()
            ->latest('id')
            ->first();
        $history = CompletionCertificate::with(['professorReviewer', 'coordinatorReviewer'])
            ->where('student_user_id', $user->id)
            ->orderByDesc('version')
            ->paginate(5, ['*'], 'history_page')
            ->withQueryString();

        return view('completionCertificates.index', $this->viewData('student', $user, compact(
            'student',
            'currentCertificate',
            'history'
        )));
    }

    public function upload(Request $request)
    {
        $user = $this->currentUser(0);
        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf|max:30720',
        ], [
            'file.mimes' => 'Upload rejected: the file content does not match the .pdf extension. Renaming a non-PDF file to .pdf is not allowed.',
        ]);

        $student = Student::with('professor')->where('user_id', $user->id)->first();

        if (! $student) {
            return back()->with('upload_error', 'Your student profile could not be found.');
        }

        $professor = $student->professor;

        $lock = $this->uploadLock->create((int) $user->id, 'certificate-of-completion');

        if (! $lock->get()) {
            return back()->withErrors(['file' => 'This certificate upload is already being validated. Please wait before trying again.']);
        }

        $storedUpload = null;

        try {
            $current = CompletionCertificate::where('student_user_id', $user->id)
                ->current()
                ->latest('id')
                ->first();

            if ($current && ! $current->canBeResubmitted()) {
                return back()->with('upload_error', 'Your current Certificate of Completion is still under review or already approved.');
            }

            try {
                $storedUpload = $this->secureUploads->store(
                    $validated['file'],
                    'pdf',
                    PrivateDocumentStorage::COMPLETION_CERTIFICATES
                );
            } catch (UnsafeUploadException $exception) {
                return back()->withErrors(['file' => $exception->getMessage()]);
            }

            $certificate = DB::transaction(function () use ($user, $student, $professor, $request, $storedUpload) {
                $current = CompletionCertificate::where('student_user_id', $user->id)
                    ->current()
                    ->lockForUpdate()
                    ->latest('id')
                    ->first();

                if ($current && ! $current->canBeResubmitted()) {
                    throw new \RuntimeException('A certificate is already under review or approved.');
                }

                if ($current) {
                    $current->update(['superseded_at' => now()]);
                }

                $version = (int) CompletionCertificate::where('student_user_id', $user->id)->max('version') + 1;

                return CompletionCertificate::create([
                    'student_id' => $student->id,
                    'student_user_id' => $user->id,
                    'professor_id' => $professor?->id,
                    'file' => $storedUpload->filename,
                    'original_name' => Str::limit(basename($request->file('file')->getClientOriginalName()), 255, ''),
                    'storage_directory' => PrivateDocumentStorage::COMPLETION_CERTIFICATES,
                    'version' => max(1, $version),
                    'status' => $professor
                        ? CompletionCertificate::PENDING_PROFESSOR
                        : CompletionCertificate::PENDING_COORDINATOR,
                ]);
            });

            $this->securityEvidence->link($storedUpload->securityRecordId, $certificate, $user->id);
            $user->touchActivity();
            AuditLogger::log('Certificate of Completion', 'Upload', 'Uploaded Certificate of Completion version '.$certificate->version, $user->id);

            return back()
                ->with('success', $professor
                    ? 'Certificate of Completion uploaded successfully. It is available to your professor and the OJT coordinator.'
                    : 'Certificate of Completion uploaded successfully and sent to the OJT coordinator for validation.')
                ->with('upload_success', $professor
                    ? 'The PDF passed security validation. Professor review is optional; coordinator validation is required.'
                    : 'The PDF passed security validation and was submitted for coordinator validation.');
        } catch (\Throwable $exception) {
            if ($storedUpload) {
                $this->documents->delete(PrivateDocumentStorage::COMPLETION_CERTIFICATES, $storedUpload->filename);
            }

            report($exception);

            return back()->with('upload_error', 'The certificate could not be submitted. Please try again.');
        } finally {
            $lock->release();
        }
    }

    public function professorIndex(Request $request)
    {
        $user = $this->currentUser(2);
        $professor = Professor::where('user_id', $user->id)->firstOrFail();
        $perPage = $this->reviewPerPage($request);
        $records = $this->reviewQuery($request)
            ->where('professor_id', $professor->id)
            ->paginate($perPage)
            ->withQueryString();

        return view('completionCertificates.index', $this->viewData('professor', $user, array_merge(
            compact('professor', 'records'),
            $this->reviewFilterOptions($professor->id)
        )));
    }

    public function coordinatorIndex(Request $request)
    {
        $user = $this->currentUser(1);
        $perPage = $this->reviewPerPage($request);
        $records = $this->reviewQuery($request)->paginate($perPage)->withQueryString();

        return view('completionCertificates.index', $this->viewData('coordinator', $user, array_merge(
            compact('records'),
            $this->reviewFilterOptions()
        )));
    }

    public function professorApprove(CompletionCertificate $certificate)
    {
        $user = $this->currentUser(2);
        $professor = Professor::where('user_id', $user->id)->firstOrFail();
        abort_unless((int) $certificate->professor_id === (int) $professor->id, 404);

        $this->transition($certificate, CompletionCertificate::PENDING_PROFESSOR, [
            'status' => CompletionCertificate::PENDING_COORDINATOR,
            'professor_remarks' => null,
            'professor_reviewed_by' => $user->id,
            'professor_reviewed_at' => now(),
        ]);

        AuditLogger::log('Certificate of Completion', 'Professor Approval', 'Approved COC for coordinator validation', $certificate->student_user_id);

        return back()->with('success', 'Certificate approved and forwarded to the coordinator.');
    }

    public function professorReject(Request $request, CompletionCertificate $certificate)
    {
        $user = $this->currentUser(2);
        $professor = Professor::where('user_id', $user->id)->firstOrFail();
        abort_unless((int) $certificate->professor_id === (int) $professor->id, 404);
        $validated = $request->validate(['remarks' => 'required|string|max:1000']);

        $this->transition($certificate, CompletionCertificate::PENDING_PROFESSOR, [
            'status' => CompletionCertificate::PROFESSOR_REJECTED,
            'professor_remarks' => $validated['remarks'],
            'professor_reviewed_by' => $user->id,
            'professor_reviewed_at' => now(),
        ]);

        AuditLogger::log('Certificate of Completion', 'Professor Rejection', 'Rejected COC: '.$validated['remarks'], $certificate->student_user_id);

        return back()->with('success', 'Certificate rejected. The student can now submit a corrected copy.');
    }

    public function coordinatorApprove(CompletionCertificate $certificate)
    {
        $user = $this->currentUser(1);

        $approvedCertificate = $this->transition($certificate, [
            CompletionCertificate::PENDING_PROFESSOR,
            CompletionCertificate::PENDING_COORDINATOR,
        ], [
            'status' => CompletionCertificate::APPROVED,
            'coordinator_remarks' => null,
            'coordinator_reviewed_by' => $user->id,
            'coordinator_reviewed_at' => now(),
        ]);
        $professorValidationSkipped = ! $approvedCertificate->hasProfessorValidation();

        AuditLogger::log(
            'Certificate of Completion',
            'Final Approval',
            $professorValidationSkipped
                ? 'Final-approved the student COC without optional professor validation'
                : 'Final-approved the student COC after professor validation',
            $certificate->student_user_id
        );

        return back()->with('success', $professorValidationSkipped
            ? 'Certificate of Completion fully validated by the coordinator. Professor validation was bypassed.'
            : 'Certificate of Completion fully approved.');
    }

    public function coordinatorReject(Request $request, CompletionCertificate $certificate)
    {
        $user = $this->currentUser(1);
        $validated = $request->validate(['remarks' => 'required|string|max:1000']);

        $this->transition($certificate, [
            CompletionCertificate::PENDING_PROFESSOR,
            CompletionCertificate::PENDING_COORDINATOR,
        ], [
            'status' => CompletionCertificate::COORDINATOR_REJECTED,
            'coordinator_remarks' => $validated['remarks'],
            'coordinator_reviewed_by' => $user->id,
            'coordinator_reviewed_at' => now(),
        ]);

        AuditLogger::log('Certificate of Completion', 'Coordinator Rejection', 'Rejected COC: '.$validated['remarks'], $certificate->student_user_id);

        return back()->with('success', 'Certificate rejected. The student can now submit a corrected copy.');
    }

    public function view(CompletionCertificate $certificate)
    {
        return $this->document($certificate, false);
    }

    public function download(CompletionCertificate $certificate)
    {
        return $this->document($certificate, true);
    }

    private function document(CompletionCertificate $certificate, bool $download)
    {
        $user = User::find(Session::get('loginId'));
        abort_unless($user && $this->documentAccess->canAccessCompletionCertificate($user, $certificate), 404);

        $path = $this->documents->resolve($certificate->storage_directory, $certificate->file);

        if ($path === null) {
            return back()->with('error', 'Certificate file not found on the server.');
        }

        $name = Str::slug($certificate->studentUser?->full_name ?: 'student', '_').'_certificate_of_completion.pdf';

        return $download
            ? $this->documentResponse->download($path, $name)
            : $this->documentResponse->inlinePdf($path, $name);
    }

    private function transition(CompletionCertificate $certificate, string|array $requiredStatus, array $updates): CompletionCertificate
    {
        return DB::transaction(function () use ($certificate, $requiredStatus, $updates) {
            $locked = CompletionCertificate::current()->lockForUpdate()->findOrFail($certificate->id);
            $requiredStatuses = (array) $requiredStatus;

            if (! in_array($locked->status, $requiredStatuses, true)) {
                abort(422, 'This certificate has already been reviewed or is not ready for this action.');
            }

            $locked->update($updates);

            return $locked->fresh();
        });
    }

    private function reviewQuery(Request $request)
    {
        $query = CompletionCertificate::with(['student.user', 'professor', 'professorReviewer', 'coordinatorReviewer'])
            ->current()
            ->latest('created_at');
        $status = trim((string) $request->query('status', ''));
        $search = trim((string) $request->query('search', ''));
        $program = trim((string) $request->query('program', ''));
        $schoolYear = trim((string) $request->query('school_year', ''));

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($search !== '') {
            $query->whereHas('student', function ($studentQuery) use ($search) {
                $studentQuery->where('studentNum', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($userQuery) => $userQuery->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($program !== '') {
            $query->whereHas('student', fn ($studentQuery) => $studentQuery->where('course', $program));
        }

        $schoolYearParts = preg_split('/\s*-\s*/', $schoolYear);

        if (count($schoolYearParts) === 2 && ctype_digit($schoolYearParts[0]) && ctype_digit($schoolYearParts[1])) {
            $query->whereHas('student', function ($studentQuery) use ($schoolYearParts) {
                $studentQuery->where('school_year_start', $schoolYearParts[0])
                    ->where('school_year_end', $schoolYearParts[1]);
            });
        }

        return $query;
    }

    private function reviewFilterOptions(?int $professorId = null): array
    {
        $students = Student::query()
            ->select(['course', 'school_year_start', 'school_year_end'])
            ->whereHas('completionCertificates', function ($certificateQuery) use ($professorId) {
                $certificateQuery->current();

                if ($professorId !== null) {
                    $certificateQuery->where('professor_id', $professorId);
                }
            })
            ->get();

        $programs = $students->pluck('course')
            ->map(fn ($program) => trim((string) $program))
            ->filter()
            ->unique()
            ->sortBy(fn ($program) => strtolower($program))
            ->values();

        $schoolYears = $students
            ->filter(fn ($student) => $student->school_year_start && $student->school_year_end)
            ->map(fn ($student) => $student->school_year_start.'-'.$student->school_year_end)
            ->unique()
            ->sortDesc()
            ->values();

        return compact('programs', 'schoolYears');
    }

    private function reviewPerPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, [10, 25, 50], true) ? $perPage : 10;
    }

    private function currentUser(int $role): User
    {
        $user = User::find(Session::get('loginId'));
        abort_unless($user && (int) $user->role === $role, 403);

        return $user;
    }

    private function viewData(string $portal, User $user, array $data): array
    {
        return array_merge($data, [
            'portal' => $portal,
            'user' => $user,
            'statuses' => [
                CompletionCertificate::PENDING_PROFESSOR => 'Professor Review Pending (Optional)',
                CompletionCertificate::PENDING_COORDINATOR => 'Pending Coordinator Review',
                CompletionCertificate::APPROVED => 'Fully Approved',
                CompletionCertificate::PROFESSOR_REJECTED => 'Professor Rejected',
                CompletionCertificate::COORDINATOR_REJECTED => 'Coordinator Rejected',
            ],
        ]);
    }
}
