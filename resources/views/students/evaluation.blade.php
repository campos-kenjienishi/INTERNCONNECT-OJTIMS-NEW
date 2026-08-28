<x-evaluation-shell
    role="student"
    title="InternConnect - Supervisor Evaluation"
    pageTitleHtml="Supervisor <span>Evaluation</span>"
    :headerBreadcrumbs="[
        ['url' => url('/student/home'), 'icon' => 'fa-home', 'label' => 'Home'],
        ['label' => 'Evaluation']
    ]"
>
    <link rel="stylesheet" href="{{ vasset('css/student/evaluation.css') }}">

    @if(session('success'))
        <div class="flash-alert success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="flash-alert error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="flash-alert error">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-paper-plane"></i></span> Send Evaluation Form</h2>
        </div>
        <div class="card-body-shell">
            <div class="panel-note">
                Enter the supervisor's email address and the system will send them a secure evaluation link.
            </div>

            @if(!empty($expectedSupervisorEmail))
                <div class="flash-alert info">
                    Suggested from submitted MOA: <strong>{{ $expectedSupervisorEmail }}</strong>
                </div>
            @endif

            <form action="{{ route('student.evaluation.send') }}" method="POST" id="sendEvaluationForm" data-expected-email="{{ strtolower((string) ($expectedSupervisorEmail ?? '')) }}">
                @csrf
                <input type="hidden" name="confirm_email_mismatch" id="confirmEmailMismatch" value="0">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="supervisor_name" class="form-label-shell">Supervisor Name <span style="font-weight:400; color:var(--text-secondary);">(optional)</span></label>
                        <input
                            type="text"
                            name="supervisor_name"
                            id="supervisor_name"
                            class="form-control-shell"
                            placeholder="Enter supervisor name"
                            value="{{ old('supervisor_name') }}"
                        >
                    </div>
                    <div class="form-group">
                        <label for="supervisor_email" class="form-label-shell">Supervisor Email <span style="color:var(--red);">*</span></label>
                        <div class="email-bubble-wrap">
                            <input
                                type="email"
                                name="supervisor_email"
                                id="supervisor_email"
                                class="form-control-shell"
                                placeholder="name@company.com"
                                value="{{ old('supervisor_email', $expectedSupervisorEmail ?? '') }}"
                                required
                            >
                            <div id="supervisorEmailBubble" class="field-bubble-shell"></div>
                        </div>
                        <div class="form-hint" style="margin-top:4px; font-size:12px; color:var(--text-secondary);">If you use an email different from your MOA entry, the system will ask for confirmation.</div>
                    </div>
                </div>

                <div class="section-gap stacked-actions" style="margin-top:16px;">
                    <button type="submit" class="btn-eval btn-eval-primary" id="sendEvaluationButton">
                        <i class="fa fa-envelope"></i> Send Evaluation Form
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-history"></i></span> Evaluation Request History</h2>
        </div>
        <div class="card-body-shell tight">
            <div class="shell-table-controls">
                <div class="shell-controls-left">
                    <div class="shell-length-form">
                        <label for="historyPerPage">Show</label>
                        <select id="historyPerPage" class="shell-length-select">
                            @foreach([5, 10, 25, 50] as $size)
                                <option value="{{ $size }}" {{ $size === 5 ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                        <span>entries</span>
                    </div>
                    <div class="shell-date-box">
                        <label for="historySort" class="muted-text" style="font-size:13px; font-weight:500; white-space:nowrap; margin:0;">Date</label>
                        <select id="historySort" class="shell-filter-select">
                            <option value="newest" selected>Newest first</option>
                            <option value="oldest">Oldest first</option>
                        </select>
                    </div>
                </div>
                <div class="shell-controls-right">
                    <div class="shell-search-box">
                        <label for="historySearch" class="muted-text" style="font-size:13px; font-weight:500; white-space:nowrap; margin:0;">Search:</label>
                        <input type="search" id="historySearch" class="shell-filter-input" placeholder="Search email, name, or status">
                    </div>
                </div>
            </div>
            <table id="historyTable" class="display table-shell" style="width:100%">
                <thead>
                    <tr>
                        <th>Supervisor Email</th>
                        <th>Supervisor Name</th>
                        <th>Status</th>
                        <th>Sent</th>
                        <th>Submitted</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($requests as $row)
                        <tr>
                            <td>{{ $row->supervisor_email }}</td>
                            <td>{{ $row->supervisor_name ?: '-' }}</td>
                            <td>
                                <span class="badge-like {{ $row->status === 'submitted' ? 'success' : ($row->status === 'expired' ? 'secondary' : ($row->status === 'cancelled' ? 'dark' : 'warning')) }}">
                                    {{ strtoupper($row->status) }}
                                </span>
                            </td>
                            <td data-order="{{ optional($row->emailed_at)->timestamp ?? 0 }}">{{ optional($row->emailed_at)->format('M d, Y h:i A') ?: '-' }}</td>
                            <td data-order="{{ optional($row->submitted_at)->timestamp ?? 0 }}">{{ optional($row->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                            <td>
                                <div class="stacked-actions">
                                    @if($row->status === 'submitted' && $row->evaluation)
                                        @if(!empty($row->evaluation->released_to_student_at))
                                            <a href="{{ route('student.evaluation.show', ['requestId' => $row->id]) }}" class="btn-eval btn-eval-outline">
                                                <i class="fa fa-eye"></i> View
                                            </a>
                                        @else
                                            <div class="permission-bubble-wrap" tabindex="0">
                                                <span class="btn-eval btn-eval-outline is-disabled" aria-disabled="true">
                                                    <i class="fa fa-lock"></i> Locked
                                                </span>
                                                <div class="field-bubble-shell">
                                                    The professor has not released this evaluation for student viewing yet.
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        @if($row->status !== 'cancelled')
                                            <form action="{{ route('student.evaluation.resend', ['requestId' => $row->id]) }}" method="POST" class="no-print">
                                                @csrf
                                                <button type="submit" class="btn-eval btn-eval-outline">
                                                    <i class="fa fa-redo"></i> Resend
                                                </button>
                                            </form>
                                        @endif

                                        @if(!in_array($row->status, ['submitted', 'cancelled']))
                                            <form action="{{ route('student.evaluation.cancel', ['requestId' => $row->id]) }}" method="POST" class="no-print cancel-evaluation-form" data-supervisor-email="{{ $row->supervisor_email }}">
                                                @csrf
                                                <button type="submit" class="btn-eval btn-eval-danger">
                                                    <i class="fa fa-ban"></i> Cancel
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</x-evaluation-shell>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    window.studentEvaluationConfig = {
        ownEmail: @json(strtolower((string) ($data->email ?? '')))
    };
</script>
    <script src="{{ vasset('js/student/evaluation.js') }}"></script>
    <script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>