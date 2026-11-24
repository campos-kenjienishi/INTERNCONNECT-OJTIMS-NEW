(() => {
    async function paginate(html, options = {}) {
        await document.fonts?.ready;
        const source = document.createElement('template');
        source.innerHTML = html.trim();
        const template = source.content.firstElementChild;
        if (!template) {
            throw new Error('The report table could not be prepared. Please reload and try again.');
        }
        if (options.sections) return paginateSections(template, options);
        if (!template.querySelector('table tbody')) throw new Error('The report table is missing.');
        const rows = Array.from(template.querySelectorAll('table tbody > tr'));
        template.querySelector('tbody').replaceChildren();
        preparePage(template, options);
        if (options.pageLabel) {
            template.lastElementChild.classList.add('report-print-footer');
            const label = document.createElement('div');
            label.className = 'report-print-page-label';
            label.textContent = 'Page 1 of 1';
            template.append(label);
        }
        const measure = document.createElement('div');
        measure.className = 'report-print-measure';
        document.body.append(measure);
        const pages = [];
        const addPage = () => {
            const page = template.cloneNode(true);
            measure.replaceChildren(page);
            pages.push(page);
            return page;
        };
        try {
            let page = addPage();
            await Promise.all(Array.from(page.querySelectorAll('img')).map((image) => image.decode().catch(() => {})));
            for (const row of rows) {
                let body = page.querySelector('tbody');
                body.append(row);
                if (page.scrollHeight > page.clientHeight + 1) {
                    row.remove();
                    if (body.children.length) {
                        page = addPage();
                        body = page.querySelector('tbody');
                    }
                    body.append(row);
                    if (page.scrollHeight > page.clientHeight + 1) {
                        throw new Error('One record is too tall for a complete A4 form. Please shorten its unusually long text before printing. No records were omitted.');
                    }
                }
            }
            pages.forEach((sheet, index) => {
                sheet.querySelectorAll('[data-report-page-records]').forEach((counter) => {
                    counter.textContent = Array.from(sheet.querySelectorAll('tbody > tr')).filter((row) => row.children.length > 1).length;
                });
                const walker = document.createTreeWalker(sheet, NodeFilter.SHOW_TEXT);
                while (walker.nextNode()) {
                    if (walker.currentNode.parentElement.closest('tbody')) continue;
                    walker.currentNode.textContent = walker.currentNode.textContent.replace(/Page \d+(?: of \d+)?/g, (label) =>
                        label.includes(' of ') ? `Page ${index + 1} of ${pages.length}` : `Page ${index + 1}`);
                }
            });
            return pages.map((sheet) => sheet.outerHTML).join('');
        } finally {
            measure.remove();
        }
    }

    function preparePage(page, options) {
        page.removeAttribute('id');
        page.classList.add('report-print-page');
        page.lastElementChild?.querySelectorAll('img').forEach((image) => image.remove());
        page.querySelectorAll('img').forEach((image) => {
            image.style.filter = 'none';
        });
        if (options.orientation === 'portrait') page.classList.add('report-print-portrait');
    }

    function fitPreview(container) {
        const page = container.querySelector('.report-print-page');
        if (!page || !container.clientWidth) return;
        const styles = getComputedStyle(container);
        const available = container.clientWidth - parseFloat(styles.paddingLeft) - parseFloat(styles.paddingRight);
        const scale = Math.min(1, available / page.offsetWidth);
        container.style.setProperty('--report-preview-scale', scale);
        container.querySelectorAll('.report-print-page').forEach((sheet) => {
            let wrapper = sheet.parentElement;
            if (!wrapper.classList.contains('report-preview-sheet')) {
                wrapper = document.createElement('div');
                wrapper.className = 'report-preview-sheet';
                sheet.before(wrapper);
                wrapper.append(sheet);
            }
            wrapper.style.width = `${sheet.offsetWidth * scale}px`;
            wrapper.style.height = `${sheet.offsetHeight * scale}px`;
        });
    }

    async function paginateSections(template, options) {
        const content = template.querySelector('[data-report-content]');
        const children = Array.from(template.children);
        const sections = content ? Array.from(content.children) : children.slice(2, -2);
        const blocks = [];
        const collect = (section) => {
            const grid = section.matches('.grid-2') || section.style.display === 'grid';
            if (grid || (section.children.length === 1 && section.firstElementChild.style.display === 'grid')) {
                Array.from(grid ? section.children : section.firstElementChild.children).forEach(collect);
            } else blocks.push(section.cloneNode(true));
        };
        sections.forEach(collect);
        if (content) content.replaceChildren();
        else {
            sections.forEach((section) => section.remove());
            const container = document.createElement('div');
            container.setAttribute('data-report-content', '');
            template.insertBefore(container, template.children[2]);
        }
        preparePage(template, options);
        const reportContent = template.querySelector('[data-report-content]');
        reportContent.nextElementSibling?.classList.add('report-print-footer');
        const pageLabel = document.createElement('div');
        pageLabel.className = 'report-print-page-label';
        template.append(pageLabel);
        const measure = document.createElement('div');
        measure.className = 'report-print-measure';
        document.body.append(measure);
        const pages = [];
        const addPage = () => {
            const page = template.cloneNode(true);
            measure.replaceChildren(page);
            pages.push(page);
            return page;
        };
        const overflow = (page) => page.scrollHeight > page.clientHeight + 1;
        try {
            let page = addPage();
            await Promise.all(Array.from(page.querySelectorAll('img')).map((image) => image.decode().catch(() => {})));
            for (const block of blocks) {
                let target = page.querySelector('[data-report-content]');
                const rows = Array.from(block.querySelectorAll('tbody > tr'));
                const splittable = block.querySelectorAll('table').length === 1 && rows.length > 0;
                if (!splittable) {
                    target.append(block);
                    if (overflow(page)) {
                        block.remove();
                        if (target.children.length) page = addPage();
                        page.querySelector('[data-report-content]').append(block);
                        if (overflow(page)) throw new Error('A report section is too tall for A4. No content was omitted. Please shorten the unusually long section.');
                    }
                    continue;
                }
                block.querySelector('tbody').replaceChildren();
                let fragment = block.cloneNode(true);
                target.append(fragment);
                for (const row of rows) {
                    fragment.querySelector('tbody').append(row);
                    if (overflow(page)) {
                        row.remove();
                        if (!fragment.querySelector('tbody').children.length) fragment.remove();
                        if (target.children.length) page = addPage();
                        target = page.querySelector('[data-report-content]');
                        fragment = block.cloneNode(true);
                        target.append(fragment);
                        fragment.querySelector('tbody').append(row);
                        if (overflow(page)) throw new Error('One report record is too tall for A4. No records were omitted.');
                    }
                }
            }
            pages.forEach((sheet, index) => {
                sheet.querySelector('.report-print-page-label').textContent = `Page ${index + 1} of ${pages.length}`;
            });
            return pages.map((sheet) => sheet.outerHTML).join('');
        } finally {
            measure.remove();
        }
    }

    async function printReport(buildReport, options = {}) {
        let wrapper = document.getElementById('print-area-wrapper');
        if (!wrapper) {
            wrapper = document.createElement('div');
            wrapper.id = 'print-area-wrapper';
            document.body.append(wrapper);
        }
        wrapper.innerHTML = await paginate(buildReport(), options);
        await Promise.all(Array.from(wrapper.querySelectorAll('img')).map((image) => image.decode().catch(() => {})));
        window.print();
    }

    function bindPrint(buttonId, buildReport, options = {}) {
        const button = document.getElementById(buttonId);
        button?.addEventListener('click', async () => {
            if (button.disabled) return;
            button.disabled = true;
            try {
                await printReport(buildReport, options);
            } catch (error) {
                window.alert(error.message || 'The report could not be prepared.');
            } finally {
                button.disabled = false;
            }
        });
    }

    function bind(buildReport, showPreview) {
        let busy = false;
        const previewButton = document.getElementById('openPreviewBtn');
        const printButton = document.getElementById('doPrintBtn');
        const run = async (printing) => {
            if (busy) return;
            busy = true;
            previewButton.disabled = true;
            printButton.disabled = true;
            try {
                const html = await paginate(buildReport());
                document.getElementById('printPreviewContent').innerHTML = html;
                fitPreview(document.getElementById('printPreviewContent'));
                if (printing) {
                    const wrapper = document.getElementById('print-area-wrapper');
                    wrapper.innerHTML = html;
                    await Promise.all(Array.from(wrapper.querySelectorAll('img')).map((image) => image.decode().catch(() => {})));
                    window.print();
                } else {
                    showPreview();
                }
            } catch (error) {
                window.alert(error.message || 'The report could not be prepared. Please try again.');
            } finally {
                busy = false;
                previewButton.disabled = false;
                printButton.disabled = false;
            }
        };
        previewButton.addEventListener('click', () => run(false));
        printButton.addEventListener('click', () => run(true));
        const preview = document.getElementById('printPreviewContent');
        const observer = new ResizeObserver(() => fitPreview(preview));
        observer.observe(preview);
        preview.closest('.modal')?.addEventListener('shown.bs.modal', () => fitPreview(preview));
        window.addEventListener('afterprint', () => document.getElementById('print-area-wrapper').replaceChildren());
    }

    window.ReportPrint = { paginate, bind, print: printReport, bindPrint, fitPreview };
})();
