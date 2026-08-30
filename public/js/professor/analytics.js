/* Professor Analytics Scripts */

// Replace your existing sidebar toggle script
(function () {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    if (!sidebar || !menuToggle) return;

    const closeMobileSidebar = function () {
        sidebar.classList.remove('mobile-open');
        if (overlay) overlay.classList.remove('active');
        document.body.classList.remove('mobile-sidebar-open');
    };

    menuToggle.addEventListener('click', function (event) {
        event.stopPropagation();
        const isMobile = window.innerWidth <= 900;

        if (isMobile) {
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                sidebar.classList.add('mobile-open');
                if (overlay) overlay.classList.add('active');
                document.body.classList.add('mobile-sidebar-open');
            }
        } else {
            sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
        }
    });

    if (overlay) {
        overlay.addEventListener('click', closeMobileSidebar);
    }

    ['click', 'touchstart'].forEach(function (eventName) {
        document.addEventListener(eventName, function (event) {
            if (window.innerWidth > 900 || !sidebar.classList.contains('mobile-open')) {
                return;
            }

            const clickedInsideSidebar = sidebar.contains(event.target);
            const clickedMenuToggle = menuToggle.contains(event.target);

            if (!clickedInsideSidebar && !clickedMenuToggle) {
                closeMobileSidebar();
            }
        });
    });

    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        }
    });
})();


