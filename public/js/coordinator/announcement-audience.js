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
        const element = (tag, className, text) => {
            const node = document.createElement(tag);
            node.className = className;
            if (text !== undefined) node.textContent = text;
            return node;
        };
        const icon = className => {
            const node = element('i', `fa ${className}`);
            node.setAttribute('aria-hidden', 'true');
            return node;
        };
        const panel = element('div', 'announcement-preview');
        panel.appendChild(element('p', 'announcement-preview-intro', 'Check the message, audience, and delivery options before publishing.'));

        const message = element('section', 'announcement-preview-message');
        message.append(element('span', 'announcement-preview-label', 'Announcement'), element('h3', '', form.elements.title.value));
        message.appendChild(element('p', 'announcement-preview-message-body', form.elements.content.value));
        panel.appendChild(message);

        const scope = element('dl', 'announcement-preview-scope');
        const programField = element('div', 'announcement-preview-field');
        const programLabel = element('dt', 'announcement-preview-label', 'Programs');
        programLabel.prepend(icon('fa-graduation-cap'));
        const chips = element('dd', 'announcement-preview-chips');
        const selectedPrograms = programMode.value === 'all' ? ['All Programs']
            : [...programs.querySelectorAll('input:checked')].map(input => input.value);
        selectedPrograms.forEach(program => chips.appendChild(element('span', 'announcement-preview-chip', program)));
        programField.append(programLabel, chips);
        const periodField = element('div', 'announcement-preview-field');
        const periodLabel = element('dt', 'announcement-preview-label', 'Academic period');
        periodLabel.prepend(icon('fa-calendar-alt'));
        const period = element('dd', 'announcement-preview-period');
        period.append(element('strong', '', form.elements.school_year.value || 'All School Years'),
            element('span', '', form.elements.semester.value || 'All Semesters'));
        periodField.append(periodLabel, period);
        scope.append(programField, periodField);
        panel.appendChild(scope);

        const recipientHeader = element('div', 'announcement-preview-recipient-heading');
        recipientHeader.append(element('h3', '', 'Recipients'), element('span', 'announcement-preview-total', `${data.students.length + data.professors.length} total`));
        panel.appendChild(recipientHeader);
        const metrics = element('div', 'announcement-preview-metrics');
        const groups = [['Students', data.students, 'fa-user-graduate'], ['OJT Professors', data.professors, 'fa-chalkboard-teacher']];
        for (const [heading, recipients, iconClass] of groups) {
            const metric = element('div', `announcement-preview-metric${recipients.length ? '' : ' is-empty'}`);
            const badge = element('span', 'announcement-preview-metric-icon');
            badge.appendChild(icon(iconClass));
            const value = element('div', 'announcement-preview-metric-value');
            value.append(element('strong', '', String(recipients.length)), element('span', '', heading));
            metric.append(badge, value);
            metrics.appendChild(metric);
        }
        panel.appendChild(metrics);

        const lists = element('div', 'announcement-preview-recipient-lists');
        for (const [heading, recipients, iconClass] of groups) {
            if (!recipients.length) continue;
            const details = element('details', 'announcement-preview-recipient-group');
            const summary = element('summary', '');
            summary.append(icon(iconClass), element('span', '', `View ${heading.toLowerCase()} (${recipients.length})`));
            const names = element('ul', 'announcement-preview-names');
            recipients.forEach(recipient => names.appendChild(element('li', '', recipient.name || recipient.full_name)));
            details.append(summary, names);
            lists.appendChild(details);
        }
        panel.appendChild(lists);

        const delivery = element('section', `announcement-preview-delivery${data.email ? ' has-email' : ''}`);
        const deliveryIcon = element('span', 'announcement-preview-delivery-icon');
        deliveryIcon.appendChild(icon(data.email ? 'fa-envelope' : 'fa-desktop'));
        const deliveryText = element('div', 'announcement-preview-delivery-text');
        if (data.email) {
            deliveryText.append(element('strong', '', data.email.capture ? 'Email test capture enabled' : 'Email notifications enabled'),
                element('p', '', `${data.email.valid} email notification(s) will be queued after publication.`));
            if (data.email.skipped) deliveryText.appendChild(element('p', '', `${data.email.skipped} recipient(s) have missing or invalid email addresses and will receive the in-app announcement only.`));
            if (data.email.capture) deliveryText.appendChild(element('span', 'announcement-preview-capture', 'Test mode: no real emails will be sent.'));
        } else {
            deliveryText.append(element('strong', '', 'In-app announcement only'),
                element('p', '', 'No email notifications will be sent.'));
        }
        delivery.append(deliveryIcon, deliveryText);
        panel.appendChild(delivery);
        const privacy = element('p', 'announcement-preview-privacy');
        privacy.append(icon('fa-lock'), element('span', '', 'Only the listed recipients will see this announcement.'));
        panel.appendChild(privacy);
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
                title: 'Publish announcement?', html: previewContent(data),
                showCancelButton: true, confirmButtonText: 'Publish Announcement', cancelButtonText: 'Keep Editing',
                confirmButtonColor: '#b91c1c', focusCancel: true, width: 720,
                showCloseButton: true, closeButtonAriaLabel: 'Close preview and keep editing',
                reverseButtons: true, heightAuto: false,
                customClass: {
                    popup: 'announcement-preview-popup', title: 'announcement-preview-title',
                    htmlContainer: 'announcement-preview-content', actions: 'announcement-preview-actions',
                    confirmButton: 'announcement-preview-confirm', cancelButton: 'announcement-preview-cancel',
                    closeButton: 'announcement-preview-close'
                }
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
