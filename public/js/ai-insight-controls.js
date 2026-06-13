(function () {
    function listItems(items, emptyText) {
        const list = Array.isArray(items) && items.length ? items : [emptyText];
        return list.map(function (item) {
            const li = document.createElement('li');
            li.textContent = item;
            return li;
        });
    }

    function replaceList(target, items, emptyText) {
        if (!target) return;
        target.innerHTML = '';
        listItems(items, emptyText).forEach(function (li) {
            target.appendChild(li);
        });
    }

    function statusMessage(data) {
        if (data.source === 'fallback') {
            return data.availability && data.availability.message
                ? data.availability.message + ' Internal insight shown.'
                : 'Gemini is unavailable or rate-limited. Internal insight shown.';
        }

        if (data.source === 'manual') {
            return 'Internal insight shown. Click Generate AI Insight to use Gemini.';
        }

        return 'AI insight generated.';
    }

    function ensureNotice(card, summary) {
        let notice = card.querySelector('[data-ai-notice]');
        if (notice) return notice;

        notice = document.createElement('div');
        notice.setAttribute('data-ai-notice', '');
        notice.style.cssText = 'display:none; align-items:flex-start; gap:10px; background:#fffbeb; border:1px solid #fde68a; border-left:4px solid #f59e0b; color:#92400e; border-radius:10px; padding:11px 13px; margin-bottom:14px; font-size:12.5px; line-height:1.55;';

        const icon = document.createElement('i');
        icon.className = 'fa fa-exclamation-triangle';
        icon.style.marginTop = '2px';

        const text = document.createElement('div');
        text.innerHTML = '<strong>Gemini is temporarily unavailable.</strong> <span data-ai-notice-text>Internal insight is shown for now.</span>';

        notice.appendChild(icon);
        notice.appendChild(text);

        if (summary && summary.parentNode) {
            summary.parentNode.insertBefore(notice, summary);
        }

        return notice;
    }

    function ensureCloseButton(resultPanel) {
        if (!resultPanel || resultPanel.querySelector('[data-ai-close-panel]')) return;

        const wrap = document.createElement('div');
        wrap.style.cssText = 'display:flex; justify-content:flex-end; margin-bottom:10px;';

        const button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('data-ai-close-panel', '');
        button.style.cssText = 'display:inline-flex; align-items:center; gap:7px; border:1px solid #e5e7eb; background:#fff; color:#374151; border-radius:9px; padding:7px 11px; font:inherit; font-size:12px; font-weight:800; cursor:pointer;';
        button.innerHTML = '<i class="fa fa-times"></i> Close';
        button.addEventListener('click', function () {
            resultPanel.style.display = 'none';
        });

        wrap.appendChild(button);
        resultPanel.insertBefore(wrap, resultPanel.firstChild);
    }

    function bindInsightButton(button) {
        button.addEventListener('click', function () {
            const card = button.closest('[data-ai-insight-card]');
            const contextName = button.getAttribute('data-ai-context');
            const endpoint = button.getAttribute('data-ai-endpoint');
            const token = button.getAttribute('data-ai-token');
            const context = contextName && window[contextName] ? window[contextName] : null;

            if (!card || !context || !endpoint || !token) return;

            const resultPanel = card.querySelector('[data-ai-result-panel]');
            const status = card.querySelector('[data-ai-status]');
            const badge = card.querySelector('[data-ai-badge]');
            const summary = card.querySelector('[data-ai-summary]');
            const findings = card.querySelector('[data-ai-findings]');
            const watchouts = card.querySelector('[data-ai-watchouts]');
            const actions = card.querySelector('[data-ai-actions]');
            const notice = ensureNotice(card, summary);

            button.disabled = true;
            if (resultPanel) {
                ensureCloseButton(resultPanel);
                resultPanel.style.display = 'block';
            }
            if (status) {
                status.textContent = 'Generating AI insight...';
                status.style.display = 'block';
            }

            fetch(endpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    report_type: context.report_type,
                    metrics: context.metrics || {},
                    highlights: (context.insight && context.insight.key_findings) || [],
                    watchouts: (context.insight && context.insight.watchouts) || [],
                    actions: (context.insight && context.insight.recommendations) || []
                })
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('AI insight request failed.');
                    return response.json();
                })
                .then(function (data) {
                    context.insight = data;

                    if (summary) summary.textContent = data.summary || 'No AI insight available.';
                    replaceList(findings, data.key_findings, 'No key findings available.');
                    replaceList(watchouts, data.watchouts, 'No major watchouts detected.');
                    replaceList(actions, data.recommendations, 'No actions suggested.');

                    if (badge) {
                        badge.textContent = data.source === 'gemini'
                            ? 'Gemini AI'
                            : (data.source === 'openai' ? 'OpenAI' : 'Internal Insight');
                    }

                    if (notice) {
                        notice.style.display = data.source === 'fallback' ? 'flex' : 'none';
                        const noticeText = notice.querySelector('[data-ai-notice-text]');
                        if (noticeText) {
                            noticeText.textContent = data.availability && data.availability.message
                                ? data.availability.message
                                : 'Internal insight is shown for now. Try again in a few minutes, or later if the daily free-tier quota was reached.';
                        }
                    }

                    if (status) {
                        status.textContent = statusMessage(data);
                        status.style.display = 'block';
                    }
                })
                .catch(function () {
                    if (status) {
                        status.textContent = 'AI insight could not be generated right now. Please try again later.';
                        status.style.display = 'block';
                    }
                })
                .finally(function () {
                    button.disabled = false;
                });
        });
    }

    document.querySelectorAll('[data-ai-insight-button]').forEach(bindInsightButton);
})();
