const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');
const source = fs.readFileSync('public/js/components/process-feedback.js', 'utf8');

function environment() {
    const timers = new Map();
    const documentEvents = {};
    const windowEvents = {};
    const alerts = [];
    let timerId = 0;
    class Element {
        constructor(tag = 'button') {
            this.tag = tag; this.children = []; this.attributes = {}; this.dataset = {};
            this.className = ''; this.textContent = ''; this.disabled = false;
            this.classList = {
                add: name => { this.className += ' ' + name; },
                remove: name => { this.className = this.className.replace(name, ''); },
                toggle: (name, on) => on ? this.classList.add(name) : this.classList.remove(name),
            };
        }
        appendChild(child) { child.parent = this; this.children.push(child); return child; }
        prepend(child) { child.parent = this; this.children.unshift(child); }
        remove() { if (this.parent) this.parent.children = this.parent.children.filter(child => child !== this); }
        setAttribute(name, value) { this.attributes[name] = String(value); }
        getAttribute(name) { return this.attributes[name] ?? null; }
        removeAttribute(name) { delete this.attributes[name]; }
        addEventListener() {}
        focus() {}
        closest(selector) {
            return selector.includes('button') && this.tag === 'button' ? this : null;
        }
        querySelector(selector) {
            for (const child of this.children) {
                if (selector === child.tag || selector.startsWith('.') && child.className.split(' ').includes(selector.slice(1))) return child;
                const found = child.querySelector(selector); if (found) return found;
            }
            return null;
        }
        querySelectorAll() { return []; }
    }
    class Form extends Element {
        constructor(action, files = []) { super('form'); this.action = action; this.method = 'post'; this.target = ''; this.files = files; this.nativeCalls = 0; }
        querySelectorAll(selector) { return selector === 'input[type="file"]' ? this.files : []; }
        submit() { this.nativeCalls++; }
    }
    class XHR {
        static instances = [];
        constructor() {
            this.listeners = {}; this.uploadListeners = {}; this.headers = {};
            this.upload = { addEventListener: (event, handler) => { this.uploadListeners[event] = handler; } };
            XHR.instances.push(this);
        }
        open(method, url) { this.method = method; this.url = url; }
        send(body) { this.body = body; }
        setRequestHeader(name, value) { this.headers[name] = value; }
        addEventListener(event, handler) { (this.listeners[event] ||= []).push(handler); }
        emit(event) { for (const handler of this.listeners[event] || []) handler(); }
    }
    const storage = new Map();
    const body = new Element('body');
    const location = { href: 'http://localhost/student/files', origin: 'http://localhost', assign(url) { this.assigned = url; } };
    const swal = { fire(options) { alerts.push(options); return Promise.resolve({ isConfirmed: true }); } };
    const document = {
        body, activeElement: null,
        createElement: tag => new Element(tag),
        addEventListener: (event, handler) => { documentEvents[event] = handler; },
        getElementById: () => ({ textContent: '{}' }), querySelectorAll: () => [],
        querySelector: selector => body.querySelector(selector),
    };
    const window = {
        Swal: swal, XMLHttpRequest: XHR,
        addEventListener: (event, handler) => { windowEvents[event] = handler; },
    };
    const context = { window, document, location, URL, XMLHttpRequest: XHR, HTMLFormElement: Form,
        FormData: class { constructor(form) { this.form = form; this.fields = []; } append(...args) { this.fields.push(args); } },
        sessionStorage: { setItem: (key, value) => storage.set(key, value), getItem: key => storage.get(key), removeItem: key => storage.delete(key) },
        setTimeout: (fn, delay) => { timers.set(++timerId, { fn, delay }); return timerId; }, clearTimeout: id => timers.delete(id),
    };
    return { context, window, document, documentEvents, windowEvents, alerts, Element, Form, XHR, location,
        run() { vm.runInNewContext(source, context); return window.InternConnectFeedback; },
        ready() { documentEvents.DOMContentLoaded(); },
        flush(delay) {
            for (const [id, task] of [...timers]) if (task.delay === delay) { timers.delete(id); task.fn(); }
        },
    };
}

