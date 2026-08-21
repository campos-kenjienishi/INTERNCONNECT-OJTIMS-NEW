/* ==========================================================================
   Coordinator Analytics Page Scripts
   Extracted from ojtCoordinator/analytics.blade.php
   ========================================================================== */

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

            fetch((window.analyticsAiContext && window.analyticsAiContext.askUrl) ? window.analyticsAiContext.askUrl : '/reports/ai/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (window.analyticsAiContext && window.analyticsAiContext.csrfToken) ? window.analyticsAiContext.csrfToken : (meta[name="csrf-token"].attr('content') || '')
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
        const analyticsHeading = document.querySelector('.heading');
        if (analyticsAiCard && analyticsHeading) {
            analyticsAiCard.style.marginTop = '0';
            analyticsAiCard.style.marginBottom = '18px';
            analyticsHeading.insertAdjacentElement('afterend', analyticsAiCard);
        }

        const darkToggle = document.getElementById('darkmodeToggle');
        const darkIcon = document.getElementById('darkmodeIcon');
        const darkKey = 'internconnect_darkmode';
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.getElementById('menuToggle');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        if (menuToggle && sidebar && sidebarOverlay) {
            menuToggle.addEventListener('click', function () {
                if (window.innerWidth <= 960) {
                    sidebar.classList.toggle('mobile-open');
                    sidebarOverlay.classList.toggle('active');
                }
            });

            sidebarOverlay.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                sidebarOverlay.classList.remove('active');
            });

            window.addEventListener('resize', function () {
                if (window.innerWidth > 960) {
                    sidebar.classList.remove('mobile-open');
                    sidebarOverlay.classList.remove('active');
                }
            });
        }

        const applyDarkMode = (isDark) => {
            document.body.classList.toggle('dark-mode', isDark);
            if (darkIcon) {
                darkIcon.className = isDark ? 'fa fa-sun' : 'fa fa-moon';
            }
        };

        const savedMode = localStorage.getItem(darkKey);
        applyDarkMode(savedMode === '1');

        if (darkToggle) {
            darkToggle.addEventListener('click', function () {
                const isDark = !document.body.classList.contains('dark-mode');
                applyDarkMode(isDark);
                localStorage.setItem(darkKey, isDark ? '1' : '0');
            });
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function buildCoordinatorPrintHTML() {
            const now = new Date();
            const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
            const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

            const studentRows = (analyticsPrintData.studentStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const fileRows = (analyticsPrintData.fileStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const courseRows = (analyticsPrintData.courseAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const companyRows = (analyticsPrintData.topCompanies || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const placementRows = (analyticsPrintData.placementAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
                </tr>
            `).join('');

            const moaRows = (analyticsPrintData.moaStatusAnalytics || []).map((item, index) => `
                <tr style="background:${index % 2 === 0 ? '#ffffff' : '#f9fafb'};">
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#111827;">${escapeHtml(item.label)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#374151;">${escapeHtml(item.count)}</td>
                    <td style="padding:8px 10px;border:1px solid #e5e7eb;font-size:10px;color:#991b1b;font-weight:700;">${escapeHtml(item.percentage)}%</td>
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
                                <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - OJT Information Management System</div>
                                <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">Coordinator Analytics Report</div>
                                <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">Taguig Branch Campus | College of Engineering and Technology</div>
                            </div>
                        </div>
                        <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
                    </div>

                    <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                        <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                            <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                                <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                                <span style="color:#6b7280;">Updated:</span>
                                <strong style="color:#111827;">${escapeHtml(analyticsPrintData.updatedAt)}</strong>
                            </div>
                            <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                                <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block;"></span>
                                <span style="color:#6b7280;">Generated:</span>
                                <strong style="color:#111827;">${dateStr} ${timeStr}</strong>
                            </div>
                        </div>
                        <div style="font-size:8.5px; color:#9ca3af;">Coordinator dashboard summary</div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Approved Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.approvedStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Pending Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.pendingStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Placed Students</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.placedStudents)}</div>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:12px;">
                                <div style="font-size:9px; color:#6b7280; text-transform:uppercase; letter-spacing:.4px;">Partner Companies</div>
                                <div style="font-size:18px; font-weight:800; color:#111827; margin-top:5px;">${escapeHtml(analyticsPrintData.partnerCompanies)}</div>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Student Status Breakdown</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${studentRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No student data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Requirement Status</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${fileRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No file data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Placement Coverage</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${placementRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No placement data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">MOA Portfolio</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Status</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Count</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${moaRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No MOA data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                            <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Course Distribution</div>
                            <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Course</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                <tbody>${courseRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No course data found.</td></tr>'}</tbody>
                            </table>
                        </div>
                    </div>

                    <div style="padding:14px 22px 0 22px;">
                        <div style="display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:14px;">
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Top Partner Companies</div>
                                <table style="width:100%; border-collapse:collapse; font-size:10px;">
                                    <thead><tr style="background:#f9fafb;"><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Company</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Students</th><th style="text-align:left; padding:7px 8px; border:1px solid #e5e7eb;">Share</th></tr></thead>
                                    <tbody>${companyRows || '<tr><td colspan="3" style="padding:8px;border:1px solid #e5e7eb;text-align:center;">No company data found.</td></tr>'}</tbody>
                                </table>
                            </div>
                            <div style="border:1px solid #e5e7eb; border-radius:10px; padding:14px; page-break-inside:avoid;">
                                <div style="font-size:12px; font-weight:700; color:#111827; margin-bottom:10px; border-left:3px solid #dc2626; padding-left:8px;">Analytics Insight</div>
                                <div style="font-size:11px; color:#374151; line-height:1.7;">${escapeHtml(analyticsPrintData.analyticsSummary || 'This printed report focuses on student status, requirement review, placement coverage, MOA health, course distribution, and partner company coverage.')}</div>
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
                            <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <div style="font-size:8px; color:rgba(255,255,255,0.5);">Ref: COORD-ANA-${now.getFullYear()}</div>
                    </div>
                </div>
            `;
        }

        document.getElementById('printBtn')?.addEventListener('click', () => {
            const wrapper = document.getElementById('print-area-wrapper');
            if (!wrapper) return;
            wrapper.innerHTML = buildCoordinatorPrintHTML();
            window.print();
            setTimeout(() => {
                wrapper.innerHTML = '';
            }, 1000);
        });

