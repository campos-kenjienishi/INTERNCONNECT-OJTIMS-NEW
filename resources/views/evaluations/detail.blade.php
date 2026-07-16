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
            <h2><span class="header-icon"><i class="fa fa-shield-alt"></i></span> Supervisor Confirmation</h2>
            @if($evaluation->released_to_student_at)
                <span class="badge-like success">Released to student</span>
            @elseif($isProfessorView)
                <span class="badge-like warning">Private until released</span>
            @endif
        </div>
        <div class="card-body-shell">
            @if(!empty($evaluation->supervisor_confirmation))
                <div class="summary-card" style="display:flex; flex-direction:column; gap:12px; padding:18px 20px;">
                    <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
                        <span class="badge-like success" style="padding:8px 14px; border-radius:999px;">
                            <i class="fa fa-check-circle"></i> Confirmed
                        </span>
                        @if($evaluation->released_to_student_at)
                            <span class="badge-like secondary" style="padding:8px 14px; border-radius:999px;">
                                Released {{ optional($evaluation->released_to_student_at)->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                    <div class="value" style="font-size:16px; line-height:1.6; max-width: 56ch;">
                        Supervisor attestation was recorded before submission and is ready for professor review.
                    </div>
                    @if($isProfessorView)
                        <div class="form-hint" style="margin-top:-2px;">
                            Keep it private until you release it to the student.
                        </div>
                    @endif
                </div>
            @else
                <p class="muted-text">No supervisor confirmation recorded.</p>
            @endif

            @if($isProfessorView)
                <div class="section-gap no-print">
                    @if(empty($evaluation->released_to_student_at))
                        <form method="POST" action="{{ route('professor.evaluation.release', ['requestId' => $requestRow->id]) }}">
                            @csrf
                            <button type="submit" class="btn-eval btn-eval-primary">
                                <i class="fa fa-paper-plane"></i> Release to Student
                            </button>
                        </form>
                    @else
                        <div class="flash-alert success" style="margin-bottom:0;">
                            Released to student on {{ optional($evaluation->released_to_student_at)->format('M d, Y h:i A') }}.
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-evaluation-shell>
