<?php

namespace App\Services;

use App\Models\User;
use App\Models\Student;
use App\Models\OJTInformation;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Log;

class SyncStudentsUnified
{
    protected IdpService $idp;
    protected GuidanceApiService $guisis;

    public function __construct(IdpService $idp, GuidanceApiService $guisis)
    {
        $this->idp = $idp;
        $this->guisis = $guisis;
    }

    /**
     * Execute IDP UUID synchronization for all local students.
     */
    public function syncIdpUuids(): array
    {
        @set_time_limit(300);

        $summary = [
            'total_students'   => 0,
            'idp_linked'       => 0,
            'idp_updated'      => 0,
            'already_linked'   => 0,
            'not_in_idp'       => 0,
            'errors'           => [],
        ];

        $students = User::where('role', 0)->get();
        $summary['total_students'] = $students->count();

        if ($students->isEmpty()) {
            return $summary;
        }

        // Fetch IdP users map (by email and complete list)
        $idpUsersByEmail = [];
        $allIdpUsers = [];
        try {
            $accessToken = $this->idp->getClientCredentialsToken();
            $page = 1;
            $limit = 100;

            do {
                $response = $this->idp->getAdminUserList($accessToken, $page, $limit);
                $users = $response['users'] ?? $response['data'] ?? $response;
                $lastPage = $response['last_page'] ?? null;

                if (!is_array($users) || empty($users)) {
                    break;
                }

                foreach ($users as $u) {
                    if (!is_array($u)) continue;
                    $allIdpUsers[] = $u;
                    $uEmail = strtolower(trim($u['email'] ?? ''));
                    if (!empty($uEmail)) {
                        $idpUsersByEmail[$uEmail] = $u;
                    }
                }

                if ($lastPage && $page >= $lastPage) {
                    break;
                }

                if (count($users) < $limit) {
                    break;
                }

                $page++;
                if ($page > 50) {
                    break;
                }
            } while (true);

            Log::info('IDP Student Sync: Loaded ' . count($allIdpUsers) . ' users from IdP.');
        } catch (\Exception $e) {
            Log::warning('IDP Student Sync: Could not load IdP user list', ['error' => $e->getMessage()]);
            $summary['errors'][] = $e->getMessage();
        }

        foreach ($students as $studentUser) {
            if (!empty($studentUser->idp_user_id)) {
                $summary['already_linked']++;
            }

            // Advanced Multi-Tier Match (Email -> Full Name -> Primary Name -> Initials -> Fuzzy)
            $idpUser = self::findBestIdpUserMatch($studentUser, $idpUsersByEmail, $allIdpUsers);

            if ($idpUser) {
                $idpUserId = $idpUser['id'] ?? $idpUser['user_id'] ?? null;
                $idpEmail = strtolower(trim($idpUser['email'] ?? ''));

                $userChanged = false;
                if (!empty($idpUserId) && empty($studentUser->idp_user_id)) {
                    $uuidTaken = User::where('idp_user_id', $idpUserId)->where('id', '!=', $studentUser->id)->exists();
                    if (!$uuidTaken) {
                        $studentUser->idp_user_id = $idpUserId;
                        $userChanged = true;
                        $summary['idp_linked']++;
                    }
                }

                // If student updated email in IDP, safely update local email if not already taken
                if (!empty($idpEmail) && $studentUser->email !== $idpEmail) {
                    $emailExists = User::where('email', $idpEmail)->where('id', '!=', $studentUser->id)->exists();
                    if (!$emailExists) {
                        $studentUser->email = $idpEmail;
                        $userChanged = true;
                    }
                }

                $firstName = $idpUser['first_name'] ?? null;
                $middleName = $idpUser['middle_name'] ?? null;
                $lastName = $idpUser['last_name'] ?? null;

                if ($firstName && $studentUser->first_name !== $firstName) {
                    $studentUser->first_name = $firstName;
                    $userChanged = true;
                }
                if ($middleName !== null && $studentUser->middle_name !== $middleName) {
                    $studentUser->middle_name = $middleName;
                    $userChanged = true;
                }
                if ($lastName && $studentUser->last_name !== $lastName) {
                    $studentUser->last_name = $lastName;
                    $userChanged = true;
                }
                if ($userChanged && $firstName && $lastName) {
                    $studentUser->full_name = trim($firstName . ' ' . $lastName);
                }

                if ($userChanged) {
                    $studentUser->save();
                    $summary['idp_updated']++;
                }
            }
        }

        $summary['not_in_idp'] = max(0, $summary['total_students'] - $summary['already_linked'] - $summary['idp_linked']);

        return $summary;
    }

