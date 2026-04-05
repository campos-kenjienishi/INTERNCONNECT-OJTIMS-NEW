<x-evaluation-shell
    role="student"
    title="Student Evaluation - InternConnect"
    pageTitle="Supervisor Evaluation"
    pageSubtitle="Send evaluation requests to your OJT supervisor and monitor submission status."
    :headerActionUrl="url('/student/home')"
    headerActionLabel="Back to Home"
>
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
                        <label class="form-label-shell">Supervisor Name <span class="muted-text">(optional)</span></label>
                        <input type="text" name="supervisor_name" class="form-control-shell" value="{{ old('supervisor_name') }}" placeholder="Enter supervisor name">
                    </div>
                    <div class="form-group">
                        <label class="form-label-shell">Supervisor Email</label>
                        <input type="email" name="supervisor_email" class="form-control-shell" value="{{ old('supervisor_email', $expectedSupervisorEmail ?? '') }}" placeholder="name@company.com" required>
                        <div class="form-hint">If you use an email different from your MOA entry, the system will ask for confirmation.</div>
                    </div>
                </div>

                <div class="section-gap stacked-actions">
                    <button type="submit" class="btn-eval btn-eval-primary">
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
            <div class="table-wrap">
                <table class="table-shell">
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
                        @forelse($requests as $row)
                            <tr>
                                <td>{{ $row->supervisor_email }}</td>
                                <td>{{ $row->supervisor_name ?: '-' }}</td>
                                <td>
                                    <span class="badge-like {{ $row->status === 'submitted' ? 'success' : ($row->status === 'expired' ? 'secondary' : ($row->status === 'cancelled' ? 'dark' : 'warning')) }}">
                                        {{ strtoupper($row->status) }}
                                    </span>
                                </td>
                                <td>{{ optional($row->emailed_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>{{ optional($row->submitted_at)->format('M d, Y h:i A') ?: '-' }}</td>
                                <td>
                                    <div class="stacked-actions">
                                        @if($row->status === 'submitted' && $row->evaluation)
                                            <a href="{{ route('student.evaluation.show', ['requestId' => $row->id]) }}" class="btn-eval btn-eval-outline">
                                                <i class="fa fa-eye"></i> View
                                            </a>
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
                                                <form action="{{ route('student.evaluation.cancel', ['requestId' => $row->id]) }}" method="POST" class="no-print" onsubmit="return confirm('Cancel this evaluation request?');">
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
                        @empty
                            <tr>
                                <td colspan="6" class="text-center-shell muted-text">No evaluation requests yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const form = document.getElementById('sendEvaluationForm');
            if (!form) {
                return;
            }

            const emailInput = form.querySelector('input[name="supervisor_email"]');
            const confirmInput = document.getElementById('confirmEmailMismatch');
            const expectedEmail = (form.dataset.expectedEmail || '').trim().toLowerCase();

            form.addEventListener('submit', function (event) {
                if (!emailInput || !confirmInput) {
                    return;
                }

                confirmInput.value = '0';
                const entered = (emailInput.value || '').trim().toLowerCase();

                if (expectedEmail && entered && expectedEmail !== entered) {
                    const proceed = window.confirm('The entered email does not match the email from your submitted MOA. Are you sure you want to continue?');
                    if (!proceed) {
                        event.preventDefault();
                        return;
                    }
                    confirmInput.value = '1';
                }
            });
        })();
    </script>
</x-evaluation-shell>
