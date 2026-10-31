<div class="announcement-targeting" id="announcementTargeting"
     data-professors-url="{{ route('announcements.professors') }}"
     data-preview-url="{{ route('announcements.preview') }}">
    @unless($announcementOptions['ready'])
        <p class="announcement-audience-error" role="alert">Targeted announcements need the new database migration before publishing.</p>
    @endunless
    <div class="field-group">
        <label class="field-label" for="announcementAudience"><i class="fa fa-users"></i> Audience</label>
        <select class="field-input" name="audience" id="announcementAudience">
            <option value="students" @selected(old('audience', 'students') === 'students')>Students</option>
            <option value="professors" @selected(old('audience') === 'professors')>OJT Professors</option>
            <option value="both" @selected(old('audience') === 'both')>Students &amp; OJT Professors</option>
        </select>
    </div>
    <div class="announcement-period-grid">
        <div class="field-group">
            <label class="field-label" for="announcementYear">School Year</label>
            <select class="field-input" name="school_year" id="announcementYear">
                <option value="">All School Years</option>
                @foreach($announcementOptions['school_years'] as $year)
                    <option value="{{ $year }}" @selected(old('school_year', $announcementOptions['school_years']->first()) === $year)>{{ $year }}</option>
                @endforeach
            </select>
        </div>
        <div class="field-group">
            <label class="field-label" for="announcementSemester">Semester</label>
            <select class="field-input" name="semester" id="announcementSemester">
                <option value="">All Semesters</option>
                @foreach($announcementOptions['semesters'] as $semester)
                    <option value="{{ $semester }}" @selected(old('semester') === $semester)>{{ $semester }}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="field-group">
        <label class="field-label" for="announcementProgramMode">Programs</label>
        <select class="field-input" id="announcementProgramMode">
            <option value="all" @selected(!old('programs'))>All Programs</option>
            <option value="selected" @selected((bool) old('programs'))>Select programs</option>
        </select>
        <div class="announcement-checklist" id="announcementPrograms" hidden>
            @foreach($announcementOptions['programs'] as $program)
                <label><input type="checkbox" name="programs[]" value="{{ $program }}" @checked(in_array($program, is_array(old('programs')) ? old('programs') : [], true))><span>{{ $program }}</span></label>
            @endforeach
        </div>
    </div>
    <div id="announcementProfessorFields" hidden>
        <div class="field-group">
            <label class="field-label" for="announcementProfessorMode">Professor Recipients</label>
            <select class="field-input" id="announcementProfessorMode" name="professor_mode">
                <option value="selected" @selected(old('professor_mode', 'selected') === 'selected')>Select specific OJT professors</option>
                <option value="all" @selected(old('professor_mode') === 'all')>All eligible OJT professors</option>
            </select>
        </div>
        <div id="announcementProfessorPicker">
            <input type="search" class="field-input" id="announcementProfessorSearch" placeholder="Search eligible OJT professors" aria-label="Search eligible OJT professors">
            <div class="announcement-checklist" id="announcementProfessorList" aria-live="polite" data-selected="{{ json_encode(is_array(old('professor_ids')) ? old('professor_ids') : []) }}"></div>
        </div>
        <p class="announcement-audience-hint" id="announcementProfessorCount" aria-live="polite"></p>
    </div>
    <div class="announcement-email-option">
        <label><input type="checkbox" name="send_email" value="1" @checked(old('send_email')) @disabled(!$announcementOptions['email']['available'])><span><strong>Send email notification</strong><small>Send individually to the same saved recipients. Preview shows how many have valid email addresses.</small></span></label>
        @if(!$announcementOptions['email']['available'])
            <p class="announcement-audience-hint">Email notifications are disabled until the email outbox migration and settings are configured.</p>
        @elseif($announcementOptions['email']['capture'])
            <p class="announcement-audience-hint">Test capture mode: emails are queued and captured, not sent to real recipients.</p>
        @else
            <p class="announcement-audience-hint">Emails are sent in the background after publication. Email failure does not remove the in-app announcement.</p>
        @endif
    </div>
    <p class="announcement-audience-hint">Recipients are saved at publication. Later FLSS syncs or assignment changes will not expand this audience. Semester filters require a matching non-archived class.</p>
    <p class="announcement-audience-error" id="announcementAudienceError" role="alert" hidden></p>
    <input type="hidden" name="publication_key" id="announcementPublicationKey">
    <input type="hidden" name="confirmation" id="announcementConfirmation">
</div>
