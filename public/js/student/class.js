/* Student Class Scripts */

$(document).ready(function () {
    if ($.fn.select2) {
        $('select[name="adviser_name"]').select2({
            placeholder: 'Select your Professor',
            allowClear: true,
            width: '100%'
        });
    }

    // Sidebar toggle
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle = document.getElementById('menuToggle');
    const overlay = document.getElementById('sidebarOverlay');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
            }
        });
    }

    if (overlay && sidebar) {
        overlay.addEventListener('click', function () {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    var modalEl = document.getElementById('filePreviewModal');
    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            var frame = document.getElementById('filePreviewFrame');
            if (frame) frame.src = 'about:blank';
        });
    }

    // Initialize Room Templates pagination, search & sort
    initRoomTemplates();

    // Initialize Class Announcements search & sort
    initAnnouncements();
});

// Join student
window.joinStudent = function (url) {
    const csrfToken = window.studentClassConfig?.csrfToken || $('meta[name="csrf-token"]').attr('content') || '';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Join this room?',
            text: 'You will be added to this class.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-sign-in-alt"></i> Yes, Join',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'swal-poppins'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    data: { _token: csrfToken },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'Successfully joined the room!',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1800
                        });
                        setTimeout(() => location.reload(), 1800);
                    },
                    error: function () {
                        Swal.fire('Oops!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    } else {
        if (confirm('Join this room? You will be added to this class.')) {
            $.ajax({
                type: 'POST',
                url: url,
                data: { _token: csrfToken },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    }
};

// Leave student
window.leaveStudent = function () {
    const csrfToken = window.studentClassConfig?.csrfToken || $('meta[name="csrf-token"]').attr('content') || '';
    const leaveUrl = window.studentClassConfig?.leaveUrl || '/student/leave';
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Leave this room?',
            text: 'You will be removed from this class.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-sign-out-alt"></i> Yes, Leave',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'POST',
                    url: leaveUrl,
                    data: { _token: csrfToken },
                    success: function () {
                        Swal.fire({
                            toast: true,
                            icon: 'success',
                            title: 'You left the room',
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 1800
                        });
                        setTimeout(() => location.reload(), 1800);
                    },
                    error: function (xhr) {
                        console.error(xhr.responseText);
                        Swal.fire('Oops!', 'Something went wrong.', 'error');
                    }
                });
            }
        });
    } else {
        if (confirm('Leave this room? You will be removed from this class.')) {
            $.ajax({
                type: 'POST',
                url: leaveUrl,
                data: { _token: csrfToken },
                success: function () {
                    location.reload();
                },
                error: function () {
                    alert('Something went wrong.');
                }
            });
        }
    }
};

// File preview modal handler
$(document).on('click', '.btn-preview-file', function (e) {
    e.preventDefault();
    var fileUrl = $(this).data('file-url');
    var fileName = $(this).data('file-name');
    var downloadUrl = $(this).data('download-url');

    $('#filePreviewTitle').text(fileName || 'Document Preview');
    $('#filePreviewSubTitle').text(fileName || '');
    $('#filePreviewDownloadBtn').attr('href', downloadUrl);
    $('#filePreviewFrame').attr('src', fileUrl);

    var modalEl = document.getElementById('filePreviewModal');
    if (modalEl && modalEl.parentNode !== document.body) {
        document.body.appendChild(modalEl);
    }
    if (modalEl && window.bootstrap && window.bootstrap.Modal) {
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
    }
});

