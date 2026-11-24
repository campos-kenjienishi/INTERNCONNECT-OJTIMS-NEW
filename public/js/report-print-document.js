(() => {
    const source = document.querySelector('[data-print-document]');
    if (!source) return;
    window.prepareReportPrint = async () => {
        const report = source.cloneNode(true);
        const footer = report.querySelector('.footer-wrap');
        if (footer) report.append(footer);
        const html = await window.ReportPrint.paginate(report.outerHTML, {
            orientation: source.dataset.printOrientation || 'landscape',
            sections: source.hasAttribute('data-print-sections'),
            pageLabel: true
        });
        let wrapper = document.getElementById('print-area-wrapper');
        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.id = 'print-area-wrapper';
            document.body.append(wrapper);
        }
        wrapper.innerHTML = html;
        await Promise.all(Array.from(wrapper.querySelectorAll('img')).map((image) => image.decode().catch(() => {})));
    };
    const button = document.getElementById('printBtn');
    button?.addEventListener('click', async () => {
        if (button.disabled) return;
        button.disabled = true;
        try {
            await window.prepareReportPrint();
            window.print();
        } catch (error) {
            window.alert(error.message || 'The report could not be prepared.');
        } finally {
            button.disabled = false;
        }
    });
})();
