/* ==========================================================================
   Professor Dashboard Scripts
   Extracted from professor/home.blade.php
   ========================================================================== */

$(document).ready(function () {
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
       DATE & TIME MODAL
    ══════════════════════════════════════════════ */

    let dtOverlay = document.getElementById('dtOverlay');
const dateBadge = document.getElementById('dateBadge');

// Create modal if it doesn't exist
if (!dtOverlay) {
    const modalHTML = `
        <div class="dt-overlay" id="dtOverlay">
            <div class="dt-modal">
                <div class="dt-modal-header">
                    <div class="dt-header-top">
                        <span class="dt-header-title">Current Date & Time</span>
                        <button class="dt-close-btn" id="dtCloseBtn">
                            <i class="fa fa-times"></i>
                        </button>
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
                        <div class="dt-date-sub" id="dtDateSub">Loading...</div>
                    </div>
                </div>
                <div class="dt-analog-wrap">
                    <div class="analog-clock" id="analogClock">
                        <div class="hand hour-hand" id="hourHand"></div>
                        <div class="hand minute-hand" id="minuteHand"></div>
                        <div class="hand second-hand" id="secondHand"></div>
                        <div class="clock-center"></div>
                    </div>
                </div>
                <div class="dt-calendar">
                    <div class="cal-nav">
                        <button class="cal-nav-btn" id="calPrev"><i class="fa fa-chevron-left"></i></button>
                        <div class="cal-month-label" id="calMonthLabel">January 2024</div>
                        <button class="cal-nav-btn" id="calNext"><i class="fa fa-chevron-right"></i></button>
                    </div>
                    <div class="cal-grid" id="calGrid"></div>
                </div>
            </div>
        </div>
        `;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    dtOverlay = document.getElementById('dtOverlay');
}

function openModal() {
    if (dtOverlay) {
        dtOverlay.classList.add('open');
        startClock();
    }
}

function closeModal() {
    if (dtOverlay) {
        dtOverlay.classList.remove('open');
        stopClock();
    }
}

// Event listeners for modal
const dtCloseBtn = document.getElementById('dtCloseBtn');
if (dtCloseBtn) {
    dtCloseBtn.addEventListener('click', closeModal);
}

if (dtOverlay) {
    dtOverlay.addEventListener('click', function (e) {
        if (e.target === dtOverlay) closeModal();
    });
}

document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeModal();
});

// Open modal when date badge is clicked
if (dateBadge) {
    dateBadge.addEventListener('click', openModal);
}

/* ── Digital Clock ── */
let clockRAF = null;

function startClock() {
    function tick() {
        const now = new Date();
        let h = now.getHours();
        const m = now.getMinutes();
        const s = now.getSeconds();
        const ampm = h >= 12 ? 'PM' : 'AM';
        h = h % 12 || 12;

        document.getElementById('dtHours').textContent = String(h).padStart(2, '0');
        document.getElementById('dtMinutes').textContent = String(m).padStart(2, '0');
        document.getElementById('dtSeconds').textContent = String(s).padStart(2, '0');
        document.getElementById('dtAmPm').textContent = ampm;
        document.getElementById('dtDateSub').textContent =
            now.toLocaleDateString('en-US', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });

        /* ── Analog hands ── */
        const secDeg = s * 6;
        const minDeg = m * 6 + s * 0.1;
        const hourDeg = (h % 12) * 30 + m * 0.5;

        document.getElementById('secondHand').style.transform = 'rotate(' + secDeg + 'deg)';
        document.getElementById('minuteHand').style.transform = 'rotate(' + minDeg + 'deg)';
        document.getElementById('hourHand').style.transform = 'rotate(' + hourDeg + 'deg)';

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
        const angle = i * 30;
        const isMajor = (i % 3 === 0);
        mark.style.cssText = `
            position: absolute;
            width: ${isMajor ? 2.5 : 1.5}px;
            height: ${isMajor ? 8 : 5}px;
            background: currentColor;
            border-radius: 2px;
            top: 4px;
            left: calc(50% - ${isMajor ? 1.25 : 0.75}px);
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
        document.getElementById('calMonthLabel').textContent = MONTHS[month] + ' ' + year;

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