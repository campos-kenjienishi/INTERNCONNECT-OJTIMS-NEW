(function () {
    'use strict';

    function route(template, replacements) {
        return Object.keys(replacements).reduce(function (value, key) {
            return value.replace(key, encodeURIComponent(replacements[key]));
        }, template);
    }

    function formatDate(value) {
        if (!value) return 'Not recorded';

        const parts = String(value).slice(0, 10).split('-');
        const date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));

        return date.toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit'
        });
    }

    function formatDateTime(value) {
        if (!value) return 'Not recorded';

        return new Date(value).toLocaleString('en-US', {
            year: 'numeric',
            month: 'short',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function textElement(tag, className, text) {
        const element = document.createElement(tag);
        element.className = className;
        element.textContent = text;
        return element;
    }

    function documentLink(config, renewalId, version, action, label, icon) {
        const link = document.createElement('a');
        const template = action === 'preview' ? config.previewRoute : config.downloadRoute;
        link.className = 'moa-history-action';
        link.href = route(template, {
            '__RENEWAL__': renewalId,
            '__VERSION__': version
        });
        link.target = '_blank';
        link.rel = 'noopener';

        const iconElement = document.createElement('i');
        iconElement.className = icon;
        link.appendChild(iconElement);
        link.appendChild(document.createTextNode(label));

        return link;
    }

    function renderHistory(payload) {
        const config = window.moaRenewalHistoryConfig;
        const summary = document.getElementById('moaHistorySummary');
        const timeline = document.getElementById('moaHistoryTimeline');

        summary.innerHTML = '';
        timeline.innerHTML = '';

        [
            ['Renewals', String(payload.company.renewal_count)],
            ['Current notarization', formatDate(payload.company.date_notarized)],
            ['Current expiration', formatDate(payload.company.valid_until)]
        ].forEach(function (item) {
            const wrapper = document.createElement('div');
            wrapper.className = 'moa-history-summary-item';
            wrapper.appendChild(textElement('span', '', item[0]));
            wrapper.appendChild(textElement('strong', '', item[1]));
            summary.appendChild(wrapper);
        });

        if (!payload.renewals.length) {
            timeline.appendChild(textElement('div', 'moa-history-state', 'This MOA has no recorded renewals yet.'));
            return;
        }

        payload.renewals.forEach(function (renewal, index) {
            const entry = document.createElement('article');
            entry.className = 'moa-history-entry';

            const head = document.createElement('div');
            head.className = 'moa-history-entry-head';
            head.appendChild(textElement('div', 'moa-history-entry-title', 'Renewal #' + (payload.renewals.length - index)));
            head.appendChild(textElement('div', 'moa-history-entry-meta', formatDateTime(renewal.renewed_at) + ' by ' + renewal.renewed_by));
            entry.appendChild(head);

            const periods = document.createElement('div');
            periods.className = 'moa-history-periods';

            const previous = document.createElement('div');
            previous.className = 'moa-history-period';
            previous.appendChild(textElement('span', '', 'Previous validity'));
            previous.appendChild(textElement('strong', '', formatDate(renewal.previous_valid_until)));
            periods.appendChild(previous);

            const renewed = document.createElement('div');
            renewed.className = 'moa-history-period';
            renewed.appendChild(textElement('span', '', 'Renewed validity'));
            renewed.appendChild(textElement('strong', '', formatDate(renewal.new_valid_until)));
            periods.appendChild(renewed);
            entry.appendChild(periods);

            if (renewal.notes) {
                entry.appendChild(textElement('p', 'moa-history-notes', renewal.notes));
            }

            const actions = document.createElement('div');
            actions.className = 'moa-history-actions';
            actions.appendChild(documentLink(config, renewal.id, 'previous', 'preview', 'Previous PDF', 'fa fa-eye'));
            actions.appendChild(documentLink(config, renewal.id, 'new', 'preview', 'Renewed PDF', 'fa fa-eye'));
            actions.appendChild(documentLink(config, renewal.id, 'new', 'download', 'Download renewed', 'fa fa-download'));
            entry.appendChild(actions);
            timeline.appendChild(entry);
        });
    }

    window.openMoaRenewalHistory = function (companyId, companyName) {
        const config = window.moaRenewalHistoryConfig;
        const modalElement = document.getElementById('moaRenewalHistoryModal');
        const title = document.getElementById('moaHistoryCompanyName');
        const summary = document.getElementById('moaHistorySummary');
        const timeline = document.getElementById('moaHistoryTimeline');

        if (!config || !modalElement || !window.bootstrap) return;

        title.textContent = companyName || 'MOA Renewal History';
        summary.innerHTML = '';
        timeline.innerHTML = '<div class="moa-history-state"><i class="fa fa-spinner fa-spin"></i> Loading renewal history...</div>';
        window.bootstrap.Modal.getOrCreateInstance(modalElement).show();

        fetch(route(config.historyRoute, { '__COMPANY__': companyId }), {
            headers: { 'Accept': 'application/json' },
            credentials: 'same-origin'
        })
            .then(function (response) {
                if (!response.ok) throw new Error('History request failed');
                return response.json();
            })
            .then(renderHistory)
            .catch(function () {
                timeline.innerHTML = '';
                timeline.appendChild(textElement('div', 'moa-history-state', 'The renewal history could not be loaded. Please try again.'));
            });
    };
})();
