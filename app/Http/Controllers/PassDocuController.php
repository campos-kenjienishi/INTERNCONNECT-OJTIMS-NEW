<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Enroll;
use App\Models\Classes;
use App\Models\Company;
use App\Models\Courses;
Use App\Mail\TemporaryPasswordNotification;
use App\Mail\RequirementDenied;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Schedule;
use App\Models\Professor;
use Illuminate\Support\Str;
use App\Models\FileCategory;
use App\Models\UploadedFile;
use Illuminate\Http\Request;
use App\Models\Announcements;
use App\Models\OJTInformation;
use App\Models\FileRequirement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Helpers\AuditLogger; 

class PassDocuController extends Controller
{
    private function resolveUserFromStudent(Student $student): ?User
    {
        $student->loadMissing('user');

        if ($student->user) {
            return $student->user;
        }

        $fullName = trim((string) ($student->full_name ?? ''));
        if ($fullName === '') {
            return null;
        }

        return User::where('full_name', $fullName)->first();
    }

    private function normalizeRequirementPhase(?string $phase): string
    {
        return $phase === 'basic' ? 'basic' : 'other';
    }

    private function sortCategoriesByPhaseAndName($categories)
    {
        return $categories
            ->sortBy(function ($category) {
                $phaseOrder = $this->normalizeRequirementPhase($category->phase ?? null) === 'basic' ? 0 : 1;
                return sprintf('%d-%s', $phaseOrder, mb_strtolower(trim((string) $category->fileName)));
            }, SORT_NATURAL | SORT_FLAG_CASE)
            ->values();
    }
    private function ensureDefaultBasicCategoriesForProfessor(?Professor $professor): void
    {
        if (!$professor) {
            return;
        }

        $defaultNames = [
            'Resume',
            'Medical Clearance',
            'Good Moral',
            'Consent Form',
            'Endorsement Letter',
            'Acceptance Letter'
        ];

        $existingNames = FileCategory::where('professor_id', $professor->id)
            ->pluck('fileName')
            ->filter()
            ->map(fn($n) => mb_strtolower(trim((string)$n)))
            ->toArray();

        foreach ($defaultNames as $name) {
            if (!in_array(mb_strtolower($name), $existingNames, true)) {
                FileCategory::create([
                    'fileName' => $name,
                    'phase' => 'basic',
                    'uploadedBy' => $professor->full_name ?? 'System',
                    'professor_id' => $professor->id,
                ]);
            }
        }
    }

