(() => {
    'use strict';

    const form = document.getElementById('targetedAnnouncementForm');
    if (!form) return;
    const root = document.getElementById('announcementTargeting');
    const audience = document.getElementById('announcementAudience');
    const programMode = document.getElementById('announcementProgramMode');
    const programs = document.getElementById('announcementPrograms');
    const professorFields = document.getElementById('announcementProfessorFields');
    const professorMode = document.getElementById('announcementProfessorMode');
    const picker = document.getElementById('announcementProfessorPicker');
    const list = document.getElementById('announcementProfessorList');
    const search = document.getElementById('announcementProfessorSearch');
    const count = document.getElementById('announcementProfessorCount');
    const error = document.getElementById('announcementAudienceError');
    const submit = form.querySelector('button[type="submit"]');
    let controller;
    let loading = false;
    let busy = false;
    let publishing = false;
    let restoredIds = new Set(JSON.parse(list.dataset.selected || '[]').map(String));

    function showError(message) {
        error.textContent = message;
        error.hidden = !message;
    }

    function filters() {
        const data = new FormData(form);
        return [...data.entries()].filter(([name]) => !['_token', 'confirmation', 'publication_key'].includes(name));
    }

    function updateCount() {
        const total = list.querySelectorAll('input').length;
        const selected = list.querySelectorAll('input:checked').length;
        count.textContent = professorMode.value === 'all'
            ? `${total} eligible OJT professor(s) will receive this announcement.`
            : `${selected} of ${total} eligible OJT professor(s) selected.`;
    }

    async function post(url, signal) {
        const response = await fetch(url, {
            method: 'POST', credentials: 'same-origin', signal,
            headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            body: new FormData(form)
        });
        const data = await response.json().catch(() => null);
        if (!response.ok || !data) {
            const messages = data?.errors && Object.values(data.errors).flat();
            throw new Error(messages?.[0] || data?.message || 'Unable to check recipients. Refresh the page and try again.');
        }
        return data;
    }

    async function refreshProfessors() {
        controller?.abort();
        professorFields.hidden = audience.value === 'students';
        picker.hidden = professorMode.value === 'all';
        showError('');
        if (audience.value === 'students') {
            loading = false;
            list.replaceChildren();
            return;
        }
        const request = new AbortController();
        controller = request;
        loading = true;
        const selected = new Set([...restoredIds, ...[...list.querySelectorAll('input:checked')].map(input => input.value)]);
        count.textContent = 'Checking eligible OJT professors…';
        try {
            const data = await post(root.dataset.professorsUrl, request.signal);
            if (request !== controller) return;
            restoredIds = new Set();
            list.replaceChildren();
            for (const professor of data.professors) {
                const label = document.createElement('label');
                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox'; checkbox.name = 'professor_ids[]'; checkbox.value = professor.id;
                checkbox.checked = selected.has(String(professor.id));
                const text = document.createElement('span'); text.textContent = professor.name;
                const details = document.createElement('small'); details.textContent = professor.programs.join(', ');
                text.appendChild(details); label.append(checkbox, text);
                list.appendChild(label);
            }
            if (!data.professors.length) {
                const message = document.createElement('p');
                message.className = 'announcement-audience-hint';
                message.textContent = 'No eligible professors match. Adjust the filters or add an OJT professor designation below.';
                list.appendChild(message);
            }
            search.value = '';
            updateCount();
        } catch (exception) {
            if (exception.name !== 'AbortError') {
                list.replaceChildren(); count.textContent = ''; showError(exception.message);
            }
        } finally {
            if (request === controller) loading = false;
        }
    }

    function togglePrograms() {
        const selected = programMode.value === 'selected';
        programs.hidden = !selected;
        programs.querySelectorAll('input').forEach(input => { input.disabled = !selected; });
    }

    programMode.addEventListener('change', () => { togglePrograms(); refreshProfessors(); });
    [audience, professorMode, document.getElementById('announcementYear'), document.getElementById('announcementSemester')]
        .forEach(select => select.addEventListener('change', refreshProfessors));
    programs.addEventListener('change', refreshProfessors);
    list.addEventListener('change', updateCount);
    search.addEventListener('input', () => {
        const term = search.value.trim().toLowerCase();
        list.querySelectorAll('label').forEach(label => { label.hidden = !label.textContent.toLowerCase().includes(term); });
    });

    function previewContent(data) {
        const panel = document.createElement('div'); panel.className = 'announcement-preview';
        const add = message => { const paragraph = document.createElement('p'); paragraph.textContent = message; panel.appendChild(paragraph); };
        add(`Title: ${form.elements.title.value}`);
        add(`Programs: ${programMode.value === 'all' ? 'All Programs' : [...programs.querySelectorAll('input:checked')].map(input => input.value).join(', ')}`);
        add(`Period: ${form.elements.school_year.value || 'All School Years'} · ${form.elements.semester.value || 'All Semesters'}`);
        add(`${data.students.length} student(s) and ${data.professors.length} OJT professor(s).`);
        for (const [heading, recipients] of [['Students', data.students], ['OJT Professors', data.professors]]) {
            if (!recipients.length) continue;
            const details = document.createElement('details');
            const summary = document.createElement('summary'); summary.textContent = `${heading} (${recipients.length})`;
            const names = document.createElement('ul');
            recipients.forEach(recipient => { const item = document.createElement('li'); item.textContent = recipient.name || recipient.full_name; names.appendChild(item); });
            details.append(summary, names); panel.appendChild(details);
        }
        if (data.email) {
            add(`Email notifications: ${data.email.valid} queued after publication; ${data.email.skipped} skipped because of missing or invalid email addresses.`);
            if (data.email.capture) add('Test capture mode: no real recipient emails will be sent.');
        } else {
            add('In-app announcement only. No email notifications will be sent.');
        }
        return panel.outerHTML;
    }

    form.addEventListener('submit', async event => {
        event.preventDefault();
        if (busy) return;
        showError('');
        if (loading) { showError('Please wait for the professor list to finish loading.'); return; }
        if (programMode.value === 'selected' && !programs.querySelector('input:checked')) {
            showError('Choose at least one program or switch to All Programs.'); return;
        }
        if (!window.Swal) { showError('The confirmation dialog could not load. Refresh the page and try again.'); return; }
        busy = true; submit.disabled = true;
        const snapshot = JSON.stringify(filters());
        try {
            const data = await post(root.dataset.previewUrl);
            if (snapshot !== JSON.stringify(filters())) throw new Error('The form changed. Preview the announcement again.');
            const result = await Swal.fire({
                title: 'Publish announcement?', html: previewContent(data), icon: 'question',
                showCancelButton: true, confirmButtonText: 'Publish Announcement', cancelButtonText: 'Keep Editing',
                confirmButtonColor: '#b91c1c', focusCancel: true, width: 640
            });
            if (!result.isConfirmed) return;
            if (snapshot !== JSON.stringify(filters())) throw new Error('The form changed. Preview the announcement again.');
            document.getElementById('announcementPublicationKey').value = data.publication_key;
            document.getElementById('announcementConfirmation').value = data.confirmation;
            publishing = true;
            submit.textContent = 'Publishing…';
            HTMLFormElement.prototype.submit.call(form);
        } catch (exception) {
            showError(exception.message);
        } finally {
            if (!publishing) { busy = false; submit.disabled = false; }
        }
    });
    window.addEventListener('pageshow', event => {
        if (event.persisted) { busy = false; publishing = false; submit.disabled = false; submit.textContent = 'Preview and Publish'; }
    });
    togglePrograms();
    if (!submit.disabled) refreshProfessors();

    document.querySelectorAll('.retry-announcement-email-form').forEach(retryForm => {
        let retrying = false;
        retryForm.addEventListener('submit', async event => {
            event.preventDefault();
            if (retrying || !window.Swal) return;
            retrying = true;
            const button = retryForm.querySelector('button');
            button.disabled = true;
            const result = await Swal.fire({
                title: 'Retry failed emails?', icon: 'question',
                text: 'Only failed notifications with fewer than three attempts will be queued again. Sent, captured, and uncertain deliveries will not be resent.',
                showCancelButton: true, confirmButtonText: 'Queue Failed Emails', focusCancel: true,
                confirmButtonColor: '#b91c1c'
            });
            if (result.isConfirmed) HTMLFormElement.prototype.submit.call(retryForm);
            else { retrying = false; button.disabled = false; }
        });
    });
})();