    /**
     * Execute unified sync (IdP UUIDs + GuiSIS profiles).
     */
    public function execute(): array
    {
        @set_time_limit(300);

        $summary = [
            'total_students'   => 0,
            'idp_linked'       => 0,
            'idp_updated'      => 0,
            'guisis_synced'    => 0,
            'guisis_not_found' => 0,
            'errors'           => [],
        ];

        // 1. Fetch only existing students from local database
        $students = User::where('role', 0)->get();
        $summary['total_students'] = $students->count();

        if ($students->isEmpty()) {
            Log::info('Unified Student Sync: No local student accounts found to sync.');
            return $summary;
        }

        // 2. Fetch IdP users map (email => idpUser) using admin client_credentials
        $idpUsersByEmail = [];
        try {
            $accessToken = $this->idp->getClientCredentialsToken();
            $page = 1;
            $limit = 100;

            do {
                $response = $this->idp->getAdminUserList($accessToken, $page, $limit);
                $users = $response['users'] ?? $response['data'] ?? $response;
                $lastPage = $response['last_page'] ?? null;

                if (!is_array($users) || empty($users)) {
                    break;
                }

                foreach ($users as $u) {
                    if (!is_array($u)) continue;
                    $uEmail = strtolower(trim($u['email'] ?? ''));
                    if (!empty($uEmail)) {
                        $idpUsersByEmail[$uEmail] = $u;
                    }
                }

                if ($lastPage && $page >= $lastPage) {
                    break;
                }

                if (count($users) < $limit) {
                    break;
                }

                $page++;
                if ($page > 50) { // Safety ceiling
                    break;
                }
            } while (true);

            Log::info('Unified Student Sync: Loaded ' . count($idpUsersByEmail) . ' users from IdP for cross-referencing.');
        } catch (\Exception $e) {
            Log::warning('Unified Student Sync: Could not load IdP user list for UUID backfill', [
                'error' => $e->getMessage(),
            ]);
            $summary['errors'][] = 'IdP User lookup note: ' . $e->getMessage();
        }

        // 3. Pre-fetch all GuiSIS profiles upfront if reachable
        $guisisProfiles = [];
        $guisisOnline = false;
        try {
            $guisisOnline = $this->guisis->isReachable();
            if ($guisisOnline) {
                $guisisProfiles = $this->guisis->getAllStudentProfiles();
                Log::info('Unified Student Sync: Loaded ' . count($guisisProfiles) . ' student profiles from GuiSIS.');
            }
        } catch (\Exception $e) {
            $guisisOnline = false;
            Log::warning('Unified Student Sync: GuiSIS error: ' . $e->getMessage());
        }

        // Build index maps for fast matching
        $guisisByUuid = [];
        $guisisByEmail = [];
        $guisisByNum = [];
        $guisisByName = [];

        foreach ($guisisProfiles as $p) {
            if (!empty($p['idpUuid'])) {
                $guisisByUuid[strtolower(trim($p['idpUuid']))] = $p;
            }
            if (!empty($p['email'])) {
                $guisisByEmail[strtolower(trim($p['email']))] = $p;
            }
            if (!empty($p['studentNumber'])) {
                $guisisByNum[strtolower(trim($p['studentNumber']))] = $p;
            }
            $fn = strtolower(preg_replace('/[^a-z0-9]/', '', ($p['firstName'] ?? '') . ' ' . ($p['lastName'] ?? '')));
            if (!empty($fn)) {
                $guisisByName[$fn] = $p;
            }
        }

        // 4. Process each existing student
        foreach ($students as $studentUser) {
            $email = strtolower(trim($studentUser->email ?? ''));

            // --- A. IdP UUID & Name Sync ---
            if (isset($idpUsersByEmail[$email])) {
                $idpUser = $idpUsersByEmail[$email];
                $idpUserId = $idpUser['id'] ?? $idpUser['user_id'] ?? null;

                $userChanged = false;
                if (!empty($idpUserId) && empty($studentUser->idp_user_id)) {
                    $studentUser->idp_user_id = $idpUserId;
                    $userChanged = true;
                    $summary['idp_linked']++;
                }

                // Check name updates
                $firstName = $idpUser['first_name'] ?? null;
                $middleName = $idpUser['middle_name'] ?? null;
                $lastName = $idpUser['last_name'] ?? null;

                if ($firstName && $studentUser->first_name !== $firstName) {
                    $studentUser->first_name = $firstName;
                    $userChanged = true;
                }
                if ($middleName !== null && $studentUser->middle_name !== $middleName) {
                    $studentUser->middle_name = $middleName;
                    $userChanged = true;
                }
                if ($lastName && $studentUser->last_name !== $lastName) {
                    $studentUser->last_name = $lastName;
                    $userChanged = true;
                }
                if ($userChanged && $firstName && $lastName) {
                    $studentUser->full_name = trim($firstName . ' ' . $lastName);
                }

                if ($userChanged) {
                    $studentUser->save();
                    if (!empty($studentUser->getOriginal('idp_user_id')) && empty($idpUserId)) {
                        $summary['idp_updated']++;
                    }
                }
            }

            // --- B. GuiSIS Academic & Demographic Profile Sync ---
            if (!$guisisOnline || empty($guisisProfiles)) {
                $summary['guisis_not_found']++;
                continue;
            }

            try {
                $stUuid = strtolower(trim($studentUser->idp_user_id ?? ''));
                $stEmail = strtolower(trim($studentUser->email ?? ''));
                $stNum = strtolower(trim($studentUser->studentNum ?? ''));
                $stName = strtolower(preg_replace('/[^a-z0-9]/', '', ($studentUser->first_name ?? '') . ' ' . ($studentUser->last_name ?? '')));

                $guisisData = null;
                if ($stUuid && isset($guisisByUuid[$stUuid])) {
                    $guisisData = $guisisByUuid[$stUuid];
                } elseif ($stEmail && isset($guisisByEmail[$stEmail])) {
                    $guisisData = $guisisByEmail[$stEmail];
                } elseif ($stNum && isset($guisisByNum[$stNum])) {
                    $guisisData = $guisisByNum[$stNum];
                } elseif ($stName && isset($guisisByName[$stName])) {
                    $guisisData = $guisisByName[$stName];
                }

                if (!$guisisData) {
                    $summary['guisis_not_found']++;
                    continue;
                }

                // If user doesn't have IDP UUID yet, but GuiSIS profile has it, backfill it if not already taken
                if (empty($studentUser->idp_user_id) && !empty($guisisData['idpUuid'])) {
                    $uuidTaken = User::where('idp_user_id', $guisisData['idpUuid'])->where('id', '!=', $studentUser->id)->exists();
                    if (!$uuidTaken) {
                        $studentUser->idp_user_id = $guisisData['idpUuid'];
                        $studentUser->save();
                    }
                }

                // Resolve student record
                $studentProfile = Student::where('user_id', $studentUser->id)->first();
                if (!$studentProfile) {
                    $studentProfile = new Student();
                    $studentProfile->user_id = $studentUser->id;
                }

                $studentNum = $guisisData['studentNumber'] ?? $guisisData['student_number'] ?? null;
                
                $course = (is_array($guisisData['program'] ?? null) ? ($guisisData['program']['name'] ?? $guisisData['program']['code'] ?? '') : ($guisisData['program'] ?? ''))
                    ?: (is_array($guisisData['course'] ?? null) ? ($guisisData['course']['name'] ?? $guisisData['course']['course'] ?? '') : ($guisisData['course'] ?? ''));

                $yearSection = null;
                if (isset($guisisData['yearLevel']) && isset($guisisData['section'])) {
                    $yearSection = $guisisData['yearLevel'] . '-' . $guisisData['section'];
                } else {
                    $yearSection = $guisisData['year_and_section'] ?? $guisisData['year_section'] ?? null;
                }

                $suffixName = $guisisData['suffixName'] ?? $guisisData['suffix_name'] ?? $guisisData['suffix'] ?? null;
                if ($suffixName && empty($studentUser->suffix)) {
                    $studentUser->suffix = $suffixName;
                    $studentUser->save();
                }

                $profileChanged = false;

                if ($studentNum && $studentProfile->studentNum !== $studentNum) {
                    $studentProfile->studentNum = $studentNum;
                    $profileChanged = true;

                    // Update OJTInformation if studentNum changed
                    $ojtInfo = OJTInformation::where('studentNum', $studentUser->studentNum)->first();
                    if ($ojtInfo) {
                        $ojtInfo->studentNum = $studentNum;
                        $ojtInfo->save();
                    }
                }

                if ($course && $studentProfile->course !== $course) {
                    $studentProfile->course = $course;
                    $profileChanged = true;
                }

                if ($yearSection && $studentProfile->year_and_section !== $yearSection) {
                    $studentProfile->year_and_section = $yearSection;
                    $profileChanged = true;
                }

                $mobile = $guisisData['mobileNumber'] ?? $guisisData['mobile_number'] ?? $guisisData['contactNumber'] ?? $guisisData['contact_number'] ?? null;
                if ($mobile && $studentProfile->contact_number !== $mobile) {
                    $studentProfile->contact_number = $mobile;
                    $profileChanged = true;
                }

                $dob = $guisisData['dateOfBirth'] ?? $guisisData['date_of_birth'] ?? null;
                if ($dob && $studentProfile->date_of_birth !== $dob) {
                    $studentProfile->date_of_birth = $dob;
                    $profileChanged = true;
                }

                $studentProfile->save();
                $summary['guisis_synced']++;

            } catch (\Exception $e) {
                $summary['errors'][] = "GuiSIS error for {$studentUser->email}: " . $e->getMessage();
                Log::error('Unified Student Sync: GuiSIS error', [
                    'email' => $studentUser->email,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        AuditLogger::log(
            'Unified Student Sync',
            'sync',
            "Unified Student Sync: {$summary['idp_linked']} IDP UUIDs linked, {$summary['guisis_synced']} GuiSIS profiles updated."
        );

        return $summary;
    }

    /**
     * Dedicated GuiSIS sync for all students.
     */
    public function syncGuisisOnly(): array
    {
        @set_time_limit(300);

        $summary = [
            'total_students'   => 0,
            'guisis_synced'    => 0,
            'guisis_not_found' => 0,
            'guisis_total_pool'=> 0,
            'errors'           => [],
        ];

        $students = User::where('role', 0)->get();
        $summary['total_students'] = $students->count();

        $profiles = $this->guisis->getAllStudentProfiles();
        $summary['guisis_total_pool'] = count($profiles);

        if (empty($profiles)) {
            $summary['errors'][] = 'Could not load student profiles from GuiSIS API.';
            return $summary;
        }

        $guisisByUuid = [];
        $guisisByEmail = [];
        $guisisByNum = [];
        $guisisByName = [];

        foreach ($profiles as $p) {
            if (!empty($p['idpUuid'])) $guisisByUuid[strtolower(trim($p['idpUuid']))] = $p;
            if (!empty($p['email'])) $guisisByEmail[strtolower(trim($p['email']))] = $p;
            if (!empty($p['studentNumber'])) $guisisByNum[strtolower(trim($p['studentNumber']))] = $p;
            $fn = strtolower(preg_replace('/[^a-z0-9]/', '', ($p['firstName'] ?? '') . ' ' . ($p['lastName'] ?? '')));
            if (!empty($fn)) $guisisByName[$fn] = $p;
        }

        foreach ($students as $studentUser) {
            $stUuid = strtolower(trim($studentUser->idp_user_id ?? ''));
            $stEmail = strtolower(trim($studentUser->email ?? ''));
            $stNum = strtolower(trim($studentUser->studentNum ?? ''));
            $stName = strtolower(preg_replace('/[^a-z0-9]/', '', ($studentUser->first_name ?? '') . ' ' . ($studentUser->last_name ?? '')));

            $guisisData = null;
            if ($stUuid && isset($guisisByUuid[$stUuid])) {
                $guisisData = $guisisByUuid[$stUuid];
            } elseif ($stEmail && isset($guisisByEmail[$stEmail])) {
                $guisisData = $guisisByEmail[$stEmail];
            } elseif ($stNum && isset($guisisByNum[$stNum])) {
                $guisisData = $guisisByNum[$stNum];
            } elseif ($stName && isset($guisisByName[$stName])) {
                $guisisData = $guisisByName[$stName];
            }

            if (!$guisisData) {
                $summary['guisis_not_found']++;
                continue;
            }

            // Backfill IDP UUID if missing on User and not claimed by another user
            if (empty($studentUser->idp_user_id) && !empty($guisisData['idpUuid'])) {
                $uuidTaken = User::where('idp_user_id', $guisisData['idpUuid'])->where('id', '!=', $studentUser->id)->exists();
                if (!$uuidTaken) {
                    $studentUser->idp_user_id = $guisisData['idpUuid'];
                    $studentUser->save();
                }
            }

            $studentProfile = Student::where('user_id', $studentUser->id)->first();
            if (!$studentProfile) {
                $studentProfile = new Student();
                $studentProfile->user_id = $studentUser->id;
            }

            $studentNum = $guisisData['studentNumber'] ?? $guisisData['student_number'] ?? null;
            $course = (is_array($guisisData['program'] ?? null) ? ($guisisData['program']['name'] ?? $guisisData['program']['code'] ?? '') : ($guisisData['program'] ?? ''))
                ?: (is_array($guisisData['course'] ?? null) ? ($guisisData['course']['name'] ?? $guisisData['course']['course'] ?? '') : ($guisisData['course'] ?? ''));

            $yearSection = null;
            if (isset($guisisData['yearLevel']) && isset($guisisData['section'])) {
                $yearSection = $guisisData['yearLevel'] . '-' . $guisisData['section'];
            } else {
                $yearSection = $guisisData['year_and_section'] ?? $guisisData['year_section'] ?? null;
            }

            $suffixName = $guisisData['suffixName'] ?? $guisisData['suffix_name'] ?? $guisisData['suffix'] ?? null;
            if ($suffixName && empty($studentUser->suffix)) {
                $studentUser->suffix = $suffixName;
                $studentUser->save();
            }

            if ($studentNum) {
                $studentProfile->studentNum = $studentNum;
            }
            if ($course) {
                $studentProfile->course = $course;
            }
            if ($yearSection) {
                $studentProfile->year_and_section = $yearSection;
            }
            $mobile = $guisisData['mobileNumber'] ?? $guisisData['mobile_number'] ?? null;
            if ($mobile) {
                $studentProfile->contact_number = $mobile;
            }

            $studentProfile->save();
            $summary['guisis_synced']++;
        }

        AuditLogger::log(
            'GuiSIS Student Sync',
            'sync',
            "GuiSIS Student Sync: {$summary['guisis_synced']} profiles updated from {$summary['guisis_total_pool']} GuiSIS records."
        );

        return $summary;
    }

    /**
     * Advanced multi-tier matching for student against IdP user pool:
     * Tier 1: Exact Email
     * Tier 2: Exact Full Name
     * Tier 3: Primary First Name + Exact Last Name ("John Cris" vs "John")
     * Tier 4: Initials + Exact Last Name ("JC" vs "John Cris")
     * Tier 5: Prefix / Substring / Fuzzy match (>= 80% similarity)
     */
    public static function findBestIdpUserMatch(User $studentUser, array $idpUsersByEmail, array $allIdpUsers): ?array
    {
        $email = strtolower(trim($studentUser->email ?? ''));

        // Tier 1: Email Match
        if (!empty($email) && isset($idpUsersByEmail[$email])) {
            return $idpUsersByEmail[$email];
        }

        $sFirst = strtolower(trim($studentUser->first_name ?? ''));
        $sLast  = strtolower(trim($studentUser->last_name ?? ''));
        $sFull  = strtolower(trim(($studentUser->first_name ?? '') . ' ' . ($studentUser->last_name ?? '')));

        if (empty($sLast)) {
            return null;
        }

        $sNormFull = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sFull));
        $sNormLast = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sLast));
        $sFirstTokens = preg_split('/\s+/', $sFirst);
        $sInitials = implode('', array_map(fn($t) => substr($t, 0, 1), $sFirstTokens));

        $candidates = [];

        foreach ($allIdpUsers as $idpUser) {
            $idpFirst = strtolower(trim($idpUser['first_name'] ?? ''));
            $idpLast  = strtolower(trim($idpUser['last_name'] ?? ''));
            $idpFull  = strtolower(trim(($idpUser['first_name'] ?? '') . ' ' . ($idpUser['last_name'] ?? '')));

            if (empty($idpLast)) continue;

            $idpNormFull = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpFull));
            $idpNormLast = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpLast));

            // Tier 2: Exact Full Name Match
            if ($sNormFull === $idpNormFull) {
                return $idpUser;
            }

            // Must have matching last name (allowing at most 1 typo)
            if ($sNormLast !== $idpNormLast && levenshtein($sNormLast, $idpNormLast) > 1) {
                continue;
            }

            $idpFirstTokens = preg_split('/\s+/', $idpFirst);
            $idpInitials = implode('', array_map(fn($t) => substr($t, 0, 1), $idpFirstTokens));

            // Tier 3: Primary First Name ("John Cris" vs "John")
            if (!empty($idpFirstTokens[0]) && !empty($sFirstTokens[0]) && $idpFirstTokens[0] === $sFirstTokens[0]) {
                $candidates[] = ['user' => $idpUser, 'score' => 95];
                continue;
            }

            // Tier 4: Initials Match ("JC" vs "John Cris")
            if ((!empty($sInitials) && $idpFirst === $sInitials) || (!empty($idpInitials) && $sFirst === $idpInitials) ||
                (!empty($sInitials) && strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $idpFirst)) === $sInitials) ||
                (!empty($idpInitials) && strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $sFirst)) === $idpInitials)) {
                $candidates[] = ['user' => $idpUser, 'score' => 90];
                continue;
            }

            // Tier 5: Prefix / Substring Match
            if (str_starts_with($idpFirst, $sFirst) || str_starts_with($sFirst, $idpFirst)) {
                $candidates[] = ['user' => $idpUser, 'score' => 85];
                continue;
            }

            // Tier 6: Fuzzy Similarity (>= 80%)
            similar_text($idpFirst, $sFirst, $sim);
            if ($sim >= 80) {
                $candidates[] = ['user' => $idpUser, 'score' => $sim];
                continue;
            }
        }

        if (count($candidates) === 1) {
            return $candidates[0]['user'];
        } elseif (count($candidates) > 1) {
            usort($candidates, fn($a, $b) => $b['score'] <=> $a['score']);
            if ($candidates[0]['score'] > $candidates[1]['score']) {
                return $candidates[0]['user'];
            }
        }

        return null;
    }
}
