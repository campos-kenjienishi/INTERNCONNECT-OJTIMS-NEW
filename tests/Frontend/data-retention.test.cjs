const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('public/js/coordinator/data-retention.js', 'utf8');

test('dialog title and action colors override shared alert styles without affecting other dialogs', () => {
    const css = fs.readFileSync('public/css/coordinator/data-retention.css', 'utf8');
    assert.match(css, /\.swal2-popup\.retention-dialog \.swal2-title\.retention-dialog-title\s*\{[^}]*color:\s*#fff\s*!important/);
    assert.match(css, /\.swal2-popup\.retention-dialog \.swal2-confirm\.retention-dialog-confirm\s*\{[^}]*background:\s*#781616\s*!important;[^}]*color:\s*#fff\s*!important/);
    assert.match(css, /\.swal2-popup\.retention-dialog \.swal2-cancel\.retention-dialog-cancel\s*\{[^}]*background:\s*#fff\s*!important;[^}]*color:\s*#40506a\s*!important/);
    assert.match(css, /\.dark-mode \.swal2-popup\.retention-dialog \.swal2-cancel\.retention-dialog-cancel\s*\{[^}]*color:\s*#f1f5f9\s*!important/);
});

function environment(options = {}) {
    const requests = [];
    const alerts = [];
    const validation = [];
    let reloads = 0;
    let handler;
    let editHandler;
    const fields = Object.fromEntries(['subject_type', 'subject_id', 'completed_on', 'clearance_finalized_on', 'hold_until', 'hold_reason', 'completion_confirmed'].map(name => [name, { value: '', disabled: false, checked: false }]));
    const groups = ['completed_on', 'clearance_finalized_on', 'completion_confirmed'].map(name => ({ hidden: false, querySelectorAll: () => [fields[name]] }));
    const form = {
        elements: fields,
        reset() { Object.values(fields).forEach(field => { field.value = ''; field.checked = false; }); },
        querySelectorAll() { return groups; }
    };
    const modalLabel = { textContent: '' };
    const editButton = {
        dataset: { subjectType: options.editType || 'student', subjectId: '2', label: 'Student One', completedOn: '2023-01-01', clearanceOn: '2023-02-01', holdUntil: '2027-01-01', holdReason: 'Pending review' },
        addEventListener(name, callback) { editHandler = callback; }
    };
    const button = {
        disabled: false, dataset: { category: 'requirements', subjectId: '2' },
        closest() { return { querySelector: () => ({ value: options.category || 'requirements' }) }; },
        addEventListener(name, callback) { handler = callback; }
    };
    const data = {
        label: options.label || 'Student One', eligible: options.eligible ?? true,
        reason: 'Retention period ended.', due_on: '2026-11-07', record_count: 1, file_count: 1,
        tables: options.tables || { file_requirements: 1 }, files: options.files || [{ disk: 'secure_uploads', path: 'requirements/test.pdf' }],
        disposal_enabled: options.enabled ?? false, token: 'bound-preview-token'
    };
    const context = {
        document: {
            addEventListener(name, callback) { callback(); },
            getElementById(id) {
                if (id === 'retention-config') return { dataset: { previewUrl: '/preview', disposeUrl: '/dispose' } };
                if (id === 'retention-details-form') return form;
                if (id === 'retention-details-label') return modalLabel;
                return null;
            },
            querySelector() { return { content: 'csrf-token' }; },
            querySelectorAll(selector) { return selector === '.retention-preview' ? [button] : selector === '.retention-edit' ? [editButton] : []; },
            createElement(tag) {
                return { tag, children: [], textContent: '', appendChild(child) { this.children.push(child); },
                    set innerHTML(value) { throw new Error('Unsafe HTML assignment'); } };
            }
        },
        fetch: async (url, request) => {
            requests.push({ url, request });
            const failed = url === '/dispose' && options.fail;
            return { ok: !failed, json: async () => url === '/preview' ? data : failed
                ? { errors: { retention: ['Preview is stale.'] } }
                : { status: 'completed', message: 'Disposal completed.' } };
        },
        Swal: {
            async fire(settings) {
                alerts.push(settings);
                if (options.confirm && settings.preConfirm) {
                    const value = await settings.preConfirm('DISPOSE');
                    return { isConfirmed: Boolean(value), value };
                }
                return { isConfirmed: false };
            },
            isLoading: () => false,
            showValidationMessage(message) { validation.push(message); }
        },
        window: { location: { reload() { reloads++; } } }
    };
    vm.runInNewContext(source, context);
    return { click: () => handler(), edit: () => editHandler(), fields, groups, modalLabel, editButton, button, requests, alerts, validation, reloads: () => reloads };
}

test('preview-only mode cannot submit disposal even for eligible records', async () => {
    const setup = environment({ confirm: true });
    await setup.click();
    assert.equal(setup.requests.length, 1);
    assert.equal(setup.alerts[0].preConfirm, undefined);
    assert.equal(setup.reloads(), 0);
    assert.equal(setup.button.disabled, false);
});

test('held or ineligible records never get a disposal confirmation input', async () => {
    const setup = environment({ enabled: true, eligible: false, confirm: true });
    await setup.click();
    assert.equal(setup.alerts[0].input, undefined);
    assert.equal(setup.requests.length, 1);
});

test('eligible explicit disposal sends the bound token and confirmation with csrf', async () => {
    const setup = environment({ enabled: true, confirm: true });
    await setup.click();
    assert.equal(setup.alerts[0].inputValidator('DELETE'), 'Type DISPOSE exactly to continue.');
    assert.equal(setup.requests.length, 2);
    assert.deepEqual(JSON.parse(setup.requests[1].request.body), { token: 'bound-preview-token', confirmation: 'DISPOSE' });
    assert.equal(setup.requests[1].request.headers['X-CSRF-TOKEN'], 'csrf-token');
    assert.equal(setup.reloads(), 1);
});

test('server rejection stays in the confirmation and does not report success', async () => {
    const setup = environment({ enabled: true, confirm: true, fail: true });
    await setup.click();
    assert.deepEqual(setup.validation, ['Preview is stale.']);
    assert.equal(setup.reloads(), 0);
    assert.equal(setup.button.disabled, false);
});

test('rapid duplicate preview clicks produce only one request', async () => {
    const setup = environment();
    const first = setup.click();
    await setup.click();
    await first;
    assert.equal(setup.requests.length, 1);
});

test('record names are inserted as text rather than executable markup', async () => {
    const label = '<img src=x onerror=alert(1)>';
    const setup = environment({ label });
    await setup.click();
    assert.equal(setup.alerts[0].html.children[0].children[0].textContent, label);
});

test('preview uses a branded compact dialog without the oversized default icon', async () => {
    const setup = environment({ enabled: true });
    await setup.click();
    const modal = setup.alerts[0];
    assert.equal(modal.customClass.popup, 'retention-dialog');
    assert.equal(modal.customClass.actions, 'retention-dialog-actions');
    assert.equal(modal.width, 620);
    assert.equal(modal.icon, undefined);
    assert.equal(modal.focusCancel, true);
    assert.equal(modal.titleText, 'Review permanent disposal');
    assert.equal(modal.html.children[1].className, 'retention-preview-metrics');
    assert.equal(modal.html.children[1].children.length, 3);
});

test('long filenames stay in collapsed technical details and remain plain text', async () => {
    const path = 'requirements/<img src=x onerror=alert(1)>-' + 'long-file-'.repeat(20) + '.pdf';
    const setup = environment({ files: [{ disk: 'secure_uploads', path }], tables: { moa_renewals: 1, companies: 1 } });
    await setup.click();
    const details = setup.alerts[0].html.children[2];
    assert.equal(details.tag, 'details');
    assert.notEqual(details.open, true);
    assert.equal(details.children[1].children[0].textContent, 'MOA renewal history: 1');
    assert.equal(details.children[2].children[0].textContent, 'secure_uploads: ' + path);
    const notice = setup.alerts[0].html.children[3];
    assert.match(notice.children[1].textContent, /Database deletion is permanent/);
    assert.match(notice.children[1].textContent, /Existing backups are not erased/);
    assert.match(notice.children[1].textContent, /Shared files are preserved/);
});

test('compact row previews the selected category rather than a fixed default', async () => {
    const setup = environment({ category: 'completion_records' });
    await setup.click();
    assert.deepEqual(JSON.parse(setup.requests[0].request.body), { category: 'completion_records', subject_id: 2 });
});

test('shared modal populates the selected student and clears prior confirmation', () => {
    const setup = environment();
    setup.fields.completion_confirmed.checked = true;
    setup.edit();
    assert.equal(setup.fields.subject_type.value, 'student');
    assert.equal(setup.fields.subject_id.value, '2');
    assert.equal(setup.fields.completed_on.value, '2023-01-01');
    assert.equal(setup.fields.hold_reason.value, 'Pending review');
    assert.equal(setup.fields.completion_confirmed.checked, false);
    assert.equal(setup.modalLabel.textContent, 'Student One');
    assert.ok(setup.groups.every(group => !group.hidden));
});

test('MOA modal disables student-only fields and safely restores them for a student', () => {
    const setup = environment({ editType: 'moa' });
    setup.edit();
    assert.ok(setup.groups.every(group => group.hidden));
    assert.equal(setup.fields.completion_confirmed.disabled, true);
    assert.equal(setup.fields.hold_until.value, '2027-01-01');
    setup.editButton.dataset.subjectType = 'student';
    setup.editButton.dataset.label = '<img src=x onerror=alert(1)>';
    setup.edit();
    assert.equal(setup.fields.completion_confirmed.disabled, false);
    assert.equal(setup.modalLabel.textContent, '<img src=x onerror=alert(1)>');
});
