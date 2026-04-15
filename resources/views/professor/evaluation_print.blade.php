<x-evaluation-shell
    role="professor"
    title="Evaluation Monitoring Print"
    pageTitle="Class Evaluation Monitoring"
    pageSubtitle="Printed at: {{ $printedAt->format('M d, Y h:i A') }}"
    :headerActionUrl="route('professor.evaluation')"
    headerActionLabel="Back to Evaluation"
>
    <div class="card-shell section-gap no-print">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-print"></i></span> Print Tools</h2>
            <button class="btn-eval btn-eval-primary" type="button" onclick="window.print()">
                <i class="fa fa-print"></i> Print
            </button>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-table"></i></span> Monitoring Sheet</h2>
            <span class="badge-like secondary">{{ $students->count() }} Students</span>
        </div>
        <div class="card-body-shell tight">
            <div class="table-wrap">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Supervisor Email</th>
                            <th>Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $latest = ($requestsByStudent[$student->id] ?? collect())->first();
                                $studentClass = $classrooms->firstWhere('id', optional($student->studentInfo)->class_id);
                                $status = optional($latest)->status ?? 'not sent';
                                $badge = $status === 'submitted' ? 'success' : ($status === 'expired' ? 'secondary' : ($status === 'cancelled' ? 'dark' : 'warning'));
                            @endphp
                            <tr>
                                <td>{{ optional($student->studentInfo)->studentNum ?: '-' }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>{{ $studentClass ? $studentClass->room : '-' }}</td>
                                <td><span class="badge-like {{ $badge }}">{{ strtoupper($status) }}</span></td>
                                <td>{{ optional($latest)->supervisor_email ?? '-' }}</td>
                                <td>{{ optional(optional($latest)->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center-shell muted-text">No students found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-evaluation-shell>
