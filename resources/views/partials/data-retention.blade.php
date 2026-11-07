<link rel="stylesheet" href="{{ vasset('css/coordinator/data-retention.css') }}">
<details class="retention-workspace" id="data-retention" @if(request()->hasAny(['retention_kind', 'retention_search', 'retention_page', 'disposal_page']) || old('subject_type')) open @endif>
    <summary class="retention-heading">
        <div><span class="retention-eyebrow">Records lifecycle</span><h2 id="retention-title"><i class="fa fa-shield-alt" aria-hidden="true"></i> Data Retention &amp; Disposal</h2></div>
        <div class="retention-heading-actions"><span class="retention-mode">{{ config('retention.disposal_enabled') ? 'Explicit disposal enabled' : 'Preview-only mode' }}</span><i class="fa fa-chevron-down retention-chevron" aria-hidden="true"></i></div>
    </summary>
    <div class="retention-body">
    <p class="retention-intro">Manage retention dates, holds and disposal previews. No automatic disposal is scheduled.</p>
    <details class="retention-policy-details">
    <summary>View retention policy and safeguards</summary>
    <div class="retention-policy">
        <span><strong>{{ config('retention.requirements_years') }} year(s)</strong> Regular requirements after completion and clearance</span>
        <span><strong>{{ config('retention.completion_records_years') }} year(s)</strong> COCs &amp; evaluations after completion and clearance</span>
        <span><strong>{{ config('retention.profiles_years') }} year(s)</strong> Profiles after completion and clearance</span>
        <span><strong>{{ config('retention.moa_years') }} year(s)</strong> MOAs after expiration, with no unfinished placements</span>
        <span><strong>{{ config('audit.retention_days') }} days</strong> Audit logs under the existing pruning policy</span>
    </div>
    <p class="retention-note">All student periods use the later of confirmed completion and clearance finalization. Holds postpone disposal. Inactive/incomplete students are reviewed, never automatically deleted. Existing backups are not altered by disposal and expire under the hosting provider's backup policy.</p>
    </details>
    @if(! $retentionWorkspace['ready'])
        <div class="alert alert-warning">The retention workspace is waiting for its database migration. No retention disposal is available yet.</div>
    @else
        @if($errors->any())
            <div class="alert alert-danger" role="alert">{{ $errors->first() }}</div>
        @endif
        <form class="retention-filters" action="{{ url('/maintenance') }}#data-retention" method="get">
            <label>Records<select name="retention_kind"><option value="student" @selected($retentionWorkspace['kind'] === 'student')>Student records</option><option value="moa" @selected($retentionWorkspace['kind'] === 'moa')>MOA records</option></select></label>
            <label>Search<input type="search" name="retention_search" value="{{ $retentionWorkspace['search'] }}" placeholder="Search by name" maxlength="100"></label>
            <button type="submit" class="retention-button">Search records</button>
        </form>
        <div class="retention-table-wrap retention-records">
            <table class="retention-table retention-subject-table">
            <thead><tr><th scope="col">Record</th><th scope="col">Status / dates</th><th scope="col">Preview scope</th><th scope="col">Actions</th></tr></thead>
            <tbody>
            @forelse($retentionWorkspace['subjects'] as $subject)
                @php
                    $state = $retentionWorkspace['states']->get($subject->id);
                    $isStudent = $retentionWorkspace['kind'] === 'student';
                    $lastActive = $isStudent ? ($subject->last_activity_at ?: $subject->created_at) : null;
                    $needsReview = $isStudent && ! $state?->completed_on && $lastActive && \Carbon\CarbonImmutable::parse($lastActive)->lte(now()->subDays(max(1, (int) config('retention.inactive_review_days'))));
                @endphp
                <tr>
                    <td><strong class="retention-record-name">{{ $subject->label }}</strong><span class="retention-record-meta">#{{ $subject->id }} @if($isStudent)&bull; {{ $subject->course }}@else &bull; MOA record @endif</span></td>
                    <td>
                        @if($state?->hold_until && $state->hold_until >= now()->toDateString())<span class="retention-tag">On hold until {{ $state->hold_until }}</span>
                        @elseif($needsReview)<span class="retention-tag">Inactive: review only</span>
                        @elseif($isStudent && (! $state?->completed_on || ! $state?->clearance_finalized_on))<span class="retention-tag">Dates unconfirmed</span>
                        @else<span class="retention-tag neutral">{{ $isStudent ? 'Dates recorded' : 'Review expiry' }}</span>@endif
                        <span class="retention-record-meta">{{ $isStudent ? 'Clearance: '.($state?->clearance_finalized_on ?: 'Not established') : 'Expires: '.($subject->valid_until ?: 'Not established') }}</span>
                    </td>
                    <td>
                        <select class="retention-category" aria-label="Preview scope for {{ $subject->label }}">
                            @if($isStudent)
                                <option value="requirements">Requirement PDFs</option>
                                <option value="completion_records">COCs &amp; evaluations</option>
                                <option value="student_profile">Student profile &amp; OJT info</option>
                            @else<option value="moa">MOA &amp; renewal history</option>@endif
                        </select>
                    </td>
                    <td><div class="retention-row-actions">
                        <button type="button" class="retention-preview retention-button secondary" data-subject-id="{{ $subject->id }}" aria-label="Preview retention for {{ $subject->label }}"><i class="fa fa-eye" aria-hidden="true"></i> Preview</button>
                        <button type="button" class="retention-edit retention-button secondary" data-bs-toggle="modal" data-bs-target="#retentionDetailsModal"
                            data-subject-type="{{ $retentionWorkspace['kind'] }}" data-subject-id="{{ $subject->id }}" data-label="{{ $subject->label }}"
                            data-completed-on="{{ $state?->completed_on }}" data-clearance-on="{{ $state?->clearance_finalized_on }}"
                            data-hold-until="{{ $state?->hold_until && $state->hold_until >= now()->toDateString() ? $state->hold_until : '' }}" data-hold-reason="{{ $state?->hold_reason }}"
                            aria-label="Manage retention dates and hold for {{ $subject->label }}"><i class="fa fa-sliders-h" aria-hidden="true"></i> Manage</button>
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="4">No matching records found.</td></tr>
            @endforelse
            </tbody></table>
        </div>
        <div class="retention-pagination">{{ $retentionWorkspace['subjects']->links('pagination::bootstrap-5') }}</div>
        <details class="retention-history" @if(request()->has('disposal_page')) open @endif>
        <summary class="retention-history-title">Disposal history <span class="retention-history-count">{{ $retentionWorkspace['disposals']->total() }}</span></summary>
        <p class="retention-note">Minimal logs retain category, numeric record reference, actor ID, counts, status and timestamps—not names or document contents. Completed file-cleanup jobs discard their file paths. Shared files are preserved; failed cleanup remains retryable.</p>
        <div class="retention-table-wrap">
            <table class="retention-table"><thead><tr><th>Reference</th><th>Category</th><th>Records removed</th><th>Executed by</th><th>Status</th><th>Date</th><th>Action</th></tr></thead><tbody>
                @forelse($retentionWorkspace['disposals'] as $disposal)
                    <tr><td>#{{ $disposal->id }} / subject #{{ $disposal->subject_id }}</td><td>{{ \App\Services\DataRetentionService::CATEGORIES[$disposal->category] ?? $disposal->category }}</td><td>{{ $disposal->records_removed }}</td><td>User #{{ $disposal->executed_by }}</td><td>{{ str_replace('_', ' ', $disposal->status) }}</td><td>{{ $disposal->created_at }}</td><td>
                        @if($disposal->status !== 'completed' && config('retention.disposal_enabled'))
                            <form action="{{ route('retention.retry', $disposal->id) }}" method="post">@csrf<button type="submit" class="retention-button secondary">Retry file cleanup</button></form>
                        @else &mdash; @endif
                    </td></tr>
                @empty <tr><td colspan="7">No disposals executed.</td></tr> @endforelse
            </tbody></table>
        </div>
        <div class="retention-pagination">{{ $retentionWorkspace['disposals']->links('pagination::bootstrap-5') }}</div>
        </details>
    @endif
    </div>
