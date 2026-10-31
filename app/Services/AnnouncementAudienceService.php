<?php

namespace App\Services;

use App\Models\Announcements;
use App\Models\Classes;
use App\Models\Courses;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AnnouncementAudienceService
{
    public const SEMESTERS = ['1st Semester', '2nd Semester', 'Summer Term'];
    public const PROFESSOR_STATUSES = ['0', '1', 'Active'];

    public function ready(): bool
    {
        return Schema::hasColumn('announcements', 'targeting')
            && Schema::hasTable('announcement_recipients')
            && Schema::hasTable('ojt_professor_designations');
    }

    public function rules(): array
    {
        return [
            'audience' => ['required', Rule::in(['students', 'professors', 'both'])],
            'programs' => ['nullable', 'array', 'max:100'],
            'programs.*' => ['required', 'string', 'distinct', Rule::exists('courses', 'course')],
            'school_year' => ['nullable', 'regex:/^20\d{2}-20\d{2}$/'],
            'semester' => ['nullable', Rule::in(self::SEMESTERS)],
            'professor_mode' => ['required', Rule::in(['all', 'selected'])],
            'professor_ids' => ['nullable', 'array', 'max:1000'],
            'professor_ids.*' => ['required', 'integer', 'distinct'],
            'send_email' => ['nullable', 'boolean'],
        ];
    }

    public function normalize(array $input): array
    {
        $programs = array_values(array_unique($input['programs'] ?? []));
        sort($programs);
        $professorIds = array_values(array_unique(array_map('intval', $input['professor_ids'] ?? [])));
        sort($professorIds);

        return [
            'audience' => $input['audience'],
            'programs' => $programs,
            'school_year' => $input['school_year'] ?? '',
            'semester' => $input['semester'] ?? '',
            'professor_mode' => $input['professor_mode'],
            'professor_ids' => $input['professor_mode'] === 'selected' ? $professorIds : [],
            'send_email' => (bool) ($input['send_email'] ?? false),
        ];
    }

    private function scopePeriod(Builder $query, array $filters): void
    {
        if ($filters['school_year'] !== '') {
            [$start, $end] = explode('-', $filters['school_year']);
            $query->where('school_year_start', $start)->where('school_year_end', $end);
        }
        if ($filters['programs'] !== []) {
            $query->whereIn('course', $filters['programs']);
        }
    }

    private function matchingClasses(array $filters): Collection
    {
        $query = Classes::query();
        $this->scopePeriod($query, $filters);
        if (Schema::hasColumn('classes', 'archived_at')) {
            $query->whereNull('archived_at');
        }
        if ($filters['semester'] !== '') {
            if (!Schema::hasColumn('classes', 'semester')) {
                throw ValidationException::withMessages(['semester' => 'Semester information is unavailable for these classes. Choose All Semesters.']);
            }
            $aliases = match ($filters['semester']) {
                '1st Semester' => ['1st Semester', '1st Sem'],
                '2nd Semester' => ['2nd Semester', '2nd Sem'],
                default => ['Summer', 'Summer Term'],
            };
            $query->whereIn('semester', $aliases);
        }

        return $query->get();
    }

    public function students(array $filters): Collection
    {
        $query = Student::query();
        $this->scopePeriod($query, $filters);
        if ($filters['semester'] !== '') {
            $query->whereIn('class_id', $this->matchingClasses($filters)->pluck('id'));
        }

        return User::where('role', 0)->where('status', 1)
            ->whereIn('id', $query->select('user_id'))->orderBy('id')->get(['id', 'full_name']);
    }

    public function eligibleProfessors(array $filters): Collection
    {
        $classes = $this->matchingClasses($filters);
        $studentQuery = Student::query()->whereHas('user', fn ($query) => $query->where('role', 0)->where('status', 1));
        $this->scopePeriod($studentQuery, $filters);
        if ($filters['semester'] !== '') {
            $studentQuery->whereIn('class_id', $classes->pluck('id'));
        } else {
            $studentQuery->where(function ($query) use ($classes) {
                $query->whereNull('class_id')->orWhereIn('class_id', $classes->pluck('id'));
            });
        }
        $assignments = $classes->concat($studentQuery->get(['adviser_name', 'course']));
        $nameCounts = User::whereIn('role', [1, 2])->get(['full_name'])
            ->countBy(fn ($user) => mb_strtolower(trim((string) $user->full_name)));
        $names = $assignments->pluck('adviser_name')
            ->filter(fn ($name) => $name && $nameCounts->get(mb_strtolower(trim($name))) === 1)->unique();
        $designations = DB::table('ojt_professor_designations');
        if ($filters['school_year'] !== '') {
            $designations->where('school_year', $filters['school_year']);
        }
        if ($filters['programs'] !== []) {
            $designations->whereIn('course', $filters['programs']);
        }
        if ($filters['semester'] !== '') {
            $designations->whereIn('semester', ['', $filters['semester']]);
        }
        $designations = $designations->get();

        return User::where('role', 2)->whereIn('status', self::PROFESSOR_STATUSES)
            ->where(function ($query) use ($names, $designations) {
                $query->whereIn('full_name', $names)->orWhereIn('id', $designations->pluck('user_id'));
            })->orderBy('full_name')->get(['id', 'full_name'])->map(function ($professor) use ($assignments, $designations) {
                return [
                    'id' => $professor->id,
                    'name' => $professor->full_name,
                    'programs' => $assignments->where('adviser_name', $professor->full_name)->pluck('course')
                        ->merge($designations->where('user_id', $professor->id)->pluck('course'))->filter()->unique()->values()->all(),
                ];
            });
    }

    public function resolve(array $filters): array
    {
        $students = in_array($filters['audience'], ['students', 'both'], true) ? $this->students($filters) : collect();
        $professors = in_array($filters['audience'], ['professors', 'both'], true) ? $this->eligibleProfessors($filters) : collect();

        if ($filters['audience'] !== 'students' && $filters['professor_mode'] === 'selected') {
            if ($filters['professor_ids'] === [] || array_diff($filters['professor_ids'], $professors->pluck('id')->all())) {
                throw ValidationException::withMessages(['professor_ids' => 'Select at least one eligible OJT professor. Refresh the list if assignments have changed.']);
            }
            $professors = $professors->whereIn('id', $filters['professor_ids'])->values();
        }
        $ids = $students->pluck('id')->merge($professors->pluck('id'))->unique()->sort()->values()->all();
        if ($ids === []) {
            throw ValidationException::withMessages(['audience' => 'No recipients match these filters. Adjust the programs or academic period.']);
        }

        return ['students' => $students, 'professors' => $professors, 'ids' => $ids];
    }

    public function fingerprint(array $filters, array $ids, int $authorId): string
    {
        return hash_hmac('sha256', json_encode([$authorId, $filters, $ids], JSON_THROW_ON_ERROR), config('app.key'));
    }

    public function visibleTo(User $user): Builder
    {
        $query = Announcements::query();
        $ready = $this->ready();
        return $query->where(function ($visible) use ($user, $ready) {
            if ($ready) {
                $visible->where(function ($targeted) use ($user) {
                    $targeted->whereNotNull('targeting')->whereExists(function ($recipient) use ($user) {
                        $recipient->selectRaw('1')->from('announcement_recipients')
                            ->whereColumn('announcement_recipients.announcement_id', 'announcements.id')->where('user_id', $user->id);
                    });
                });
            }
            if ((string) $user->role === '0') {
                $method = $ready ? 'orWhere' : 'where';
                $visible->{$method}(function ($legacy) use ($ready) {
                    if ($ready) {
                        $legacy->whereNull('targeting');
                    }
                    if (Schema::hasColumn('announcements', 'audience')) {
                        $legacy->where(fn ($audience) => $audience->where('audience', 'all_students')->orWhereNull('audience'));
                    }
                    $legacy->where(function ($owner) {
                        if (Schema::hasColumn('announcements', 'announcer_user_id')) {
                            $owner->whereIn('announcer_user_id', User::where('role', 1)->select('id'))
                                ->orWhere(function ($fallback) {
                                    $fallback->whereNull('announcer_user_id')->whereIn('announcer', User::where('role', 1)->select('full_name'));
                                });
                        } else {
                            $owner->whereIn('announcer', User::where('role', 1)->select('full_name'));
                        }
                    });
                });
            } elseif (!$ready) {
                $visible->whereRaw('1 = 0');
            }
        });
    }

    public function formOptions(): array
    {
        $years = Student::whereNotNull('school_year_start')->whereNotNull('school_year_end')
            ->get(['school_year_start', 'school_year_end'])->toBase()
            ->merge(Classes::whereNotNull('school_year_start')->whereNotNull('school_year_end')->get(['school_year_start', 'school_year_end']))
            ->map(fn ($row) => $row->school_year_start.'-'.$row->school_year_end);
        $year = now()->month >= 6 ? now()->year : now()->year - 1;

        return [
            'ready' => $this->ready(),
            'email' => app(AnnouncementEmailService::class)->options(),
            'programs' => Courses::orderBy('course')->pluck('course')->unique()->values(),
            'school_years' => $years->push($year.'-'.($year + 1))->unique()->sortDesc()->values(),
            'semesters' => Schema::hasColumn('classes', 'semester') ? self::SEMESTERS : [],
            'faculty' => User::where('role', 2)->whereIn('status', self::PROFESSOR_STATUSES)->orderBy('full_name')->get(['id', 'full_name']),
            'designations' => $this->ready() ? DB::table('ojt_professor_designations')->join('users', 'users.id', '=', 'ojt_professor_designations.user_id')
                ->select('ojt_professor_designations.*', 'users.full_name')->orderByDesc('school_year')->get() : collect(),
        ];
    }
}
