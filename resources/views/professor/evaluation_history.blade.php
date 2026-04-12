<x-evaluation-shell
    role="professor"
    title="Evaluation History - InternConnect"
    pageTitle="Evaluation Request History"
    pageSubtitle="{{ $student->full_name }} | {{ optional($student->studentInfo)->studentNum ?: 'No Student No.' }} | {{ $class->room }}"
    :headerActionUrl="route('professor.evaluation.class', ['classId' => $class->id])"
    headerActionLabel="Back to Class Evaluation List"
>
    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-history"></i></span> Request Timeline</h2>
            <span class="badge-like secondary">{{ $requests->count() }} Requests</span>
        </div>
        <div class="card-body-shell tight">
            <div class="table-wrap">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Supervisor Name</th>
                            <th>Supervisor Email</th>
                            <th>Sent</th>
                            <th>Opened</th>
                            <th>Submitted</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $row)
                            <tr>
                                <td>
                                    <span class="badge-like {{ $row->status === 'submitted' ? 'success' : ($row->status === 'expired' ? 'secondary' : ($row->status === 'cancelled' ? 'dark' : 'warning')) }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td>{{ $row->supervisor_name ?: '-' }}</td>
                                <td>{{ $row->supervisor_email }}</td>
                                <td>{{ optional($row->emailed_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>{{ optional($row->opened_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>{{ optional($row->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>
                                    @if($row->status === 'submitted' && $row->evaluation)
                                        <a href="{{ route('professor.evaluation.show', ['requestId' => $row->id]) }}" class="btn-eval btn-eval-outline">
                                            <i class="fa fa-eye"></i> View
                                        </a>
                                    @else
                                        <span class="muted-text">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center-shell muted-text">No evaluation requests yet for this student.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-evaluation-shell>
