@php
    $evaluationSubtitleHtml = '<a href="' . url('/professor/home') . '"><i class="fa fa-home"></i> Dashboard</a> <span class="crumb-sep"><i class="fa fa-chevron-right"></i></span> <span>Evaluation</span>';
    $ratingItems = $template ? $template->items->where('input_type', 'rating')->values() : collect();
    $fixedItems = $template ? $template->items->where('input_type', '!=', 'rating')->values() : collect();
@endphp

<x-evaluation-shell
    role="professor"
    title="Professor Evaluation - InternConnect"
    pageTitleHtml="Class <span>Evaluation</span>"
    :pageSubtitleHtml="$evaluationSubtitleHtml"
>
    <style>
        .question-confirm-overlay {
            position: fixed;
            inset: 0;
            display: none;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background: rgba(15, 23, 42, 0.48);
            backdrop-filter: blur(6px);
            z-index: 1200;
        }

        .question-confirm-overlay.open {
            display: flex;
        }

        .question-confirm-dialog {
            width: min(100%, 440px);
            border-radius: 22px;
            overflow: hidden;
            background:
                radial-gradient(circle at top right, rgba(220, 38, 38, 0.18), transparent 38%),
                linear-gradient(180deg, #ffffff 0%, #fff7f7 100%);
            border: 1px solid rgba(220, 38, 38, 0.14);
            box-shadow: 0 28px 70px rgba(15, 23, 42, 0.22);
        }

        .question-confirm-header {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 22px 24px 10px;
        }

        .question-confirm-icon {
            width: 48px;
            height: 48px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            box-shadow: 0 14px 28px rgba(220, 38, 38, 0.26);
            font-size: 18px;
        }

        .question-confirm-title {
            margin: 0;
            font-size: 20px;
            font-weight: 800;
            color: #111827;
        }

        .question-confirm-subtitle {
            margin: 4px 0 0;
            font-size: 13px;
            line-height: 1.55;
            color: #6b7280;
        }

        .question-confirm-body {
            padding: 0 24px 18px;
        }

        .question-confirm-message {
            margin: 0;
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.84);
            border: 1px solid rgba(220, 38, 38, 0.1);
            color: #374151;
            font-size: 14px;
            line-height: 1.7;
        }

        .question-confirm-preview {
            margin-top: 14px;
            padding: 16px 18px;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.92);
            border: 1px dashed rgba(220, 38, 38, 0.22);
        }

        .question-confirm-preview-label {
            margin: 0 0 10px;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #b91c1c;
        }

        .question-confirm-preview-grid {
            display: grid;
            gap: 10px;
        }

        .question-confirm-preview-item {
            padding: 10px 12px;
            border-radius: 12px;
            background: rgba(248, 250, 252, 0.95);
            border: 1px solid rgba(148, 163, 184, 0.18);
        }

        .question-confirm-preview-item span {
            display: block;
        }

        .question-confirm-preview-key {
            margin-bottom: 4px;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6b7280;
        }

        .question-confirm-preview-value {
            font-size: 14px;
            font-weight: 700;
            color: #111827;
            line-height: 1.5;
            word-break: break-word;
        }

        .question-confirm-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 0 24px 24px;
        }

        .question-confirm-btn {
            border: 0;
            border-radius: 14px;
            padding: 11px 18px;
            font-size: 13px;
            font-weight: 700;
            transition: transform .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .question-confirm-btn:hover {
            transform: translateY(-1px);
        }

        .question-confirm-cancel {
            background: #f3f4f6;
            color: #374151;
        }

        .question-confirm-cancel:hover {
            box-shadow: 0 10px 20px rgba(148, 163, 184, 0.2);
        }

        .question-confirm-delete {
            background: linear-gradient(135deg, #dc2626, #991b1b);
            color: #fff;
            box-shadow: 0 12px 24px rgba(220, 38, 38, 0.24);
        }

        .question-confirm-delete:hover {
            box-shadow: 0 16px 30px rgba(220, 38, 38, 0.3);
        }

        body.dark-mode .question-confirm-dialog {
            background:
                radial-gradient(circle at top right, rgba(248, 113, 113, 0.18), transparent 42%),
                linear-gradient(180deg, #22252f 0%, #1c1f27 100%);
            border-color: rgba(248, 113, 113, 0.14);
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.42);
        }

        body.dark-mode .question-confirm-title {
            color: #f9fafb;
        }

        body.dark-mode .question-confirm-subtitle,
        body.dark-mode .question-confirm-message {
            color: #d1d5db;
        }

        body.dark-mode .question-confirm-message {
            background: rgba(31, 41, 55, 0.82);
            border-color: rgba(248, 113, 113, 0.12);
        }

        body.dark-mode .question-confirm-cancel {
            background: #374151;
            color: #f3f4f6;
        }

        body.dark-mode .question-confirm-preview {
            background: rgba(17, 24, 39, 0.92);
            border-color: rgba(248, 113, 113, 0.22);
        }

        body.dark-mode .question-confirm-preview-item {
            background: rgba(31, 41, 55, 0.9);
            border-color: rgba(75, 85, 99, 0.35);
        }

        body.dark-mode .question-confirm-preview-key {
            color: #9ca3af;
        }

        body.dark-mode .question-confirm-preview-value {
            color: #f9fafb;
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

    @if($template)
        <div class="card-shell section-gap">
            <div class="card-header-shell">
                <h2><span class="header-icon"><i class="fa fa-clipboard-list"></i></span> Active Evaluation Template</h2>
                <span class="badge-like primary">Version {{ $template->version ?? 1 }}</span>
            </div>
            <div class="card-body-shell">
                <div class="panel-note">
                    Template changes will create a new version so older submissions remain tied to the template they used.
                </div>

                <form method="POST" action="{{ route('professor.evaluation.template.update', $template->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label-shell">Template Title</label>
                            <input type="text" name="title" class="form-control-shell" value="{{ old('title', $template->title) }}" required>
                        </div>

                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label-shell">Description</label>
                            <textarea name="description" class="form-textarea-shell" rows="2">{{ old('description', $template->description) }}</textarea>
                        </div>
                    </div>

                    <div class="section-gap" style="display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px;">
                        <div>
                            <div class="rating-section" style="margin-bottom:6px;">Question Blocks</div>
                            <div class="form-hint" style="margin:0;">Add or remove rating questions. Existing submitted evaluations keep their original template version.</div>
                        </div>
                        <button type="button" class="btn-eval btn-eval-primary" id="addQuestionBlockBtn">
                            <i class="fa fa-plus"></i> Add Question Block
                        </button>
                    </div>

                    <div id="questionBlockNotice" class="panel-note" style="display:none; margin-bottom:14px; border-left-color:var(--danger); background:rgba(185, 28, 28, 0.08); color:var(--text-primary);">
                        Question block added.
                    </div>

                    <div class="section-gap rating-list" id="ratingQuestionList">
                        @foreach($ratingItems as $index => $item)
                            <div class="rating-row template-question-row" style="grid-template-columns: 1.2fr 2fr 120px 80px; align-items:end;">
                                <div>
                                    <div class="rating-section">Section</div>
                                    <input type="hidden" name="item_keys[]" value="{{ $item->id }}">
                                    <input type="hidden" name="item_ids[]" value="{{ $item->id }}">
                                    <input type="text" name="item_sections[]" class="form-control-shell rating-select" value="{{ old('item_sections.' . $index, $item->section) }}">
                                </div>
                                <div>
                                    <div class="rating-section">Question</div>
                                    <input type="text" name="item_labels[]" class="form-control-shell rating-select" value="{{ old('item_labels.' . $index, $item->label) }}" required>
                                </div>
                                <div>
                                    <div class="rating-section">Required</div>
                                    <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary); font-weight:600;">
                                        <input type="checkbox" value="{{ $item->id }}" name="item_required[]" {{ $item->is_required ? 'checked' : '' }}>
                                        Yes
                                    </label>
                                </div>
                                <div class="row-actions" style="display:flex; justify-content:flex-end; align-items:flex-end;">
                                    <button type="button" class="btn-eval btn-eval-danger remove-question-btn" title="Remove Question Block">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <template id="questionBlockTemplate">
                        <div class="rating-row template-question-row" style="grid-template-columns: 1.2fr 2fr 120px 80px; align-items:end;">
                            <div>
                                <div class="rating-section">Section</div>
                                <input type="hidden" name="item_keys[]" value="__KEY__">
                                <input type="hidden" name="item_ids[]" value="">
                                <input type="text" name="item_sections[]" class="form-control-shell rating-select" value="">
                            </div>
                            <div>
                                <div class="rating-section">Question</div>
                                <input type="text" name="item_labels[]" class="form-control-shell rating-select" value="" required>
                            </div>
                            <div>
                                <div class="rating-section">Required</div>
                                <label style="display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-primary); font-weight:600;">
                                    <input type="checkbox" value="__KEY__" name="item_required[]" checked>
                                    Yes
                                </label>
                            </div>
                            <div class="row-actions" style="display:flex; justify-content:flex-end; align-items:flex-end;">
                                <button type="button" class="btn-eval btn-eval-danger remove-question-btn" title="Remove Question Block">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </template>

                    @if($fixedItems->isNotEmpty())
                        <div class="section-gap" style="margin-top:28px;">
                            <div class="rating-section" style="margin-bottom:10px;">Fixed Text Items</div>
                            <div class="panel-note" style="margin-bottom:12px;">
                                These text items are kept with the template and are not part of the add/remove question blocks yet.
                            </div>
                            @foreach($fixedItems as $item)
                                <div class="summary-card" style="margin-bottom:10px;">
                                    <div class="label">{{ $item->section ?: 'Text Item' }}</div>
                                    <div class="value">{{ $item->label }}</div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="section-gap stacked-actions">
                        <button class="btn-eval btn-eval-primary" type="submit">
                            <i class="fa fa-save"></i> Save Template Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-robot"></i></span> AI Evaluation Insight</h2>
            <button type="button" class="btn-eval btn-eval-primary" id="generateEvaluationInsightBtn">
                <i class="fa fa-magic"></i> Generate AI Insight
            </button>
        </div>
        <div class="card-body-shell" id="evaluationInsightPanel" style="display:none;">
            <div style="display:flex; justify-content:flex-end; margin-bottom:10px;">
                <button type="button" id="evaluationInsightCloseBtn" class="btn-eval btn-eval-outline" style="padding:7px 11px; font-size:12px;">
                    <i class="fa fa-times"></i> Close
                </button>
            </div>
            <div class="panel-note" id="evaluationInsightIntro">
                AI insight is generated only when you click the button, so Gemini usage is controlled by the user.
            </div>
            <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(160px, 1fr)); gap:12px; margin-bottom:14px;">
                <div class="summary-card"><div class="label">Submitted</div><div class="value">{{ $evaluationAiData['metrics']['submitted_evaluations'] ?? 0 }}</div></div>
                <div class="summary-card"><div class="label">Pending</div><div class="value">{{ $evaluationAiData['metrics']['pending_evaluations'] ?? 0 }}</div></div>
                <div class="summary-card"><div class="label">Expired</div><div class="value">{{ $evaluationAiData['metrics']['expired_requests'] ?? 0 }}</div></div>
                <div class="summary-card"><div class="label">Classes Pending</div><div class="value">{{ $evaluationAiData['metrics']['classes_with_pending'] ?? 0 }}</div></div>
            </div>
            <div id="evaluationAiStatus" style="display:none; font-size:12px; color:var(--text-secondary); margin-bottom:12px;"></div>
            <div id="evaluationAiResult" style="display:none;">
                <p id="evaluationAiSummary" style="font-size:14px; line-height:1.7; color:var(--text-primary); margin:0 0 16px;"></p>
                <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:14px;">
                    <div style="background:var(--surface-muted); border:1px solid var(--border); border-radius:12px; padding:14px;">
                        <div style="font-size:12px; font-weight:800; color:var(--danger); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Key Findings</div>
                        <ul id="evaluationAiFindings" style="margin:0; padding-left:18px; color:var(--text-primary); line-height:1.65;"></ul>
                    </div>
                    <div style="background:var(--surface-muted); border:1px solid var(--border); border-radius:12px; padding:14px;">
                        <div style="font-size:12px; font-weight:800; color:var(--danger); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Watchouts</div>
                        <ul id="evaluationAiWatchouts" style="margin:0; padding-left:18px; color:var(--text-primary); line-height:1.65;"></ul>
                    </div>
                    <div style="background:var(--surface-muted); border:1px solid var(--border); border-radius:12px; padding:14px;">
                        <div style="font-size:12px; font-weight:800; color:var(--danger); margin-bottom:8px; text-transform:uppercase; letter-spacing:.4px;">Recommended Actions</div>
                        <ul id="evaluationAiActions" style="margin:0; padding-left:18px; color:var(--text-primary); line-height:1.65;"></ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-door-open"></i></span> Student Evaluation Status by Class</h2>
            <span class="badge-like secondary">{{ $classroomsTotal }} Classes</span>
        </div>
        <div class="card-body-shell tight">
            <div class="shell-table-controls">
                <div class="shell-length-form">
                    <label for="classStatusPerPage">Show</label>
                    <select id="classStatusPerPage" class="shell-length-select">
                        @foreach([10, 25, 50] as $size)
                            <option value="{{ $size }}" {{ $size === 10 ? 'selected' : '' }}>{{ $size }}</option>
                        @endforeach
                    </select>
                    <span>entries</span>
                </div>
                <div class="shell-filter-form">
                    <label for="classStatusSearch" class="muted-text" style="font-size:13px; font-weight:500;">Search:</label>
                    <input type="search" id="classStatusSearch" class="shell-filter-input" placeholder="Search class or room">
                </div>
            </div>
            <div class="table-wrap history-datatable-wrap">
                <table id="classStatusTable" class="display table-shell" style="width:100%">
                    <thead>
                        <tr>
                            <th>Class / Room</th>
                            <th>Total Students</th>
                            <th>Submitted</th>
                            <th>Pending</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($classrooms as $room)
                            <tr>
                                <td>{{ $room->room }}</td>
                                <td>{{ $room->total_count ?? 0 }}</td>
                                <td><span class="badge-like success">{{ $room->submitted_count ?? 0 }}</span></td>
                                <td><span class="badge-like warning">{{ $room->pending_count ?? 0 }}</span></td>
                                <td>
                                    <a href="{{ route('professor.evaluation.class', ['classId' => $room->id]) }}" class="btn-eval btn-eval-outline">
                                        <i class="fa fa-list"></i> Show List
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="question-confirm-overlay" id="removeQuestionConfirm" aria-hidden="true">
        <div class="question-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="removeQuestionConfirmTitle">
            <div class="question-confirm-header">
                <div class="question-confirm-icon">
                    <i class="fa fa-trash"></i>
                </div>
                <div>
                    <h3 class="question-confirm-title" id="removeQuestionConfirmTitle">Remove Question Block?</h3>
                    <p class="question-confirm-subtitle">This removes the block from the editor before you save the template.</p>
                </div>
            </div>
            <div class="question-confirm-body">
                <p class="question-confirm-message">
                    The selected question block will be removed from the current template draft. Submitted evaluations are not affected.
                </p>
                <div class="question-confirm-preview">
                    <p class="question-confirm-preview-label">Block Preview</p>
                    <div class="question-confirm-preview-grid">
                        <div class="question-confirm-preview-item">
                            <span class="question-confirm-preview-key">Section</span>
                            <span class="question-confirm-preview-value" id="removeQuestionPreviewSection">-</span>
                        </div>
                        <div class="question-confirm-preview-item">
                            <span class="question-confirm-preview-key">Question</span>
                            <span class="question-confirm-preview-value" id="removeQuestionPreviewLabel">-</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="question-confirm-actions">
                <button type="button" class="question-confirm-btn question-confirm-cancel" id="removeQuestionCancelBtn">Keep Block</button>
                <button type="button" class="question-confirm-btn question-confirm-delete" id="removeQuestionConfirmBtn">
                    <i class="fa fa-trash"></i> Remove Block
                </button>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script>
        (function () {
            if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('classStatusTable')) {
                return;
            }

            const classStatusTable = $('#classStatusTable').DataTable({
                dom: 't<"history-bottom"ip>',
                pageLength: 10,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                language: {
                    emptyTable: 'No classes found for your account.'
                },
                columnDefs: [
                    { targets: [4], orderable: false, searchable: false }
                ]
            });

            $('#classStatusPerPage').on('change', function () {
                classStatusTable.page.len(Number(this.value)).draw();
            });

            $('#classStatusSearch').on('input', function () {
                classStatusTable.search(this.value).draw();
            });
        })();

        (function () {
            const evaluationAiData = @json($evaluationAiData ?? []);
            const generateInsightBtn = document.getElementById('generateEvaluationInsightBtn');
            const insightPanel = document.getElementById('evaluationInsightPanel');
            const insightCloseBtn = document.getElementById('evaluationInsightCloseBtn');
            const insightStatus = document.getElementById('evaluationAiStatus');
            const insightIntro = document.getElementById('evaluationInsightIntro');
            const insightResult = document.getElementById('evaluationAiResult');
            const insightSummary = document.getElementById('evaluationAiSummary');
            const insightFindings = document.getElementById('evaluationAiFindings');
            const insightWatchouts = document.getElementById('evaluationAiWatchouts');
            const insightActions = document.getElementById('evaluationAiActions');

            function renderList(target, items, emptyText) {
                if (!target) return;
                target.innerHTML = '';
                const list = Array.isArray(items) && items.length ? items : [emptyText];
                list.forEach(function (item) {
                    const li = document.createElement('li');
                    li.textContent = item;
                    target.appendChild(li);
                });
            }

            function renderEvaluationInsight(data) {
                if (!insightResult || !insightSummary) return;

                insightSummary.textContent = data.summary || 'No AI insight was returned.';
                renderList(insightFindings, data.key_findings, 'No key findings available.');
                renderList(insightWatchouts, data.watchouts, 'No major watchouts detected.');
                renderList(insightActions, data.recommendations, 'No actions suggested.');
                insightResult.style.display = 'block';
                if (insightIntro) insightIntro.style.display = 'none';
            }

            if (generateInsightBtn) {
                generateInsightBtn.addEventListener('click', function () {
                    generateInsightBtn.disabled = true;
                    if (insightPanel) {
                        insightPanel.style.display = 'block';
                    }
                    if (insightStatus) {
                        insightStatus.textContent = 'Generating AI insight...';
                        insightStatus.style.display = 'block';
                    }

                    fetch(@json(route('reports.ai.insight')), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token())
                        },
                        body: JSON.stringify(evaluationAiData)
                    })
                        .then(function (response) {
                            if (!response.ok) throw new Error('AI insight request failed.');
                            return response.json();
                        })
                        .then(function (data) {
                            renderEvaluationInsight(data);
                            if (insightStatus) {
                                insightStatus.textContent = data.source === 'fallback'
                                    ? ((data.availability && data.availability.message) ? data.availability.message + ' Internal insight shown.' : 'Gemini is unavailable or rate-limited. Internal insight shown.')
                                    : 'AI insight generated.';
                            }
                        })
                        .catch(function () {
                            if (insightStatus) {
                                insightStatus.textContent = 'AI insight could not be generated right now. Please try again later.';
                                insightStatus.style.display = 'block';
                            }
                        })
                        .finally(function () {
                            generateInsightBtn.disabled = false;
                        });
                });
            }

            if (insightCloseBtn && insightPanel) {
                insightCloseBtn.addEventListener('click', function () {
                    insightPanel.style.display = 'none';
                });
            }

            const questionList = document.getElementById('ratingQuestionList');
            const addButton = document.getElementById('addQuestionBlockBtn');
            const template = document.getElementById('questionBlockTemplate');
            const questionBlockNotice = document.getElementById('questionBlockNotice');
            const removeQuestionConfirm = document.getElementById('removeQuestionConfirm');
            const removeQuestionCancelBtn = document.getElementById('removeQuestionCancelBtn');
            const removeQuestionConfirmBtn = document.getElementById('removeQuestionConfirmBtn');
            const removeQuestionPreviewSection = document.getElementById('removeQuestionPreviewSection');
            const removeQuestionPreviewLabel = document.getElementById('removeQuestionPreviewLabel');
            let questionNoticeTimeout = null;
            let pendingRemoveRow = null;

            if (!questionList || !addButton || !template) {
                return;
            }

            function makeKey() {
                return 'new_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
            }

            function showQuestionBlockNotice(message) {
                if (!questionBlockNotice) return;

                questionBlockNotice.textContent = message;
                questionBlockNotice.style.display = 'block';

                if (questionNoticeTimeout) {
                    clearTimeout(questionNoticeTimeout);
                }

                questionNoticeTimeout = window.setTimeout(function () {
                    questionBlockNotice.style.display = 'none';
                }, 2500);
            }

            function closeRemoveConfirm() {
                if (!removeQuestionConfirm) return;

                removeQuestionConfirm.classList.remove('open');
                removeQuestionConfirm.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                pendingRemoveRow = null;
            }

            function openRemoveConfirm(row) {
                if (!removeQuestionConfirm) return;

                const sectionInput = row ? row.querySelector('input[name="item_sections[]"]') : null;
                const labelInput = row ? row.querySelector('input[name="item_labels[]"]') : null;
                const sectionText = sectionInput && sectionInput.value.trim() ? sectionInput.value.trim() : 'No section entered';
                const labelText = labelInput && labelInput.value.trim() ? labelInput.value.trim() : 'No question entered';

                pendingRemoveRow = row;
                if (removeQuestionPreviewSection) {
                    removeQuestionPreviewSection.textContent = sectionText;
                }
                if (removeQuestionPreviewLabel) {
                    removeQuestionPreviewLabel.textContent = labelText;
                }
                removeQuestionConfirm.classList.add('open');
                removeQuestionConfirm.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');

                if (removeQuestionConfirmBtn) {
                    removeQuestionConfirmBtn.focus();
                }
            }

            function bindRemoveButtons() {
                questionList.querySelectorAll('.remove-question-btn').forEach(function (button) {
                    button.onclick = function () {
                        const rows = questionList.querySelectorAll('.template-question-row');
                        if (rows.length <= 1) {
                            alert('At least one question block is required.');
                            return;
                        }
                        openRemoveConfirm(button.closest('.template-question-row'));
                    };
                });
            }

            addButton.addEventListener('click', function () {
                const key = makeKey();
                const html = template.innerHTML.replace(/__KEY__/g, key);
                questionList.insertAdjacentHTML('beforeend', html);
                bindRemoveButtons();
                showQuestionBlockNotice('Question block added.');
            });

            bindRemoveButtons();

            if (removeQuestionCancelBtn) {
                removeQuestionCancelBtn.addEventListener('click', closeRemoveConfirm);
            }

            if (removeQuestionConfirmBtn) {
                removeQuestionConfirmBtn.addEventListener('click', function () {
                    if (pendingRemoveRow) {
                        pendingRemoveRow.remove();
                    }

                    closeRemoveConfirm();
                });
            }

            if (removeQuestionConfirm) {
                removeQuestionConfirm.addEventListener('click', function (event) {
                    if (event.target === removeQuestionConfirm) {
                        closeRemoveConfirm();
                    }
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && removeQuestionConfirm && removeQuestionConfirm.classList.contains('open')) {
                    closeRemoveConfirm();
                }
            });
        })();
    </script>
</x-evaluation-shell>
