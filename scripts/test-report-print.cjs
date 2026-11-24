const assert = require('node:assert/strict');
const fs = require('node:fs');
const os = require('node:os');
const path = require('node:path');
const { spawn } = require('node:child_process');

const root = path.resolve(__dirname, '..');
const browserPath = process.env.CHROME_PATH || 'C:/Program Files/Google/Chrome/Application/chrome.exe';
const profile = fs.mkdtempSync(path.join(os.tmpdir(), 'internconnect-report-print-'));
const browser = spawn(browserPath, [
    '--headless=new', '--no-first-run', '--disable-extensions', '--disable-background-networking',
    '--remote-debugging-port=0', `--user-data-dir=${profile}`, 'about:blank',
], { windowsHide: true });
let socket;
let nextId = 0;
const pending = new Map();

function command(method, params = {}) {
    return new Promise((resolve, reject) => {
        const id = ++nextId;
        pending.set(id, { resolve, reject });
        socket.send(JSON.stringify({ id, method, params }));
    });
}

async function evaluate(expression) {
    const result = await command('Runtime.evaluate', { expression, awaitPromise: true, returnByValue: true });
    if (result.exceptionDetails) throw new Error(JSON.stringify(result.exceptionDetails));
    return result.result.value;
}

async function main() {
    const address = await new Promise((resolve, reject) => {
        const timeout = setTimeout(() => reject(new Error('Chrome startup timed out')), 20000);
        browser.on('error', reject);
        browser.stderr.on('data', (data) => {
            const match = data.toString().match(/DevTools listening on (ws:\/\/[^\s]+)/);
            if (match) { clearTimeout(timeout); resolve(new URL(match[1])); }
        });
    });
    const targets = await (await fetch(`http://${address.host}/json/list`)).json();
    socket = new WebSocket(targets.find((target) => target.type === 'page').webSocketDebuggerUrl);
    await new Promise((resolve, reject) => {
        socket.addEventListener('open', resolve, { once: true });
        socket.addEventListener('error', reject, { once: true });
    });
    socket.addEventListener('message', ({ data }) => {
        const result = JSON.parse(data);
        const waiter = pending.get(result.id);
        if (!waiter) return;
        pending.delete(result.id);
        if (result.error) waiter.reject(new Error(JSON.stringify(result.error)));
        else waiter.resolve(result.result);
    });
    await command('Page.enable');
    await command('Network.enable');
    await command('Network.setBlockedURLs', { urls: ['http://*', 'https://*'] });
    const css = fs.readFileSync(path.join(root, 'public/css/report-print.css'), 'utf8');
    const dashboardCss = fs.readFileSync(path.join(root, 'public/css/dashboard-global.css'), 'utf8');
    const darkCss = fs.readFileSync(path.join(root, 'public/css/darkmode.css'), 'utf8');
    const logo = 'data:image/png;base64,' + fs.readFileSync(path.join(root, 'public/images/final-puptg_logo-ojtims_nbg.png')).toString('base64');
    const helper = fs.readFileSync(path.join(root, 'public/js/report-print.js'), 'utf8');
    const output = path.join(root, 'storage/app/testing/report-print');
    fs.mkdirSync(output, { recursive: true });
    for (const [kind, script, stylesheet] of [
        ['student', 'coordinator/reports-table.js', 'coordinator/reports-table.css'],
        ['coordinator-moa', 'coordinator/reports-expired.js', 'coordinator/reports-expired.css'],
        ['professor-moa', 'professor/reports-expired.js', 'professor/reports-expired.css'],
    ]) {
        const source = fs.readFileSync(path.join(root, 'public/js', script), 'utf8');
        const start = source.indexOf('function buildPrintHTML()');
        const end = source.indexOf('const previewModalEl', start);
        const builder = source.slice(start, end);
        const originalCss = fs.readFileSync(path.join(root, 'public/css', stylesheet), 'utf8');
        for (const count of [0, 1, 10, 35]) {
            const frame = (await command('Page.getFrameTree')).frameTree.frame.id;
            await command('Page.setDocumentContent', {
                frameId: frame,
                html: `<html><head><style>${dashboardCss}\n${originalCss}\n${darkCss}\n${css}</style></head><body><div id="print-area-wrapper"></div><div id="printPreviewContent" style="width:900px"></div></body></html>`,
            });
            await evaluate(helper);
            const result = await evaluate(`(async () => {
                const rows = Array.from({ length: ${count} }, (_, index) => {
                    const row = document.createElement('tr');
                    row.dataset.course = 'Bachelor of Science in Information Technology';
                    row.dataset.section = '4-1';
                    row.dataset.natureOfBus = 'Technology services and professional training';
                    row.dataset.moaStatus = 'active';
                    for (let column = 0; column < 11; column++) {
                        const cell = row.insertCell();
                        cell.textContent = column === 0 ? 'Student ' + index : 'Record ' + index + ' detailed sample information';
                        if (column === 1) cell.innerHTML = '<span class="company-name-text">Company ' + index + '</span>';
                    }
                    return row;
                });
                window.$ = () => ({ DataTable: () => ({ rows: (selection) => {
                    if (selection.page) throw new Error('Printing must not depend on table pagination');
                    return { nodes: () => rows };
                }, page: { info: () => ({ start: 20, page: 2, pages: 99 }) } }) });
                window.__reportsConfig = { coordinatorName: 'Test Professor', preparedByRole: 'OJT Professor', useAppliedFilters: true, classLabel: 'My Class' };
                const build = new Function(${JSON.stringify(builder + '\nreturn buildPrintHTML;')})();
                const original = document.createElement('div');
                original.innerHTML = build();
                original.querySelectorAll('img').forEach((image) => image.src = ${JSON.stringify(logo)});
                const html = await ReportPrint.paginate(original.innerHTML);
                document.getElementById('print-area-wrapper').innerHTML = html;
                document.getElementById('printPreviewContent').innerHTML = html;
                ReportPrint.fitPreview(document.getElementById('printPreviewContent'));
                const sheets = Array.from(document.querySelectorAll('#printPreviewContent .report-print-page'));
                return {
                    pages: sheets.length,
                    rows: sheets.reduce((total, sheet) => total + Array.from(sheet.querySelectorAll('tbody > tr')).filter((row) => row.children.length > 1).length, 0),
                    overflow: sheets.some((sheet) => sheet.lastElementChild.getBoundingClientRect().bottom > sheet.getBoundingClientRect().bottom + 2),
                    overflowingCells: sheets.some((sheet) => Array.from(sheet.querySelectorAll('td')).some((cell) => cell.scrollWidth > cell.clientWidth + 1)),
                    complete: sheets.every((sheet, index) => sheet.textContent.includes('Disclaimer') && sheet.textContent.includes('Page ' + (index + 1) + ' of ' + sheets.length)),
                    counters: sheets.map((sheet) => Number(sheet.querySelector('[data-report-page-records]').textContent)),
                    logos: sheets.map((sheet) => sheet.querySelectorAll('img').length),
                    previewFits: sheets.every((sheet) => sheet.getBoundingClientRect().right <= document.getElementById('printPreviewContent').getBoundingClientRect().right),
                };
            })()`);
            assert.equal(result.rows, count, `${kind}: no lost or duplicate records`);
            assert.equal(result.overflow, false, `${kind}: no overflowing forms`);
            assert.equal(result.overflowingCells, false, `${kind}: no overlapping cell text`);
            assert.equal(result.complete, true, `${kind}: repeated full forms and correct page numbers`);
            assert.equal(result.previewFits, true, `${kind}: preview fits without horizontal cropping`);
            assert.ok(result.logos.every((count) => count === 1), `${kind}: exactly one header logo per form`);
            assert.equal(result.counters.reduce((total, count) => total + count, 0), count);
            const pdf = await command('Page.printToPDF', { preferCSSPageSize: true, printBackground: true, displayHeaderFooter: false });
            const buffer = Buffer.from(pdf.data, 'base64');
            const physicalPages = (buffer.toString('latin1').match(/\/Type\s*\/Page\b/g) || []).length;
            assert.equal(physicalPages, result.pages, `${kind}: one physical PDF sheet per complete form`);
            fs.writeFileSync(path.join(output, `${kind}-${count}-records.pdf`), buffer);
            if (count === 35) {
                await evaluate("ReportPrint.fitPreview(document.getElementById('printPreviewContent'))");
                await command('Emulation.setDeviceMetricsOverride', { width: 1200, height: 1700, deviceScaleFactor: 1, mobile: false });
                const screenshot = await command('Page.captureScreenshot', { format: 'png', captureBeyondViewport: false });
                fs.writeFileSync(path.join(output, `${kind}-preview.png`), Buffer.from(screenshot.data, 'base64'));
            }
            console.log(`PASS ${kind}: ${count} records, ${result.pages} complete forms, ${physicalPages} PDF pages`);
        }
    }
    await require('./test-remaining-report-print.cjs')({ root, command, evaluate, css, helper, output });
}

main().catch((error) => { console.error(error); process.exitCode = 1; }).finally(async () => {
    if (socket?.readyState === WebSocket.OPEN) {
        await command('Browser.close').catch(() => {});
        socket.close();
    }
    browser.kill();
    await new Promise((resolve) => setTimeout(resolve, 750));
    const resolved = path.resolve(profile);
    if (path.dirname(resolved) === path.resolve(os.tmpdir()) && path.basename(resolved).startsWith('internconnect-report-print-')) {
        try {
            fs.rmSync(resolved, { recursive: true, force: true, maxRetries: 8, retryDelay: 250 });
        } catch (error) {
            console.warn(`Temporary browser profile could not be cleaned up: ${error.code}`);
        }
    }
});
