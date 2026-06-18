<x-evaluation-shell
    role="student"
    title="Student Evaluation - InternConnect"
    pageTitle="Supervisor Evaluation"
    pageSubtitle="Send evaluation requests to your OJT supervisor and monitor submission status."
    :headerActionUrl="url('/student/home')"
    headerActionLabel="Back to Home"
>
    <style>
        .email-bubble-wrap {
            position: relative;
            overflow: visible;
        }

        .field-bubble-shell {
            position: absolute;
            left: 0;
            right: 0;
            bottom: calc(100% + 8px);
            z-index: 20;
            padding: 8px 11px;
            border-radius: 12px;
            background: #fff7ed;
            border: 1px solid #fdba74;
            color: #9a3412;
            font-size: 11.5px;
            line-height: 1.35;
            box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            visibility: hidden;
            opacity: 0;
            transform: translateY(4px);
            transition: opacity 0.18s ease, transform 0.18s ease, visibility 0.18s ease;
            pointer-events: none;
        }

        .field-bubble-shell.active {
            visibility: visible;
            opacity: 1;
            transform: translateY(0);
        }

        .field-bubble-shell::before {
            content: '';
            position: absolute;
            bottom: -7px;
            left: 22px;
            width: 12px;
            height: 12px;
            background: #fff7ed;
            border-bottom: 1px solid #fdba74;
            border-right: 1px solid #fdba74;
            transform: rotate(45deg);
        }
    </style>

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
                        <div class="email-bubble-wrap">
                            <input type="email" name="supervisor_email" class="form-control-shell" value="{{ old('supervisor_email', $expectedSupervisorEmail ?? '') }}" placeholder="name@company.com" required>
                            <div id="supervisorEmailBubble" class="field-bubble-shell"></div>
                        </div>
                        <div class="form-hint">If you use an email different from your MOA entry, the system will ask for confirmation.</div>
                    </div>
                </div>

                <div class="section-gap stacked-actions">
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
                <div class="shell-length-form">
                    <label for="historyPerPage">Show</label>
                    <select id="historyPerPage" class="shell-length-select">
                        @foreach([5, 10, 25, 50] as $size)
                            <option value="{{ $size }}" {{ $size === 5 ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </div>
                <div class="shell-filter-form">
                    <label for="historySearch" class="muted-text" style="font-size:13px; font-weight:500;">Search:</label>
                    <input type="search" id="historySearch" class="shell-filter-input" placeholder="Search email, name, or status">
                    <label for="historySort" class="muted-text" style="font-size:13px; font-weight:500;">Date</label>
                    <select id="historySort" class="shell-filter-select">
                        <option value="newest" selected>Newest first</option>
                        <option value="oldest">Oldest first</option>
                    </select>
                </div>
            </div>
            <div class="table-wrap history-datatable-wrap">
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
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        (function () {
            if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('historyTable')) {
                return;
            }

            const historyTable = $('#historyTable').DataTable({
                dom: 't<"history-bottom"ip>',
                order: [[3, 'desc']],
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                autoWidth: false,
                language: {
                    emptyTable: 'No evaluation requests yet.'
                },
                columnDefs: [
                    { targets: [5], orderable: false, searchable: false }
                ]
            });

            $('#historyPerPage').on('change', function () {
                historyTable.page.len(Number(this.value)).draw();
            });

            $('#historySearch').on('input', function () {
                historyTable.search(this.value).draw();
            });

            $('#historySort').on('change', function () {
                historyTable.order([[3, this.value === 'oldest' ? 'asc' : 'desc']]).draw();
            });
        })();

        (function () {
            const form = document.getElementById('sendEvaluationForm');
            if (!form) {
                return;
            }

            const emailInput = form.querySelector('input[name="supervisor_email"]');
            const confirmInput = document.getElementById('confirmEmailMismatch');
            const submitButton = document.getElementById('sendEvaluationButton');
            const emailBubble = document.getElementById('supervisorEmailBubble');
            const expectedEmail = (form.dataset.expectedEmail || '').trim().toLowerCase();
            const ownEmail = @json(strtolower((string) ($data->email ?? '')));

            function showEmailBubble(message) {
                if (!emailBubble) {
                    return;
                }

                if (!message) {
                    emailBubble.textContent = '';
                    emailBubble.classList.remove('active');
                    return;
                }

                emailBubble.textContent = message;
                emailBubble.classList.add('active');
            }

            function syncSupervisorEmailGuard() {
                if (!emailInput) {
                    return false;
                }

                const entered = (emailInput.value || '').trim().toLowerCase();
                const isOwnEmail = Boolean(ownEmail) && entered !== '' && entered === ownEmail;

                if (submitButton) {
                    submitButton.disabled = isOwnEmail;
                    submitButton.style.opacity = isOwnEmail ? '0.6' : '';
                    submitButton.style.cursor = isOwnEmail ? 'not-allowed' : '';
                }

                if (isOwnEmail) {
                    emailInput.setCustomValidity('Do not use your own student email.');
                    emailInput.style.borderColor = '#dc2626';
                    emailInput.style.boxShadow = '0 0 0 3px rgba(220,38,38,0.1)';
                    showEmailBubble("Don't use your own student email. Enter your supervisor's email.");
                    return true;
                }

                emailInput.setCustomValidity('');
                emailInput.style.borderColor = '';
                emailInput.style.boxShadow = '';
                showEmailBubble('');
                return false;
            }

            if (emailInput) {
                emailInput.addEventListener('input', syncSupervisorEmailGuard);
                emailInput.addEventListener('blur', syncSupervisorEmailGuard);
                syncSupervisorEmailGuard();
            }

            form.addEventListener('submit', function (event) {
                if (!emailInput || !confirmInput) {
                    return;
                }

                if (syncSupervisorEmailGuard()) {
                    event.preventDefault();
                    return;
                }

                confirmInput.value = '0';
                const entered = (emailInput.value || '').trim().toLowerCase();

                if (expectedEmail && entered && expectedEmail !== entered) {
                    event.preventDefault();

                    const proceed = function () {
                        confirmInput.value = '1';
                        form.submit();
                    };

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('The entered email does not match the email from your submitted MOA. Are you sure you want to continue?')) {
                            proceed();
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Use different supervisor email?',
                        html: 'The email you entered does not match the supervisor email from your submitted MOA.<br><br>Only continue if this is intentional.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, continue',
                        cancelButtonText: 'Go back',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceed();
                        }
                    });
                }
            });
        })();

        (function () {
            document.querySelectorAll('.cancel-evaluation-form').forEach(function (form) {
                form.addEventListener('submit', function (event) {
                    event.preventDefault();

                    const email = form.dataset.supervisorEmail || 'this supervisor';
                    const proceed = function () { form.submit(); };

                    if (typeof Swal === 'undefined') {
                        if (window.confirm('Cancel this evaluation request for ' + email + '?')) {
                            proceed();
                        }
                        return;
                    }

                    Swal.fire({
                        title: 'Cancel evaluation request?',
                        html: 'This will cancel the request sent to <strong>' + email + '</strong>.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Yes, cancel it',
                        cancelButtonText: 'Keep it',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            proceed();
                        }
                    });
                });
            });
        })();
    </script>
</x-evaluation-shell>