test('coverage includes uploads, evaluations, sync, passwords and bulk reviews; excludes remote and local validation', () => {
    const e = environment(); const api = e.run();
    for (const path of ['/uploadReq', '/uploadfile', '/companyCreate', '/student/evaluation/send', '/coordinator/sync-faculty', '/change_password/1', '/update/approve/status/bulk']) {
        assert.equal(api.classify(path, 'POST').mode, 'modal', path);
    }
    assert.equal(api.classify('/student/edit/1', 'PUT').mode, 'toast');
    assert.equal(api.classify('/login-user', 'POST').mode, 'none');
    assert.equal(api.classify('/dashboard', 'GET').read, true);
    for (const path of ['https://external.example/upload', '/chatbot/message', '/change_password/verify-current/1', '/reports/ai/ask']) assert.equal(api.classify(path, 'POST'), null);
});

test('success requires server confirmation; business failures and validation errors never become success', () => {
    const e = environment(); const api = e.run();
    assert.equal(api.resultOf(200, null), null);
    assert.equal(api.resultOf(200, { answer: 'hello' }), null);
    assert.equal(api.resultOf(200, { success: true, message: 'Uploaded' }).type, 'success');
    assert.equal(api.resultOf(200, { success: true, title: 'Upload successful', message: 'Uploaded' }).title, 'Upload successful');
    assert.equal(api.resultOf(200, { success: false, message: 'Duplicate' }).type, 'error');
    assert.equal(api.resultOf(422, { errors: { file: ['Only PDFs'], name: ['Required'] } }).message, 'Only PDFs\nRequired');
    assert.match(api.resultOf(419, null).message, /session expired/i);
    assert.match(api.resultOf(413, null).message, /too large/i);
    assert.equal(api.resultOf(0, null).type, 'error');
});

test('concurrent operations restore a shared button only after both complete', () => {
    const e = environment(); const api = e.run(); const button = new e.Element();
    const first = api.begin({ title: 'Saving', mode: 'toast' }, null, button);
    const second = api.begin({ title: 'Saving', mode: 'toast' }, null, button);
    assert.equal(button.disabled, true); assert.equal(button.children.length, 1);
    api.end(first); assert.equal(button.disabled, true);
    api.end(second); assert.equal(button.disabled, false); assert.equal(button.children.length, 0);
    assert.equal(button.getAttribute('aria-busy'), null);
});

test('existing handlers retain ownership of pre-disabled buttons', () => {
    const e = environment(); const api = e.run(); const button = new e.Element(); button.disabled = true;
    const op = api.begin({ mode: 'toast' }, null, button);
    button.disabled = false; // Existing request handler finished first.
    api.end(op); assert.equal(button.disabled, false);
});

test('fetch observation preserves the response and original errors', async () => {
    const e = environment();
    const response = { status: 200, headers: { get: () => 'application/json' }, clone: () => ({ json: () => Promise.resolve({ success: true, message: 'Saved' }) }) };
    e.window.fetch = async () => response;
    e.run(); assert.equal(await e.window.fetch('/student/edit', { method: 'PUT' }), response);
    await Promise.resolve(); e.flush(50); assert.equal(e.alerts.length, 1); assert.equal(e.alerts[0].text, 'Saved');
    const failed = environment(); const error = new Error('offline'); failed.window.fetch = async () => { throw error; }; failed.run();
    await assert.rejects(failed.window.fetch('/uploadfile', { method: 'POST' }), received => received === error);
    failed.flush(50); assert.equal(failed.alerts[0].icon, 'error');
});

test('legacy AJAX result dialog is reused instead of showing a second success', () => {
    const e = environment(); e.run(); const xhr = new e.XHR();
    xhr.open('POST', '/announcements'); xhr.send('body');
    e.window.Swal.fire({ icon: 'success', title: 'Posted' });
    xhr.status = 200; xhr.responseText = JSON.stringify({ success: true, message: 'Posted' }); xhr.emit('loadend');
    e.flush(50); assert.equal(e.alerts.length, 1); assert.match(e.alerts[0].customClass.popup, /ic-feedback-popup/);
});

