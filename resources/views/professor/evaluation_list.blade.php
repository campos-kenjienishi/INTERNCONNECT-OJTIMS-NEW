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
                <button type="button" class="btn-eval btn-eval-outline" id="openEvalPrintModalBtn">
                    <i class="fa fa-print"></i> Print Report
                </button>
            </div>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-users"></i></span> Student Evaluation Status</h2>
            <span class="badge-like secondary">{{ $studentsTotal }} Students</span>
        </div>
        <div class="card-body-shell tight">
            <div class="shell-table-controls">
                <div class="shell-length-form">
                    <label for="studentStatusPerPage">Show</label>
                    <select id="studentStatusPerPage" class="shell-length-select">
                        @foreach([10, 25, 50] as $size)
                            <option value="{{ $size }}" {{ $size === 10 ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </div>
                <div class="shell-filter-form">
                    <label for="studentStatusSearch" class="muted-text" style="font-size:13px; font-weight:500;">Search:</label>
                    <input type="search" id="studentStatusSearch" class="shell-filter-input" placeholder="Search student, number, email, or status">
                    <label for="studentStatusFilter" class="muted-text" style="font-size:13px; font-weight:500;">Status</label>
                    <select id="studentStatusFilter" class="shell-filter-select">
                        <option value="all" selected>All statuses</option>
                        <option value="submitted">Submitted</option>
                        <option value="in_progress">In progress</option>
                        <option value="other">Other</option>
                        <option value="not_sent">Not sent</option>
                    </select>
                </div>
            </div>
            <div class="table-wrap history-datatable-wrap">
                <table id="studentStatusTable" class="display table-shell" style="width:100%">
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
                        @foreach($students as $student)
                            @php
                                $studentRequests = $requestsByStudent[$student->id] ?? collect();
                                $latest = $studentRequests->first();
                                $statusRank = match ($latest->status ?? null) {
                                    'submitted' => 0,
                                    'sent', 'opened' => 1,
                                    'expired', 'cancelled' => 2,
                                    null => 3,
                                    default => 2,
                                };
                            @endphp
                            <tr>
                                <td>{{ optional($student->studentInfo)->studentNum ?: '-' }}</td>
                                <td>{{ $student->full_name }}</td>
                                <td data-order="{{ $statusRank }}">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-evaluation-shell>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    (function () {
        if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('studentStatusTable')) {
            return;
        }

        const studentStatusTable = $('#studentStatusTable').DataTable({
            dom: 't<"history-bottom"ip>',
            order: [[2, 'asc']],
            pageLength: 10,
            lengthMenu: [[10, 25, 50], [10, 25, 50]],
            autoWidth: false,
            language: {
                emptyTable: 'No students found for this class.'
            },
            columnDefs: [
                { targets: [5, 6], orderable: false }
            ]
        });

        $('#studentStatusPerPage').on('change', function () {
            studentStatusTable.page.len(Number(this.value)).draw();
        });

        $('#studentStatusSearch').on('input', function () {
            studentStatusTable.search(this.value).draw();
        });

        $('#studentStatusFilter').on('change', function () {
            const value = this.value;
            let pattern = '';

            if (value === 'submitted') {
                pattern = '^SUBMITTED$';
            } else if (value === 'in_progress') {
                pattern = '^(SENT|OPENED)$';
            } else if (value === 'other') {
                pattern = '^(EXPIRED|CANCELLED)$';
            } else if (value === 'not_sent') {
                pattern = '^NOT SENT$';
            }

            studentStatusTable.column(2).search(pattern, true, false).draw();
        });
    })();

    (function () {
        const openBtn = document.getElementById('openEvalPrintModalBtn');
        const reportUrl = @json(route('professor.evaluation.print', ['class_id' => $classroom->id]));

        if (!openBtn) {
            return;
        }

        openBtn.addEventListener('click', function () {
            const frame = document.createElement('iframe');
            frame.style.position = 'fixed';
            frame.style.right = '0';
            frame.style.bottom = '0';
            frame.style.width = '0';
            frame.style.height = '0';
            frame.style.border = '0';
            frame.style.opacity = '0';
            frame.setAttribute('aria-hidden', 'true');
            frame.src = reportUrl;

            let cleanedUp = false;
            const cleanup = function () {
                if (cleanedUp) {
                    return;
                }
                cleanedUp = true;
                window.removeEventListener('afterprint', cleanup);
                if (frame.parentNode) {
                    frame.parentNode.removeChild(frame);
                }
            };

            frame.onload = function () {
                setTimeout(function () {
                    if (frame.contentWindow) {
                        frame.contentWindow.focus();
                        frame.contentWindow.print();
                        window.addEventListener('afterprint', cleanup, { once: true });
                        setTimeout(cleanup, 1500);
                    } else {
                        cleanup();
                    }
                }, 150);
            };

            document.body.appendChild(frame);
        });
    })();
</script>
