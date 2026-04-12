<x-evaluation-shell
    role="professor"
    title="Evaluation List - InternConnect"
    pageTitle="Student Evaluation Status"
    pageSubtitle="{{ $classroom->room }}"
    :headerActionUrl="route('professor.evaluation')"
    headerActionLabel="Back to Evaluation Classes"
>
    @if(session('success'))
        <div class="flash-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash-alert error">{{ session('error') }}</div>
    @endif

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-tools"></i></span> Class Tools</h2>
            <div class="stacked-actions">
                <a href="{{ route('professor.evaluation.export', ['class_id' => $classroom->id]) }}" class="btn-eval btn-eval-success">
                    <i class="fa fa-file-csv"></i> Export CSV
                </a>
                <a href="{{ route('professor.evaluation.print', ['class_id' => $classroom->id]) }}" class="btn-eval btn-eval-outline" target="_blank">
                    <i class="fa fa-print"></i> Print View
                </a>
            </div>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-users"></i></span> Student Evaluation Status</h2>
            <span class="badge-like secondary">{{ $students->count() }} Students</span>
        </div>
        <div class="card-body-shell tight">
            <div class="table-wrap">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Student No.</th>
                            <th>Student Name</th>
                            <th>Latest Status</th>
                            <th>Supervisor Email</th>
                            <th>Submitted</th>
                            <th>History</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $studentRequests = $requestsByStudent[$student->id] ?? collect();
                                $latest = $studentRequests->first();
                            @endphp
                            <tr>
                                <td>{{ optional($student->studentInfo)->studentNum ?: '-' }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td>
                                    @if($latest)
                                        <span class="badge-like {{ $latest->status === 'submitted' ? 'success' : ($latest->status === 'expired' ? 'secondary' : ($latest->status === 'cancelled' ? 'dark' : 'warning')) }}">
                                            {{ strtoupper($latest->status) }}
                                        </span>
                                    @else
                                        <span class="badge-like secondary">NOT SENT</span>
                                    @endif
                                </td>
                                <td>{{ optional($latest)->supervisor_email ?: '-' }}</td>
                                <td>{{ optional(optional($latest)->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>
                                    <a href="{{ route('professor.evaluation.history', ['studentId' => $student->id]) }}" class="btn-eval btn-eval-outline">
                                        <i class="fa fa-history"></i> View History
                                    </a>
                                </td>
                                <td>
                                    @if($latest && $latest->status === 'submitted' && $latest->evaluation)
                                        <a href="{{ route('professor.evaluation.show', ['requestId' => $latest->id]) }}" class="btn-eval btn-eval-outline">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="muted-text">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center-shell muted-text">No students found for this class.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-evaluation-shell>