    private function buildRequirementPhaseState(User $user, ?Student $student, ?Professor $professor): array
    {
        if ($professor) {
            $this->ensureDefaultBasicCategoriesForProfessor($professor);
        }

        $fileCategories = $professor
            ? $this->sortCategoriesByPhaseAndName(
                FileCategory::where('professor_id', $professor->id)->get()
            )
            : collect();

        $defaultBasicNames = [
            'Resume',
            'Medical Clearance',
            'Good Moral',
            'Consent Form',
            'Endorsement Letter',
            'Acceptance Letter'
        ];

        if ($fileCategories->isEmpty()) {
            $fileCategories = collect($defaultBasicNames)->map(function ($name) {
                $fc = new FileCategory();
                $fc->id = 0;
                $fc->fileName = $name;
                $fc->phase = 'basic';
                $fc->uploadedBy = 'System';
                return $fc;
            });
        } else {
            $existingBasicNames = $fileCategories
                ->filter(fn ($c) => $this->normalizeRequirementPhase($c->phase ?? null) === 'basic')
                ->pluck('fileName')
                ->map(fn ($n) => mb_strtolower(trim((string)$n)))
                ->toArray();

            foreach ($defaultBasicNames as $defaultName) {
                if (!in_array(mb_strtolower($defaultName), $existingBasicNames, true)) {
                    $fc = new FileCategory();
                    $fc->id = 0;
                    $fc->fileName = $defaultName;
                    $fc->phase = 'basic';
                    $fc->uploadedBy = 'System';
                    $fileCategories->push($fc);
                }
            }

            $fileCategories = $this->sortCategoriesByPhaseAndName($fileCategories);
        }

        $basicCategories = $fileCategories
            ->filter(fn ($category) => $this->normalizeRequirementPhase($category->phase ?? null) === 'basic')
            ->values();
        $otherCategories = $fileCategories
            ->filter(fn ($category) => $this->normalizeRequirementPhase($category->phase ?? null) !== 'basic')
            ->values();

        $submittedRequirements = FileRequirement::forUser($user)->get();
        $submittedRequirementNames = $submittedRequirements
            ->pluck('fileName')
            ->filter()
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->unique()
            ->values();
        $validSubmittedNames = $submittedRequirements
            ->filter(fn ($requirement) => (int) ($requirement->status ?? 0) !== 2)
            ->pluck('fileName')
            ->filter()
            ->map(fn ($name) => mb_strtolower(trim((string) $name)))
            ->unique()
            ->values();

        $submittedBasicNames = $basicCategories
            ->filter(fn ($category) => $validSubmittedNames->contains(mb_strtolower(trim((string) $category->fileName))))
            ->pluck('fileName')
            ->values();
        $missingBasicCategories = $basicCategories
            ->reject(fn ($category) => $validSubmittedNames->contains(mb_strtolower(trim((string) $category->fileName))))
            ->values();

        $hasSubmittedNotarizedMoa = $validSubmittedNames->contains(mb_strtolower('Notarized MOA'));
        $otherRequirementsUnlocked = $missingBasicCategories->isEmpty() && $hasSubmittedNotarizedMoa;

        return [
            'fileCategories' => $fileCategories,
            'basicCategories' => $basicCategories,
            'otherCategories' => $otherCategories,
            'submittedRequirements' => $submittedRequirements,
            'submittedRequirementNames' => $submittedRequirementNames,
            'submittedBasicNames' => $submittedBasicNames,
            'missingBasicCategories' => $missingBasicCategories,
            'hasSubmittedNotarizedMoa' => $hasSubmittedNotarizedMoa,
            'otherRequirementsUnlocked' => $otherRequirementsUnlocked,
        ];
    }

    private function updateCompanyStudentDisplay(Company $company, ?string $removedName = null): void
    {
        if (!Schema::hasColumn('companies', 'student_names_display')) {
            return;
        }

        $existingNames = collect(explode(',', (string) ($company->student_names_display ?? '')))
            ->map(fn ($name) => trim((string) $name))
            ->filter();

        $linkedNames = $company->students()->with('user')->get()
            ->pluck('full_name')
            ->map(fn ($name) => trim((string) $name))
            ->filter();

        $manualNames = $existingNames->reject(function ($name) use ($linkedNames, $removedName) {
            return $linkedNames->contains($name) || (!empty($removedName) && $name === trim($removedName));
        });

        $company->student_names_display = $manualNames
            ->merge($linkedNames)
            ->filter()
            ->unique()
            ->implode(', ');

        $company->save();
    }

    private function syncStudentNotarizedRequirement(Company $company, Student $student, ?FileRequirement $sourceRequirement = null): void
    {
        $user = $this->resolveUserFromStudent($student);

        if (!$user) {
            return;
        }

        $sourceRequirement = $sourceRequirement ?: FileRequirement::where('uploadedBy', $company->uploader_name)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->latest('id')
            ->first();

        $requirement = FileRequirement::forUser($user)
            ->where('fileName', 'Notarized MOA')
            ->where('file', $company->file)
            ->first();

        $requirement = $requirement ?: new FileRequirement();
        $requirement->fileName = 'Notarized MOA';
        $requirement->file = $company->file;
        $requirement->status = $sourceRequirement->status ?? 0;
        $requirement->adviser = $user->adviser_name;
        $requirement->uploadedBy = $user->full_name;
        $requirement->uploader_user_id = $user->id;

        if (Schema::hasColumn('file_requirements', 'denial_reason')) {
            $requirement->denial_reason = $sourceRequirement->denial_reason ?? null;
        }

        if (Schema::hasColumn('file_requirements', 'professor_id') && isset($sourceRequirement->professor_id)) {
            $requirement->professor_id = $sourceRequirement->professor_id;
        }

        $requirement->save();
    }

