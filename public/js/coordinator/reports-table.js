/* ==========================================================================
   Coordinator Reports Table Scripts
   Extracted from ojtCoordinator/reportsT.blade.php
   ========================================================================== */

const studentAiContext = window.studentAiContext || {};

    function renderStudentAiAnswer(data) {
    const answerBox = document.getElementById('studentAiAnswerBox');
    const answerText = document.getElementById('studentAiAnswerText');
    const answerSteps = document.getElementById('studentAiAnswerSteps');
    const answerSource = document.getElementById('studentAiAnswerSource');

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

function askStudentAi(question) {
    const status = document.getElementById('studentAiAskStatus');
    const button = document.getElementById('studentAskAiBtn');

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

    fetch((window.reportAiContext && window.reportAiContext.askUrl) ? window.reportAiContext.askUrl : '/reports/ai/ask', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': (window.reportAiContext && window.reportAiContext.csrfToken) ? window.reportAiContext.csrfToken : (meta[name = "csrf-token"].attr('content') || '')
        },
        body: JSON.stringify({
            question: question,
            report_type: studentAiContext.report_type,
            metrics: studentAiContext.metrics,
            insight: studentAiContext.insight
        })
    })
        .then(function (response) {
            if (!response.ok) throw new Error('AI request failed.');
            return response.json();
        })
        .then(function (data) {
            renderStudentAiAnswer(data);
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

document.querySelectorAll('.student-ai-quick-question').forEach(function (btn) {
    btn.addEventListener('click', function () {
        const input = document.getElementById('studentAiQuestionInput');
        const question = btn.getAttribute('data-question') || '';
        if (input) input.value = question;
        askStudentAi(question);
    });
});

const studentAskAiBtn = document.getElementById('studentAskAiBtn');
if (studentAskAiBtn) {
    studentAskAiBtn.addEventListener('click', function () {
        const input = document.getElementById('studentAiQuestionInput');
        askStudentAi(input ? input.value : '');
    });
}

/* ── Sidebar toggle ── */
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');

menuToggle.addEventListener('click', function () {
    const isMobile = window.innerWidth <= 900;
    if (isMobile) {
        sidebar.classList.toggle('mobile-open');
        overlay.classList.toggle('active');
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
});

overlay.addEventListener('click', function () {
    sidebar.classList.remove('mobile-open');
    overlay.classList.remove('active');
});

// Dark mode toggle
const darkmodeToggle = document.getElementById('darkmodeToggle');
const isDarkMode = localStorage.getItem('darkMode') === 'enabled';

if (isDarkMode) {
    document.body.classList.add('dark-mode');
    darkmodeToggle.innerHTML = '<i class="fa fa-sun"></i>';
}

darkmodeToggle.addEventListener('click', function () {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark ? 'enabled' : 'disabled');
    darkmodeToggle.innerHTML = isDark ? '<i class="fa fa-sun"></i>' : '<i class="fa fa-moon"></i>';
});

/* ── DataTable ── */
$(document).ready(function () {
    $('#fileTable').DataTable({
        order: [],
        scrollX: true,
        autoWidth: false
    });
});

/* ══════════════════════════════════════════════
   BUILD PRINT HTML
══════════════════════════════════════════════ */
function buildPrintHTML() {
    const now = new Date();
    const dateStr = now.toLocaleDateString('en-US', { year: 'numeric', month: 'long', day: 'numeric' });
    const timeStr = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    const dt = $('#fileTable').DataTable();
    const currentPageNodes = dt.rows({ page: 'current' }).nodes();
    const total = currentPageNodes.length;
    const pageInfo = dt.page.info();
    const pageNum = pageInfo.page + 1;
    const pageCount = pageInfo.pages;

    const schoolYearEl = document.getElementById('school_year');
    const courseEl = document.getElementById('course');

    const rawSchoolYear = schoolYearEl ? (schoolYearEl.value || (schoolYearEl.selectedIndex >= 0 ? schoolYearEl.options[schoolYearEl.selectedIndex].text : '')) : '';
    const schoolYear = (rawSchoolYear && rawSchoolYear !== 'Select School Year') ? rawSchoolYear : (window.studentAiContext?.metrics?.school_year || window.__reportsConfig?.schoolYear || 'All School Years');

    const rawCourse = courseEl ? (courseEl.value || (courseEl.selectedIndex >= 0 ? courseEl.options[courseEl.selectedIndex].text : '')) : '';
    const courseName = (rawCourse && rawCourse !== 'Select Course') ? rawCourse : (window.studentAiContext?.metrics?.course || window.__reportsConfig?.course || 'All Courses');

    const semesterEl = document.getElementById('semester');
    const rawSemester = semesterEl ? (semesterEl.value || (semesterEl.selectedIndex >= 0 ? semesterEl.options[semesterEl.selectedIndex].text : '')) : '';
    const semester = rawSemester || window.__reportsConfig?.semester || window.reportConfig?.semester || '1st Semester';

    const campusName = window.__reportsConfig?.campusName || window.reportConfig?.campusName || 'PUP Taguig Campus';
    const coordinatorName = window.__reportsConfig?.coordinatorName || window.reportConfig?.coordinatorName || 'OJT Coordinator';

    let rowsHTML = '';
    for (let i = 0; i < currentPageNodes.length; i++) {
        const tr = currentPageNodes[i];
        const tds = tr.querySelectorAll('td');

        const getName = () => {
            if (!tds[0]) return '';
            const nt = tds[0].querySelector('.name-text');
            return nt ? nt.textContent.trim() : tds[0].textContent.trim();
        };
        const getCompany = () => {
            if (!tds[3]) return '';
            return tds[3].textContent.trim();
        };

        const rowNum = pageInfo.start + i + 1;
        const rowBg = i % 2 === 0 ? '#ffffff' : '#f9fafb';
        const studentName = getName();
        const courseMajor = tr.getAttribute('data-course') || (tds[1] ? tds[1].textContent.trim() : '');
        const section = tr.getAttribute('data-section') || (tds[2] ? tds[2].textContent.trim() : '');
        const companyName = getCompany();
        const startDate = tds[6] ? tds[6].textContent.trim() : '';
        const assignedDepartment = tr.getAttribute('data-department') || (tds[4] ? tds[4].textContent.trim() : '');
        const studentRole = tr.getAttribute('data-role') || (tds[5] ? tds[5].textContent.trim() : '');

        rowsHTML += `
            <tr style="background:${rowBg}; border-bottom:1px solid #e5e7eb;">
                <td style="padding:7px 6px; font-size:9px; font-weight:700; color:#6b7280; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb;">${rowNum}</td>
                <td style="padding:7px 6px; font-size:9.5px; font-weight:700; color:#111827; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; word-break:break-word;">${studentName}</td>
                <td style="padding:7px 6px; font-size:9px; color:#374151; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; word-break:break-word;">${courseMajor}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb;">${section}</td>
                <td style="padding:7px 6px; font-size:9px; font-weight:600; color:#111827; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; word-break:break-word;">${companyName}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#ca8a04; font-weight:600; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; white-space:nowrap;">${startDate}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; word-break:break-word;">${assignedDepartment}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#374151; vertical-align:middle; text-align:center; border-right:1px solid #e5e7eb; word-break:break-word;">${studentRole}</td>
                <td style="padding:7px 6px; font-size:8.5px; color:#4b5563; vertical-align:middle; text-align:center;"></td>
            </tr>`;
    }

    return `
        <div style="font-family:'Poppins',Arial,sans-serif; background:#fff;">

            <!-- HEADER -->
            <div style="background:linear-gradient(135deg,#7f0000 0%,#991b1b 55%,#dc2626 100%); padding:0;">
                <div style="background:rgba(255,255,255,0.12); height:4px;"></div>
                <div style="padding:16px 22px; display:flex; align-items:center; gap:14px;">
                    <div style="width:50px; height:50px; background:rgba(255,255,255,0.18); border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; border:1.5px solid rgba(255,255,255,0.25);">
                        <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:36px; height:36px; object-fit:contain; filter:brightness(1.4);">
                    </div>
                    <div style="flex:1;">
                        <div style="font-size:6.5px; font-weight:700; color:rgba(255,255,255,0.55); text-transform:uppercase; letter-spacing:2px; margin-bottom:3px;">Polytechnic University of the Philippines — Taguig Campus</div>
                        <div style="font-size:15px; font-weight:800; color:#fff; letter-spacing:-0.3px; line-height:1.15;">OJT REPORT FORM</div>
                        <div style="font-size:8.5px; color:rgba(255,255,255,0.6); margin-top:3px;">${campusName}</div>
                    </div>
                    <div style="text-align:right; flex-shrink:0;">
                        <div style="display:inline-block; background:rgba(255,255,255,0.2); border:1px solid rgba(255,255,255,0.3); border-radius:6px; padding:5px 12px; text-align:center;">
                            <div style="font-size:18px; font-weight:800; color:#fff; line-height:1;">${total}</div>
                            <div style="font-size:7.5px; color:rgba(255,255,255,0.7); text-transform:uppercase; letter-spacing:1px; margin-top:1px;">Page Records</div>
                        </div>
                        <div style="font-size:8.5px; color:rgba(255,255,255,0.55); margin-top:4px; text-align:center;">Page ${pageNum} of ${pageCount}</div>
                    </div>
                </div>
                <div style="background:rgba(0,0,0,0.15); height:3px;"></div>
            </div>

            <!-- META ROW -->
            <div style="background:#f8f9fa; border-bottom:1.5px solid #e5e7eb; padding:8px 22px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:6px;">
                <div style="display:flex; align-items:center; gap:14px; flex-wrap:wrap;">
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">School Year:</span>
                        <strong style="color:#111827;">${schoolYear}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Semester:</span>
                        <strong style="color:#111827;">${semester}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Course:</span>
                        <strong style="color:#111827;">${courseName}</strong>
                    </div>
                    <div style="display:flex; align-items:center; gap:4px; font-size:9.5px; color:#374151;">
                        <span style="width:5px; height:5px; background:#dc2626; border-radius:50%; display:inline-block; flex-shrink:0;"></span>
                        <span style="color:#6b7280;">Total No. of Students:</span>
                        <strong style="color:#111827;">${total}</strong>
                    </div>
                </div>
                <div style="font-size:8.5px; color:#9ca3af;">Generated: ${dateStr} at ${timeStr}</div>
            </div>

            <!-- SECTION LABEL -->
            <div style="padding:9px 22px 3px 22px;">
                <div style="font-size:8px; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1.5px; border-left:3px solid #dc2626; padding-left:6px;">Student OJT Placement Details — Page ${pageNum}</div>
            </div>

            <!-- DATA TABLE (Template columns: NO., STUDENT NAME, COURSE / MAJOR, SECTION, COMPANY NAME, START DATE OF OJT, ASSIGNED DEPARTMENT, ROLE, REMARKS (HIRED)) -->
            <div style="padding:4px 22px 0 22px;">
                <table style="width:100%; table-layout:fixed; border-collapse:collapse; font-family:'Poppins',Arial,sans-serif; border:1px solid #d1d5db;">
                    <colgroup>
                        <col style="width:4%;">   <!-- NO. -->
                        <col style="width:18%;">  <!-- STUDENT NAME -->
                        <col style="width:12%;">  <!-- COURSE / MAJOR -->
                        <col style="width:8%;">   <!-- SECTION -->
                        <col style="width:20%;">  <!-- COMPANY NAME -->
                        <col style="width:12%;">  <!-- START DATE OF OJT -->
                        <col style="width:12%;">  <!-- ASSIGNED DEPARTMENT -->
                        <col style="width:10%;">  <!-- ROLE -->
                        <col style="width:14%;">  <!-- REMARKS (HIRED) -->
                    </colgroup>
                    <thead>
                        <tr style="background:#7f0000;">
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">NO.</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">STUDENT NAME</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">COURSE / MAJOR</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">SECTION</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">COMPANY NAME</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">START DATE OF OJT</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">ASSIGNED DEPARTMENT</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle; border-right:1px solid rgba(255,255,255,0.15);">ROLE</th>
                            <th style="padding:7px 5px; color:#fff; font-size:7px; font-weight:700; text-transform:uppercase; letter-spacing:0.4px; text-align:center; vertical-align:middle;">REMARKS (HIRED)</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHTML || `<tr><td colspan="9" style="text-align:center; padding:28px; color:#9ca3af; font-size:11px; font-style:italic; background:#fff;">No records found for the selected filters.</td></tr>`}
                    </tbody>
                </table>
            </div>

            <!-- SUBMITTED BY SECTION -->
            <div style="padding:22px 22px 10px 22px; font-size:11px;">
                <div style="width:40%;">
                    <div style="font-weight:800; color:#111827; margin-top:8px; font-size:11.5px;">${coordinatorName}</div>
                    <div style="font-size:9.5px; color:#6b7280; font-weight:600;">OJT Coordinator</div>
                </div>
            </div>

            <!-- REPORT DISCLAIMER -->
            <div style="padding:10px 22px 12px 22px;">
                <div style="border-top:1px dashed #d1d5db; padding-top:14px;">
                    <div style="background:#f8fafc; border:1px solid #e5e7eb; border-left:4px solid #dc2626; border-radius:8px; padding:12px 14px;">
                        <div style="font-size:9px; font-weight:700; color:#111827; text-transform:uppercase; letter-spacing:0.6px; margin-bottom:4px;">Disclaimer</div>
                        <div style="font-size:8.5px; color:#4b5563; line-height:1.6;">
                            This report was generated by the InternConnect OJT Information Management System and does not require a physical or handwritten signature.
                        </div>
                    </div>
                </div>
            </div>

            <!-- DOCUMENT FOOTER -->
            <div style="background:#7f0000; padding:8px 22px; display:flex; align-items:center; justify-content:space-between;">
                <div style="display:flex; align-items:center; gap:6px;">
                    <img src="/images/final-puptg_logo-ojtims_nbg.png" style="width:13px; height:13px; object-fit:contain; opacity:0.7; filter:brightness(2);">
                    <span style="font-size:8px; color:rgba(255,255,255,0.75); font-weight:500;">&copy; 1998–${now.getFullYear()} <strong style="color:#fca5a5;">Polytechnic University of the Philippines</strong> — Taguig Campus</span>
                </div>
                <span style="font-size:8px; color:rgba(255,255,255,0.65);">InternConnect - OJTIMS &nbsp;|&nbsp; Ref: OJT-RPT-${now.getFullYear()} &nbsp;|&nbsp; Page ${pageNum} of ${pageCount}</span>
            </div>

        </div>`;
}

/* ══════════════════════════════════════════════
   Single modal trigger
══════════════════════════════════════════════ */
const previewModalEl = document.getElementById('printPreviewModal');
const previewModal = new bootstrap.Modal(previewModalEl, { backdrop: 'static', keyboard: true });

document.getElementById('openPreviewBtn').addEventListener('click', function () {
    document.getElementById('printPreviewContent').innerHTML = buildPrintHTML();
    previewModal.show();
});

/* ══════════════════════════════════════════════
   PRINT
   Native Print via hidden #print-area-wrapper
══════════════════════════════════════════════ */
document.getElementById('doPrintBtn').addEventListener('click', function () {
    // 1. Inject the HTML into the print wrapper
    document.getElementById('print-area-wrapper').innerHTML = buildPrintHTML();

    // 2. Trigger the native print dialog
    window.print();

    // 3. Clean up memory
    setTimeout(function () {
        document.getElementById('print-area-wrapper').innerHTML = '';
    }, 1000);
});

