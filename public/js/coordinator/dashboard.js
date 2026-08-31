/* ==========================================================================
   Coordinator Dashboard Scripts
   Extracted from ojtCoordinator/dashboard.blade.php
   ========================================================================== */

        $(document).ready(function () {
            const coordinatorAnnouncementTable = $('#coordinatorAnnouncementTable').DataTable({
                pageLength: 5,
                lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                scrollX: true,
                scrollCollapse: true,
                autoWidth: false,
                order: [[2, 'desc']],
                columnDefs: [
                    { orderable: false, targets: 3 }
                ],
                language: {
                    emptyTable: 'No announcements posted yet.'
                }
            });

            $('#coordinatorAnnouncementSort').on('change', function () {
                coordinatorAnnouncementTable.order([2, this.value]).draw();
            });
        });

        // Sidebar toggle
        const sidebar     = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle  = document.getElementById('menuToggle');
        const overlay     = document.getElementById('sidebarOverlay');

        menuToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                if (sidebar.classList.contains('mobile-open')) {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('active');
                    document.body.classList.remove('mobile-sidebar-open');
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

        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.classList.remove('mobile-sidebar-open');
        });

        const closeMobileSidebar = function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
            document.body.classList.remove('mobile-sidebar-open');
        };

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

        document.querySelectorAll('.delete-announcement-form').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                const title = form.dataset.announcementTitle || 'this announcement';
                const proceed = function () { form.submit(); };

                if (typeof Swal === 'undefined') {
                    if (window.confirm('Delete "' + title + '"? This cannot be undone.')) {
                        proceed();
                    }
                    return;
                }

                Swal.fire({
                    title: 'Delete announcement?',
                    html: 'This will permanently delete <strong>' + title + '</strong>.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        proceed();
                    }
                });
            });
        });

        /* ══════════════════════════════════════════════
           DATE & TIME MODAL
        ══════════════════════════════════════════════ */

        const dateEl = document.getElementById('currentDate');
        if (dateEl) {
            dateEl.textContent = new Date().toLocaleDateString('en-US', {
                weekday: 'short', year: 'numeric',
                month: 'long', day: 'numeric'
            });
        }

        const dtOverlay  = document.getElementById('dtOverlay') || createDTModal();
        const dtCloseBtn = document.getElementById('dtCloseBtn');
        const dateBadge  = document.getElementById('dateBadge');

        function createDTModal() {
            const html = `
            <div class="dt-overlay" id="dtOverlay">
                <div class="dt-modal" id="dtModal">
                    <div class="dt-modal-header">
                        <div class="dt-header-top">
                            <span class="dt-header-title"><i class="fa fa-clock" style="margin-right:6px;"></i>Date & Time</span>
                            <button class="dt-close-btn" id="dtCloseBtn"><i class="fa fa-times"></i></button>
                        </div>
                        <div class="dt-clock-display">
                            <div class="dt-time-big">
                                <span id="dtHours">00</span>
                                <span class="colon">:</span>
                                <span id="dtMinutes">00</span>
                                <span class="colon">:</span>
                                <span id="dtSeconds">00</span>
                                <span class="dt-time-ampm" id="dtAmPm">AM</span>
                            </div>
                            <div class="dt-date-sub" id="dtDateSub"></div>
                        </div>
                    </div>
                    <div class="dt-analog-wrap">
                        <div class="analog-clock" id="analogClock">
                            <div class="clock-center"></div>
                            <div class="hand hour-hand" id="hourHand"></div>
                            <div class="hand minute-hand" id="minuteHand"></div>
                            <div class="hand second-hand" id="secondHand"></div>
                        </div>
                    </div>
                    <div class="dt-calendar">
                        <div class="cal-nav">
                            <button class="cal-nav-btn" id="calPrev"><i class="fa fa-chevron-left"></i></button>
                            <span class="cal-month-label" id="calMonthLabel"></span>
                            <button class="cal-nav-btn" id="calNext"><i class="fa fa-chevron-right"></i></button>
                        </div>
                        <div class="cal-grid" id="calGrid"></div>
                    </div>
                </div>
            </div>
            `;
            document.body.insertAdjacentHTML('beforeend', html);
            return document.getElementById('dtOverlay');
        }

        /* Open / Close */
        if (dateBadge) {
            dateBadge.addEventListener('click', function () {
                dtOverlay.classList.add('open');
                startClock();
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        function closeModal() {
            dtOverlay.classList.remove('open');
            stopClock();
        }

        if (dtCloseBtn) {
            dtCloseBtn.addEventListener('click', closeModal);
        }
        dtOverlay.addEventListener('click', function (e) {
            if (e.target === dtOverlay) closeModal();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        /* ── Digital Clock ── */
        let clockRAF = null;

        function startClock() {
            function tick() {
                const now  = new Date();
                let   h    = now.getHours();
                const m    = now.getMinutes();
                const s    = now.getSeconds();
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12 || 12;

                document.getElementById('dtHours').textContent   = String(h).padStart(2,'0');
                document.getElementById('dtMinutes').textContent = String(m).padStart(2,'0');
                document.getElementById('dtSeconds').textContent = String(s).padStart(2,'0');
                document.getElementById('dtAmPm').textContent    = ampm;
                document.getElementById('dtDateSub').textContent =
                    now.toLocaleDateString('en-US', { weekday:'long', year:'numeric', month:'long', day:'numeric' });

                /* ── Analog hands ── */
                const secDeg  = s * 6;
                const minDeg  = m * 6 + s * 0.1;
                const hourDeg = (h % 12) * 30 + m * 0.5;

                document.getElementById('secondHand').style.transform = `rotate(${secDeg}deg)`;
                document.getElementById('minuteHand').style.transform = `rotate(${minDeg}deg)`;
                document.getElementById('hourHand').style.transform   = `rotate(${hourDeg}deg)`;

                clockRAF = requestAnimationFrame(tick);
            }
            tick();
        }

        function stopClock() {
            if (clockRAF) { cancelAnimationFrame(clockRAF); clockRAF = null; }
        }

        /* ── Build hour tick marks ── */
        (function buildMarks() {
            const clock = document.getElementById('analogClock');
            if (!clock) return;
            for (let i = 0; i < 12; i++) {
                const mark = document.createElement('div');
                mark.className = 'clock-mark';
                const angle  = i * 30;
                mark.style.cssText = `
                    position: absolute;
                    width:  ${i % 3 === 0 ? 2.5 : 1.5}px;
                    height: ${i % 3 === 0 ? 8 : 5}px;
                    background: currentColor;
                    border-radius: 2px;
                    top: 4px;
                    left: calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px);
                    transform-origin: center 53px;
                    transform: rotate(${angle}deg);
                `;
                clock.appendChild(mark);
            }
        })();

        /* ── Calendar ── */
        const MONTHS = ['January','February','March','April','May','June',
                        'July','August','September','October','November','December'];
        const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

        const today       = new Date();
        let calViewYear   = today.getFullYear();
        let calViewMonth  = today.getMonth();
        let selectedDay   = today.getDate();

        const calPrev = document.getElementById('calPrev');
        const calNext = document.getElementById('calNext');

        if (calPrev) {
            calPrev.addEventListener('click', function () {
                calViewMonth--;
                if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        if (calNext) {
            calNext.addEventListener('click', function () {
                calViewMonth++;
                if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
                renderCalendar(calViewYear, calViewMonth);
            });
        }

        function renderCalendar(year, month) {
            document.getElementById('calMonthLabel').textContent = `${MONTHS[month]} ${year}`;

            const grid      = document.getElementById('calGrid');
            grid.innerHTML  = '';

            /* Day-name headers */
            DAYS.forEach(d => {
                const el = document.createElement('div');
                el.className   = 'cal-day-name';
                el.textContent = d;
                grid.appendChild(el);
            });

            const firstDay = new Date(year, month, 1).getDay();
            const daysInMonth = new Date(year, month + 1, 0).getDate();

            /* Empty leading cells */
            for (let i = 0; i < firstDay; i++) {
                const el = document.createElement('div');
                el.className = 'cal-day empty';
                grid.appendChild(el);
            }

            /* Day cells */
            for (let d = 1; d <= daysInMonth; d++) {
                const el = document.createElement('div');
                el.className   = 'cal-day';
                el.textContent = d;

                const isToday  = d === today.getDate() &&
                                 month === today.getMonth() &&
                                 year  === today.getFullYear();
                const isSel    = d === selectedDay &&
                                 month === calViewMonth &&
                                 year  === calViewYear;

                if (isToday) el.classList.add('today');
                else if (isSel) el.classList.add('selected');

                el.addEventListener('click', function () {
                    selectedDay  = d;
                    calViewYear  = year;
                    calViewMonth = month;
                    renderCalendar(year, month);
                });

                grid.appendChild(el);
            }
        }


        function renderDashboardAiAnswer(data) {
            const answerBox = document.getElementById('dashboardAiAnswer');
            const answerText = document.getElementById('dashboardAiAnswerText');
            const nextStepsWrap = document.getElementById('dashboardAiNextStepsWrap');
            const nextStepsList = document.getElementById('dashboardAiNextSteps');

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

        function askDashboardAi(question) {
            const status = document.getElementById('dashboardAiAskStatus');
            const button = document.getElementById('dashboardAskAiBtn');

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

            fetch((window.dashboardAiContext && window.dashboardAiContext.askUrl) ? window.dashboardAiContext.askUrl : '/reports/ai/ask', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': (window.dashboardAiContext && window.dashboardAiContext.csrfToken) ? window.dashboardAiContext.csrfToken : (meta[name="csrf-token"].attr('content') || '')
                },
                body: JSON.stringify({
                    question: question,
                    report_type: dashboardAiContext.report_type,
                    metrics: dashboardAiContext.metrics,
                    insight: dashboardAiContext.insight
                })
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('AI request failed.');
                    return response.json();
                })
                .then(function (data) {
                    renderDashboardAiAnswer(data);
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

        document.querySelectorAll('.dashboard-ai-quick-question').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const input = document.getElementById('dashboardAiQuestionInput');
                const question = btn.getAttribute('data-question') || '';
                if (input) {
                    input.value = question;
                    input.focus();
                }
            });
        });

        const dashboardAskAiBtn = document.getElementById('dashboardAskAiBtn');
        if (dashboardAskAiBtn) {
            dashboardAskAiBtn.addEventListener('click', function () {
                const input = document.getElementById('dashboardAiQuestionInput');
                askDashboardAi(input ? input.value : '');
            });
        }

        $('.btn-edit-announcement').on('click', function () {
            $('#editAnnouncementForm').attr('action', $(this).data('announcement-action'));
            $('#editAnnouncementTitle').val($(this).data('announcement-title'));
            $('#editAnnouncementContent').val($(this).data('announcement-content'));
        });