    private function reconcileStudentNotarizedRequirements(User $user): void
    {
        $student = Student::where('user_id', $user->id)->first();

        if (!$student) {
            FileRequirement::forUser($user)
                ->where('fileName', 'Notarized MOA')
                ->delete();
            return;
        }

        $linkedCompanies = $student->companies()->get()->filter(fn ($company) => !empty($company->file))->values();
        $validFiles = $linkedCompanies->pluck('file')->filter()->unique()->values();

        $query = FileRequirement::forUser($user)
            ->where('fileName', 'Notarized MOA');

        if ($validFiles->isEmpty()) {
            $query->delete();
            return;
        }

        $query->whereNotIn('file', $validFiles)->delete();

        foreach ($linkedCompanies as $linkedCompany) {
            $this->syncStudentNotarizedRequirement($linkedCompany, $student);
        }
    }

    private function transferCompanyOwnership(Company $company, ?FileRequirement $sourceRequirement = null): ?string
    {
        $company->loadMissing('students.user');

        $newOwner = $company->students->first(function ($student) {
            return !empty(trim((string) ($student->full_name ?? ''))) || $this->resolveUserFromStudent($student);
        });

        if (!$newOwner) {
            return null;
        }

        $newOwnerUser = $this->resolveUserFromStudent($newOwner);
        $newOwnerName = trim((string) ($newOwnerUser->full_name ?? $newOwner->full_name ?? ''));

        if ($newOwnerName === '') {
            return null;
        }

        $company->uploader_name = $newOwnerName;
        $company->save();

        if (class_exists(\App\Models\Voucher::class)) {
            \App\Models\Voucher::where('company_id', $company->id)
                ->update(['uploader_name' => $newOwnerName]);
        }

        $this->syncStudentNotarizedRequirement($company, $newOwner, $sourceRequirement);

        if ($newOwnerUser) {
            $this->reconcileStudentNotarizedRequirements($newOwnerUser);
        }

        return $newOwnerName;
    }

    private function requireStudentSession()
    {
        if (!Session::has('loginId')) {
            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        $user = User::where('id', Session::get('loginId'))->first();

        if (!$user || (string) $user->role !== '0') {
            request()->session()->invalidate();
            request()->session()->regenerateToken();

            return response()->view('errors.something-went-wrong', ['statusCode' => 401], 401);
        }

        return $user;
    }

    private function propagateSharedNotarizedMoaStatus(FileRequirement $fileRequirement, int $status, ?string $reason = null): void
    {
        if ($fileRequirement->fileName !== 'Notarized MOA' || empty($fileRequirement->file)) {
            $fileRequirement->status = $status;
            if (Schema::hasColumn('file_requirements', 'denial_reason')) {
                $fileRequirement->denial_reason = $status === 2 ? $reason : null;
            }
            $fileRequirement->save();
            return;
        }

        $company = Company::with('students')
            ->where('file', $fileRequirement->file)
            ->first();

        $uploadedByNames = collect([$fileRequirement->uploadedBy]);

        if ($company) {
            $uploadedByNames = $company->students->pluck('full_name')
                ->filter()
                ->push($company->uploader_name)
                ->unique()
                ->values();
        }

        $query = FileRequirement::where('fileName', 'Notarized MOA')
            ->where('file', $fileRequirement->file)
            ->whereIn('uploadedBy', $uploadedByNames);

        $updates = ['status' => $status];

        if (Schema::hasColumn('file_requirements', 'denial_reason')) {
            $updates['denial_reason'] = $status === 2 ? $reason : null;
        }

        $query->update($updates);
    }

    public function maintainFileCategory() {
        $data = [];
        $userName = '';

        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name ?? '';
        }

        $professor = Professor::where('user_id', $data->id)->first();
        if ($professor) {
            $this->ensureDefaultBasicCategoriesForProfessor($professor);
        }

        $files = $professor
            ? $this->sortCategoriesByPhaseAndName(
                FileCategory::where('professor_id', $professor->id)->get()
            )
            : collect();

        return view('professor.fileCategory', compact('data', 'userName', 'files'));
    }


