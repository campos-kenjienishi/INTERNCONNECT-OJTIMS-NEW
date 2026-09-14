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

        /* ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ 
           ACADEMIC CHRONO & CALENDAR HUB
        ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═ ═  */

        // 1. LIVE DIGITAL CHRONO STATION & MOVING CLOCK (Philippine Standard Time UTC+8)
        function initDashboardLiveClock() {
            const hoursEl = document.getElementById('dashHours');
            const minsEl = document.getElementById('dashMins');
            const secsEl = document.getElementById('dashSecs');
            const ampmEl = document.getElementById('dashAmPm');
            const dayNameEl = document.getElementById('dashDayName');
            const fullDateEl = document.getElementById('dashFullDate');
            const greetingMsgEl = document.getElementById('dashGreetingMsg');
            const greetingIconEl = document.getElementById('dashGreetingIcon');

            // Moving Clock elements
            const hourHandEl = document.getElementById('chronoHourHand');
            const minHandEl = document.getElementById('chronoMinHand');
            const secHandEl = document.getElementById('chronoSecHand');
            const dialMarksEl = document.getElementById('chronoDialMarks');

            if (!hoursEl) return;

            // Generate 12 clock dial marks once
            if (dialMarksEl && !dialMarksEl.children.length) {
                for (let i = 0; i < 12; i++) {
                    const mark = document.createElement('div');
                    mark.className = 'chrono-clock-mark' + (i % 3 === 0 ? ' major' : '');
                    mark.style.transform = `rotate(${i * 30}deg)`;
                    dialMarksEl.appendChild(mark);
                }
            }

            const DAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const MONTH_NAMES = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            function tick() {
                const now = new Date();

                const secNum = now.getSeconds();
                const minNum = now.getMinutes();
                const rawHours = now.getHours();
                const hourNum = rawHours % 12;
                const ampm = rawHours >= 12 ? 'PM' : 'AM';

                const minutes = String(minNum).padStart(2, '0');
                const seconds = String(secNum).padStart(2, '0');
                const displayHours = String(hourNum || 12).padStart(2, '0');

                // Numeric Pods (Right side)
                hoursEl.textContent = displayHours;
                if (minsEl) minsEl.textContent = minutes;
                if (secsEl) secsEl.textContent = seconds;
                if (ampmEl) ampmEl.textContent = ampm;

                // Moving Clock Hands (Left side)
                const secDeg = secNum * 6;
                const minDeg = minNum * 6 + secNum * 0.1;
                const hourDeg = hourNum * 30 + minNum * 0.5;

                if (secHandEl) secHandEl.style.transform = `rotate(${secDeg}deg)`;
                if (minHandEl) minHandEl.style.transform = `rotate(${minDeg}deg)`;
                if (hourHandEl) hourHandEl.style.transform = `rotate(${hourDeg}deg)`;

                // Date Ribbon
                if (dayNameEl) {
                    dayNameEl.textContent = DAY_NAMES[now.getDay()];
                }

                if (fullDateEl) {
                    const month = MONTH_NAMES[now.getMonth()];
                    const day = String(now.getDate()).padStart(2, '0');
                    const year = now.getFullYear();
                    fullDateEl.textContent = `${month} ${day}, ${year}`;
                }

                // Dynamic greeting by hour of the day
                if (greetingMsgEl) {
                    let greeting = 'Good day';
                    let iconClass = 'fa-sun';

                    if (rawHours >= 5 && rawHours < 12) {
                        greeting = 'Good morning';
                        iconClass = 'fa-sun';
                    } else if (rawHours >= 12 && rawHours < 18) {
                        greeting = 'Good afternoon';
                        iconClass = 'fa-cloud-sun';
                    } else {
                        greeting = 'Good evening';
                        iconClass = 'fa-moon';
                    }

                    greetingMsgEl.textContent = greeting;
                    if (greetingIconEl) {
                        greetingIconEl.innerHTML = `<i class="fa ${iconClass}"></i>`;
                    }
                }
            }

            tick();
            setInterval(tick, 1000);
        }

        // 2. INTERACTIVE MONTHLY CALENDAR PANE
        function initDashboardCalendarWidget() {
            const monthLabelEl = document.getElementById('dashCalMonthLabel');
            const gridEl = document.getElementById('dashCalGrid');
            const prevBtn = document.getElementById('dashCalPrev');
            const nextBtn = document.getElementById('dashCalNext');
            const todayBtn = document.getElementById('dashCalToday');
            const selectedLabelEl = document.getElementById('dashCalSelectedLabel');

            if (!gridEl || !monthLabelEl) return;

            const MONTH_NAMES = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const MONTH_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

            const realToday = new Date();
            let currentYear = realToday.getFullYear();
            let currentMonth = realToday.getMonth();
            let selectedDate = new Date(realToday.getFullYear(), realToday.getMonth(), realToday.getDate());

            function updateSelectedLabel() {
                if (selectedLabelEl && selectedDate) {
                    selectedLabelEl.textContent = `${MONTH_SHORT[selectedDate.getMonth()]} ${selectedDate.getDate()}, ${selectedDate.getFullYear()}`;
                }
            }

            function renderCalendar(year, month) {
                monthLabelEl.textContent = `${MONTH_NAMES[month]} ${year}`;
                gridEl.innerHTML = '';

                const firstDayIndex = new Date(year, month, 1).getDay(); // 0 = Sun
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const daysInPrevMonth = new Date(year, month, 0).getDate();

                // Previous month trailing days
                for (let i = firstDayIndex - 1; i >= 0; i--) {
                    const dayNum = daysInPrevMonth - i;
                    const cell = document.createElement('div');
                    cell.className = 'cal-day-cell other-month';
                    cell.textContent = dayNum;
                    gridEl.appendChild(cell);
                }

                // Current month days
                for (let day = 1; day <= daysInMonth; day++) {
                    const cell = document.createElement('div');
                    cell.className = 'cal-day-cell';
                    cell.textContent = day;

                    const isToday = day === realToday.getDate() &&
                                    month === realToday.getMonth() &&
                                    year === realToday.getFullYear();

                    const isSelected = selectedDate &&
                                       day === selectedDate.getDate() &&
                                       month === selectedDate.getMonth() &&
                                       year === selectedDate.getFullYear();

                    if (isToday) {
                        cell.classList.add('today');
                    } else if (isSelected) {
                        cell.classList.add('selected');
                    }

                    cell.addEventListener('click', function () {
                        selectedDate = new Date(year, month, day);
                        updateSelectedLabel();
                        renderCalendar(year, month);
                    });

                    gridEl.appendChild(cell);
                }

                // Next month leading days to complete grid row
                const totalCells = firstDayIndex + daysInMonth;
                const remaining = (7 - (totalCells % 7)) % 7;
                for (let nextDay = 1; nextDay <= remaining; nextDay++) {
                    const cell = document.createElement('div');
                    cell.className = 'cal-day-cell other-month';
                    cell.textContent = nextDay;
                    gridEl.appendChild(cell);
                }

                updateSelectedLabel();
            }

            if (prevBtn) {
                prevBtn.addEventListener('click', function () {
                    currentMonth--;
                    if (currentMonth < 0) {
                        currentMonth = 11;
                        currentYear--;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', function () {
                    currentMonth++;
                    if (currentMonth > 11) {
                        currentMonth = 0;
                        currentYear++;
                    }
                    renderCalendar(currentYear, currentMonth);
                });
            }

            if (todayBtn) {
                todayBtn.addEventListener('click', function () {
                    currentYear = realToday.getFullYear();
                    currentMonth = realToday.getMonth();
                    selectedDate = new Date(realToday.getFullYear(), realToday.getMonth(), realToday.getDate());
                    renderCalendar(currentYear, currentMonth);
                });
            }

            renderCalendar(currentYear, currentMonth);
        }

        initDashboardLiveClock();
        initDashboardCalendarWidget();


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
