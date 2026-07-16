<x-evaluation-shell
    role="public"
    title="OJT Evaluation Form"
    pageTitle="OJT Evaluation Form"
    pageSubtitle="Complete the evaluation, review it, and then submit it once verified."
>
    @if(session('error'))
        <div class="flash-alert error">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="flash-alert error">
            <ul style="margin:0; padding-left:18px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-shield-alt"></i></span> {{ optional($requestRow->template)->title ?: 'OJT Evaluation Form' }}</h2>
        </div>
        <div class="card-body-shell">
            @if($cancelled ?? false)
                <div class="flash-alert warning">
                    This evaluation request was cancelled by the student. If you believe this was cancelled by mistake, please contact the student before taking any further action.
                </div>
            @elseif($expired)
                <div class="flash-alert warning">
                    This evaluation link has expired. Please contact the student and ask them to resend the evaluation form if you still need to complete it.
                </div>
            @elseif($submitted)
                <div class="flash-alert success">This evaluation has already been submitted. Thank you.</div>
            @else
                <div class="summary-card section-gap" style="margin-bottom:18px;">
                    <div class="label">Student</div>
                    <div class="value">{{ $requestRow->student_name ?: $requestRow->student_num }}</div>
                    <div class="form-hint">Student Number: {{ $requestRow->student_num }}</div>
                </div>

                <form method="POST" action="{{ route('evaluation.form.review', ['token' => $requestRow->token]) }}">
                    @csrf

                    <div class="form-grid">
                        <div class="form-group" style="grid-column: 1 / -1;">
                            <label class="form-label-shell">Supervisor Name</label>
                            <input type="text" name="supervisor_name" class="form-control-shell" value="{{ old('supervisor_name', $requestRow->supervisor_name) }}" required>
                        </div>
                    </div>

                    <div class="section-gap rating-list">
                        <div class="flash-alert info" style="margin:0 0 14px 0;">
                            Rating guide: <strong>1 = lowest</strong>, <strong>5 = highest</strong>.
                        </div>
                        @php $currentSection = null; @endphp
                        @foreach($requestRow->template->items as $item)
                            @if($item->section !== $currentSection)
                                @php $currentSection = $item->section; @endphp
                                @if($currentSection)
                                    <div class="summary-card">
                                        <div class="label">{{ $currentSection }}</div>
                                    </div>
                                @endif
                            @endif

                            @if($item->input_type === 'rating')
                                <div class="rating-row">
                                    <div>
                                        <div class="rating-section">Question</div>
                                        <div class="rating-label">{{ $item->label }} {{ $item->is_required ? '*' : '' }}</div>
                                    </div>
                                    <div class="muted-text">Rate the student based on this criterion.</div>
                                    <div>
                                        <select name="rating_{{ $item->id }}" class="form-select-shell rating-select" {{ $item->is_required ? 'required' : '' }}>
                                            <option value="">Select score</option>
                                            @for($score = 1; $score <= 5; $score++)
                                                <option value="{{ $score }}" {{ old('rating_' . $item->id) == $score ? 'selected' : '' }}>{{ $score }}</option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="form-group section-gap">
                                    <label class="form-label-shell">{{ $item->label }}</label>
                                    <textarea name="comments" class="form-textarea-shell" rows="4">{{ old('comments') }}</textarea>
                                </div>
                            @endif
                        @endforeach
                    </div>

                    <div class="section-gap form-group" style="padding:16px; border:1px solid #d9e2f2; border-radius:14px; background:#f8fbff;">
                        <label style="display:flex; gap:12px; align-items:flex-start; cursor:pointer; line-height:1.55;">
                            <input type="checkbox" name="supervisor_confirmation" value="1" {{ old('supervisor_confirmation') ? 'checked' : '' }} required style="margin-top:4px; transform:scale(1.1);">
                            <span style="font-size:14px; color:#24324a;">
                                By submitting this evaluation form, I confirm that I am the authorized Company supervisor of the above-named student and that I have personally completed this evaluation. I certify that all information provided is accurate and based on my own evaluation.
                            </span>
                        </label>
                    </div>

                    <div class="section-gap stacked-actions">
                        <button type="submit" class="btn-eval btn-eval-primary">
                            <i class="fa fa-check-circle"></i> Review Evaluation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-evaluation-shell>