    public function fileCategory(Request $request){
        $request->validate([
            'fileName' => 'required|string|max:255',
            'phase' => 'required|in:basic,other',
        ]);

        $user = User::where('id', Session::get('loginId'))->first();
        $professor = Professor::where('user_id', $user->id)->first();
        $trimmedName = trim((string)$request->fileName);

        $existing = FileCategory::where(function($q) use ($professor) {
                if ($professor) {
                    $q->where('professor_id', $professor->id);
                }
            })
            ->whereRaw('LOWER(TRIM(fileName)) = ?', [mb_strtolower($trimmedName)])
            ->first();

        if ($existing) {
            return back()->with('fail', 'Requirement category "' . $trimmedName . '" is already listed.');
        }

        $files = new FileCategory();
        $files->fileName = $trimmedName;
        $files->phase = $this->normalizeRequirementPhase($request->phase);
        $files->uploadedBy = $request->uploadedBy ?? ($user->full_name ?? 'System');
        $files->professor_id = $professor ? $professor->id : null;
        $res = $files->save();

        if($res){
            AuditLogger::log(
                'FileCategory',
                'Create',
                'Added new file category: ' . $files->fileName,
                Session::get('loginId') ?? null,
                null,
                ['fileName' => $files->fileName, 'phase' => $files->phase, 'uploadedBy' => $files->uploadedBy, 'professor_id' => $files->professor_id]
            );
            return back()->with('success','File category added successfully!');
        }
        else{
            return back()->with('fail','Oh no! Something went wrong.');
        }
    }

    public function removeCategory($id)
    {

        $data = FileCategory::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        if (!empty($data->file)) {
            $filePath = public_path('assets/' . $data->file);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    
        $data->delete();
        AuditLogger::log(
            'FileCategory',
            'Delete',
            'Deleted file category: ' . $data->fileName,
            Session::get('loginId') ?? null,
            ['id' => $data->id, 'fileName' => $data->fileName, 'uploadedBy' => $data->uploadedBy],
            null
        );
        return redirect()->back();
    }

    public function updateCategory(Request $request, $id)
    {
        $request->validate([
            'fileName' => 'required|string|max:255',
            'phase' => 'required|in:basic,other',
        ]);

        $category = FileCategory::find($id);

        if (!$category) {
            return redirect()->back()->with('error', 'File category not found.');
        }

        $trimmedName = trim((string)$request->fileName);
        $existing = FileCategory::where('professor_id', $category->professor_id)
            ->where('id', '!=', $id)
            ->whereRaw('LOWER(TRIM(fileName)) = ?', [mb_strtolower($trimmedName)])
            ->first();

        if ($existing) {
            return back()->with('fail', 'Requirement category "' . $trimmedName . '" is already listed.');
        }

        $category->fileName = $trimmedName;
        $category->phase = $this->normalizeRequirementPhase($request->phase);
        $category->save();

        AuditLogger::log(
            'FileCategory',
            'Update',
            'Updated file category: ' . $category->fileName,
            Session::get('loginId') ?? null
        );

        return redirect()->back()->with('success', 'File category updated successfully.');
    }


    public function fileReq(Request $request)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $student = Student::where('user_id', $user->id)->first();
        $professor = $student ? Professor::where('full_name', $student->adviser_name)->first() : null;
        $phaseState = $this->buildRequirementPhaseState($user, $student, $professor);
        $data = $phaseState['submittedRequirements'];

        return view('students.fileReq', array_merge($phaseState, [
            'user' => $user,
            'data' => $data,
        ]));
    }

public function fileReqCreate(Request $request){
    $sessionCheck = $this->requireStudentSession();

    if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
        return $sessionCheck;
    }

    $request->validate([
        'fileName' => 'required|string',
        'phase' => 'required|in:basic,other',
        'file' => 'required|file|mimes:pdf|max:30720',
        'uploadedBy' => 'required|string',
        'adviser' => 'required|string',
    ], [
        'file.mimes' => 'Only PDF files are accepted for requirement uploads.',
    ]);

    $user = $sessionCheck;
    $student = Student::where('user_id', $user->id)->first();
    $professor = $student ? Professor::where('full_name', $student->adviser_name)->first() : null;
    $phaseState = $this->buildRequirementPhaseState($user, $student, $professor);

    $category = $phaseState['fileCategories']->first(function ($item) use ($request) {
        return trim((string) $item->fileName) === trim((string) $request->fileName);
    });

    if (!$category) {
        return back()->with('fail', 'Selected requirement category is not assigned to your professor.');
    }

    $categoryPhase = $this->normalizeRequirementPhase($category->phase ?? null);

    if ($request->phase !== $categoryPhase) {
        return back()->with('fail', 'Selected requirement category does not belong to the chosen phase.');
    }

