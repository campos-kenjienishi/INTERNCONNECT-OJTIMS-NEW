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
            <h2><span class="header-icon"><i class="fa fa-door-open"></i></span> Student Evaluation Status by Class</h2>
            <span class="badge-like secondary">{{ $classrooms->count() }} Classes</span>
        </div>
        <div class="card-body-shell tight">
            <div class="table-wrap">
                <table class="table-shell">
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
                        @forelse($classrooms as $room)
                            @php
                                $classStudents = $students->filter(function ($student) use ($room) {
                                    return (string) optional($student->studentInfo)->class_id === (string) $room->id;
                                });

                                $submittedCount = 0;
                                foreach ($classStudents as $student) {
                                    $latest = ($requestsByStudent[$student->id] ?? collect())->first();
                                    if ($latest && $latest->status === 'submitted') {
                                        $submittedCount++;
                                    }
                                }

                                $totalCount = $classStudents->count();
                                $pendingCount = max($totalCount - $submittedCount, 0);
                            @endphp
                            <tr>
                                <td>{{ $room->room }}</td>
                                <td>{{ $totalCount }}</td>
                                <td><span class="badge-like success">{{ $submittedCount }}</span></td>
                                <td><span class="badge-like warning">{{ $pendingCount }}</span></td>
                                <td>
                                    <a href="{{ route('professor.evaluation.class', ['classId' => $room->id]) }}" class="btn-eval btn-eval-outline">
                                        <i class="fa fa-list"></i> Show List
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center-shell muted-text">No classes found for your account.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const questionList = document.getElementById('ratingQuestionList');
            const addButton = document.getElementById('addQuestionBlockBtn');
            const template = document.getElementById('questionBlockTemplate');

            if (!questionList || !addButton || !template) {
                return;
            }

            function makeKey() {
                return 'new_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
            }

            function bindRemoveButtons() {
                questionList.querySelectorAll('.remove-question-btn').forEach(function (button) {
                    button.onclick = function () {
                        const rows = questionList.querySelectorAll('.template-question-row');
                        if (rows.length <= 1) {
                            alert('At least one question block is required.');
                            return;
                        }
                        button.closest('.template-question-row').remove();
                    };
                });
            }

            addButton.addEventListener('click', function () {
                const key = makeKey();
                const html = template.innerHTML.replace(/__KEY__/g, key);
                questionList.insertAdjacentHTML('beforeend', html);
                bindRemoveButtons();
            });

            bindRemoveButtons();
        })();
    </script>
</x-evaluation-shell>
