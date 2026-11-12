const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const test = require('node:test');
const vm = require('node:vm');

const source = fs.readFileSync(path.join(__dirname, '../../public/js/student/companies.js'), 'utf8');

function environment() {
    const elements = new Map();
    function element(id) {
        const node = {
            dataset: {}, disabled: false, listeners: {}, valid: true,
            addEventListener(type, handler) {
                (this.listeners[type] ||= []).push(handler);
            },
            fire(type, event) {
                for (const handler of this.listeners[type] || []) handler.call(this, event);
            },
            checkValidity() { return this.valid; },
            reportValidity() { this.reported = true; },
            querySelector() { return { value: 'Test company' }; },
        };
        elements.set(id, node);
        return node;
    }
    const form = element('studentMoaForm');
    element('editMoaForm');
    const confirmButton = element('btnConfirmLockSubmit');
    const modal = element('confirmMoaLockModal');
    element('addMoaModal');
    let submissions = 0;
    let valid = true;
    form.requestSubmit = function () {
        const event = { defaultPrevented: false, preventDefault() { this.defaultPrevented = true; } };
        this.fire('submit', event);
        if (!event.defaultPrevented) submissions++;
    };
    const window = {};
    const context = {
        window,
        document: { getElementById: id => elements.get(id), querySelectorAll: () => [] },
        bootstrap: {
            Modal: class {
                constructor(node) { this.node = node; node.instance = this; }
                show() { this.node.visible = true; }
                hide() { this.node.visible = false; }
                static getInstance(node) { return node.instance; }
            },
        },
        validateForm: () => valid,
        $: target => ({
            on(type, handler) {
                const node = typeof target === 'string' ? elements.get(target.slice(1)) : target;
                node.addEventListener(type, handler);
            },
        }),
    };
    const validationStart = source.indexOf("    ['#studentMoaForm', '#editMoaForm'].forEach");
    const validationEnd = source.indexOf('    window.bindPdfInputValidation(', validationStart);
    const confirmationStart = source.indexOf('    let pendingFormType = null;');
    const confirmationEnd = source.indexOf("    $('.submitBtn')", confirmationStart);
    assert.ok(validationStart >= 0 && validationEnd > validationStart);
    assert.ok(confirmationStart >= 0 && confirmationEnd > confirmationStart);
    vm.runInNewContext(source.slice(validationStart, validationEnd) + source.slice(confirmationStart, confirmationEnd), context);
    return { form, confirmButton, modal, window, submissions: () => submissions, invalidate: () => { valid = false; } };
}

test('MOA confirmation does not pre-lock or block the confirmed upload', () => {
    const env = environment();
    env.form.requestSubmit();
    assert.equal(env.submissions(), 0);
    assert.equal(env.modal.visible, true);
    assert.equal(env.form.dataset.submitting, undefined);
    env.confirmButton.fire('click');
    assert.equal(env.submissions(), 1);
    assert.equal(env.modal.visible, false);
    assert.equal(env.confirmButton.disabled, false);
    assert.equal(env.window.moaLockConfirmed, false);
});

test('MOA retry requires confirmation again without a stale submitting flag', () => {
    const env = environment();
    env.form.requestSubmit();
    env.confirmButton.fire('click');
    env.form.requestSubmit();
    assert.equal(env.submissions(), 1);
    assert.equal(env.modal.visible, true);
    env.confirmButton.fire('click');
    assert.equal(env.submissions(), 2);
});

test('invalid MOA input does not open confirmation or submit', () => {
    const env = environment();
    env.invalidate();
    env.form.requestSubmit();
    assert.equal(env.submissions(), 0);
    assert.notEqual(env.modal.visible, true);
});

test('MOA confirmation rechecks browser validity before submitting', () => {
    const env = environment();
    env.form.requestSubmit();
    env.form.valid = false;
    env.confirmButton.fire('click');
    assert.equal(env.submissions(), 0);
    assert.equal(env.form.reported, true);
    assert.equal(env.modal.visible, true);
});
