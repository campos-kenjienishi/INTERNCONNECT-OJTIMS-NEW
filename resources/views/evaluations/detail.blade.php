@php
    $shellRole = str_contains($backUrl, '/professor/') ? 'professor' : 'student';
@endphp

<x-evaluation-shell
    :role="$shellRole"
    title="Evaluation Details - InternConnect"
    pageTitle="Submitted Evaluation"
    pageSubtitle="Read-only view of the completed student evaluation submitted by the supervisor."
    :headerActionUrl="$backUrl"
    headerActionLabel="Back"
>
    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-user-check"></i></span> Evaluation Summary</h2>
            @if(optional($requestRow->template)->version)
                <span class="badge-like secondary">Version {{ $requestRow->template->version }}</span>
            @endif
        </div>
        <div class="card-body-shell">
            <div class="evaluation-summary">
                <div class="summary-card">
                    <div class="label">Student</div>
                    <div class="value">{{ $requestRow->student_name ?: $requestRow->student_num }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Student Number</div>
                    <div class="value">{{ $requestRow->student_num }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Supervisor</div>
                    <div class="value">{{ $evaluation->supervisor_name ?: ($requestRow->supervisor_name ?: '-') }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Submitted At</div>
                    <div class="value">{{ optional($evaluation->submitted_at)->format('M d, Y h:i A') ?: '-' }}</div>
                </div>
                <div class="summary-card">
                    <div class="label">Template</div>
                    <div class="value">{{ optional($requestRow->template)->title ?: 'OJT Evaluation Form' }}</div>
                </div>
            </div>
        </div>
    </div>

    @php
        $responses = $evaluation->responses;
        $total = 0;
        $count = 0;
        foreach ($responses as $response) {
            $score = (int) ($response['score'] ?? 0);
            if ($score > 0) {
                $total += $score;
                $count++;
            }
        }
        $average = $count > 0 ? round($total / $count, 2) : null;
    @endphp

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-star"></i></span> Ratings</h2>
            <span class="badge-like primary">Average: {{ $average !== null ? $average . ' / 5' : 'N/A' }}</span>
        </div>
        <div class="card-body-shell tight">
            <div class="table-wrap">
                <table class="table-shell">
                    <thead>
                        <tr>
                            <th>Section</th>
                            <th>Question</th>
                            <th>Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($responses as $response)
                            <tr>
                                <td>{{ $response['section'] ?? '-' }}</td>
                                <td>{{ $response['label'] ?? '-' }}</td>
                                <td><strong>{{ $response['score'] ?? '-' }}</strong> / 5</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center-shell muted-text">No rating responses found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-comment-dots"></i></span> Comments and Suggestions</h2>
        </div>
        <div class="card-body-shell">
            <p class="muted-text" style="white-space: pre-line;">{{ $evaluation->comments ?: 'No comments provided.' }}</p>
        </div>
    </div>

    <div class="card-shell section-gap">
        <div class="card-header-shell">
            <h2><span class="header-icon"><i class="fa fa-signature"></i></span> Supervisor Signature Proof</h2>
        </div>
        <div class="card-body-shell">
            @if(!empty($evaluation->signature_path) && !empty($signaturePreviewDataUri))
                @if(!empty($signaturePreviewMime) && str_starts_with($signaturePreviewMime, 'image/'))
                    <img src="{{ $signaturePreviewDataUri }}" alt="Supervisor Signature" style="max-width:100%; width:460px; border:1px solid #ddd; border-radius:10px;">
                @else
                    <iframe src="{{ $signaturePreviewDataUri }}" title="Supervisor Signature" style="width:100%; min-height:420px; border:1px solid #ddd; border-radius:10px; background:#fff;"></iframe>
                @endif
            @else
                <p class="muted-text">No signature proof uploaded.</p>
            @endif
        </div>
    </div>
</x-evaluation-shell>