</details>
@if($retentionWorkspace['ready'])
<div class="modal fade" id="retentionDetailsModal" tabindex="-1" aria-labelledby="retentionDetailsTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
        <form action="{{ route('retention.update') }}" method="post" id="retention-details-form">
            @csrf
            <div class="modal-header"><div><h5 class="modal-title" id="retentionDetailsTitle"><i class="fa fa-shield-alt" aria-hidden="true"></i> Retention details</h5><p id="retention-details-label" class="retention-modal-label"></p></div><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
            <div class="modal-body retention-state-form">
                <input type="hidden" name="subject_type"><input type="hidden" name="subject_id">
                <label data-retention-student>Confirmed OJT completion<input type="date" name="completed_on" max="{{ now()->toDateString() }}"></label>
                <label data-retention-student>Clearance finalized<input type="date" name="clearance_finalized_on" max="{{ now()->toDateString() }}"></label>
                <label>Hold until (optional)<input type="date" name="hold_until" min="{{ now()->toDateString() }}"></label>
                <label>Hold reason<input name="hold_reason" maxlength="500" placeholder="Pending review or unresolved concern"></label>
                <label class="retention-check" data-retention-student><input type="checkbox" name="completion_confirmed" value="1" required> I verified these dates against actual completion and clearance. Leaving both dates blank keeps the record unconfirmed.</label>
                <p class="retention-note">COC approval does not set these dates automatically. Clear the hold date to release a hold. Saving does not delete records.</p>
            </div>
            <div class="modal-footer"><button type="button" class="btn-modal-close" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn-modal-submit">Save retention details</button></div>
        </form>
    </div></div>
</div>
@endif
<div id="retention-config" data-preview-url="{{ route('retention.preview') }}" data-dispose-url="{{ route('retention.dispose') }}" hidden></div>