    if ($categoryPhase === 'other' && !$phaseState['otherRequirementsUnlocked']) {
        return back()->with('fail', 'Submit all basic requirements and your Notarized MOA first before uploading other requirements.');
    }

    $normalizedCategoryName = mb_strtolower(trim((string) $request->fileName));
    $alreadySubmitted = $phaseState['submittedRequirementNames']
        ->contains($normalizedCategoryName);

    if ($alreadySubmitted) {
        return back()->withInput()->with('fail', 'This requirement is already submitted. Remove the existing submission first before uploading another file for it.');
    }
    
    // Create a new instance of FileRequirement model
    $fileup = new FileRequirement();
    $fileup->fileName = $request->fileName; 
    $file=$request->file;
    $filename=time().'.'.$file->getClientOriginalExtension();
    $request->file->move('assets',$filename);
    $fileup->file=$filename;
    $fileup->status = 0;
    $fileup->adviser = $request->adviser;
    $fileup->uploadedBy = $user->full_name;
    $fileup->uploader_user_id = $user->id;
    
    // Save the model instance
    $res = $fileup->save();

    if($res){
        AuditLogger::log(
            'PassDocu',
            'Upload',
            'Uploaded file: ' . $fileup->fileName,
            Session::get('loginId') ?? null,
            null,
            ['fileName' => $fileup->fileName, 'phase' => $categoryPhase, 'file' => $fileup->file, 'uploadedBy' => $fileup->uploadedBy]
        );
        return back()->with('success', 'File uploaded successfully!');
    } else {
        // If saving fails, delete the uploaded file
        Storage::delete('assets/' . $filename);
        return back()->with('fail', 'Failed to upload file.');
    }
}