test('a legacy success alert cannot hide a server-reported business failure', () => {
    const e = environment(); e.run(); const xhr = new e.XHR();
    xhr.open('POST', '/announcements'); xhr.send('body');
    e.window.Swal.fire({ icon: 'success', title: 'Saved' });
    xhr.status = 200; xhr.responseText = JSON.stringify({ success: false, message: 'Permission denied' }); xhr.emit('loadend');
    e.flush(50); assert.equal(e.alerts.length, 2); assert.equal(e.alerts[1].icon, 'error');
    assert.equal(e.alerts[1].text, 'Permission denied');
});

test('regular XHR form fields never show misleading file-upload progress', () => {
    const e = environment(); e.run(); const xhr = new e.XHR();
    xhr.open('POST', '/announcements'); xhr.send('title=Updated');
    assert.equal(xhr.uploadListeners.progress, undefined);
});

test('ordinary forms preserve native submission and block repeated submits', () => {
    const e = environment(); e.run(); e.ready();
    const form = new e.Form('/student/edit'); form.submit(); form.submit();
    assert.equal(form.nativeCalls, 1);
    const cancelled = new e.Form('/student/edit');
    e.windowEvents.submit({ target: cancelled, defaultPrevented: true });
    cancelled.submit(); assert.equal(cancelled.nativeCalls, 1);
});

test('file upload reports real transfer progress, waits for confirmation, then redirects', async () => {
    const e = environment(); e.run(); e.ready();
    const form = new e.Form('/uploadReq', [{ files: [{ name: 'requirements.pdf' }] }]);
    form.dataset.uploadTitle = 'Validating requirement';
    form.dataset.uploadDescription = 'Checking the requirement PDF securely.';
    const button = new e.Element(); button.name = 'action'; button.value = 'upload';
    let prevented = false;
    e.windowEvents.submit({ target: form, submitter: button, defaultPrevented: false, preventDefault() { prevented = true; } });
    assert.equal(prevented, true); assert.equal(form.nativeCalls, 0);
    const xhr = e.XHR.instances[0];
    assert.equal(xhr.headers['X-InternConnect-Upload'], '1'); assert.equal(xhr.body.fields[0][1], 'upload');
    e.flush(180);
    assert.equal(e.document.body.querySelector('h2').textContent, 'Validating requirement');
    assert.equal(e.document.body.querySelector('p').textContent, 'Checking the requirement PDF securely.');
    xhr.uploadListeners.progress({ lengthComputable: true, loaded: 100, total: 100 });
    assert.equal(e.alerts.length, 0);
    assert.equal(e.document.body.querySelector('h2').textContent, 'Validating requirement');
    xhr.status = 200; xhr.responseText = JSON.stringify({ success: true, title: 'Upload successful', message: 'Uploaded', redirect: '/student/files' }); xhr.emit('load');
    await Promise.resolve();
    assert.equal(button.disabled, false); assert.match(e.alerts[0].text, /requirements.pdf/);
    assert.equal(e.alerts[0].title, 'Upload successful');
    assert.equal(e.location.assigned, 'http://localhost/student/files');
});

test('upload validation and connection failures preserve the form and allow retry', () => {
    for (const networkError of [false, true]) {
        const e = environment(); e.run(); e.ready(); const form = new e.Form('/uploadReq', [{ files: [{ name: 'invalid.png' }] }]);
        const button = new e.Element();
        e.windowEvents.submit({ target: form, submitter: button, defaultPrevented: false, preventDefault() {} });
        const xhr = e.XHR.instances[0];
        if (networkError) xhr.emit('error');
        else { xhr.status = 422; xhr.responseText = JSON.stringify({ success: false, errors: { file: ['Only PDF files are accepted.'] } }); xhr.emit('load'); }
        assert.equal(button.disabled, false); assert.equal(e.location.assigned, undefined);
        assert.equal(form.files[0].files[0].name, 'invalid.png'); assert.equal(e.alerts[0].icon, 'error');
        e.windowEvents.submit({ target: form, submitter: button, defaultPrevented: false, preventDefault() {} });
        assert.equal(e.XHR.instances.length, 2);
    }
});
