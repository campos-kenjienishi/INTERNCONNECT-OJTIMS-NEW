const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const { execFileSync } = require('node:child_process');

module.exports = async ({ root, command, evaluate, css, helper, output }) => {
    const read = file => fs.readFileSync(path.join(root, file), 'utf8');
    const fixture = (view, count) => execFileSync('php', ['scripts/report-print-fixture.php', view, String(count)], {cwd:root, encoding:'utf8',maxBuffer:5000000});
    const load = async html => {
        const frame = (await command('Page.getFrameTree')).frameTree.frame.id;
        await command('Page.setDocumentContent', {frameId:frame, html:html
            .replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi, '').replace(/<link\b[^>]*>/gi, '')
            .replace(/src="[^"]+"/g, 'src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"')
            .replace('</head>', `<style>${css}</style></head>`)});
        await evaluate(helper);
    };
    const verify = async (name, expectedRows) => {
        const result = await evaluate(`(() => {
            const sheets = Array.from(document.querySelectorAll('#print-area-wrapper .report-print-page'));
            return {pages:sheets.length,
                overflow:sheets.some(sheet => sheet.scrollHeight > sheet.clientHeight + 1),
                rows:sheets.reduce((total,sheet)=>total+sheet.querySelectorAll('tbody > tr').length,0),
                complete:sheets.every((sheet,index)=>sheet.textContent.includes('Disclaimer') && sheet.textContent.includes('Page '+(index+1)+' of '+sheets.length))};
        })()`);
        assert.equal(result.overflow,false,`${name}: no overflow`);
        assert.equal(result.complete,true,`${name}: complete footer and numbering`);
        if (expectedRows !== undefined) assert.equal(result.rows,expectedRows,`${name}: all records preserved`);
        const pdf = await command('Page.printToPDF',{preferCSSPageSize:true,printBackground:true,displayHeaderFooter:false});
        const buffer = Buffer.from(pdf.data,'base64');
        const pages = (buffer.toString('latin1').match(/\/Type\s*\/Page\b/g)||[]).length;
        assert.equal(pages,result.pages,`${name}: one form per PDF page`);
        fs.writeFileSync(path.join(output,`${name}.pdf`),buffer);
        console.log(`PASS ${name}: ${result.pages} forms, ${pages} physical PDF pages`);
    };
    for (const count of [0,35]) {
        for (const view of ['professor.evaluation_print','professor.analytics_print']) {
            await load(fixture(view,count));
            await evaluate(read('public/js/report-print-document.js'));
            await evaluate('window.prepareReportPrint()');
            await verify(`${view}-${count}`,view.includes('evaluation')?Math.max(count,1):undefined);
        }
    }
    for (const [kind,file,name,start,end] of [
        ['coordinator-analytics','public/js/coordinator/analytics.js','buildCoordinatorPrintHTML','function buildCoordinatorPrintHTML()',"window.ReportPrint.bindPrint"],
        ['professor-analytics-fallback','public/js/professor/analytics.js','buildAnalyticsPrintHTML','function buildAnalyticsPrintHTML()',"document.getElementById('printBtn')"],
        ['requirements','public/js/professor/requirement-status.js','buildRequirementPrintHTML','function escapeReportHtml','window.ReportPrint.bindPrint']
    ]) {
        const source = read(file);
        const builder = source.slice(source.indexOf(start),source.indexOf(end,source.indexOf(start)));
        await load('<html><head></head><body><div id="print-area-wrapper"></div></body></html>');
        await evaluate(`(async () => {
            const escapeHtml = value => String(value ?? '').replace(/[&<>"']/g,'_');
            const entries = Array.from({length:35},(_,index)=>({label:'Fixture '+index,count:5,percentage:10,total_students:5,submitted:3,completion:60}));
            const analyticsPrintData = {courseAnalytics:entries,topCompanies:entries,studentStatusAnalytics:entries,fileStatusAnalytics:entries,placementAnalytics:entries,moaStatusAnalytics:entries};
            window.professorAnalyticsConfig = {classAnalytics:entries,fileMetrics:entries};
            window.requirementReportData = {activeView:'overview',course:'BSIT',room:'Class A',schoolYear:'2026-2027',totalStudents:35,categoryCount:2,completeStudents:0,averageCompletion:50,rows:entries.map((entry,index)=>({studentName:'Fixture Student '+index,studentNumber:'TEST-'+index,completion:50,submitted:['Resume'],missing:['Consent'],approvedCount:1,pendingCount:0,deniedCount:0}))};
            ${builder}
            const html = ${name}().replace(/src="[^"]+"/g,'src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7"');
            document.getElementById('print-area-wrapper').innerHTML = await ReportPrint.paginate(html,${kind==='requirements'?'{pageLabel:true}':'{sections:true}'});
        })()`);
        await verify(kind,kind==='requirements'?35:(kind==='coordinator-analytics'?210:74));
    }
    const html = fixture('reports.analytics_export',24);
    assert.equal((html.match(/class="export-page"/g)||[]).length,3);
    const serverPdf = execFileSync('php',['scripts/report-print-fixture.php','reports.analytics_export','24','pdf'],{cwd:root,maxBuffer:5000000});
    assert.equal((serverPdf.toString('latin1').match(/\/Type\s*\/Page\b/g)||[]).length,3);
    fs.writeFileSync(path.join(output,'analytics-export-24-months.pdf'),serverPdf);
    console.log('PASS server analytics export: three physical PDF pages for 24 months');
    const voucher = fixture('students.voucher',1).replace('</head>',`<style>${read('public/css/student/voucher.css')}</style></head>`);
    const frame = (await command('Page.getFrameTree')).frameTree.frame.id;
    await command('Page.setDocumentContent',{frameId:frame,html:voucher.replace(/<script\b[^>]*>[\s\S]*?<\/script>/gi,'').replace(/<link\b[^>]*>/gi,'')});
    const voucherPdf = await command('Page.printToPDF',{preferCSSPageSize:true,printBackground:true});
    const voucherBuffer = Buffer.from(voucherPdf.data,'base64');
    assert.equal((voucherBuffer.toString('latin1').match(/\/Type\s*\/Page\b/g)||[]).length,1);
    fs.writeFileSync(path.join(output,'voucher.pdf'),voucherBuffer);
    console.log('PASS voucher: one physical portrait PDF page');
};
