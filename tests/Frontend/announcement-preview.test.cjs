const { test } = require('node:test');
const assert = require('node:assert/strict');
const fs = require('node:fs');
const vm = require('node:vm');

const source = fs.readFileSync('public/js/coordinator/announcement-audience.js', 'utf8');
const start = source.indexOf('    function previewContent(data) {');
const end = source.indexOf("    form.addEventListener('submit'", start);
assert.ok(start >= 0 && end > start, 'Announcement preview renderer exists');

function escape(value) {
    return String(value).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
}

class Element {
    constructor(tag) {
        this.tag = tag;
        this.className = '';
        this.attributes = {};
        this.children = [];
        this.text = '';
    }
    set textContent(value) { this.text = String(value); this.children = []; }
    setAttribute(name, value) { this.attributes[name] = String(value); }
    append(...children) { this.children.push(...children); }
    appendChild(child) { this.children.push(child); return child; }
    prepend(child) { this.children.unshift(child); }
    get outerHTML() {
        const attributes = { ...this.attributes, ...(this.className ? { class: this.className } : {}) };
        const serialized = Object.entries(attributes).map(([name, value]) => ` ${name}="${escape(value)}"`).join('');
        return `<${this.tag}${serialized}>${escape(this.text)}${this.children.map(child => child.outerHTML).join('')}</${this.tag}>`;
    }
}

function render(data, options = {}) {
    const environment = {
        document: { createElement: tag => new Element(tag) },
        form: { elements: {
            title: { value: options.title || 'OJT orientation' },
            content: { value: options.content || 'Please attend the orientation.' },
            school_year: { value: options.year || '' },
            semester: { value: options.semester || '' },
        } },
        programMode: { value: options.programs ? 'selected' : 'all' },
        programs: { querySelectorAll: () => (options.programs || []).map(value => ({ value })) },
    };
    return vm.runInNewContext(`(${source.slice(start, end).trim()})`, environment)(data);
}

test('preview has compact recipient cards and expandable names with explicit email opt-out', () => {
    const html = render({ students: [{ full_name: 'Student One' }], professors: [], email: null });
    assert.match(html, /announcement-preview-metrics/);
    assert.match(html, /1 total/);
    assert.match(html, /View students \(1\)/);
    assert.match(html, /<details/);
    assert.match(html, /Student One/);
    assert.match(html, /In-app announcement only/);
    assert.match(html, /No email notifications will be sent\./);
    assert.doesNotMatch(html, /View ojt professors/);
});

test('preview displays selected programs and academic period', () => {
    const html = render({ students: [], professors: [{ name: 'Professor One' }], email: null }, {
        programs: ['BSIT', 'BSECE'], year: '2026-2027', semester: '1st Semester',
    });
    assert.match(html, /announcement-preview-chip">BSIT/);
    assert.match(html, /announcement-preview-chip">BSECE/);
    assert.match(html, /2026-2027/);
    assert.match(html, /1st Semester/);
    assert.match(html, /View ojt professors \(1\)/);
});

test('live email warning shows queued and skipped counts without a capture assurance', () => {
    const html = render({ students: [{ full_name: 'Student' }], professors: [], email: { valid: 1, skipped: 2, capture: false } });
    assert.match(html, /Email notifications enabled/);
    assert.match(html, /1 email notification\(s\) will be queued/);
    assert.match(html, /2 recipient\(s\) have missing or invalid email addresses/);
    assert.doesNotMatch(html, /No email notifications will be sent/);
    assert.doesNotMatch(html, /no real emails will be sent/);
});

test('capture mode is distinguished from live email delivery', () => {
    const html = render({ students: [], professors: [{ name: 'Professor' }], email: { valid: 1, skipped: 0, capture: true } });
    assert.match(html, /Email test capture enabled/);
    assert.match(html, /Test mode: no real emails will be sent\./);
    assert.doesNotMatch(html, /missing or invalid email addresses/);
});

test('message, program, and recipient text is escaped instead of interpreted as HTML', () => {
    const payload = '<img src=x onerror="alert(1)">';
    const html = render({ students: [{ full_name: payload }], professors: [{ name: payload }], email: null }, {
        title: payload, content: payload, programs: [payload],
    });
    assert.doesNotMatch(html, /<img/);
    assert.match(html, /&lt;img/);
    assert.match(html, /&quot;alert\(1\)&quot;/);
});

test('large audiences retain every name inside the scrollable recipient section', () => {
    const students = Array.from({ length: 91 }, (_, index) => ({ full_name: `Student ${index + 1}` }));
    const html = render({ students, professors: [], email: null });
    assert.match(html, /91 total/);
    assert.equal((html.match(/<li>/g) || []).length, 91);
    assert.match(html, /Student 91/);
});
