<?php

namespace Tests\Unit;

use App\Models\Company;
use App\Models\CompletionCertificate;
use App\Models\FileRequirement;
use App\Models\Professor;
use App\Models\Student;
use App\Models\UploadedFile;
use App\Models\User;
use App\Services\DocumentAccessService;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\TestCase;

class DocumentAccessServiceTest extends TestCase
{
    public function test_global_templates_are_available_to_authenticated_roles(): void
    {
        $file = (new UploadedFile)->forceFill(['class_id' => null]);

        $this->assertTrue($this->service()->canAccessTemplate($this->user(0), $file));
        $this->assertTrue($this->service()->canAccessTemplate($this->user(2), $file));
    }

    public function test_students_only_access_templates_for_their_class(): void
    {
        $student = new Student(['class_id' => 14]);
        $user = $this->user(0);
        $user->setRelation('studentInfo', $student);

        $matchingFile = (new UploadedFile)->forceFill(['class_id' => 14]);
        $otherFile = (new UploadedFile)->forceFill(['class_id' => 15]);

        $this->assertTrue($this->service()->canAccessTemplate($user, $matchingFile));
        $this->assertFalse($this->service()->canAccessTemplate($user, $otherFile));
    }

    public function test_requirement_access_is_owner_adviser_or_coordinator_scoped(): void
    {
        $requirement = new FileRequirement([
            'uploader_user_id' => 50,
            'uploadedBy' => 'Student Owner',
            'adviser' => 'Professor Assigned',
        ]);

        $student = $this->user(0, 50, 'Student Owner');
        $otherStudent = $this->user(0, 51, 'Other Student');
        $professor = $this->user(2, 60, 'Professor Assigned');
        $otherProfessor = $this->user(2, 61, 'Other Professor');

        $this->assertTrue($this->service()->canAccessRequirement($student, $requirement));
        $this->assertFalse($this->service()->canAccessRequirement($otherStudent, $requirement));
        $this->assertTrue($this->service()->canAccessRequirement($professor, $requirement));
        $this->assertFalse($this->service()->canAccessRequirement($otherProfessor, $requirement));
        $this->assertTrue($this->service()->canAccessRequirement($this->user(1), $requirement));
    }

    public function test_students_can_access_linked_moas_but_not_unrelated_moas(): void
    {
        $linkedCompany = (new Company)->forceFill(['company_name' => 'Linked Company']);
        $linkedCompany->id = 20;
        $otherCompany = (new Company)->forceFill(['company_name' => 'Other Company', 'file' => 'other.pdf']);
        $otherCompany->id = 21;

        $student = new Student(['course' => 'BSIT']);
        $student->id = 8;
        $student->setRelation('companies', new Collection([$linkedCompany]));

        $user = $this->user(0, 70, 'Student User');
        $user->setRelation('studentInfo', $student);

        $this->assertTrue($this->service()->canAccessMoa($user, $linkedCompany));
        $this->assertFalse($this->service()->canAccessMoa($user, $otherCompany));
    }

    public function test_completion_certificate_access_is_owner_professor_or_coordinator_scoped(): void
    {
        $studentProfile = new Student(['adviser_name' => 'Professor Assigned']);
        $certificate = new CompletionCertificate([
            'student_user_id' => 50,
            'professor_id' => 7,
        ]);
        $certificate->setRelation('student', $studentProfile);

        $student = $this->user(0, 50, 'Student Owner');
        $otherStudent = $this->user(0, 51, 'Other Student');
        $professor = $this->user(2, 60, 'Professor Assigned');
        $professorProfile = new Professor;
        $professorProfile->id = 7;
        $professor->setRelation('professorInfo', $professorProfile);
        $otherProfessor = $this->user(2, 61, 'Other Professor');
        $otherProfessorProfile = new Professor;
        $otherProfessorProfile->id = 8;
        $otherProfessor->setRelation('professorInfo', $otherProfessorProfile);

        $this->assertTrue($this->service()->canAccessCompletionCertificate($student, $certificate));
        $this->assertFalse($this->service()->canAccessCompletionCertificate($otherStudent, $certificate));
        $this->assertTrue($this->service()->canAccessCompletionCertificate($professor, $certificate));
        $this->assertFalse($this->service()->canAccessCompletionCertificate($otherProfessor, $certificate));
        $this->assertTrue($this->service()->canAccessCompletionCertificate($this->user(1), $certificate));
    }

    public function test_unlinked_students_can_preview_active_course_matching_moas(): void
    {
        $student = new Student(['course' => 'BSIT']);
        $student->setRelation('companies', new Collection);

        $user = $this->user(0, 70, 'Student User');
        $user->setRelation('studentInfo', $student);

        $matchingCompany = (new Company)->forceFill([
            'file' => 'matching.pdf',
            'course' => 'BSIT, BSCS',
            'valid_until' => now()->addDay()->toDateString(),
        ]);
        $otherCourse = (new Company)->forceFill([
            'file' => 'other.pdf',
            'course' => 'BSA',
            'valid_until' => now()->addDay()->toDateString(),
        ]);

        $this->assertTrue($this->service()->canAccessMoa($user, $matchingCompany));
        $this->assertFalse($this->service()->canAccessMoa($user, $otherCourse));
        $this->assertTrue($this->service()->canAccessMoa($this->user(2), $otherCourse));
    }

    private function service(): DocumentAccessService
    {
        return new DocumentAccessService(new \App\Services\MoaValidityService);
    }

    private function user(int $role, int $id = 1, string $name = 'Test User'): User
    {
        $user = new User(['role' => $role, 'full_name' => $name]);
        $user->id = $id;

        return $user;
    }
}
