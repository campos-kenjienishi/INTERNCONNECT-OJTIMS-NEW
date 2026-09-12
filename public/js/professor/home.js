/* ==========================================================================
   Professor Dashboard Scripts
   Extracted from professor/home.blade.php
   ========================================================================== */

$(document).ready(function () {
    if (!$('#studentsTable').length) return;

    var table = $('#studentsTable').DataTable({
        "paging": true,
        "info": false,
        "lengthChange": false,
        "pageLength": 8,
        "scrollX": true,
        "scrollCollapse": true,
        "autoWidth": false,
        "order": [[0, 'asc']],
        "language": {
            "emptyTable": "No students to display"
        }
    });

    function updateFilters() {
        var courseVal = $('#courseFilter').val();
        var classVal = $('#classFilter').val();

        $('#classFilter option').each(function () {
            var optCourse = $(this).data('course');
            if (!optCourse) return;

            if (!courseVal || optCourse === courseVal) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });

        if (courseVal && classVal) {
            var $selectedOpt = $('#classFilter option:selected');
            if ($selectedOpt.val() && $selectedOpt.data('course') !== courseVal) {
                $('#classFilter').val('');
                classVal = '';
            }
        }

        table.column(2).search(courseVal ? '^' + $.fn.dataTable.util.escapeRegex(courseVal) + '$' : '', true, false);
        table.column(3).search(classVal ? '^' + $.fn.dataTable.util.escapeRegex(classVal) + '$' : '', true, false);
        table.draw();
    }

    $('#courseFilter').on('change', function () {
        updateFilters();
    });

    $('#classFilter').on('change', function () {
        var selectedCourse = $(this).find('option:selected').data('course');
        if (selectedCourse && !$('#courseFilter').val()) {
            $('#courseFilter').val(selectedCourse);
        }
        updateFilters();
    });
});

// Current date
const dateEl = document.getElementById('currentDate');
if (dateEl) {
    dateEl.textContent = new Date().toLocaleDateString('en-US', {
        weekday: 'short', year: 'numeric',
        month: 'long', day: 'numeric'
    });
}

// Sidebar toggle - wrapped in a safe initialization function
function initSidebarToggle() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    // Ensure all elements exist
    if (!sidebar) {
        console.error('Sidebar element not found');
        return;
    }
    if (!mainContent) {
        console.error('Main content element not found');
        return;
    }
    if (!menuToggle) {
        console.error('Menu toggle element not found');
        return;
    }
    if (!overlay) {
        console.error('Sidebar overlay element not found');
        return;
    }

    // Click handler for menu toggle
    menuToggle.addEventListener('click', function (e) {
        e.preventDefault();
        e.stopPropagation();

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

    // Click handler for overlay
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

    // Handle window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth > 900) {
            closeMobileSidebar();
        } else {
            if (!sidebar.classList.contains('mobile-open')) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
            }
        }
    });
}

