/* Student Scripts */

    const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle  = document.getElementById('menuToggle');
const overlay     = document.getElementById('sidebarOverlay');

function openMobileSidebar() {
    sidebar.classList.add('mobile-open');
    overlay.style.display = 'block';   // force override the inline display:none
}

function closeMobileSidebar() {
    sidebar.classList.remove('mobile-open');
    overlay.style.display = 'none';
}

menuToggle.addEventListener('click', function () {
    const isMobile = window.innerWidth <= 900;
    if (isMobile) {
        if (sidebar.classList.contains('mobile-open')) {
            closeMobileSidebar();
        } else {
            openMobileSidebar();
        }
    } else {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('expanded');
    }
});

overlay.addEventListener('click', closeMobileSidebar);



    /* ── Live date badge ── */
    const dateEl = document.getElementById('currentDate');
    function updateBadgeDate() {
        dateEl.textContent = new Date().toLocaleDateString('en-US', {
            weekday: 'short', year: 'numeric', month: 'long', day: 'numeric'
        });
    }
    updateBadgeDate();
    setInterval(updateBadgeDate, 60000);

    /* ── Date & Time Modal ── */
    const dtOverlay  = document.getElementById('dtOverlay');
    const dtCloseBtn = document.getElementById('dtCloseBtn');
    const dateBadge  = document.getElementById('dateBadge');

    function openModal() {
        dtOverlay.classList.add('open');
        startClock();
        renderCalendar(calViewYear, calViewMonth);
    }

    dateBadge.addEventListener('click', openModal);
    dateBadge.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openModal(); }
    });

    function closeModal() {
        dtOverlay.classList.remove('open');
        stopClock();
    }

    dtCloseBtn.addEventListener('click', closeModal);
    dtOverlay.addEventListener('click', function (e) { if (e.target === dtOverlay) closeModal(); });
    document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeModal(); });

    /* ── Digital + Analog Clock ── */
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

    /* ── Analog clock marks ── */
    (function buildMarks() {
        const clock = document.getElementById('analogClock');
        for (let i = 0; i < 12; i++) {
            const mark = document.createElement('div');
            mark.className = 'clock-mark';
            mark.style.cssText = `
                position: absolute;
                width:  ${i % 3 === 0 ? 2.5 : 1.5}px;
                height: ${i % 3 === 0 ? 8 : 5}px;
                background: var(--text-muted);
                border-radius: 2px;
                top: 4px;
                left: calc(50% - ${i % 3 === 0 ? 1.25 : 0.75}px);
                transform-origin: center 51px;
                transform: rotate(${i * 30}deg);
            `;
            clock.appendChild(mark);
        }
    })();

    /* ── Calendar ── */
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];
    const today       = new Date();
    let calViewYear   = today.getFullYear();
    let calViewMonth  = today.getMonth();
    let selectedDay   = today.getDate();

    document.getElementById('calPrev').addEventListener('click', function () {
        calViewMonth--;
        if (calViewMonth < 0) { calViewMonth = 11; calViewYear--; }
        renderCalendar(calViewYear, calViewMonth);
    });

    document.getElementById('calNext').addEventListener('click', function () {
        calViewMonth++;
        if (calViewMonth > 11) { calViewMonth = 0; calViewYear++; }
        renderCalendar(calViewYear, calViewMonth);
    });

    function renderCalendar(year, month) {
        document.getElementById('calMonthLabel').textContent = `${MONTHS[month]} ${year}`;
        const grid = document.getElementById('calGrid');
        grid.innerHTML = '';
        DAYS.forEach(d => {
            const el = document.createElement('div');
            el.className = 'cal-day-name';
            el.textContent = d;
            grid.appendChild(el);
        });
        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();
        for (let i = 0; i < firstDay; i++) {
            const el = document.createElement('div');
            el.className = 'cal-day empty';
            grid.appendChild(el);
        }
        for (let d = 1; d <= daysInMonth; d++) {
            const el = document.createElement('div');
            el.className = 'cal-day';
            el.textContent = d;
            const isToday = d === today.getDate() && month === today.getMonth() && year === today.getFullYear();
            const isSel   = d === selectedDay && month === calViewMonth && year === calViewYear;
            if (isToday) el.classList.add('today');
            else if (isSel) el.classList.add('selected');
            el.addEventListener('click', function () {
                selectedDay = d; calViewYear = year; calViewMonth = month;
                renderCalendar(year, month);
            });
            grid.appendChild(el);
        }
    }