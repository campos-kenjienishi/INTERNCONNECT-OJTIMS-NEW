<x-evaluation-shell
    role="public"
    title="Review OJT Evaluation"
    pageTitle="Review Before Submit"
    pageSubtitle="Please verify all details before sending the final evaluation."
>
    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-search"></i></span> Review Details</h2>
        </div>
        <div class="card-body-shell">
            <p class="muted-text">Please review the evaluation details before final submission.</p>

            <div class="evaluation-summary section-gap">
                <div class="summary-card">
                    <div class="label">Student</div>
                    <div class="value">{{ $requestRow->student_name ?: $requestRow->student_num }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Supervisor</div>
                    <div class="value">{{ $validated['supervisor_name'] }}</div>
                </div>
            </div>

            <div class="table-wrap section-gap">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Question</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($responses as $response)
                            <tr>
                                <td>{{ $response['section'] ?: '-' }}</td>
                                <td>{{ $response['label'] }}</td>
                                <td><strong>{{ $response['score'] }}</strong> / 5</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="section-gap">
                <div class="form-group">
                    <label class="form-label-shell">Comments</label>
                    <div class="summary-card" style="white-space: pre-line;">{{ $validated['comments'] ?: 'No comments provided.' }}</div>
                </div>
            </div>

            <div class="section-gap">
                <div class="form-group">
                    <label class="form-label-shell">Signature Proof</label>
                    <div class="summary-card">
                        @if(\Illuminate\Support\Str::endsWith(strtolower($signatureTempPath), ['.jpg', '.jpeg', '.png']))
                            <img src="{{ asset('storage/' . $signatureTempPath) }}" alt="Supervisor Signature" style="max-width:100%; width:420px; border:1px solid #ddd; border-radius:10px;">
                        @else
                            <a href="{{ asset('storage/' . $signatureTempPath) }}" target="_blank" class="btn-eval btn-eval-outline">
                                <i class="fa fa-file-pdf"></i> View Uploaded Signature File
                            </a>
                        @endif
                        <div class="form-hint" style="margin-top:10px;">{{ $signatureOriginalName }}</div>
                    </div>
                </div>
            </div>

            <div class="section-gap stacked-actions no-print">
                <form method="POST" action="{{ route('evaluation.form.submit', ['token' => $requestRow->token]) }}">
                    @csrf
                    <input type="hidden" name="supervisor_name" value="{{ $validated['supervisor_name'] }}">
                    <input type="hidden" name="comments" value="{{ $validated['comments'] ?? '' }}">
                    <input type="hidden" name="signature_temp_path" value="{{ $signatureTempPath }}">
                    @foreach($requestRow->template->items as $item)
                        @if($item->input_type === 'rating')
                            <input type="hidden" name="rating_{{ $item->id }}" value="{{ $validated['rating_' . $item->id] ?? '' }}">
                        @endif
                    @endforeach
                    <button type="submit" class="btn-eval btn-eval-primary">
                        <i class="fa fa-paper-plane"></i> Confirm and Submit
                    </button>
                </form>

                <a href="{{ route('evaluation.form.show', ['token' => $requestRow->token]) }}" class="btn-eval btn-eval-outline">
                    <i class="fa fa-edit"></i> Go Back and Edit
                </a>
            </div>
        </div>
    </div>
</x-evaluation-shell>