// Wait for DOM to be fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initSidebarToggle);
} else {
    // DOM is already loaded
    initSidebarToggle();
}

    /* ══════════════════════════════════════════════
       UNIQUE ACADEMIC CHRONO & CALENDAR HUB
    ══════════════════════════════════════════════ */

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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initDashboardLiveClock();
            initDashboardCalendarWidget();
        });
    } else {
        initDashboardLiveClock();
        initDashboardCalendarWidget();
    }

    /* ══════════════════════════════════════════════
       DASHBOARD CHART.JS VISUALIZATIONS
    ══════════════════════════════════════════════ */
    (function() {
        if (typeof Chart === 'undefined') return;

        function isDarkMode() {
            return document.documentElement.classList.contains('dark-mode') || document.body.classList.contains('dark-mode');
        }

        function getChartThemeColors() {
            var dark = isDarkMode();
            return {
                textColor: dark ? '#94a3b8' : '#64748b',
                gridColor: dark ? 'rgba(255, 255, 255, 0.06)' : 'rgba(0, 0, 0, 0.05)',
                tooltipBg: dark ? '#181b22' : '#ffffff',
                tooltipText: dark ? '#f1f5f9' : '#1e293b',
                tooltipBorder: dark ? '#2e3542' : '#e2e8f0',
            };
        }

        var monthlyChart = null;
        var donutChart = null;

        function initCharts() {
            var cfg = window.professorDashboardConfig || {};

            // Line chart
            var lineEl = document.getElementById('monthlyActivityChart');
            if (lineEl) {
                var theme = getChartThemeColors();
                var monthlyList = cfg.monthlyActivity || [];
                var labels = monthlyList.map(function(i) { return i.label; });
                var sentData = monthlyList.map(function(i) { return i.sent; });
                var submittedData = monthlyList.map(function(i) { return i.submitted; });

                if (labels.length === 0) {
                    labels = ['No data'];
                    sentData = [0];
                    submittedData = [0];
                }

                if (monthlyChart) {
                    monthlyChart.destroy();
                }

                monthlyChart = new Chart(lineEl.getContext('2d'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            {
                                label: 'Requests Sent',
                                data: sentData,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.12)',
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#3b82f6',
                                pointBorderColor: isDarkMode() ? '#1e222b' : '#ffffff',
                                pointBorderWidth: 2,
                                fill: true
                            },
                            {
                                label: 'Submitted',
                                data: submittedData,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16,185,129,0.12)',
                                tension: 0.35,
                                pointRadius: 4,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#10b981',
                                pointBorderColor: isDarkMode() ? '#1e222b' : '#ffffff',
                                pointBorderWidth: 2,
                                fill: true
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                mode: 'index', intersect: false,
                                backgroundColor: theme.tooltipBg,
                                titleColor: theme.tooltipText,
                                bodyColor: theme.tooltipText,
                                borderColor: theme.tooltipBorder,
                                borderWidth: 1, padding: 10, boxPadding: 4, usePointStyle: true
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { color: theme.textColor, font: { size: 11 } } },
                            y: { beginAtZero: true, grid: { color: theme.gridColor }, ticks: { color: theme.textColor, font: { size: 11 }, precision: 0 } }
                        }
                    }
                });
            }

            // Donut chart
            var donutEl = document.getElementById('overviewDonutChart');
            if (donutEl) {
                var theme2 = getChartThemeColors();
                var approved = Number(cfg.approvedStudents || 0);
                var pending = Number(cfg.pendingApprovals || 0);
                var denied = Number(cfg.deniedStudents || 0);
                var inactive = Number(cfg.inactiveStudents || 0);
                var total = approved + pending + denied + inactive;
                var dataValues = total === 0 ? [0, 0, 0, 1] : [approved, pending, denied, inactive];

                if (donutChart) {
                    donutChart.destroy();
                }

                donutChart = new Chart(donutEl.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: ['Approved', 'Pending', 'Denied', 'Inactive'],
                        datasets: [{
                            data: dataValues,
                            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#3b82f6'],
                            borderWidth: 3,
                            borderColor: isDarkMode() ? '#1e222b' : '#ffffff',
                            hoverOffset: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: theme2.tooltipBg,
                                titleColor: theme2.tooltipText,
                                bodyColor: theme2.tooltipText,
                                borderColor: theme2.tooltipBorder,
                                borderWidth: 1, padding: 10, boxPadding: 4, usePointStyle: true,
                                callbacks: {
                                    label: function(context) {
                                        var val = context.raw || 0;
                                        var pct = total > 0 ? Math.round((val / total) * 100) : 0;
                                        return ' ' + context.label + ': ' + val + ' students (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCharts);
        } else {
            initCharts();
        }

        // Dynamic dark mode observation
        var themeObserver = new MutationObserver(function() {
            initCharts();
        });
        themeObserver.observe(document.documentElement, { attributes: true, attributeFilter: ['class', 'data-theme'] });
        themeObserver.observe(document.body, { attributes: true, attributeFilter: ['class', 'data-theme'] });
    })();