public function removeFile($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $data = FileRequirement::find($id);

        if (!$data) {
            return redirect()->back()->with('error', 'File not found.');
        }

        $isOwner = false;
        if (!empty($data->uploader_user_id)) {
            $isOwner = ((int) $data->uploader_user_id === (int) $sessionCheck->id);
        } else {
            $isOwner = ($data->uploadedBy === $sessionCheck->full_name);
        }

        if (!$isOwner) {
            return redirect()->back()->with('error', 'You do not have permission to remove this file.');
        }

        if ($data->fileName === 'Notarized MOA' && !empty($data->file)) {
            $student = Student::where('user_id', $sessionCheck->id)->first();
            $company = Company::with('students')
                ->where('file', $data->file)
                ->where(function ($query) use ($data, $student) {
                    $query->where('uploader_name', $data->uploadedBy);

                    if ($student) {
                        $query->orWhereHas('students', function ($studentQuery) use ($student) {
                            $studentQuery->where('students.id', $student->id);
                        });
                    }
                })
                ->first();

            if ($company) {
                $isOwner = $company->uploader_name === $sessionCheck->full_name;
                $ownerRequirement = $isOwner ? clone $data : null;

                if ($student && $company->students->contains('id', $student->id)) {
                    $company->students()->detach($student->id);
                    $company = $company->fresh('students');
                    $this->updateCompanyStudentDisplay($company, $sessionCheck->full_name);
                }

                $this->reconcileStudentNotarizedRequirements($sessionCheck);

                if ($company->students->isEmpty()) {
                    $ownerRequirement = FileRequirement::where('uploadedBy', $company->uploader_name)
                        ->where('fileName', 'Notarized MOA')
                        ->where('file', $company->file);

                    if ($data->uploadedBy !== $company->uploader_name) {
                        $ownerRequirement->delete();
                    }

                    $filePath = public_path('assets/' . $company->file);
                    if (!empty($company->file) && file_exists($filePath)) {
                        @unlink($filePath);
                    }

                    $company->delete();
                } elseif ($isOwner) {
                    $newOwnerName = $this->transferCompanyOwnership($company, $ownerRequirement);
                    if ($newOwnerName) {
                        AuditLogger::log(
                            'PassDocu',
                            'Transfer Ownership',
                            'Transferred MOA ownership for ' . $company->company_name . ' to ' . $newOwnerName,
                            Session::get('loginId') ?? null,
                            ['previous_owner' => $sessionCheck->full_name],
                            ['new_owner' => $newOwnerName]
                        );
                    }
                }
            }
        }
    
        $data->delete();
        AuditLogger::log(
            'PassDocu',
            'Delete',
            'Deleted file: ' . $data->fileName,
            Session::get('loginId') ?? null,
            ['id' => $data->id, 'fileName' => $data->fileName, 'file' => $data->file],
            null
        );
        return redirect()->back();
    }

    public function viewFile($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $fileRequirement = FileRequirement::where('id', $id)
            ->forUser($user)
            ->firstOrFail();

        $filePath = public_path('assets/' . $fileRequirement->file);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found.');
        }

        return response()->file($filePath);
    }

    public function downloadStudent($id)
    {
        $sessionCheck = $this->requireStudentSession();

        if ($sessionCheck instanceof \Illuminate\Http\RedirectResponse) {
            return $sessionCheck;
        }

        $user = $sessionCheck;

        $fileRequirement = FileRequirement::where('id', $id)
            ->forUser($user)
            ->firstOrFail();

        $filePath = public_path('assets/' . $fileRequirement->file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileRequirement->file);
        }

        return back()->with(['error' => 'File not found.'], 404);
    }

    public function studentRequirements(Request $request){
        // Retrieve the value from the query parameter
        $value = $request->input('value');
        $data = [];

        $student = User::where('full_name', '=', $value)->first();
        if (!$student) {
            return back()->with('error', 'Student not found.');
        }

        $course = $student->course;
        $roomId = $request->input('roomId');
        if (empty($roomId) && isset($student->class_id)) {
            $roomId = $student->class_id;
        }
       
        
        if (Session::has('loginId')) {
            $data = User::where('id', '=', Session::get('loginId'))->first();
            $userName = $data->full_name;
        }
        $files = FileRequirement::where('adviser', '=', $data->full_name)
            ->forUser($student)
            ->get();
        
        return view('professor.studentRequire', compact('data','files', 'value','course', 'roomId'));

            
            }


            public function updateApproveStatus(Request $request, $id)
            {
                // Validate the request data if needed
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $this->propagateSharedNotarizedMoaStatus($fileRequirement, 1);
                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Approved file: ' . $fileRequirement->fileName,
                    Session::get('loginId') ?? null,
                    ['status' => 0],
                    ['status' => 1]
                );
                return back()->with('success', 'You have updated the information successfully!');
            }

            public function updateApproveStatusBulk(Request $request)
            {
                $request->validate([
                    'student_name' => 'required|string',
                    'roomId' => 'nullable',
                ]);

                $data = null;

                if (Session::has('loginId')) {
                    $data = User::where('id', Session::get('loginId'))->first();
                }

                if (!$data) {
                    return back()->with('error', 'Professor account not found.');
                }

                $files = FileRequirement::where('adviser', $data->full_name)
                    ->where('uploadedBy', $request->student_name)
                    ->whereNotIn('status', [1, 2])
                    ->get();

                if ($files->isEmpty()) {
                    return back()->with('info', 'There are no pending files to approve.');
                }

                foreach ($files as $fileRequirement) {
                    $this->propagateSharedNotarizedMoaStatus($fileRequirement, 1);
                }

                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Approved all pending files for student: ' . $request->student_name . ' (' . $files->count() . ' files)',
                    Session::get('loginId') ?? null
                );

                return back()->with('success', $files->count() . ' file(s) approved successfully.');
            }


            public function updateDeniedStatus(Request $request, $id)
            {
                $validated = $request->validate([
                    'reason' => 'required|string|max:1000',
                ]);
        
                // Find the file requirement by ID
                $fileRequirement = FileRequirement::findOrFail($id);
        
                // Update the status based on the request data
                $this->propagateSharedNotarizedMoaStatus($fileRequirement, 2, $validated['reason']);

                $student = User::where('role', 0)
                    ->where('full_name', $fileRequirement->uploadedBy)
                    ->first();

                if ($student && !empty($student->email)) {
                    Mail::to($student->email)->send(new RequirementDenied($fileRequirement, $validated['reason']));
                }

                AuditLogger::log(
                    'PassDocu',
                    'Update',
                    'Denied file: ' . $fileRequirement->fileName . '. Reason: ' . $validated['reason'],
                    Session::get('loginId') ?? null,
                    ['status' => 0],
                    ['status' => 2, 'denial_reason' => $validated['reason']]
                );
                return back()->with('success', 'You have updated the information successfully!');
            }

            public function requirementsView(Request $request){

                // Retrieve the value from the query parameter
                $value = $request->input('value');
                $file = $request->input('file');
                $roomId = $request->input('roomId');
                $data = [];
               
                
                if (Session::has('loginId')) {
                    $data = User::where('id', '=', Session::get('loginId'))->first();
                    $userName = $data->full_name;
                }
                $files=FileRequirement::where('adviser', '=',$data->full_name)
                                        ->where('uploadedBy', '=', $value)
                                        ->where('fileName', '=', $file)
                                        ->get();
                
                return view('professor.requireView', compact('data','files','value','file','roomId'));
        
                    
                    }

     public function download($id)
    {
        // Find the FileRequirement by ID
        $fileRequirement = FileRequirement::findOrFail($id);

        // Get the file path
        $filePath = public_path('assets/' . $fileRequirement->file);

        // Check if the file exists
        if (file_exists($filePath)) {
            // Return the file as a download response
            return response()->download($filePath, $fileRequirement->file);
        } else {
            // File not found
            return back()->with(['error' => 'File not found.'], 404);
        }
    }

    public function coordinatorStudentRequirements(Request $request)
    {
        $user = null;
        if (Session::has('loginId')) {
            $user = User::where('id', '=', Session::get('loginId'))->first();
        }

        if (!$user || (int)$user->role !== 1) {
            return redirect('/login');
        }

        $selectedCourse = trim((string)$request->query('course', ''));
        $selectedSchoolYear = trim((string)$request->query('school_year', ''));
        $selectedProfessorId = trim((string)$request->query('professor_id', ''));
        $selectedStatus = trim((string)$request->query('status', ''));
        $search = trim((string)$request->query('search', ''));

        $dbCourses = Courses::pluck('course')->filter()->map(fn($c) => trim((string)$c));
        $studentCourses = Student::pluck('course')->filter()->map(fn($c) => trim((string)$c));
        $courses = $dbCourses->merge($studentCourses)->unique()->sort()->values();

        $professors = Professor::orderBy('full_name')->get();

        $studentSchoolYears = Student::whereNotNull('school_year_start')
            ->whereNotNull('school_year_end')
            ->get()
            ->map(fn($s) => trim($s->school_year_start . '-' . $s->school_year_end))
            ->filter();

        $classSchoolYears = Classes::whereNotNull('school_year_start')
            ->whereNotNull('school_year_end')
            ->get()
            ->map(fn($c) => trim($c->school_year_start . '-' . $c->school_year_end))
            ->filter();

        $schoolYears = $studentSchoolYears
            ->merge($classSchoolYears)
            ->merge(['2026-2027', '2025-2026', '2024-2025'])
            ->unique()
            ->sortDesc()
            ->values();

        $query = Student::with(['user', 'companies'])
            ->orderBy('school_year_end', 'desc')
            ->orderBy('school_year_start', 'desc')
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc');

        if ($selectedCourse !== '') {
            $query->where('course', $selectedCourse);
        }

        if ($selectedSchoolYear !== '') {
            if (str_contains($selectedSchoolYear, '-')) {
                [$syStart, $syEnd] = explode('-', $selectedSchoolYear, 2);
                $query->where(function ($q) use ($syStart, $syEnd) {
                    $q->where(function ($sub) use ($syStart, $syEnd) {
                        $sub->where('school_year_start', trim($syStart))
                            ->where('school_year_end', trim($syEnd));
                    });
                });
            }
        }

        if ($selectedProfessorId !== '') {
            $prof = Professor::find($selectedProfessorId);
            if ($prof && !empty($prof->full_name)) {
                $query->where('adviser_name', $prof->full_name);
            }
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('studentNum', 'like', '%' . $search . '%')
                    ->orWhere('year_and_section', 'like', '%' . $search . '%')
                    ->orWhere('course', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('full_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $students = $query->get();

        $userIds = $students->pluck('user_id')->filter()->toArray();
        $userNames = $students->pluck('full_name')->filter()->toArray();

        $allRequirements = FileRequirement::where(function ($q) use ($userIds, $userNames) {
            if (!empty($userIds)) {
                $q->whereIn('uploader_user_id', $userIds);
            }
            if (!empty($userNames)) {
                $q->orWhereIn('uploadedBy', $userNames);
            }
        })->get();

        $studentMatrix = $students->map(function ($student) use ($allRequirements) {
            $studentUser = $student->user;
            $professor = !empty($student->adviser_name) && $student->adviser_name !== 'Not Yet Listed'
                ? Professor::where('full_name', $student->adviser_name)->first()
                : null;

            $phaseState = $this->buildRequirementPhaseState($studentUser ?: new User(), $student, $professor);

            $studentCategoryNames = $phaseState['basicCategories']
                ->pluck('fileName')
                ->filter()
                ->map(fn($n) => trim((string)$n))
                ->unique();

            if (!$studentCategoryNames->contains('Notarized MOA')) {
                $studentCategoryNames->prepend('Notarized MOA');
            }

            $categoryList = $studentCategoryNames->values();

            $userReqs = $allRequirements->filter(function ($req) use ($student, $studentUser) {
                if (!empty($req->uploader_user_id) && $studentUser) {
                    return (int)$req->uploader_user_id === (int)$studentUser->id;
                }
                return !empty($student->full_name) && $req->uploadedBy === $student->full_name;
            });

            $categoriesState = [];
            $submittedCount = 0;
            $missingCount = 0;

            foreach ($categoryList as $catName) {
                $matchingReq = $userReqs->first(function ($r) use ($catName) {
                    return mb_strtolower(trim((string)$r->fileName)) === mb_strtolower(trim((string)$catName));
                });

                if ($matchingReq) {
                    $submittedCount++;
                    $categoriesState[$catName] = [
                        'submitted' => true,
                        'status' => (int)($matchingReq->status ?? 0),
                        'file_id' => $matchingReq->id,
                        'file_name' => $matchingReq->file,
                        'denial_reason' => $matchingReq->denial_reason ?? null,
                    ];
                } else {
                    $missingCount++;
                    $categoriesState[$catName] = [
                        'submitted' => false,
                        'status' => -1,
                        'file_id' => null,
                        'file_name' => null,
                        'denial_reason' => null,
                    ];
                }
            }

            $totalCategories = count($categoryList);
            $isFullySubmitted = ($totalCategories > 0 && $submittedCount >= $totalCategories);

            return [
                'student' => $student,
                'user' => $studentUser,
                'categories' => $categoriesState,
                'category_list' => $categoryList,
                'total_categories' => $totalCategories,
                'submitted_count' => $submittedCount,
                'missing_count' => $missingCount,
                'is_fully_submitted' => $isFullySubmitted,
                'all_files' => $userReqs->values(),
            ];
        });

        if ($selectedStatus !== '') {
            $studentMatrix = $studentMatrix->filter(function ($item) use ($selectedStatus) {
                if ($selectedStatus === 'completed' || $selectedStatus === 'complete') return $item['is_fully_submitted'];
                if ($selectedStatus === 'incomplete') return !$item['is_fully_submitted'];
                return true;
            })->values();
        }

        $totalStudentsTracked = count($studentMatrix);
        $fullySubmittedCount = $studentMatrix->filter(fn($i) => $i['is_fully_submitted'])->count();
        $incompleteCount = $studentMatrix->filter(fn($i) => !$i['is_fully_submitted'])->count();
        $totalFilesSubmitted = $allRequirements->count();

        return view('ojtCoordinator.studentRequirements', compact(
            'user',
            'courses',
            'schoolYears',
            'professors',
            'studentMatrix',
            'selectedCourse',
            'selectedSchoolYear',
            'selectedProfessorId',
            'selectedStatus',
            'search',
            'totalStudentsTracked',
            'fullySubmittedCount',
            'incompleteCount',
            'totalFilesSubmitted'
        ));
    }

    public function coordinatorViewRequirement($id)
    {
        $fileRequirement = FileRequirement::findOrFail($id);
        $filePath = public_path('assets/' . $fileRequirement->file);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found on server.');
        }

        return response()->file($filePath);
    }

    public function coordinatorDownloadRequirement($id)
    {
        $fileRequirement = FileRequirement::findOrFail($id);
        $filePath = public_path('assets/' . $fileRequirement->file);

        if (file_exists($filePath)) {
            return response()->download($filePath, $fileRequirement->file);
        }

        return back()->with('error', 'File not found on server.');
    }
}