(function () {
    // Prepare data from Blade collection
    // Chart instance
    let monthlyChart = null;

    function createOrUpdateChart(labels, sentData, submittedData) {
        const ctx = document.getElementById('monthlyActivityChart');
        if (!ctx) return;

        const datasets = [
            {
                label: 'Evaluation Requests Sent',
                data: sentData,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,0.08)',
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
            },
            {
                label: 'Evaluation Responses Submitted',
                data: submittedData,
                borderColor: '#16a34a',
                backgroundColor: 'rgba(16,163,74,0.08)',
                tension: 0.3,
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
            }
        ];

        if (monthlyChart) {
            monthlyChart.data.labels = labels;
            monthlyChart.data.datasets[0].data = sentData;
            monthlyChart.data.datasets[1].data = submittedData;
            monthlyChart.update();
            return;
        }

        monthlyChart = new Chart(ctx.getContext('2d'), {
            type: 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { position: 'top' }, tooltip: { mode: 'index', intersect: false } },
                scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { precision: 0 } } },
                onClick: (evt, elems) => {
                    if (elems.length > 0) {
                        const idx = elems[0].index;
                        const label = labels[idx];
                        const [month, year] = label.split(' ');
                        const monthNum = new Date(Date.parse(month + ' 1')).getMonth() + 1;
                        window.drilldownYear = year;
                        window.drilldownMonth = monthNum;
                        openDrilldownModal(label, year, monthNum);
                    }
                }
            }
        });
    }

    async function fetchMonthlyData(params = {}, opts = { showLoading: true }) {
        const applyBtn = document.getElementById('applyFilters');
        const spinner = applyBtn?.querySelector('.ic-spinner');
        if (opts.showLoading && applyBtn) { applyBtn.disabled = true; if (spinner) spinner.style.display = 'inline-block'; }
        const url = new URL(window.professorAnalyticsConfig?.dataUrl || '/professor/analytics/data', window.location.origin);
        Object.keys(params).forEach(k => { if (params[k] !== undefined && params[k] !== null && params[k] !== '') url.searchParams.set(k, params[k]); });
        try {
            const res = await fetch(url.toString(), { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Failed to fetch');
            const json = await res.json();
            createOrUpdateChart(json.labels || [], json.sent || [], json.submitted || []);
        } catch (e) {
            console.error('Monthly data load error', e);
        } finally {
            if (opts.showLoading && applyBtn) { applyBtn.disabled = false; if (spinner) spinner.style.display = 'none'; }
        }
    }

    // controls
    (function setupFilters() {
        const classFilter = document.getElementById('classFilter');
        const startInput = document.getElementById('startMonth');
        const endInput = document.getElementById('endMonth');
        const applyBtn = document.getElementById('applyFilters');

        // restore from localStorage
        try {
            const stored = JSON.parse(localStorage.getItem('prof_analytics_filters') || 'null');
            if (stored) {
                if (stored.classId && classFilter) classFilter.value = stored.classId;
                if (stored.start && startInput) startInput.value = stored.start;
                if (stored.end && endInput) endInput.value = stored.end;
            }
        } catch (e) { }

        applyBtn?.addEventListener('click', function () {
            const classId = classFilter?.value || '';
            const startMonth = startInput?.value || '';
            const endMonth = endInput?.value || '';
            try { localStorage.setItem('prof_analytics_filters', JSON.stringify({ classId: classId, start: startMonth, end: endMonth })); } catch (e) { }
            const start = startMonth ? startMonth + '-01' : '';
            const end = endMonth ? endMonth + '-01' : '';
            fetchMonthlyData({ class_id: classId, start: start ? start : undefined, end: end ? end : undefined }, { showLoading: true });
        });
    })();

    // load initial data (use restored filters if any)
    (function initLoad() {
        try {
            const stored = JSON.parse(localStorage.getItem('prof_analytics_filters') || 'null') || {};
            const params = {};
            if (stored.classId) params.class_id = stored.classId;
            if (stored.start) params.start = stored.start + '-01';
            if (stored.end) params.end = stored.end + '-01';
            fetchMonthlyData(params, { showLoading: true });
        } catch (e) {
            fetchMonthlyData({}, { showLoading: true });
        }
    });

    function escapeHtml(text) {
        return String(text ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    const cfg = window.professorAnalyticsConfig || {};
    const analyticsPrintData = {
        title: 'Professor Analytics Report',
        subtitle: cfg.subtitle || '',
        summary: cfg.summary || 'No insight available.',
        generatedAt: cfg.generatedAt || '',
        totalStudents: cfg.totalStudents || 0,
        classCount: cfg.classCount || 0,
        submittedRequests: cfg.submittedRequests || 0,
        templateCount: cfg.templateCount || 0,
        classAnalytics: cfg.classAnalytics || [],
        requestAnalytics: cfg.requestAnalytics || [],
        fileMetrics: cfg.fileMetrics || [],
    };

    window.analyticsAiContext = {
        report_type: 'professor_analytics',
        metrics: cfg.payload || {},
        insight: cfg.insight || []
    };

    const analyticsAiContext = window.analyticsAiContext;

    function renderAnalyticsAiAnswer(data) {
        const answerBox = document.getElementById('analyticsAiAnswerBox');
        const answerText = document.getElementById('analyticsAiAnswerText');
        const answerSteps = document.getElementById('analyticsAiAnswerSteps');
        const answerSource = document.getElementById('analyticsAiAnswerSource');

        if (!answerBox || !answerText || !answerSteps || !answerSource) return;

        answerText.textContent = data.answer || 'No answer was generated.';
        answerSource.textContent = data.source === 'gemini' ? 'Gemini AI' : (data.source === 'openai' ? 'OpenAI' : 'Internal Insight');
        answerSteps.innerHTML = '';
        (data.next_steps || []).forEach(function (step) {
            const li = document.createElement('li');
            li.textContent = step;
            answerSteps.appendChild(li);
        });
        answerBox.style.display = 'block';
    }

    function askAnalyticsAi(question) {
        const status = document.getElementById('analyticsAiAskStatus');
        const button = document.getElementById('analyticsAskAiBtn');

        if (!question.trim()) {
            if (status) {
                status.textContent = 'Type a question first.';
                status.style.display = 'block';
            }
            return;
        }

        if (button) button.disabled = true;
        if (status) {
            status.textContent = 'Asking AI...';
            status.style.display = 'block';
        }

        fetch((window.professorAnalyticsConfig?.aiAskUrl || '/reports/ai/ask'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (window.professorAnalyticsConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
            },
            body: JSON.stringify({
                question: question,
                report_type: analyticsAiContext.report_type,
                metrics: analyticsAiContext.metrics,
                insight: analyticsAiContext.insight
            })
        })
            .then(function (response) {
                if (!response.ok) throw new Error('AI request failed.');
                return response.json();
            })
            .then(function (data) {
                renderAnalyticsAiAnswer(data);
                if (status) {
                    status.textContent = data.source === 'fallback'
                        ? ((data.availability && data.availability.message) ? data.availability.message + ' Internal answer shown.' : 'Gemini is unavailable or rate-limited. Internal answer shown; try again in a few minutes, or later if daily quota was reached.')
                        : 'Answer generated.';
                }
            })
            .catch(function () {
                if (status) {
                    status.textContent = 'AI could not answer right now. Please try again later.';
                    status.style.display = 'block';
                }
            })
            .finally(function () {
                if (button) button.disabled = false;
            });
    }

    document.querySelectorAll('.analytics-ai-quick-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById('analyticsAiQuestionInput');
            const question = btn.getAttribute('data-question') || '';
            if (input) input.value = question;
            askAnalyticsAi(question);
        });
    });

    const analyticsAskAiBtn = document.getElementById('analyticsAskAiBtn');
    if (analyticsAskAiBtn) {
        analyticsAskAiBtn.addEventListener('click', function () {
            const input = document.getElementById('analyticsAiQuestionInput');
            askAnalyticsAi(input ? input.value : '');
        });
    }

    const analyticsAiCard = document.querySelector('[data-ai-insight-card]');
    const analyticsPageHeader = document.querySelector('.page-header');
    if (analyticsAiCard && analyticsPageHeader) {
        analyticsAiCard.style.marginTop = '0';
        analyticsAiCard.style.marginBottom = '24px';
        analyticsPageHeader.insertAdjacentElement('afterend', analyticsAiCard);
    }

    function buildAnalyticsPrintHTML() {
        const now = new Date();
        const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
        const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });
        const cfg = window.professorAnalyticsConfig || {};

        const classRows = (cfg.classAnalytics || []).map((room, index) => `
            <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#111827;">${escapeHtml(room.label)}</td>
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">${room.total_students ?? 0}</td>
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">${room.submitted ?? 0}</td>
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#991b1b; font-weight:700;">${room.completion ?? 0}%</td>
            </tr>
        `).join('');

        const studentRows = `
            <tr style="background:#ffffff;"><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">Approved students</td><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${cfg.approvedStudents || 0}</td></tr>
            <tr style="background:#f9fafb;"><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">Pending students</td><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${cfg.pendingApprovals || 0}</td></tr>
            <tr style="background:#ffffff;"><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">Denied students</td><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${cfg.deniedStudents || 0}</td></tr>
            <tr style="background:#f9fafb;"><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">Inactive students</td><td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${cfg.inactiveStudents || 0}</td></tr>
        `;

        const fileRows = (cfg.fileMetrics || []).map((metric, index) => `
            <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#111827;">${escapeHtml(metric.label)}</td>
                <td style="padding:8px 10px; border:1px solid #e5e7eb; font-size:10px; color:#374151;">${metric.count ?? 0}</td>
            </tr>
        `).join('');

        return `
            <div style="font-family:'Poppins',Arial,sans-serif; background:#fff; color:#111827;">
                <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
                    <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
                    <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                        <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);" alt="PUP">
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - Taguig Campus</div>
                            <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Professor Analytics Report</div>
                            <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">PUP Taguig Campus</div>
                        </div>
                    </div>
                    <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
                </div>

                <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                        <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                            <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                            <span style="color:#6b7280;">Professor:</span>
                            <strong style="color:#111827;">${escapeHtml(cfg.subtitle || '')}</strong>
                        </div>
                        <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                            <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                            <span style="color:#6b7280;">Students:</span>
                            <strong style="color:#111827;">${escapeHtml(cfg.totalStudents ?? 0)}</strong>
                        </div>
                        <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                            <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                            <span style="color:#6b7280;">Submitted:</span>
                            <strong style="color:#111827;">${escapeHtml(cfg.submittedRequests ?? 0)}</strong>
                        </div>
                        <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                            <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                            <span style="color:#6b7280;">Generated:</span>
                            <strong style="color:#111827;">${dateStr} ${timeStr}</strong>
                        </div>
                    </div>
                    <div style="font-size:8.5px; color:#9ca3af;">Analytics snapshot</div>
                </div>

                <div style="padding:14px 22px 0 22px;">
                    <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px;">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                            <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Total Advisees</div>
                            <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(cfg.totalStudents ?? 0)}</div>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                            <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Active Classes</div>
                            <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(cfg.classCount ?? 0)}</div>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                            <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Submitted Evaluations</div>
                            <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(cfg.submittedRequests ?? 0)}</div>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                            <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">File Categories</div>
                            <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(cfg.templateCount ?? 0)}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:14px 22px 0 22px;">
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Student Standing</div>
                            <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th></tr></thead>
                                <tbody>${studentRows}</tbody>
                            </table>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Class Overview</div>
                            <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Class</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Submitted</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Completion</th></tr></thead>
                                <tbody>${classRows || '<tr><td colspan="4" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No classes found.</td></tr>'}</tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div style="padding:14px 22px 0 22px;">
                    <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Requirement Review</div>
                            <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th></tr></thead>
                                <tbody>${fileRows || '<tr><td colspan="2" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No file data found.</td></tr>'}</tbody>
                            </table>
                        </div>
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Analytics Insight</div>
                            <div style="font-size:11px; color:#374151; line-height:1.7;">${escapeHtml(cfg.summary || 'This printed report focuses on the current adviser snapshot, student standing, class overview, and file requirements.')}</div>
                        </div>
                    </div>
                </div>

                <div style="padding:18px 22px 12px 22px;">
                    <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                        <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                            <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                            <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.</div>
                        </div>
                    </div>
                </div>

                <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:6px;">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);" alt="PUP">
                        <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">© 1998–${now.getFullYear()} <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — InternConnect OJT IMS</span>
                    </div>
                    <div style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: PROF-ANA-${now.getFullYear()}</div>
                </div>
            </div>
        `;
    }

    document.getElementById('printBtn')?.addEventListener('click', function () {
        const wrapper = document.getElementById('print-area-wrapper');
        if (!wrapper) return;
        wrapper.innerHTML = buildAnalyticsPrintHTML();
        window.print();
        setTimeout(function () {
            wrapper.innerHTML = '';
        }, 1000);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && drilldownModal && drilldownModal.style.display === 'flex') {
            drilldownModal.style.display = 'none';
        }
    });

    // Drilldown modal functions
    const drilldownModal = document.getElementById('drilldownModal');
    let currentPage = 1;

    function openDrilldownModal(label, year, month) {
        document.getElementById('drilldownTitle').textContent = 'Requests Submitted - ' + label;
        currentPage = 1;
        fetchDrilldownData(year, month, 1);
        drilldownModal.style.display = 'flex';
    }

    async function fetchDrilldownData(year, month, page) {
        const classId = document.getElementById('classFilter')?.value || '';
        const status = document.getElementById('drilldownStatusFilter')?.value || '';
        const queryText = document.getElementById('drilldownSearch')?.value?.trim() || '';
        const url = new URL(window.professorAnalyticsConfig?.drilldownUrl || '/professor/analytics/drilldown', window.location.origin);
        url.searchParams.set('year', year);
        url.searchParams.set('month', month);
        if (classId) url.searchParams.set('class_id', classId);
        if (status) url.searchParams.set('status', status);
        if (queryText) url.searchParams.set('q', queryText);
        url.searchParams.set('page', page);

        try {
            const res = await fetch(url.toString(), { credentials: 'same-origin' });
            if (!res.ok) throw new Error('Failed to fetch');
            const json = await res.json();
            renderDrilldownTable(json);
        } catch (e) {
            console.error('Drilldown fetch error', e);
        }
    }

    function renderDrilldownTable(data) {
        const tbody = document.getElementById('drilldownTableBody');
        tbody.innerHTML = '';

        if (!data.data || data.data.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align:center;padding:16px;">No records found</td></tr>';
            return;
        }

        data.data.forEach(item => {
            const row = document.createElement('tr');
            row.innerHTML = `
                    <td style="padding:8px;">${item.student?.first_name} ${item.student?.last_name || ''}</td>
                    <td style="padding:8px;">${item.company || '-'}</td>
                    <td style="padding:8px;"><span style="background:#fee2e2;color:#dc2626;padding:2px 8px;border-radius:4px;font-size:11px;">${item.status}</span></td>
                    <td style="padding:8px;font-size:12px;color:#666;">${new Date(item.submitted_at).toLocaleDateString()}</td>
                `;
            tbody.appendChild(row);
        });

        document.getElementById('drilldownPaginationInfo').textContent = `Page ${data.current_page} of ${Math.ceil(data.total / data.per_page)}`;
        document.getElementById('drilldownPrevBtn').disabled = data.current_page === 1;
        document.getElementById('drilldownNextBtn').disabled = data.current_page >= Math.ceil(data.total / data.per_page);
        currentPage = data.current_page;
    }

    document.getElementById('drilldownCloseBtn')?.addEventListener('click', () => {
        drilldownModal.style.display = 'none';
    });

    document.getElementById('drilldownPrevBtn')?.addEventListener('click', () => {
        if (currentPage > 1) fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage - 1);
    });

    document.getElementById('drilldownNextBtn')?.addEventListener('click', () => {
        fetchDrilldownData(window.drilldownYear, window.drilldownMonth, currentPage + 1);
    });

    document.getElementById('drilldownSearchBtn')?.addEventListener('click', () => {
        currentPage = 1;
        fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
    });

    document.getElementById('drilldownSearch')?.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            currentPage = 1;
            fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
        }
    });

    document.getElementById('drilldownStatusFilter')?.addEventListener('change', () => {
        currentPage = 1;
        fetchDrilldownData(window.drilldownYear, window.drilldownMonth, 1);
    });

    window.addEventListener('click', (e) => {
        if (e.target === drilldownModal) drilldownModal.style.display = 'none';
    });

    // load initial data
    fetchMonthlyData();
})();
