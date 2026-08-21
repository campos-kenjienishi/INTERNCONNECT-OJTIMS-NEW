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
    <link rel="stylesheet" href="{{ vasset('css/professor/evaluation.css') }}">

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

</x-evaluation-shell>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="//cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    window.professorEvalConfig = {
        evaluationAiData: @json($evaluationAiData ?? []),
        aiInsightUrl: @json(route('reports.ai.insight')),
        csrfToken: @json(csrf_token())
    };
</script>
<script src="{{ vasset('js/professor/evaluation.js') }}"></script>
<script src="{{ vasset('assets/js/dark-mode.js') }}"></script>
@include('partials.password-setup-modal')
<script src="{{ vasset('assets/js/voice-input.js') }}"></script>
</body>
</html>