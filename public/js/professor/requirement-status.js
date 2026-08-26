/* Requirement Status Tracking Scripts */

    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    const closeMobileSidebar = function () {
        sidebar.classList.remove('mobile-open');
        overlay.classList.remove('active');
        document.body.classList.remove('mobile-sidebar-open');
    };

    document.getElementById('menuToggle').addEventListener('click', function (event) {
        event.stopPropagation();
        if (window.innerWidth <= 900) {
            if (sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                sidebar.classList.add('mobile-open');
                overlay.classList.add('active');
                document.body.classList.add('mobile-sidebar-open');
            }
        } else {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
        }
    });

    overlay.addEventListener('click', closeMobileSidebar);

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


    function renderRequirementAiAnswer(data) {
        const answerBox = document.getElementById('requirementAiAnswer');
        const answerText = document.getElementById('requirementAiAnswerText');
        const nextStepsWrap = document.getElementById('requirementAiNextStepsWrap');
        const nextStepsList = document.getElementById('requirementAiNextSteps');

        if (!answerBox || !answerText) return;

        answerText.textContent = data.answer || 'No answer was returned.';
        answerBox.style.display = 'block';

        if (nextStepsList) nextStepsList.innerHTML = '';
        if (Array.isArray(data.next_steps) && data.next_steps.length && nextStepsWrap && nextStepsList) {
            data.next_steps.forEach(function (step) {
                const li = document.createElement('li');
                li.textContent = step;
                nextStepsList.appendChild(li);
            });
            nextStepsWrap.style.display = 'block';
        } else if (nextStepsWrap) {
            nextStepsWrap.style.display = 'none';
        }
    }

    function askRequirementAi(question) {
        const status = document.getElementById('requirementAiAskStatus');
        const button = document.getElementById('requirementAskAiBtn');

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

        fetch((window.requirementAiAskUrl || '/reports/ai/ask'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': (window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
            },
            body: JSON.stringify({
                question: question,
                report_type: requirementAiContext.report_type,
                metrics: requirementAiContext.metrics,
                insight: requirementAiContext.insight
            })
        })
            .then(function (response) {
                if (!response.ok) throw new Error('AI request failed.');
                return response.json();
            })
            .then(function (data) {
                renderRequirementAiAnswer(data);
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

    document.querySelectorAll('.requirement-ai-quick-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const input = document.getElementById('requirementAiQuestionInput');
            const question = btn.getAttribute('data-question') || '';
            if (input) input.value = question;
            askRequirementAi(question);
        });
    });

    const requirementAskAiBtn = document.getElementById('requirementAskAiBtn');
    if (requirementAskAiBtn) {
        requirementAskAiBtn.addEventListener('click', function () {
            const input = document.getElementById('requirementAiQuestionInput');
            askRequirementAi(input ? input.value : '');
        });
    }

    function escapeReportHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderRequirementList(items, emptyText) {
        if (!items || items.length === 0) {
            return `<span style="color:#9ca3af; font-style:italic;">${escapeReportHtml(emptyText)}</span>`;
        }

        return items.map(function (item) {
            return `<span style="display:inline-block; margin:1px 3px 3px 0; padding:2px 5px; border-radius:4px; background:#f3f4f6; color:#374151; font-size:7.5px; line-height:1.25;">${escapeReportHtml(item)}</span>`;
        }).join('');
    }

    function statusBadge(label, count, bg, color) {
        return `<span style="display:inline-block; margin:1px 2px 3px 0; padding:2px 6px; border-radius:999px; background:${bg}; color:${color}; font-size:7.5px; font-weight:700;">${label} ${count}</span>`;
    }

    function buildRequirementPrintHTML() {
        const report = requirementReportData;
        const title = report.activeView === 'overview'
            ? 'Student Requirement Status Report'
            : `${report.activeView.charAt(0).toUpperCase() + report.activeView.slice(1)} Requirement Report`;
        const sectionLabel = report.activeView === 'overview'
            ? 'Student Requirement Matrix'
            : `${report.activeView.charAt(0).toUpperCase() + report.activeView.slice(1)} Requirements`;

        let rowsHTML = '';

        report.rows.forEach(function (row, index) {
            const rowBg = index % 2 === 0 ? '#ffffff' : '#f9fafb';

            if (report.activeView === 'overview') {
                rowsHTML += `
                    <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                        <td style="padding:7px 6px; font-size:8px; font-weight:700; color:#6b7280; text-align:center; border-right:1px solid #e5e7eb;">${index + 1}</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">
                            <div style="font-size:9px; font-weight:800; color:#111827;">${escapeReportHtml(row.studentName)}</div>
                            <div style="font-size:7.5px; color:#6b7280; margin-top:1px;">${escapeReportHtml(row.studentNumber)}</div>
                        </td>
                        <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#15803d; text-align:center; border-right:1px solid #e5e7eb;">${row.completion}%</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(row.passed, 'No submitted requirements yet.')}</td>
                        <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(row.missing, 'Complete')}</td>
                        <td style="padding:7px 6px;">
                            ${statusBadge('Approved', row.approvedCount, '#dcfce7', '#15803d')}
                            ${statusBadge('Pending', row.pendingCount, '#fef9c3', '#a16207')}
                            ${statusBadge('Denied', row.deniedCount, '#fee2e2', '#b91c1c')}
                        </td>
                    </tr>`;
                return;
            }

            const focusedItems = row[report.activeView] || [];
            rowsHTML += `
                <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                    <td style="padding:7px 6px; font-size:8px; font-weight:700; color:#6b7280; text-align:center; border-right:1px solid #e5e7eb;">${index + 1}</td>
                    <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">
                        <div style="font-size:9px; font-weight:800; color:#111827;">${escapeReportHtml(row.studentName)}</div>
                        <div style="font-size:7.5px; color:#6b7280; margin-top:1px;">${escapeReportHtml(row.studentNumber)}</div>
                    </td>
                    <td style="padding:7px 6px; border-right:1px solid #e5e7eb;">${renderRequirementList(focusedItems, `No ${report.activeView} requirements found.`)}</td>
                    <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#111827; text-align:center; border-right:1px solid #e5e7eb;">${focusedItems.length}</td>
                    <td style="padding:7px 6px; font-size:9px; font-weight:800; color:#15803d; text-align:center;">${row.completion}%</td>
                </tr>`;
        });

        const tableHead = report.activeView === 'overview'
            ? `
                <tr style="background:#7f0000;">
                    <th style="width:4%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">#</th>
                    <th style="width:17%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Student</th>
                    <th style="width:8%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">Completion</th>
                    <th style="width:29%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Submitted Requirements</th>
                    <th style="width:27%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Missing Requirements</th>
                    <th style="width:15%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left;">Approval Status</th>
                </tr>`
            : `
                <tr style="background:#7f0000;">
                    <th style="width:4%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">#</th>
                    <th style="width:24%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">Student</th>
                    <th style="width:52%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:left; border-right:1px solid rgba(255,255,255,.18);">${escapeReportHtml(sectionLabel)}</th>
                    <th style="width:8%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center; border-right:1px solid rgba(255,255,255,.18);">Count</th>
                    <th style="width:12%; padding:7px 5px; color:#fff; font-size:7px; text-transform:uppercase; text-align:center;">Completion</th>
                </tr>`;

        return `
            <div style="font-family:'Poppins',Arial,sans-serif; background:#fff;">
                <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%);">
                    <div style="background:rgba(255,255,255,.12); height:4px;"></div>
                    <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                        <div style="width:50px; height:50px; background:rgba(255,255,255,.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,.25);">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);">
                        </div>
                        <div style="flex:1;">
                            <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines - OJT Information Management System</div>
                            <div style="font-size:15px; font-weight:800; color:#fff; line-height:1.15;">${escapeReportHtml(title)}</div>
                            <div style="font-size:8.5px; color:rgba(255,255,255,.6); margin-top:3px;">Taguig Branch Campus | College of Engineering and Technology</div>
                        </div>
                        <div style="text-align:right; flex-shrink:0;">
                            <div style="display:inline-block; background:rgba(255,255,255,.2); border:1px solid rgba(255,255,255,.3); border-radius:6px; padding:5px 12px; text-align:center;">
                                <div style="font-size:18px; font-weight:800; color:#fff; line-height:1;">${report.rows.length}</div>
                                <div style="font-size:7.5px; color:rgba(255,255,255,.7); text-transform:uppercase; letter-spacing:1px; margin-top:1px;">Report Rows</div>
                            </div>
                        </div>
                    </div>
                    <div style="background:rgba(0,0,0,.15); height:3px;"></div>
                </div>

                <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                    <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">Class:</span> <strong>${escapeReportHtml(report.course)} ${escapeReportHtml(report.room)}</strong></div>
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">School Year:</span> <strong>${escapeReportHtml(report.schoolYear)}</strong></div>
                        <div style="font-size:9.5px; color:#374151;"><span style="color:#6b7280;">View:</span> <strong>${escapeReportHtml(sectionLabel)}</strong></div>
                    </div>
                    <div style="font-size:8.5px; color:#9ca3af;">Generated: ${escapeReportHtml(report.generatedAt)}</div>
                </div>

                <div style="padding:10px 22px 4px 22px; display:grid; grid-template-columns:repeat(4, 1fr); gap:8px;">
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.totalStudents}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Students</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.categoryCount}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Categories</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #16a34a; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.completeStudents}</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Complete Students</div></div>
                    <div style="border:1px solid #e5e7eb; border-left:4px solid #16a34a; border-radius:7px; padding:8px 10px;"><div style="font-size:15px; font-weight:800; color:#111827;">${report.averageCompletion}%</div><div style="font-size:7.5px; color:#6b7280; text-transform:uppercase;">Average Completion</div></div>
                </div>

                <div style="padding:8px 22px 3px 22px;">
                    <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">${escapeReportHtml(sectionLabel)}</div>
                </div>

                <div style="padding:4px 22px 0 22px;">
                    <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                        <thead>${tableHead}</thead>
                        <tbody>
                            ${rowsHTML || `<tr><td colspan="${report.activeView === 'overview' ? 6 : 5}" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for this report view.</td></tr>`}
                        </tbody>
                    </table>
                </div>

                <div style="page-break-inside:avoid; break-inside:avoid; display:table; width:100%;">
                    <div style="padding:18px 22px 12px 22px;">
                        <div style="border-top:1px dashed #d1d5db; padding-top:16px;">
                            <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                                <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:.6px; margin-bottom:4px;">Disclaimer</div>
                                <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">
                                    This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                        <div style="display:flex; align-items:center; gap:6px;">
                            <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:.7; filter:brightness(2);">
                            <span style="font-size:8px; color:rgba(255,255,255,.75); font-weight:500;">Polytechnic University of the Philippines - InternConnect OJT IMS</span>
                        </div>
                        <span style="font-size:8px; color:rgba(255,255,255,.5);">Ref: REQ-STATUS-${new Date().getFullYear()}</span>
                    </div>
                </div>
            </div>`;
    }

    document.getElementById('printReportBtn').addEventListener('click', function () {
        document.getElementById('print-area-wrapper').innerHTML = buildRequirementPrintHTML();
        window.print();
        setTimeout(function () {
            document.getElementById('print-area-wrapper').innerHTML = '';
        }, 1000);
    });

    const requirementModal = document.getElementById('requirementListModal');
    const requirementModalTitle = document.getElementById('requirementListTitle');
    const requirementModalStudent = document.getElementById('requirementListStudent');
    const requirementModalList = document.getElementById('requirementModalList');
    const requirementModalClose = document.getElementById('requirementModalClose');

    function requirementIcon(type, empty) {
        if (empty && type === 'missing') {
            return 'fa-check-circle';
        }
        if (type === 'pending') {
            return 'fa-clock';
        }
        if (type === 'missing') {
            return 'fa-times';
        }
        if (type === 'denied') {
            return 'fa-times';
        }
        return 'fa-check';
    }

    function closeRequirementModal() {
        requirementModal.classList.remove('open');
        requirementModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('modal-open');
    }

    function openRequirementModal(button) {
        const type = button.dataset.modalType || 'passed';
        const emptyText = button.dataset.emptyText || 'No requirements found.';
        let requirements = [];

        try {
            requirements = JSON.parse(button.dataset.requirements || '[]');
        } catch (error) {
            requirements = [];
        }

        requirementModalTitle.textContent = button.dataset.modalTitle || 'Requirements';
        requirementModalStudent.textContent = button.dataset.studentName || '';
        requirementModalList.innerHTML = '';

        if (requirements.length === 0) {
            requirements = [emptyText];
        }

        requirements.forEach(function (item) {
            const isEmptyComplete = item === 'Complete';
            const row = document.createElement('div');
            row.className = 'requirement-item ' + (isEmptyComplete ? 'passed' : type);

            const icon = document.createElement('i');
            icon.className = 'fa ' + requirementIcon(type, isEmptyComplete);

            const label = document.createElement('span');
            label.textContent = item;

            row.appendChild(icon);
            row.appendChild(label);
            requirementModalList.appendChild(row);
        });

        requirementModal.classList.add('open');
        requirementModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('modal-open');
        requirementModalClose.focus();
    }

    document.querySelectorAll('.requirement-modal-trigger').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.stopPropagation();
            openRequirementModal(button);
        });
    });

    requirementModalClose.addEventListener('click', closeRequirementModal);

    requirementModal.addEventListener('click', function (event) {
        if (event.target === requirementModal) {
            closeRequirementModal();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && requirementModal.classList.contains('open')) {
            closeRequirementModal();
        }
    });
