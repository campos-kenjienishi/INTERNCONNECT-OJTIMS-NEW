<?php

namespace App\Services;

use App\Models\Courses;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Schedule;
use App\Models\Announcements;
use App\Helpers\AuditLogger;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class SyncProgramsFromPuptas
{
    protected PuptasApiService $api;

    public function __construct(PuptasApiService $api)
    {
        $this->api = $api;
    }

    /**
     * Execute the program synchronization from PUPTAS Admission System.
     *
     * @param bool $forceRefresh
     * @return array
     */
    public function execute(bool $forceRefresh = true): array
    {
        $summary = [
            'success'          => false,
            'created'          => 0,
            'updated'          => 0,
            'unchanged'        => 0,
            'students_updated' => 0,
            'classes_updated'  => 0,
            'total_remote'     => 0,
            'details'          => [],
            'errors'           => [],
            'from_cache'       => false,
            'message'          => '',
        ];

        $response = $this->api->getPrograms($forceRefresh);

        if (!$response['success'] || empty($response['data'])) {
            $errorMsg = $response['error'] ?? 'No programs returned from PUPTAS Admission System.';
            $summary['errors'][] = $errorMsg;
            $summary['message'] = $errorMsg;
            Log::error('PUPTAS sync failed: ' . $errorMsg);
            return $summary;
        }

        $programs = $response['data'];
        $summary['total_remote'] = count($programs);
        $summary['from_cache'] = $response['from_cache'] ?? false;
        $loginId = Session::get('loginId') ?? null;

        foreach ($programs as $item) {
            $code = trim((string) ($item['code'] ?? $item['acronym'] ?? ''));
            $name = trim((string) ($item['name'] ?? $item['course'] ?? ''));

            if (empty($code) || empty($name)) {
                continue;
            }

            // 1. Try finding an existing course by acronym/code (case-insensitive)
            $existing = Courses::whereRaw('LOWER(TRIM(acronym)) = ?', [strtolower($code)])->first();

            // 2. Fallback: try finding by exact program name
            if (!$existing) {
                $existing = Courses::whereRaw('LOWER(TRIM(course)) = ?', [strtolower($name)])->first();
            }

            // 3. Fallback: try finding if course column was saved as the acronym (e.g. course == 'BSIT')
            if (!$existing) {
                $existing = Courses::whereRaw('LOWER(TRIM(course)) = ?', [strtolower($code)])->first();
            }

            // 4. Fallback: try matching legacy base acronyms or partial titles (e.g. DOMT -> DOMT-LOM)
            if (!$existing) {
                $baseAcronym = explode('-', $code)[0];
                if ($baseAcronym && strlen($baseAcronym) >= 3) {
                    $prefix = substr($name, 0, 35);
                    $existing = Courses::whereRaw('LOWER(TRIM(acronym)) = ?', [strtolower($baseAcronym)])
                        ->orWhere('course', 'LIKE', $prefix . '%')
                        ->first();
                }
            }

            // Also search for any legacy aliases that might be stored in students/classes
            $legacyAliases = [$code, $name];
            if (str_contains($code, '-')) {
                $baseCode = explode('-', $code)[0];
                $legacyAliases[] = $baseCode;
            }
            if ($code === 'DOMT-LOM') {
                $legacyAliases[] = 'DOMT';
                $legacyAliases[] = 'Diploma in Office Management Technology';
            }

            if ($existing) {
                $oldName = trim((string) $existing->course);
                $oldAcronym = trim((string) $existing->acronym);

                $nameDiffers = (strcasecmp($oldName, $name) !== 0 || $oldName !== $name);
                $acronymDiffers = (strcasecmp($oldAcronym, $code) !== 0 || $oldAcronym !== $code);

                if ($nameDiffers || $acronymDiffers) {
                    $existing->course = $name;
                    $existing->acronym = $code;
                    $existing->save();

                    // Cascade update to Student records
                    $searchValues = array_values(array_unique(array_filter(array_merge([$oldName, $oldAcronym], $legacyAliases))));
                    $studentsUpdated = Student::whereIn('course', $searchValues)
                        ->where('course', '!=', $name)
                        ->update(['course' => $name]);

                    // Cascade update to Classes records
                    $classesUpdated = Classes::whereIn('course', $searchValues)
                        ->where('course', '!=', $name)
                        ->update(['course' => $name]);

                    // Cascade update to Schedules if table exists
                    if (Schema::hasTable('schedules')) {
                        try {
                            Schedule::whereIn('course', $searchValues)
                                ->where('course', '!=', $name)
                                ->update(['course' => $name]);
                        } catch (\Exception $e) {
                            Log::warning('PUPTAS sync: schedule cascade update note: ' . $e->getMessage());
                        }
                    }

                    // Cascade update to Announcements if target_course exists
                    if (Schema::hasTable('announcements') && Schema::hasColumn('announcements', 'target_course')) {
                        try {
                            Announcements::whereIn('target_course', $searchValues)
                                ->where('target_course', '!=', $name)
                                ->update(['target_course' => $name]);
                        } catch (\Exception $e) {
                            Log::warning('PUPTAS sync: announcement cascade update note: ' . $e->getMessage());
                        }
                    }

                    AuditLogger::log(
                        'Maintenance',
                        'Update',
                        "Synced and standardized program from PUPTAS: '{$oldName}' -> '{$name}' ({$code}) [Cascaded to {$studentsUpdated} students, {$classesUpdated} classes]",
                        $loginId,
                        ['course' => $oldName, 'acronym' => $oldAcronym],
                        ['course' => $name, 'acronym' => $code]
                    );

                    $summary['updated']++;
                    $summary['students_updated'] += $studentsUpdated;
                    $summary['classes_updated'] += $classesUpdated;
                    $summary['details'][] = [
                        'type'             => 'updated',
                        'code'             => $code,
                        'name'             => $name,
                        'old_name'         => $oldName,
                        'students_updated' => $studentsUpdated,
                    ];
                } else {
                    // Even if program record matches, backfill any students using shorthand acronym
                    $studentsFixed = Student::where('course', $code)
                        ->where('course', '!=', $name)
                        ->update(['course' => $name]);

                    $summary['unchanged']++;
                    $summary['students_updated'] += $studentsFixed;
                }
            } else {
                // New Program
                $newCourse = new Courses();
                $newCourse->course = $name;
                $newCourse->acronym = $code;
                $newCourse->save();

                // Align any existing students/classes that might have had this code
                $studentsUpdated = Student::where('course', $code)
                    ->where('course', '!=', $name)
                    ->update(['course' => $name]);

                $classesUpdated = Classes::where('course', $code)
                    ->where('course', '!=', $name)
                    ->update(['course' => $name]);

                AuditLogger::log(
                    'Maintenance',
                    'Create',
                    "Synced new program from PUPTAS: '{$name}' ({$code})",
                    $loginId,
                    null,
                    ['course' => $name, 'acronym' => $code]
                );

                $summary['created']++;
                $summary['students_updated'] += $studentsUpdated;
                $summary['classes_updated'] += $classesUpdated;
                $summary['details'][] = [
                    'type'             => 'created',
                    'code'             => $code,
                    'name'             => $name,
                    'students_updated' => $studentsUpdated,
                ];
            }
        }

        // Cleanup old redundant courses that were superseded by official PUPTAS programs (e.g. DOMT superseded by DOMT-LOM)
        if (Courses::where('acronym', 'DOMT-LOM')->exists()) {
            Courses::where('acronym', 'DOMT')->delete();
        }

        if ($lomCourse = Courses::where('acronym', 'DOMT-LOM')->first()) {
            $domtStudents = Student::whereIn('course', ['DOMT', 'Diploma in Office Management Technology'])
                ->where('course', '!=', $lomCourse->course)
                ->update(['course' => $lomCourse->course]);

            $domtClasses = Classes::whereIn('course', ['DOMT', 'Diploma in Office Management Technology'])
                ->where('course', '!=', $lomCourse->course)
                ->update(['course' => $lomCourse->course]);

            $summary['students_updated'] += $domtStudents;
            $summary['classes_updated'] += $domtClasses;
        }

        $summary['success'] = true;
        $summary['message'] = "Sync completed: {$summary['created']} added, {$summary['updated']} updated to official names, {$summary['unchanged']} already up to date ({$summary['students_updated']} student records cascaded).";

        return $summary;
    }
}
