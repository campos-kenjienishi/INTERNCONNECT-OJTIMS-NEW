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
                <a href="{{ route('professor.evaluation.print', ['class_id' => $classroom->id]) }}" class="btn-eval btn-eval-outline" target="_blank">
                    <i class="fa fa-print"></i> Print Report
                </a>
            </div>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-users"></i></span> Student Evaluation Status</h2>
            <span class="badge-like secondary">{{ $students->total() }} Students</span>
        </div>
        <div class="card-body-shell tight">
            <div style="display:flex; justify-content:flex-end; margin-bottom:12px;">
                <form method="get" action="{{ route('professor.evaluation.class', ['classId' => $classroom->id]) }}" style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">
                    <label for="studentPerPage" style="font-size:12px; font-weight:700; color:var(--text-secondary);">Show</label>
                    <select id="studentPerPage" name="per_page" onchange="this.form.submit()" style="border:1px solid var(--border); border-radius:8px; padding:5px 10px; font-family:'Poppins',sans-serif; font-size:12.5px; color:var(--text-primary); background:var(--surface); outline:none;">
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10</option>
                        <option value="25" {{ request('per_page', 10) == 25 ? 'selected' : '' }}>25</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50</option>
                    </select>
                    <label for="studentPerPage" style="font-size:12px; font-weight:700; color:var(--text-secondary);">entries</label>
                </form>
            </div>
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
            @if($students->hasPages())
                <div style="display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-top:16px; padding-top:14px; border-top:1px solid var(--border);">
                    <div style="font-size:12px; font-weight:600; color:var(--text-secondary);">
                        Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} students
                    </div>
                    <div style="display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        <a href="{{ $students->previousPageUrl() ?: '#' }}" class="btn-eval btn-eval-outline {{ $students->onFirstPage() ? 'disabled' : '' }}" style="padding:7px 10px;">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                        @for($page = 1; $page <= $students->lastPage(); $page++)
                            @if($page === $students->currentPage())
                                <span class="btn-eval btn-eval-primary" style="padding:7px 12px; pointer-events:none;">{{ $page }}</span>
                            @else
                                <a href="{{ $students->url($page) }}" class="btn-eval btn-eval-outline" style="padding:7px 12px;">{{ $page }}</a>
                            @endif
                        @endfor
                        <a href="{{ $students->nextPageUrl() ?: '#' }}" class="btn-eval btn-eval-outline {{ $students->hasMorePages() ? '' : 'disabled' }}" style="padding:7px 10px;">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-evaluation-shell>
