/* Shared, server-confirmed process feedback. No business rules are changed here. */
(function () {
    'use strict';
    if (window.InternConnectFeedback) return;
    const storageKey = 'internconnect.process-feedback';
    const operations = new Set();
    const busyForms = new WeakMap();
    const buttonStates = new Map();
    const skeletonStates = new Map();
    let lastTrigger = null;
    let lastTriggerTime = 0;
    let alertVersion = 0;
    let lastAlert = null;
    let acknowledgedFlash = null;
    let loader;
    let dialog;
    let nativeSubmit;

    function remember(value) {
        try { sessionStorage.setItem(storageKey, JSON.stringify({ ...value, time: Date.now() })); } catch (_) {}
    }
    function recall() {
        try {
            const value = JSON.parse(sessionStorage.getItem(storageKey));
            sessionStorage.removeItem(storageKey);
            return value && Date.now() - value.time < 60000 ? value : null;
        } catch (_) { return null; }
    }
    function clearStored() { try { sessionStorage.removeItem(storageKey); } catch (_) {} }
    function classify(url, method) {
        let parsed;
        try { parsed = new URL(url, location.href); } catch (_) { return null; }
        if (parsed.origin !== location.origin || !/^https?:$/.test(parsed.protocol)) return null;
        const path = parsed.pathname.toLowerCase();
        const write = !['GET', 'HEAD', 'OPTIONS'].includes(String(method || 'GET').toUpperCase());
        // These already have local typing/validation feedback, not success dialogs.
        if (/chatbot\/message|verify-current|check-email|reports\/ai\/(ask|insight)/.test(path)) return null;
        if (!write) return { read: true, mode: 'none', title: 'Loading…' };
        if (/upload|companycreate|student\/moa\//.test(path)) return { mode: 'modal', title: 'Processing document…' };
        if (/evaluation/.test(path)) return { mode: 'modal', title: /send|resend/.test(path) ? 'Sending evaluation…' : 'Processing evaluation…' };
        if (/sync-|prune-/.test(path)) return { mode: 'modal', title: 'Synchronizing records…' };
        if (/password|register|onboarding/.test(path)) return { mode: 'modal', title: 'Saving account…' };
        if (/login-user/.test(path)) return { mode: 'none', title: 'Signing in…' };
        if (/send-email|sendfile|notify/.test(path)) return { mode: 'modal', title: 'Sending email…' };
        if (/report|approve.*bulk|approve-all|transfer-role/.test(path)) return { mode: 'modal', title: 'Processing request…' };
        return { mode: 'toast', title: 'Saving changes…' };
    }
    function resultOf(status, data) {
        const title = data && typeof data.title === 'string' && data.title.trim() ? data.title.trim() : null;
        if (status < 200 || status >= 300 || (data && (data.success === false || data.success === 0
            || data.status === false || data.status === 'error' || data.status === 'failed' || data.error))) {
            let message = data && (data.message || (typeof data.error === 'string' ? data.error : ''));
            if (data && data.errors) message = Object.values(data.errors).flat().join('\n') || message;
            if (status === 419) message = 'Your session expired. Refresh the page before trying again.';
            if (status === 401) message = 'Please sign in again to continue.';
            if (status === 413) message = 'This file is too large. Choose a smaller file and try again.';
            return { type: 'error', title, message: String(message || (status === 0
                ? 'Connection interrupted. Check your connection and try again.'
                : 'The request could not be completed. Please try again.')) };
        }
        // HTML redirects, HTTP 200 alone, and unconfirmed payloads are not success.
        if (data && (data.success === true || typeof data.message === 'string')) {
            return { type: 'success', title, message: data.message || 'Changes saved successfully.' };
        }
        return null;
    }
    function patchSwal() {
        const swal = window.Swal;
        if (!swal || swal.__icFeedbackPatched) return;
        const fire = swal.fire;
        swal.fire = function (...args) {
            const options = typeof args[0] === 'object' ? { ...args[0] }
                : { title: args[0], html: args[1], icon: args[2] };
            // A server flash after our upload redirect is the same result already acknowledged.
            let pending;
            try { pending = JSON.parse(sessionStorage.getItem(storageKey)); } catch (_) {}
            const message = [options.title, options.text, options.html].filter(Boolean).join(' ');
            const dismissed = acknowledgedFlash || (pending && Date.now() - pending.time < 60000 ? pending.dismissFlash : null);
            if (options.icon === 'success' && dismissed && message.includes(dismissed)) {
                acknowledgedFlash = null;
                return Promise.resolve({ isConfirmed: true });
            }
            if (options.icon === 'success' || options.icon === 'error') {
                alertVersion++;
                lastAlert = { type: options.icon, message };
            }
            const custom = typeof options.customClass === 'object' ? options.customClass : {};
            options.customClass = {
                ...custom,
                popup: [typeof options.customClass === 'string' ? options.customClass : custom.popup, 'ic-feedback-popup'].filter(Boolean).join(' '),
                confirmButton: [custom.confirmButton, 'ic-feedback-confirm'].filter(Boolean).join(' '),
                cancelButton: [custom.cancelButton, 'ic-feedback-cancel'].filter(Boolean).join(' '),
            };
            return fire.call(this, options);
        };
        swal.__icFeedbackPatched = true;
    }
    function textElement(tag, text, className) {
        const element = document.createElement(tag);
        if (className) element.className = className;
        element.textContent = text;
        return element;
    }
    function showResult(result, mode) {
        patchSwal();
        const title = result.title || (result.type === 'success' ? 'Completed successfully' : 'Unable to complete');
        if (window.Swal) {
            return window.Swal.fire({
                icon: result.type, title,
                text: result.message, toast: mode === 'toast', position: mode === 'toast' ? 'top-end' : 'center',
                showConfirmButton: mode !== 'toast', confirmButtonText: 'Okay',
                timer: mode === 'toast' && result.type !== 'error' ? 4500 : undefined,
                timerProgressBar: mode === 'toast', allowOutsideClick: result.type !== 'success' || mode === 'toast',
            });
        }
        if (mode === 'toast') {
            let host = document.querySelector('.ic-feedback-toasts');
            if (!host) { host = document.createElement('div'); host.className = 'ic-feedback-toasts'; document.body.appendChild(host); }
            const toast = textElement('div', result.message, 'ic-feedback-toast' + (result.type === 'error' ? ' is-error' : ''));
            toast.setAttribute('role', result.type === 'error' ? 'alert' : 'status');
            const close = textElement('button', '×'); close.type = 'button'; close.setAttribute('aria-label', 'Dismiss notification');
            close.addEventListener('click', () => toast.remove()); toast.appendChild(close); host.appendChild(toast);
            if (result.type !== 'error') {
                let timer = setTimeout(() => toast.remove(), 4500);
                toast.addEventListener('mouseenter', () => clearTimeout(timer));
                toast.addEventListener('mouseleave', () => { timer = setTimeout(() => toast.remove(), 4500); });
                toast.addEventListener('focusin', () => clearTimeout(timer));
            }
            return Promise.resolve();
        }
        return new Promise(resolve => {
            const previous = document.activeElement;
            dialog = document.createElement('div'); dialog.className = 'ic-feedback-dialog';
            dialog.setAttribute('role', 'dialog'); dialog.setAttribute('aria-modal', 'true'); dialog.setAttribute('aria-labelledby', 'ic-feedback-result-title');
            const panel = document.createElement('div'); panel.className = 'ic-feedback-panel';
            panel.appendChild(textElement('div', result.type === 'success' ? '✓' : '!', 'ic-feedback-result-icon' + (result.type === 'error' ? ' is-error' : '')));
            const titleElement = textElement('h2', title); titleElement.id = 'ic-feedback-result-title'; panel.appendChild(titleElement);
            panel.appendChild(textElement('p', result.message));
            const close = textElement('button', 'Okay', 'ic-feedback-dismiss'); close.type = 'button';
            const dismiss = () => { dialog.remove(); dialog = null; previous?.focus(); resolve(); };
            close.addEventListener('click', dismiss);
            panel.appendChild(close); dialog.appendChild(panel); document.body.appendChild(dialog); close.focus();
            dialog.addEventListener('keydown', event => {
                if (event.key === 'Escape') { event.preventDefault(); dismiss(); }
                if (event.key === 'Tab') { event.preventDefault(); close.focus(); }
            });
        });
    }
    function renderLoading() {
        const active = [...operations].filter(op => !op.read);
        if (!active.length) { if (loader) loader.hidden = true; return; }
        if (!document.body) return;
        if (!loader) {
            loader = document.createElement('div'); loader.className = 'ic-feedback-loading';
            loader.setAttribute('role', 'status'); loader.setAttribute('aria-live', 'polite');
            const panel = document.createElement('div'); panel.className = 'ic-feedback-panel';
            const spinner = document.createElement('span'); spinner.className = 'ic-feedback-spinner'; spinner.setAttribute('aria-hidden', 'true');
            panel.appendChild(spinner); panel.appendChild(textElement('h2', '', 'ic-feedback-loading-title'));
            panel.appendChild(textElement('p', '', 'ic-feedback-loading-message'));
            const progress = document.createElement('progress'); progress.className = 'ic-feedback-progress'; progress.max = 100; progress.hidden = true; progress.setAttribute('aria-label', 'Upload progress');
            panel.appendChild(progress); loader.appendChild(panel); document.body.appendChild(loader);
        }
        const op = active[active.length - 1]; loader.hidden = false;
        loader.querySelector('h2').textContent = op.title;
        loader.querySelector('p').textContent = op.description || 'Please wait while we complete your request.';
        const progress = loader.querySelector('progress'); progress.hidden = op.progress == null;
        if (op.progress != null) progress.value = op.progress;
    }
    function begin(info, form, button) {
        patchSwal();
        const op = { ...info, form, button, alertVersion };
        operations.add(op);
        if (button && !info.read) {
            let state = buttonStates.get(button);
            if (!state) {
                const spinner = document.createElement('span'); spinner.className = 'ic-feedback-button-spinner'; spinner.setAttribute('aria-hidden', 'true');
                state = { disabled: button.disabled, busy: button.getAttribute('aria-busy'), spinner, count: 0 };
                buttonStates.set(button, state); button.prepend(spinner);
            }
            state.count++; button.disabled = true; button.setAttribute('aria-busy', 'true'); button.setAttribute('data-ic-busy', '');
        }
        if (info.read && button) {
            const target = button.closest('[data-feedback-skeleton], .table-card, .table-container, .analytics-card, .card');
            if (target) {
                op.skeleton = target;
                skeletonStates.set(target, (skeletonStates.get(target) || 0) + 1);
                target.classList.add('ic-feedback-skeleton'); target.setAttribute('aria-busy', 'true');
            }
        }
        op.timer = setTimeout(renderLoading, 180);
        return op;
    }
    function end(op) {
        if (!op || !operations.has(op)) return;
        operations.delete(op); clearTimeout(op.timer);
        if (op.form) busyForms.delete(op.form);
        const state = buttonStates.get(op.button);
        if (state && --state.count === 0) {
            state.spinner.remove();
            // A pre-disabled button belongs to its existing request handler.
            if (!state.disabled) op.button.disabled = false;
            if (state.busy == null) op.button.removeAttribute('aria-busy'); else op.button.setAttribute('aria-busy', state.busy);
            op.button.removeAttribute('data-ic-busy'); buttonStates.delete(op.button);
        }
        if (op.skeleton) {
            const count = (skeletonStates.get(op.skeleton) || 1) - 1;
            if (count) skeletonStates.set(op.skeleton, count);
            else { skeletonStates.delete(op.skeleton); op.skeleton.classList.remove('ic-feedback-skeleton'); op.skeleton.removeAttribute('aria-busy'); }
        }
        renderLoading();
    }
    function finish(op, status, data) {
        end(op);
        if (op.read || op.mode === 'none') return;
        const result = resultOf(status, data);
        if (!result) return;
        remember({ result, mode: op.mode }); // Retain server-confirmed feedback across immediate legacy reloads.
        setTimeout(() => {
            if (alertVersion !== op.alertVersion && lastAlert?.type === result.type) { clearStored(); return; }
            clearStored(); showResult(result, result.type === 'error' ? 'modal' : op.mode);
        }, 50);
    }
    function trigger() { return Date.now() - lastTriggerTime < 1500 ? lastTrigger : null; }
    function parseJson(text) { try { return JSON.parse(text); } catch (_) { return null; } }

    // Observe existing AJAX clients without consuming response bodies or changing their callbacks.
    if (window.fetch) {
        const originalFetch = window.fetch;
        window.fetch = function (input, options) {
            const info = classify(typeof input === 'string' || input instanceof URL ? String(input) : input.url,
                options?.method || input?.method || 'GET');
            if (!info) return originalFetch.apply(this, arguments);
            const op = begin(info, null, trigger());
            return originalFetch.apply(this, arguments).then(response => {
                if (info.read) { end(op); return response; }
                if (!(response.headers.get('content-type') || '').includes('application/json')) {
                    finish(op, response.status, null); return response;
                }
                response.clone().json().then(data => finish(op, response.status, data), () => finish(op, response.status, null));
                return response;
            }, error => { finish(op, 0, null); throw error; });
        };
    }
    if (window.XMLHttpRequest) {
        const originalOpen = XMLHttpRequest.prototype.open;
        const originalSend = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.open = function (method, url, ...args) {
            this.__icInfo = classify(url, method); return originalOpen.call(this, method, url, ...args);
        };
        XMLHttpRequest.prototype.send = function (...args) {
            if (this.__icInfo && !this.__icOwnUpload) {
                const op = begin(this.__icInfo, null, trigger());
                this.addEventListener('loadend', () => {
                    let data = null;
                    try { data = this.responseType === 'json' ? this.response : (!this.responseType || this.responseType === 'text') ? parseJson(this.responseText) : null; } catch (_) {}
                    finish(op, this.status, data);
                }, { once: true });
                const multipartUpload = args[0] instanceof FormData && typeof args[0].values === 'function'
                    && [...args[0].values()].some(value => typeof Blob !== 'undefined' && value instanceof Blob);
                if (multipartUpload) this.upload?.addEventListener('progress', event => {
                    if (!event.lengthComputable || op.read || !operations.has(op)) return;
                    op.progress = Math.round(event.loaded / event.total * 100);
                    op.title = op.progress >= 100 ? 'Processing uploaded file…' : 'Uploading file…';
                    renderLoading();
                });
                try { return originalSend.apply(this, args); } catch (error) { end(op); throw error; }
            }
            return originalSend.apply(this, args);
        };
    }
    function submitForm(form, submitter) {
        if (form.dataset.feedback === 'off' || form.target && form.target !== '_self') return false;
        const info = classify(form.action || location.href, form.method);
        if (!info || info.read) return false;
        if (busyForms.has(form)) return true;
        const upload = [...form.querySelectorAll('input[type="file"]')].some(input => input.files?.length);
        if (upload && String(form.method).toUpperCase() === 'POST') {
            // Construct before disabling the submitter; preserve multipart fields and CSRF.
            const body = new FormData(form);
            if (submitter?.name) body.append(submitter.name, submitter.value);
            const uploadTitle = form.dataset.uploadTitle || 'Uploading file…';
            const uploadDescription = form.dataset.uploadDescription || 'Checking the file type, structure, size, and security requirements. Please keep this page open.';
            const op = begin({ ...info, title: uploadTitle, description: uploadDescription, mode: 'modal', progress: 0 }, form, submitter);
            busyForms.set(form, op);
            const xhr = new XMLHttpRequest(); xhr.__icOwnUpload = true;
            xhr.open('POST', form.action || location.href);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-InternConnect-Upload', '1');
            xhr.upload.addEventListener('progress', event => {
                if (!event.lengthComputable) return;
                op.progress = Math.round(event.loaded / event.total * 100);
                op.title = op.progress >= 100 ? uploadTitle : 'Uploading file…';
                op.description = op.progress >= 100 ? uploadDescription : `${op.progress}% transferred`;
                renderLoading();
            });
            xhr.addEventListener('load', () => {
                end(op);
                const data = parseJson(xhr.responseText);
                const result = resultOf(xhr.status, data) || { type: 'error', message: 'The server did not confirm the upload. Please try again.' };
                const filename = [...form.querySelectorAll('input[type="file"]')].flatMap(input => [...input.files]).map(file => file.name).join(', ');
                const displayed = { ...result, message: result.message + (result.type === 'success' && filename ? `\n${filename}` : '') };
                showResult(displayed, 'modal').then(() => {
                    if (result.type !== 'success') return;
                    // The following page will contain the same server flash; show it only once.
                    remember({ dismissFlash: result.message });
                    let destination = location.href;
                    try { const url = new URL(data.redirect || location.href, location.href); if (url.origin === location.origin) destination = url.href; } catch (_) {}
                    location.assign(destination);
                });
            });
            for (const event of ['error', 'abort', 'timeout']) xhr.addEventListener(event, () => { end(op); showResult(resultOf(0, null), 'modal'); });
            try { xhr.send(body); } catch (_) { end(op); showResult(resultOf(0, null), 'modal'); }
            return true;
        }
        // Regular forms keep their existing browser navigation and server validation.
        // Named submitter fields must remain successful form controls when disabled.
        if (submitter?.name) {
            const field = document.createElement('input'); field.type = 'hidden'; field.name = submitter.name; field.value = submitter.value; field.dataset.icSubmitter = ''; form.appendChild(field);
        }
        const op = begin(info, form, submitter); busyForms.set(form, op);
        remember({ intent: info.mode });
        return false;
    }
    document.addEventListener('click', event => {
        lastTrigger = event.target.closest('button, input[type="submit"], a, [role="button"]'); lastTriggerTime = Date.now();
    }, true);
    // CDN libraries can load after this head script; style their first dialog too.
    document.addEventListener('load', event => {
        if (event.target.tagName === 'SCRIPT') patchSwal();
    }, true);
    document.addEventListener('DOMContentLoaded', () => {
        patchSwal();
        window.addEventListener('submit', event => {
            if (event.defaultPrevented || !(event.target instanceof HTMLFormElement)) return;
            if (submitForm(event.target, event.submitter)) event.preventDefault();
        });
        nativeSubmit = HTMLFormElement.prototype.submit;
        HTMLFormElement.prototype.submit = function () {
            if (!submitForm(this, this.querySelector('button[type="submit"], input[type="submit"]') || trigger())) return nativeSubmit.call(this);
        };
        const saved = recall();
        acknowledgedFlash = saved?.dismissFlash || null;
        setTimeout(() => { acknowledgedFlash = null; }, 1500);
        const config = parseJson(document.getElementById('ic-process-feedback-config')?.textContent || '{}');
        const flash = config?.flash;
        if (flash) {
            document.querySelectorAll('.alert-success, .alert-danger').forEach(element => {
                if (element.textContent.includes(flash.message)) element.hidden = true;
            });
        }
        setTimeout(() => {
            const feedbackClosed = () => {
                if (window.uploadValidationFeedback) document.dispatchEvent(new CustomEvent('upload-validation:feedback-closed', {
                    detail: window.uploadValidationFeedback,
                }));
            };
            if (flash && saved?.dismissFlash === flash.message) { feedbackClosed(); return; }
            const result = flash || saved?.result;
            if (!result) return;
            if (lastAlert && lastAlert.type === result.type && (lastAlert.message.includes(result.message) || result.message.includes(lastAlert.message))) return;
            showResult(result, result.type === 'error' ? 'modal' : saved?.intent || saved?.mode || 'toast').then(feedbackClosed);
        }, 250);
        if (window.jQuery) {
            window.jQuery(document).on('processing.dt.icFeedback', function (event, settings, processing) {
                if (!settings?.ajax || !settings.nTable?.parentElement) return;
                const target = settings.nTable.parentElement;
                target.classList.toggle('ic-feedback-skeleton', processing);
                if (processing) target.setAttribute('aria-busy', 'true'); else target.removeAttribute('aria-busy');
            });
        }
    });
    window.addEventListener('pageshow', event => {
        if (event.persisted) { [...operations].forEach(end); document.querySelectorAll('[data-ic-submitter]').forEach(element => element.remove()); clearStored(); }
    });
    window.InternConnectFeedback = { begin, end, success: (message, mode = 'toast') => showResult({ type: 'success', message }, mode),
        error: message => showResult({ type: 'error', message }, 'modal'), classify, resultOf };
})();