// ==========================================
// Room Templates: Search, Sort & Pagination (Max 3 per page)
// ==========================================
function initRoomTemplates() {
    const gridContainer = document.getElementById('templateGridContainer');
    if (!gridContainer) return;

    const cards = Array.from(gridContainer.querySelectorAll('.doc-template-card'));
    if (cards.length === 0) return;

    const searchInput = document.getElementById('templateSearchInput');
    const sortSelect = document.getElementById('templateSortSelect');
    const templateCount = document.getElementById('templateCount');
    const emptySearch = document.getElementById('templateEmptySearch');
    const paginationWrapper = document.getElementById('templatePaginationWrapper');
    const pageRangeEl = document.getElementById('templatePageRange');
    const totalCountEl = document.getElementById('templateTotalCount');
    const paginationControls = document.getElementById('templatePaginationControls');

    const ITEMS_PER_PAGE = 3;
    let currentPage = 1;
    let filteredCards = [...cards];

    function applySort(items) {
        const sortVal = sortSelect ? sortSelect.value : 'newest';
        items.sort((a, b) => {
            const timeA = parseInt(a.getAttribute('data-timestamp') || '0', 10);
            const timeB = parseInt(b.getAttribute('data-timestamp') || '0', 10);
            const nameA = (a.getAttribute('data-name') || '').toLowerCase();
            const nameB = (b.getAttribute('data-name') || '').toLowerCase();

            switch (sortVal) {
                case 'oldest':
                    return timeA - timeB;
                case 'az':
                    return nameA.localeCompare(nameB);
                case 'za':
                    return nameB.localeCompare(nameA);
                case 'newest':
                default:
                    return timeB - timeA;
            }
        });
        return items;
    }

    function render(shouldScroll) {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();

        // 1. Filter
        filteredCards = cards.filter(card => {
            if (!query) return true;
            const name = (card.getAttribute('data-name') || '').toLowerCase();
            const file = (card.getAttribute('data-file') || '').toLowerCase();
            return name.includes(query) || file.includes(query);
        });

        // 2. Sort
        applySort(filteredCards);

        // Reorder DOM elements in container
        filteredCards.forEach(card => gridContainer.appendChild(card));

        const totalItems = filteredCards.length;
        const totalPages = Math.ceil(totalItems / ITEMS_PER_PAGE) || 1;

        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        // 3. Handle Empty State
        if (totalItems === 0) {
            cards.forEach(card => card.classList.add('template-card-hidden'));
            gridContainer.style.display = 'none';
            if (emptySearch) emptySearch.style.display = 'block';
            if (paginationWrapper) paginationWrapper.style.display = 'none';
            if (templateCount) templateCount.textContent = '0';
            return;
        }

        if (emptySearch) emptySearch.style.display = 'none';
        gridContainer.style.display = 'grid';

        // 4. Paginate - Max 3 items visible
        const startIndex = (currentPage - 1) * ITEMS_PER_PAGE;
        const endIndex = Math.min(startIndex + ITEMS_PER_PAGE, totalItems);

        cards.forEach(card => {
            const indexInFiltered = filteredCards.indexOf(card);
            if (indexInFiltered >= startIndex && indexInFiltered < endIndex) {
                card.classList.remove('template-card-hidden');
            } else {
                card.classList.add('template-card-hidden');
            }
        });

        // 5. Update Counters
        if (templateCount) {
            if (totalItems <= ITEMS_PER_PAGE) {
                templateCount.textContent = totalItems;
            } else {
                templateCount.textContent = `${startIndex + 1}–${endIndex} of ${totalItems}`;
            }
        }

        // 6. Update Pagination UI
        if (paginationWrapper) {
            if (totalPages <= 1) {
                paginationWrapper.style.display = 'none';
            } else {
                paginationWrapper.style.display = 'flex';
                if (pageRangeEl) pageRangeEl.textContent = `${startIndex + 1}–${endIndex}`;
                if (totalCountEl) totalCountEl.textContent = totalItems;
                renderPaginationButtons(totalPages);
            }
        }

        if (shouldScroll) {
            const containerRect = gridContainer.getBoundingClientRect();
            if (containerRect.top < 70 || containerRect.top > window.innerHeight) {
                gridContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    }

    function renderPaginationButtons(totalPages) {
        if (!paginationControls) return;
        paginationControls.innerHTML = '';

        // Previous button
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = `template-page-btn nav-btn ${currentPage === 1 ? 'disabled' : ''}`;
        prevBtn.innerHTML = '<i class="fa fa-chevron-left"></i> Prev';
        prevBtn.setAttribute('aria-label', 'Previous Page');
        if (currentPage > 1) {
            prevBtn.addEventListener('click', () => {
                currentPage--;
                render(true);
            });
        }
        paginationControls.appendChild(prevBtn);

        // Page number buttons
        for (let p = 1; p <= totalPages; p++) {
            const pageBtn = document.createElement('button');
            pageBtn.type = 'button';
            pageBtn.className = `template-page-btn ${p === currentPage ? 'active' : ''}`;
            pageBtn.textContent = p;
            pageBtn.setAttribute('aria-label', `Page ${p}`);
            if (p !== currentPage) {
                const targetPage = p;
                pageBtn.addEventListener('click', () => {
                    currentPage = targetPage;
                    render(true);
                });
            }
            paginationControls.appendChild(pageBtn);
        }

        // Next button
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = `template-page-btn nav-btn ${currentPage === totalPages ? 'disabled' : ''}`;
        nextBtn.innerHTML = 'Next <i class="fa fa-chevron-right"></i>';
        nextBtn.setAttribute('aria-label', 'Next Page');
        if (currentPage < totalPages) {
            nextBtn.addEventListener('click', () => {
                currentPage++;
                render(true);
            });
        }
        paginationControls.appendChild(nextBtn);
    }

    // Search input event
    if (searchInput) {
        searchInput.addEventListener('input', function () {
            currentPage = 1;
            render(false);
        });
    }

    // Sort select event
    if (sortSelect) {
        sortSelect.addEventListener('change', function () {
            currentPage = 1;
            render(false);
        });
    }

    // Initial render
    render(false);
}

// ==========================================
// Class Announcements: Search & Sort
// ==========================================
function initAnnouncements() {
    const feedStream = document.getElementById('announcementFeedStream');
    if (!feedStream) return;

    const cards = Array.from(feedStream.querySelectorAll('.announcement-feed-card'));
    if (cards.length === 0) return;

    const searchInput = document.getElementById('announcementSearchInput');
    const sortSelect = document.getElementById('announcementSortSelect');
    const announcementCount = document.getElementById('announcementCount');
    const emptySearch = document.getElementById('announcementEmptySearch');

    function applySort(items) {
        const sortVal = sortSelect ? sortSelect.value : 'newest';
        items.sort((a, b) => {
            const timeA = parseInt(a.getAttribute('data-timestamp') || '0', 10);
            const timeB = parseInt(b.getAttribute('data-timestamp') || '0', 10);
            return sortVal === 'oldest' ? timeA - timeB : timeB - timeA;
        });
        return items;
    }

    function render() {
        const query = (searchInput ? searchInput.value : '').trim().toLowerCase();

        const filtered = cards.filter(card => {
            if (!query) return true;
            const title = (card.getAttribute('data-title') || '').toLowerCase();
            const content = (card.getAttribute('data-content') || '').toLowerCase();
            const announcer = (card.getAttribute('data-announcer') || '').toLowerCase();
            return title.includes(query) || content.includes(query) || announcer.includes(query);
        });

        applySort(filtered);
        filtered.forEach(card => feedStream.appendChild(card));

        cards.forEach(card => {
            if (filtered.includes(card)) {
                card.classList.remove('announcement-card-hidden');
            } else {
                card.classList.add('announcement-card-hidden');
            }
        });

        if (announcementCount) {
            announcementCount.textContent = filtered.length;
        }

        if (emptySearch) {
            emptySearch.style.display = filtered.length === 0 ? 'block' : 'none';
        }
        feedStream.style.display = filtered.length === 0 ? 'none' : 'flex';
    }

    if (searchInput) {
        searchInput.addEventListener('input', render);
    }
    if (sortSelect) {
        sortSelect.addEventListener('change', render);
    }

    render();
}


