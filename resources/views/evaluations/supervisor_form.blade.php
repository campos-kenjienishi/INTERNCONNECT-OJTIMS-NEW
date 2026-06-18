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
            <h2><span class="header-icon"><i class="fa fa-file-signature"></i></span> {{ optional($requestRow->template)->title ?: 'OJT Evaluation Form' }}</h2>
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

                <div class="flash-alert info" style="margin-top:0;">
                    Legitimacy check: Please upload a photo/scan of your signature over your printed name as proof that this evaluation was completed by the supervising officer.
                </div>

                <form method="POST" action="{{ route('evaluation.form.review', ['token' => $requestRow->token]) }}" enctype="multipart/form-data">
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

                    <div class="section-gap form-group">
                        <label class="form-label-shell">Supervisor Signature (image or PDF) *</label>
                        <input type="file" name="signature_file" class="form-control-shell" accept=".jpg,.jpeg,.png,.pdf" required>
                        <div class="form-hint">Accepted formats: JPG, PNG, PDF. Max file size: 4 MB.</div>
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
