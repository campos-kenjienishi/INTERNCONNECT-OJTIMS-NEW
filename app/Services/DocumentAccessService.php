<?php

namespace App\Services;

use App\Models\Classes;
use App\Models\Company;
use App\Models\FileRequirement;
use App\Models\Student;
use App\Models\UploadedFile;
use App\Models\User;

class DocumentAccessService
{
    public function __construct(private readonly MoaValidityService $moaValidity)
    {
    }

    public function canAccessTemplate(User $user, UploadedFile $file): bool
    {
        $classId = (int) ($file->class_id ?? 0);

        if ($classId === 0) {
            return true;
        }

        if ((int) $user->role === 1) {
            return true;
        }

        if ((int) $user->role === 2) {
            if (! empty($file->uploader_user_id) && (int) $file->uploader_user_id === (int) $user->id) {
                return true;
            }

            return Classes::where('id', $classId)
                ->where('adviser_name', $user->full_name)
                ->exists();
        }

        if ((int) $user->role !== 0) {
            return false;
        }

        $student = $this->studentFor($user);

        return $student !== null && (int) $student->class_id === $classId;
    }

    public function canAccessRequirement(User $user, FileRequirement $requirement): bool
    {
        if ((int) $user->role === 1) {
            return true;
        }

        if ((int) $user->role === 2) {
            return trim((string) $requirement->adviser) === trim((string) $user->full_name);
        }

        if ((int) $user->role !== 0) {
            return false;
        }

        if (! empty($requirement->uploader_user_id)) {
            return (int) $requirement->uploader_user_id === (int) $user->id;
        }

        return trim((string) $requirement->uploadedBy) === trim((string) $user->full_name);
    }

    public function canAccessMoa(User $user, Company $company): bool
    {
        if (in_array((int) $user->role, [1, 2], true)) {
            return true;
        }

        if ((int) $user->role !== 0) {
            return false;
        }

        if (trim((string) $company->uploader_name) === trim((string) $user->full_name)) {
            return true;
        }

        $student = $this->studentFor($user);

        if ($student === null) {
            return false;
        }

        $linkedCompanies = $student->relationLoaded('companies')
            ? $student->companies
            : $student->companies()->get();

        if ($linkedCompanies->contains('id', $company->id)) {
            return true;
        }

        if ($linkedCompanies->isNotEmpty() || empty($company->file) || ! $this->isActiveMoa($company)) {
            return false;
        }

        return $this->companyMatchesCourse($company, $student->course);
    }

    private function studentFor(User $user): ?Student
    {
        if ($user->relationLoaded('studentInfo')) {
            return $user->studentInfo;
        }

        return Student::where('user_id', $user->id)->first();
    }

    private function isActiveMoa(Company $company): bool
    {
        return $this->moaValidity->isActive($company);
    }

    private function companyMatchesCourse(Company $company, ?string $course): bool
    {
        $course = trim((string) $course);

        if ($course === '' || empty($company->course)) {
            return true;
        }

        $courses = preg_split('/[\r\n,]+/', (string) $company->course) ?: [];
        $courses = array_map(fn ($value) => mb_strtolower(trim((string) $value)), $courses);

        return in_array(mb_strtolower($course), $courses, true);
    }
}
