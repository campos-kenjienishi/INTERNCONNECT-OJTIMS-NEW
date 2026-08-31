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

                    <div class="rating-guide-card section-gap">
                        <div class="rating-guide-header">
                            <div class="rating-guide-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>
                            <div class="rating-guide-content">
                                <h3 class="rating-guide-title">Evaluation Instructions &amp; Rating Scale Guide</h3>
                                <p class="rating-guide-desc">
                                    Please evaluate the student intern objectively based on their actual performance, attendance, and professional conduct during their internship. Select a rating from <strong>1 to 5</strong> for each criterion below.
                                </p>
                            </div>
                        </div>
                        <div class="rating-guide-scale">
                            <div class="rating-scale-item">
                                <span class="rating-scale-num scale-1">1</span>
                                <div class="rating-scale-text">
                                    <strong>Poor</strong>
                                    <span>Unsatisfactory</span>
                                </div>
                            </div>
                            <div class="rating-scale-item">
                                <span class="rating-scale-num scale-2">2</span>
                                <div class="rating-scale-text">
                                    <strong>Fair</strong>
                                    <span>Below Average</span>
                                </div>
                            </div>
                            <div class="rating-scale-item">
                                <span class="rating-scale-num scale-3">3</span>
                                <div class="rating-scale-text">
                                    <strong>Satisfactory</strong>
                                    <span>Competent</span>
                                </div>
                            </div>
                            <div class="rating-scale-item">
                                <span class="rating-scale-num scale-4">4</span>
                                <div class="rating-scale-text">
                                    <strong>Very Satisfactory</strong>
                                    <span>Above Average</span>
                                </div>
                            </div>
                            <div class="rating-scale-item">
                                <span class="rating-scale-num scale-5">5</span>
                                <div class="rating-scale-text">
                                    <strong>Outstanding</strong>
                                    <span>Exceptional</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="section-gap rating-list">
                        @php $currentSection = null; @endphp
                        @foreach($requestRow->template->items as $item)
                            @if($item->section !== $currentSection)
                                @php $currentSection = $item->section; @endphp
                                @if($currentSection)
                                    <div class="summary-card" style="margin-top: 10px;">
                                        <div class="label" style="font-weight: 700; color: var(--text-primary);">{{ $currentSection }}</div>
                                    </div>
                                @endif
                            @endif

                            @if($item->input_type === 'rating')
                                <div class="rating-row">
                                    <div class="rating-col-info">
                                        <div class="rating-section-pill">Criterion</div>
                                        <div class="rating-label">{{ $item->label }} @if($item->is_required)<span class="req-star" style="color:#dc2626;">*</span>@endif</div>
                                    </div>
                                    <div class="rating-col-scale">
                                        <div class="rating-scale-options" role="radiogroup" aria-label="{{ $item->label }}">
                                            @for($score = 1; $score <= 5; $score++)
                                                <label class="rating-circle-opt" title="Score {{ $score }} out of 5">
                                                    <input type="radio" 
                                                           name="rating_{{ $item->id }}" 
                                                           value="{{ $score }}" 
                                                           {{ (string)old('rating_' . $item->id) === (string)$score ? 'checked' : '' }}
                                                           {{ $item->is_required ? 'required' : '' }}>
                                                    <span class="rating-circle-disc">{{ $score }}</span>
                                                </label>
                                            @endfor
                                        </div>
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
                        <button type="submit" class="btn-eval btn-eval-emerald btn-eval-lg">
                            <i class="fa fa-check-circle"></i> Review Evaluation
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-evaluation-shell>
