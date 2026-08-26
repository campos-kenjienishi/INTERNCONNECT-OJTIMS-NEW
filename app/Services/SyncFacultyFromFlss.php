<?php

namespace App\Services;

use App\Models\User;
use App\Models\Professor;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SyncFacultyFromFlss
{
    protected FacultyApiService $api;
    protected IdpService $idp;
    protected array $idpUsersByEmail = [];

    public function __construct(FacultyApiService $api, IdpService $idp)
    {
        $this->api = $api;
        $this->idp = $idp;
    }

    /**
     * Execute the faculty sync from FLSS.
     *
     * @return array Summary with keys: created, updated, skipped, errors
     */
    public function execute(): array
    {
        $summary = [
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors'  => [],
        ];

        // 1. Fetch IdP users map to backfill any missing UUIDs
        try {
            $accessToken = $this->idp->getClientCredentialsToken();
            $page = 1;
            $limit = 100;
            do {
                $response = $this->idp->getAdminUserList($accessToken, $page, $limit);
                $users = $response['data'] ?? $response['users'] ?? $response;
                if (!is_array($users) || empty($users) || !isset($users[0]) || !is_array($users[0])) {
                    break;
                }
                foreach ($users as $u) {
                    $uEmail = strtolower(trim($u['email'] ?? ''));
                    if (!empty($uEmail)) {
                        $this->idpUsersByEmail[$uEmail] = $u;
                    }
                }
                if (count($users) < $limit) {
                    break;
                }
                $page++;
                if ($page > 50) break;
            } while (true);
        } catch (\Exception $e) {
            Log::info('FLSS sync: IdP user map not loaded (will rely on FLSS data): ' . $e->getMessage());
        }

        $response = $this->api->getFacultyList();

        if (!$response || !isset($response['faculties'])) {
            $summary['errors'][] = 'Failed to fetch faculty list from FLSS API.';
            Log::error('FLSS sync: Failed to fetch faculty list', ['response' => $response]);
            return $summary;
        }

        $faculties = $response['faculties'];

        if (empty($faculties)) {
            Log::info('FLSS sync: No faculty records returned from API.');
            return $summary;
        }

        foreach ($faculties as $faculty) {
            try {
                $this->processFacultyRecord($faculty, $summary);
            } catch (\Exception $e) {
                $summary['errors'][] = 'Error processing ' . ($faculty['first_name'] ?? '') . ' ' . ($faculty['last_name'] ?? '') . ': ' . $e->getMessage();
                Log::error('FLSS sync: Error processing faculty', [
                    'faculty' => $faculty,
                    'error'   => $e->getMessage(),
                ]);
            }
        }

        $summary['missing_accounts'] = $this->getMissingFacultyAccounts($faculties);

        Log::info('FLSS sync completed', $summary);

        return $summary;
    }

    /**
     * Process a single faculty record from FLSS.
     */
    protected function processFacultyRecord(array $faculty, array &$summary): void
    {
        $email     = $faculty['email'] ?? null;
        $firstName = $faculty['first_name'] ?? '';
        $lastName  = $faculty['last_name'] ?? '';
        $middleName = $faculty['middle_name'] ?? '';
        $suffix    = $faculty['suffix_name'] ?? '';
        $status    = $faculty['status'] ?? 'Active';

        if (empty($email) || empty($firstName) || empty($lastName)) {
            $summary['skipped']++;
            return;
        }

        // Skip inactive faculty
        if (strtolower($status) !== 'active') {
            $summary['skipped']++;
            return;
        }

        // Only sync official PUP emails (ignore @example.com, test emails, non-pup emails)
        if (!str_ends_with(strtolower(trim($email)), '@pup.edu.ph')) {
            $summary['skipped']++;
            return;
        }

        $fullName = trim($firstName . ' ' . $lastName);

        // 1. Try exact email match
        $user = User::where('email', $email)->first();

        // 2. If no email match, try name match (for professors with dummy emails)
        if (!$user) {
            $user = $this->findByNormalizedName($firstName, $lastName);
        }

        if ($user) {
            // Update existing user
            $this->updateExistingUser($user, $faculty);
            $summary['updated']++;
        } else {
            // Create new user + professor record
            $this->createNewProfessor($faculty);
            $summary['created']++;
        }
    }

    /**
     * Find an existing user by normalized name match (case-insensitive).
     */
    protected function findByNormalizedName(string $firstName, string $lastName): ?User
    {
        $normalizedTarget = Str::lower(trim($firstName . ' ' . $lastName));

        // Search for professors (role=2) whose full_name matches
        $users = User::where('role', 2)->get();

        foreach ($users as $user) {
            $normalizedExisting = Str::lower(trim($user->full_name ?? ''));
            if ($normalizedExisting === $normalizedTarget) {
                return $user;
            }

            // Also try first_name + last_name fields
            $nameFromFields = Str::lower(trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')));
            if ($nameFromFields === $normalizedTarget) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Update an existing user and professor record with FLSS data.
     */
    protected function updateExistingUser(User $user, array $faculty): void
    {
        $email     = $faculty['email'];
        $firstName = $faculty['first_name'] ?? '';
        $lastName  = $faculty['last_name'] ?? '';
        $middleName = $faculty['middle_name'] ?? '';
        $suffix    = $faculty['suffix_name'] ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);

        // Update email if it was a dummy (replace with real FLSS email)
        if ($user->email !== $email) {
            // Only update if the new email isn't already taken by another user
            $emailTaken = User::where('email', $email)->where('id', '!=', $user->id)->exists();
            if (!$emailTaken) {
                $user->email = $email;
            }
        }

        // Update name fields
        $user->first_name = $firstName;
        $user->last_name = $lastName;
        $user->middle_name = $middleName ?: $user->middle_name;
        $user->suffix = $suffix ?: $user->suffix;
        $user->full_name = $fullName;

        // Ensure role is professor - NEVER downgrade a coordinator (role = 1)
        if ((int)$user->role !== 1) {
            $user->role = 2;
        }

        // Link IdP user ID from FLSS or IdP map if available and not already set
        if (!$user->idp_user_id) {
            $idpId = $faculty['idp_user_id'] ?? null;
            if (empty($idpId) && !empty($this->idpUsersByEmail[strtolower(trim($email))])) {
                $idpId = $this->idpUsersByEmail[strtolower(trim($email))]['id'] ?? $this->idpUsersByEmail[strtolower(trim($email))]['user_id'] ?? null;
            }
            if (!empty($idpId)) {
                $user->idp_user_id = $idpId;
            }
        }

        $user->save();

        // Ensure Professor record exists and is linked
        $professor = Professor::where('user_id', $user->id)->first();
        if (!$professor) {
            $professor = Professor::where('full_name', $fullName)->first();
        }

        if ($professor) {
            $professor->user_id = $user->id;
            $professor->full_name = $fullName;
            $professor->email = $user->email;
            $professor->save();
        } else {
            $professor = new Professor();
            $professor->user_id = $user->id;
            $professor->full_name = $fullName;
            $professor->email = $user->email;
            $professor->save();
        }

        AuditLogger::log(
            'Faculty Sync',
            'update',
            'Updated professor from FLSS: ' . $fullName . ' (' . $user->email . ')',
            $user->id
        );
    }

    /**
     * Create a new user and professor record from FLSS data.
     */
    protected function createNewProfessor(array $faculty): void
    {
        $email     = $faculty['email'];
        $firstName = $faculty['first_name'] ?? '';
        $lastName  = $faculty['last_name'] ?? '';
        $middleName = $faculty['middle_name'] ?? '';
        $suffix    = $faculty['suffix_name'] ?? '';
        $fullName  = trim($firstName . ' ' . $lastName);

        // Check if email already exists (safety check)
        if (User::where('email', $email)->exists()) {
            return;
        }

        $user = new User();
        $user->first_name = $firstName;
        $user->middle_name = $middleName;
        $user->last_name = $lastName;
        $user->suffix = $suffix;
        $user->full_name = $fullName;
        $user->email = $email;
        $user->password = Hash::make('Password123!');
        $user->has_local_password = true;
        $user->role = 2; // Professor
        $user->status = 'Active';

        // Link IdP user ID from FLSS or IdP map if available
        $idpId = $faculty['idp_user_id'] ?? null;
        if (empty($idpId) && !empty($this->idpUsersByEmail[strtolower(trim($email))])) {
            $idpId = $this->idpUsersByEmail[strtolower(trim($email))]['id'] ?? $this->idpUsersByEmail[strtolower(trim($email))]['user_id'] ?? null;
        }
        if (!empty($idpId)) {
            $user->idp_user_id = $idpId;
        }

        $user->save();

        $professor = new Professor();
        $professor->user_id = $user->id;
        $professor->full_name = $fullName;
        $professor->email = $email;
        $professor->save();

        AuditLogger::log(
            'Faculty Sync',
            'create',
            'Created professor from FLSS: ' . $fullName . ' (' . $email . ')',
            $user->id
        );
    }

    /**
     * Identify faculty accounts in local DB that were NOT present in the latest FLSS response.
     */
    public function getMissingFacultyAccounts(array $flssFaculties): array
    {
        $flssEmails = [];
        $flssNames  = [];

        foreach ($flssFaculties as $f) {
            $email = $f['email'] ?? null;
            $firstName = $f['first_name'] ?? '';
            $lastName  = $f['last_name'] ?? '';
            $status    = $f['status'] ?? 'Active';

            // Only consider active FLSS accounts with official @pup.edu.ph email addresses
            if (strtolower($status) === 'active' && !empty($email) && str_ends_with(strtolower(trim($email)), '@pup.edu.ph')) {
                $flssEmails[] = Str::lower(trim($email));
                if (!empty($firstName) && !empty($lastName)) {
                    $flssNames[] = Str::lower(trim($firstName . ' ' . $lastName));
                }
            }
        }

        // Retrieve active local professors (role=2) and coordinators (role=1)
        $localFaculty = User::whereIn('role', [1, 2])
            ->where('status', 'Active')
            ->get();

        $missing = [];

        foreach ($localFaculty as $user) {
            $userEmail = Str::lower(trim($user->email ?? ''));
            $userFullName = Str::lower(trim($user->full_name ?: ($user->first_name . ' ' . $user->last_name)));

            $inFlss = in_array($userEmail, $flssEmails) || in_array($userFullName, $flssNames);

            if (!$inFlss) {
                // Count advisees/students assigned
                $studentCount = \App\Models\Student::where('adviser_name', $user->full_name)
                    ->orWhere('adviser_name', $user->first_name . ' ' . $user->last_name)
                    ->count();

                $classCount = \App\Models\Classes::where('adviser_name', $user->full_name)
                    ->orWhere('adviser_name', $user->first_name . ' ' . $user->last_name)
                    ->count();

                $isManuallyAdded = empty($user->idp_user_id) && !str_contains(strtolower($user->email ?? ''), 'pupt');

                $missing[] = [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'full_name' => $user->full_name ?: ($user->first_name . ' ' . $user->last_name),
                    'email' => $user->email,
                    'role_id' => $user->role,
                    'role_label' => (int)$user->role === 1 ? 'OJT Coordinator' : 'Professor',
                    'source_label' => $isManuallyAdded ? 'Manually Added' : 'FLSS / IdP Synced',
                    'is_manually_added' => $isManuallyAdded,
                    'student_count' => $studentCount,
                    'class_count' => $classCount,
                ];
            }
        }

        return $missing;
    }
}
