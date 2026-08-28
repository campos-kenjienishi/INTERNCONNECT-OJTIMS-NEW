/* Student Scripts */

const SIDEBAR_COLLAPSED_KEY = 'internconnect_sidebar_collapsed';
const sidebar     = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle  = document.getElementById('menuToggle');
const overlay     = document.getElementById('sidebarOverlay');

// Restore persisted desktop sidebar state
if (localStorage.getItem(SIDEBAR_COLLAPSED_KEY) === 'true' && window.innerWidth > 900) {
    if (sidebar) sidebar.classList.add('collapsed');
    if (mainContent) mainContent.classList.add('expanded');
    document.documentElement.classList.add('sidebar-is-collapsed');
}

function openMobileSidebar() {
    if (sidebar) sidebar.classList.add('mobile-open');
    if (overlay) {
        overlay.classList.add('active');
        overlay.style.display = 'block';
    }
}

function closeMobileSidebar() {
    if (sidebar) sidebar.classList.remove('mobile-open');
    if (overlay) {
        overlay.classList.remove('active');
        overlay.style.display = 'none';
    }
}

if (menuToggle) {
    menuToggle.addEventListener('click', function () {
        const isMobile = window.innerWidth <= 900;
        if (isMobile) {
            if (sidebar && sidebar.classList.contains('mobile-open')) {
                closeMobileSidebar();
            } else {
                openMobileSidebar();
            }
        } else {
            if (sidebar) sidebar.classList.toggle('collapsed');
            if (mainContent) mainContent.classList.toggle('expanded');
            const isCollapsed = sidebar ? sidebar.classList.contains('collapsed') : false;
            localStorage.setItem(SIDEBAR_COLLAPSED_KEY, isCollapsed ? 'true' : 'false');
            if (isCollapsed) {
                document.documentElement.classList.add('sidebar-is-collapsed');
            } else {
                document.documentElement.classList.remove('sidebar-is-collapsed');
            }
        }
    });
}

if (overlay) {
    overlay.addEventListener('click', closeMobileSidebar);
}

$(document).ready(function() {
    if ($.fn.select2) {
        $('select[name="adviser_name"]').select2({
            placeholder: 'Select your Professor',
            allowClear: true,
            width: '100%'
        });
    }
});
