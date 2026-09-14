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

    function applyShimmer(card) {
        const summary = card.querySelector('[data-ai-summary]');
        const findings = card.querySelector('[data-ai-findings]');
        const watchouts = card.querySelector('[data-ai-watchouts]');
        const actions = card.querySelector('[data-ai-actions]');

        if (summary) {
            summary.innerHTML = '<div class="bud-shimmer" style="height:18px; width:92%; margin-bottom:8px;"></div><div class="bud-shimmer" style="height:18px; width:76%;"></div>';
        }
        if (findings) {
            findings.innerHTML = '<li class="bud-shimmer" style="height:16px; width:88%; margin-bottom:6px;"></li><li class="bud-shimmer" style="height:16px; width:70%;"></li>';
        }
        if (watchouts) {
            watchouts.innerHTML = '<li class="bud-shimmer" style="height:16px; width:85%; margin-bottom:6px;"></li><li class="bud-shimmer" style="height:16px; width:65%;"></li>';
        }
        if (actions) {
            actions.innerHTML = '<li class="bud-shimmer" style="height:16px; width:90%; margin-bottom:6px;"></li><li class="bud-shimmer" style="height:16px; width:75%;"></li>';
        }
    }

    function statusMessage(data) {
        if (data.source === 'gemini') {
            return '✓ AI insights generated via Google Gemini';
        }
        if (data.source === 'openai') {
            return '✓ AI insights generated via OpenAI';
        }
        if (data.source === 'fallback') {
            return data.availability && data.availability.message
                ? data.availability.message + ' Internal insight shown.'
                : 'Internal analytical insight active.';
        }
        return 'Internal analytical insight active.';
    }

    function ensureCloseButton(resultPanel) {
        if (!resultPanel || resultPanel.querySelector('[data-ai-close-panel]')) return;

        const wrap = document.createElement('div');
        wrap.style.cssText = 'display:flex; justify-content:flex-end; margin-bottom:12px;';

        const button = document.createElement('button');
        button.type = 'button';
        button.setAttribute('data-ai-close-panel', '');
        button.style.cssText = 'display:inline-flex; align-items:center; gap:6px; border:1px solid #e2e8f0; background:#fff; color:#475569; border-radius:8px; padding:6px 12px; font-family:\'Poppins\',sans-serif; font-size:11.5px; font-weight:700; cursor:pointer; transition:all .2s ease;';
        button.innerHTML = '<i class="fa fa-chevron-up"></i> Hide Insight';
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

            const originalBtnHtml = button.innerHTML;
            button.disabled = true;
            button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Analyzing...';

            if (resultPanel) {
                ensureCloseButton(resultPanel);
                resultPanel.style.display = 'block';
                applyShimmer(card);
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

                    if (summary) {
                        summary.innerHTML = '';
                        summary.textContent = data.summary || 'No AI insight available.';
                    }
                    replaceList(findings, data.key_findings, 'No key findings available.');
                    replaceList(watchouts, data.watchouts, 'No major watchouts detected.');
                    replaceList(actions, data.recommendations, 'No actions suggested.');

                    if (badge) {
                        badge.textContent = data.source === 'gemini'
                            ? 'Gemini AI'
                            : (data.source === 'openai' ? 'OpenAI' : 'Internal Insight');
                    }

                    if (status) {
                        status.textContent = statusMessage(data);
                        status.style.display = 'block';
                    }
                })
                .catch(function () {
                    if (status) {
                        status.textContent = 'Internal insight shown. (AI service busy)';
                        status.style.display = 'block';
                    }
                })
                .finally(function () {
                    button.disabled = false;
                    button.innerHTML = '<i class="fa fa-magic"></i> Refresh AI Insight';
                });
        });
    }

    document.querySelectorAll('[data-ai-insight-button]').forEach(bindInsightButton);
})();
