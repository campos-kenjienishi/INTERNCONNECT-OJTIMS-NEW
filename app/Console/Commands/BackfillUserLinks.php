<?php

namespace App\Console\Commands;

use App\Models\Professor;
use App\Models\Student;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class BackfillUserLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:backfill-user-links {--dry-run : Preview changes without saving}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backfill students.user_id and professors.user_id using existing user records';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (!Schema::hasColumn('students', 'user_id') || !Schema::hasColumn('professors', 'user_id')) {
            $this->error('Missing user_id column on students or professors. Run the migration first.');
            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');

        $studentLinked = 0;
        $studentSkipped = 0;
        $studentAlreadyLinked = 0;
        $studentConflict = 0;

        $professorLinked = 0;
        $professorSkipped = 0;
        $professorAlreadyLinked = 0;
        $professorConflict = 0;

        DB::beginTransaction();

        try {
            Student::query()
                ->orderBy('id')
                ->chunkById(200, function ($students) use (
                    &$studentLinked,
                    &$studentSkipped,
                    &$studentAlreadyLinked,
                    &$studentConflict,
                    $isDryRun
                ) {
                    foreach ($students as $student) {
                        if (!empty($student->user_id)) {
                            $studentAlreadyLinked++;
                            continue;
                        }

                        $user = null;

                        if (Schema::hasColumn('students', 'email') && !empty($student->email)) {
                            $user = User::where('email', $student->email)->first();
                        }

                        if (!$user && Schema::hasColumn('users', 'studentNum') && !empty($student->studentNum)) {
                            $user = User::where('studentNum', $student->studentNum)->first();
                        }

                        if (!$user) {
                            $studentSkipped++;
                            continue;
                        }

                        $isUsedByAnotherStudent = Student::where('user_id', $user->id)
                            ->where('id', '!=', $student->id)
                            ->exists();

                        if ($isUsedByAnotherStudent) {
                            $studentConflict++;
                            continue;
                        }

                        $studentLinked++;

                        if (!$isDryRun) {
                            $student->user_id = $user->id;
                            $student->save();
                        }
                    }
                });

            Professor::query()
                ->orderBy('id')
                ->chunkById(200, function ($professors) use (
                    &$professorLinked,
                    &$professorSkipped,
                    &$professorAlreadyLinked,
                    &$professorConflict,
                    $isDryRun
                ) {
                    foreach ($professors as $professor) {
                        if (!empty($professor->user_id)) {
                            $professorAlreadyLinked++;
                            continue;
                        }

                        $user = null;

                        if (!empty($professor->email)) {
                            $user = User::where('email', $professor->email)->first();
                        }

                        if (!$user && !empty($professor->full_name)) {
                            $user = User::where('full_name', $professor->full_name)
                                ->where('role', 2)
                                ->first();
                        }

                        if (!$user) {
                            $professorSkipped++;
                            continue;
                        }

                        $isUsedByAnotherProfessor = Professor::where('user_id', $user->id)
                            ->where('id', '!=', $professor->id)
                            ->exists();

                        if ($isUsedByAnotherProfessor) {
                            $professorConflict++;
                            continue;
                        }

                        $professorLinked++;

                        if (!$isDryRun) {
                            $professor->user_id = $user->id;
                            $professor->save();
                        }
                    }
                });

            if ($isDryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->error('Backfill failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->line('');
        $this->info($isDryRun ? 'Dry run complete (no changes saved).' : 'Backfill complete.');
        $this->line('');
        $this->line('Students:');
        $this->line('  Linked: ' . $studentLinked);
        $this->line('  Already linked: ' . $studentAlreadyLinked);
        $this->line('  Skipped (no matching user): ' . $studentSkipped);
        $this->line('  Skipped (user already linked elsewhere): ' . $studentConflict);
        $this->line('');
        $this->line('Professors:');
        $this->line('  Linked: ' . $professorLinked);
        $this->line('  Already linked: ' . $professorAlreadyLinked);
        $this->line('  Skipped (no matching user): ' . $professorSkipped);
        $this->line('  Skipped (user already linked elsewhere): ' . $professorConflict);

        return self::SUCCESS;
    }
}
