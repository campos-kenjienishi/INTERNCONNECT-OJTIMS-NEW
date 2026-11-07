document.addEventListener('DOMContentLoaded', function () {
    const config = document.getElementById('retention-config');
    if (!config || typeof Swal === 'undefined') return;

    const panel = document.getElementById('data-retention');
    if (panel) {
        try {
            if (sessionStorage.getItem('internconnect.retention.open') === 'true' || window.location.hash === '#data-retention') panel.open = true;
            panel.addEventListener('toggle', function () { sessionStorage.setItem('internconnect.retention.open', String(panel.open)); });
        } catch (error) {}
    }

    document.querySelectorAll('.retention-edit').forEach(function (button) {
        button.addEventListener('click', function () {
            const form = document.getElementById('retention-details-form');
            if (!form) return;
            form.reset();
            const student = button.dataset.subjectType === 'student';
            form.elements.subject_type.value = button.dataset.subjectType;
            form.elements.subject_id.value = button.dataset.subjectId;
            form.elements.completed_on.value = button.dataset.completedOn || '';
            form.elements.clearance_finalized_on.value = button.dataset.clearanceOn || '';
            form.elements.hold_until.value = button.dataset.holdUntil || '';
            form.elements.hold_reason.value = button.dataset.holdReason || '';
            form.querySelectorAll('[data-retention-student]').forEach(function (group) {
                group.hidden = !student;
                group.querySelectorAll('input').forEach(function (input) { input.disabled = !student; });
            });
            document.getElementById('retention-details-label').textContent = button.dataset.label;
        });
    });

    async function post(url, body) {
        const response = await fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify(body)
        });
        let data;
        try { data = await response.json(); }
        catch (error) { throw new Error('The server did not return a valid response. Refresh the page and check the disposal history before retrying.'); }
        if (!response.ok) throw new Error(Object.values(data.errors || {}).flat()[0] || data.message || 'The request could not be completed. No successful disposal was confirmed.');
        return data;
    }

    function previewContent(data) {
        function node(tag, className, text) {
            const element = document.createElement(tag);
            element.className = className;
            if (text !== undefined) element.textContent = text;
            return element;
        }
        const content = node('div', 'retention-preview-content');
        const subject = node('div', 'retention-preview-subject');
        subject.appendChild(node('h3', 'retention-preview-name', data.label));
        subject.appendChild(node('span', 'retention-preview-status ' + (data.eligible ? 'eligible' : 'blocked'), data.eligible ? 'Retention period reached' : 'Disposal blocked'));
        subject.appendChild(node('p', 'retention-preview-reason', data.reason));
        content.appendChild(subject);

        const metrics = node('div', 'retention-preview-metrics');
        [['Retention due', data.due_on || 'Not established'], ['Database records', data.record_count], ['Possible file locations', data.file_count]].forEach(function ([label, value]) {
            const card = node('div', 'retention-preview-metric');
            card.appendChild(node('span', '', label));
            card.appendChild(node('strong', '', value));
            metrics.appendChild(card);
        });
        content.appendChild(metrics);

        const details = node('details', 'retention-preview-details');
        details.appendChild(node('summary', '', 'View affected records and file locations'));
        const tableLabels = {
            file_requirements: 'Requirement submissions', completion_certificates: 'Completion certificates',
            ojt_evaluations: 'Supervisor evaluations', ojt_evaluation_requests: 'Evaluation invitations',
            moa_renewals: 'MOA renewal history', companies: 'Company MOA records', company_student: 'Placement links',
            users: 'Student accounts', students: 'Student profiles', o_j_t_information: 'OJT information',
            vouchers: 'MOA vouchers', moa_unlock_requests: 'MOA unlock requests'
        };
        const tables = node('ul', 'retention-preview-tables');
        Object.entries(data.tables).forEach(function ([table, count]) {
            tables.appendChild(node('li', '', (tableLabels[table] || table.replaceAll('_', ' ')) + ': ' + count));
        });
        details.appendChild(tables);
        if (data.files.length) {
            const list = node('ul', 'retention-preview-files');
            data.files.forEach(function (file) { list.appendChild(node('li', '', file.disk + ': ' + file.path)); });
            details.appendChild(list);
            details.appendChild(node('p', 'retention-preview-detail-note', 'Up to 100 possible locations are listed. Private and legacy copies may not both exist. Shared files are preserved.'));
        }
        content.appendChild(details);

        const notice = node('div', 'retention-preview-warning');
        notice.appendChild(node('strong', '', 'Important before you continue'));
        notice.appendChild(node('p', '', 'Database deletion is permanent. Existing backups are not erased. Shared files are preserved, and failed file cleanup is recorded for retry.'));
        content.appendChild(notice);
        if (!data.disposal_enabled) content.appendChild(node('p', 'retention-preview-readonly', 'Preview-only mode: permanent disposal is disabled in server configuration.'));

        return content;
    }

    document.querySelectorAll('.retention-preview').forEach(function (button) {
        button.addEventListener('click', async function () {
            if (button.disabled) return;
            button.disabled = true;
            try {
                const category = button.closest('tr')?.querySelector('.retention-category')?.value || button.dataset.category;
                const data = await post(config.dataset.previewUrl, { category, subject_id: Number(button.dataset.subjectId) });
                const canDispose = data.eligible && data.disposal_enabled;
                const result = await Swal.fire({
                    titleText: canDispose ? 'Review permanent disposal' : 'Retention preview',
                    html: previewContent(data), width: 620,
                    customClass: {
                        popup: 'retention-dialog', title: 'retention-dialog-title', htmlContainer: 'retention-dialog-body',
                        actions: 'retention-dialog-actions', input: 'retention-dialog-input', inputLabel: 'retention-dialog-input-label',
                        confirmButton: 'retention-dialog-confirm', cancelButton: 'retention-dialog-cancel', validationMessage: 'retention-dialog-validation'
                    },
                    showCloseButton: !canDispose, focusCancel: canDispose,
                    showCancelButton: canDispose, confirmButtonText: canDispose ? 'Permanently dispose' : 'Close preview',
                    cancelButtonText: 'Keep records', confirmButtonColor: '#781616', reverseButtons: true,
                    input: canDispose ? 'text' : undefined, inputLabel: canDispose ? 'Type DISPOSE to confirm' : undefined,
                    inputValidator: canDispose ? value => value === 'DISPOSE' ? undefined : 'Type DISPOSE exactly to continue.' : undefined,
                    showLoaderOnConfirm: canDispose,
                    allowOutsideClick: () => !Swal.isLoading(),
                    allowEscapeKey: () => !Swal.isLoading(),
                    preConfirm: canDispose ? async value => {
                        try { return await post(config.dataset.disposeUrl, { token: data.token, confirmation: value }); }
                        catch (error) { Swal.showValidationMessage(error.message); return false; }
                    } : undefined
                });
                if (canDispose && result.isConfirmed) {
                    await Swal.fire({ title: result.value.status === 'completed' ? 'Disposal completed' : 'File cleanup pending', text: result.value.message, icon: result.value.status === 'completed' ? 'success' : 'warning', confirmButtonColor: '#781616' });
                    window.location.reload();
                }
            } catch (error) {
                await Swal.fire({ title: 'Unable to complete request', text: error.message, icon: 'error', confirmButtonColor: '#781616' });
            } finally {
                button.disabled = false;
            }
        });
    });
});
