<details class="panel-card announcement-designations">
    <summary><i class="fa fa-user-check"></i> OJT Professor Designations <span>For professors without an OJT class or student assignment</span></summary>
    <div class="panel-card-body">
        <p class="announcement-audience-hint">Designations only control eligibility for coordinator announcements. They do not grant student-record access or change FLSS data. Removing a designation does not revoke announcements already received.</p>
        @if($announcementOptions['ready'])
            <form method="POST" action="{{ route('announcements.designate') }}" class="announcement-designation-form">
                @csrf
                <label>Professor<select class="field-input" name="user_id" required><option value="">Select professor</option>@foreach($announcementOptions['faculty'] as $faculty)<option value="{{ $faculty->id }}">{{ $faculty->full_name }}</option>@endforeach</select></label>
                <label>OJT Program<select class="field-input" name="course" required><option value="">Select program</option>@foreach($announcementOptions['programs'] as $program)<option value="{{ $program }}">{{ $program }}</option>@endforeach</select></label>
                <label>School Year<select class="field-input" name="school_year" required>@foreach($announcementOptions['school_years'] as $year)<option value="{{ $year }}">{{ $year }}</option>@endforeach</select></label>
                <label>Semester<select class="field-input" name="semester"><option value="">All Semesters</option>@foreach($announcementOptions['semesters'] as $semester)<option value="{{ $semester }}">{{ $semester }}</option>@endforeach</select></label>
                <button type="submit" class="btn-submit"><i class="fa fa-plus"></i> Designate</button>
            </form>
            <div class="announcement-designation-list">
                @forelse($announcementOptions['designations'] as $designation)
                    <div class="announcement-designation-row">
                        <div><strong>{{ $designation->full_name }}</strong><span>{{ $designation->course }} · {{ $designation->school_year }} · {{ $designation->semester ?: 'All Semesters' }}</span></div>
                        <form method="POST" action="{{ route('announcements.designations.destroy', $designation->id) }}" class="delete-announcement-form" data-announcement-title="this OJT professor designation"><input type="hidden" name="_method" value="DELETE">@csrf<button type="submit" class="btn-delete-announcement" aria-label="Remove designation for {{ $designation->full_name }}">Remove</button></form>
                    </div>
                @empty
                    <p class="announcement-audience-hint">No manual designations. Professors with matching OJT classes or students qualify automatically.</p>
                @endforelse
            </div>
        @endif
    </div>
</details>
