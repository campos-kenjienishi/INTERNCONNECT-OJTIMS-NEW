/* ==========================================================================
   Professor Classroom & Students Scripts
   Extracted from professor/class.blade.php
   ========================================================================== */
                    $(document).ready(function () {
                        $('#fileTable').DataTable({
                            scrollX: true,
                            autoWidth: false
                        });

                        const profAnnouncementTable = $('#profAnnouncementTable').DataTable({
                            pageLength: 5,
                            lengthMenu: [[5, 10, 25, 50], [5, 10, 25, 50]],
                            order: [[2, 'desc']],
                            columnDefs: [
                                { orderable: false, targets: 3 }
                            ],
                            language: {
                                emptyTable: 'No announcements posted yet.'
                            }
                        });

                        $('#profAnnouncementSort').on('change', function () {
                            profAnnouncementTable.order([2, this.value]).draw();
                        });
                    });
                

    // Sidebar toggle
    const sidebar     = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const menuToggle  = document.getElementById('menuToggle');
    const overlay     = document.getElementById('sidebarOverlay');

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

    document.querySelectorAll('.delete-announcement-form').forEach(function (form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            const title = form.dataset.announcementTitle || 'this announcement';

            Swal.fire({
                title: 'Delete announcement?',
                html: 'This will permanently delete <strong>' + title + '</strong>.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it',
                cancelButtonText: 'Cancel',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

    $(document).ready(function () {
        document.querySelectorAll('[data-template-search]').forEach(function (input) {
            input.addEventListener('input', function () {
                const panel = input.closest('.template-list-panel');
                if (!panel) return;

                const list = panel.querySelector('[data-template-list]');
                const emptyState = panel.querySelector('[data-template-empty-state]');
                if (!list) return;

                const query = (input.value || '').trim().toLowerCase();
                let visibleCount = 0;

                list.querySelectorAll('[data-template-item]').forEach(function (item) {
                    const name = item.getAttribute('data-template-name') || '';
                    const matches = query === '' || name.includes(query);
                    item.style.display = matches ? '' : 'none';
                    if (matches) visibleCount++;
                });

                if (emptyState) {
                    emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
                }
            });
        });

        function buildScheduleMapFromArray(scheduleArray) {
            const map = {};
            if (!Array.isArray(scheduleArray)) return map;
            scheduleArray.forEach(item => {
                if (!item || !item.day) return;
                if (!map[item.day]) map[item.day] = [];
                map[item.day].push({
                    start_time: item.start_time || '',
                    end_time: item.end_time || ''
                });
            });
            return map;
        }

        function buildScheduleMapFromForm(form) {
            const map = {};
            if (!form) return map;

            const timeInputs = form.querySelectorAll('.schedule-time-input[name]');
            timeInputs.forEach(input => {
                const startMatch = input.name.match(/^(.*)_start_time_(\d+)$/);
                const endMatch = input.name.match(/^(.*)_end_time_(\d+)$/);

                if (!startMatch && !endMatch) return;

                const day = (startMatch || endMatch)[1];
                const slotIndex = parseInt((startMatch || endMatch)[2], 10) - 1;
                if (!map[day]) map[day] = [];
                if (!map[day][slotIndex]) {
                    map[day][slotIndex] = { start_time: '', end_time: '' };
                }

                if (startMatch) {
                    map[day][slotIndex].start_time = input.value || '';
                }
                if (endMatch) {
                    map[day][slotIndex].end_time = input.value || '';
                }
            });

            return map;
        }

        function buildTimeOptions() {
            let options = '';

            for (let hour = 0; hour < 24; hour++) {
                for (let minute = 0; minute < 60; minute += 15) {
                    const value = String(hour).padStart(2, '0') + ':' + String(minute).padStart(2, '0');
                    options += '<option value="' + value + '"></option>';
                }
            }

            return options;
        }

        function ensureTimeSuggestions() {
            if (document.getElementById('scheduleTimeSuggestions')) {
                return;
            }

            const datalist = document.createElement('datalist');
            datalist.id = 'scheduleTimeSuggestions';
            datalist.innerHTML = buildTimeOptions();
            document.body.appendChild(datalist);
        }

        function buildTimeInput(name, selectedValue) {
            return '<input class="modal-field-select schedule-time-input" ' +
                'type="text" ' +
                'name="' + name + '" ' +
                'value="' + (selectedValue || '') + '" ' +
                'placeholder="HH:MM" ' +
                'inputmode="numeric" ' +
                'list="scheduleTimeSuggestions" ' +
                'pattern="^([01]\\d|2[0-3]):([0-5]\\d)$" ' +
                'title="Enter time in 24-hour format like 08:00 or 13:30" ' +
                'required>';
        }

        function renderScheduleInputs(daySelector, slotSelectId, containerId, fallbackScheduleMap) {
            const container = document.getElementById(containerId);
            if (!container) return;

            ensureTimeSuggestions();

            const slotSelect = document.getElementById(slotSelectId);
            const slots = parseInt((slotSelect && slotSelect.value) ? slotSelect.value : '1', 10);
            const selectedDays = Array.from(document.querySelectorAll(daySelector)).map(el => el.value);

            const form = container.closest('form');
            const currentMap = buildScheduleMapFromForm(form);
            const scheduleMap = Object.keys(currentMap).length > 0 ? currentMap : (fallbackScheduleMap || {});

            if (selectedDays.length === 0) {
                container.innerHTML = '<p style="font-size:12px;color:#888;margin:0;">Select at least one day to set time slots.</p>';
                return;
            }

            let html = '';
            selectedDays.forEach(day => {
                html += '<div style="border:1px solid #eee;border-radius:10px;padding:10px;margin-bottom:10px;background:#fafafa;">';
                html += '<strong style="font-size:13px;color:#444;">' + day + '</strong>';

                for (let i = 1; i <= slots; i++) {
                    const startName = day + '_start_time_' + i;
                    const endName = day + '_end_time_' + i;
                    const existing = scheduleMap[day] && scheduleMap[day][i - 1] ? scheduleMap[day][i - 1] : { start_time: '', end_time: '' };

                    html += '<div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:8px;">';
                    html += buildTimeInput(startName, existing.start_time || '');
                    html += buildTimeInput(endName, existing.end_time || '');
                    html += '</div>';
                }

                html += '</div>';
            });

            container.innerHTML = html;
        }

        function renderAddRoomScheduleInputs() {
            renderScheduleInputs('.add-schedule-day:checked', 'add_time_slots', 'addRoomScheduleInputs', {});
        }

        function renderEditRoomScheduleInputs(roomId) {
            const container = document.getElementById('editRoomScheduleInputs' + roomId);
            if (!container) return;

            let initialSchedule = [];
            try {
                initialSchedule = JSON.parse(container.getAttribute('data-initial-schedule') || '[]');
            } catch (e) {
                initialSchedule = [];
            }

            renderScheduleInputs(
                '.edit-schedule-day[data-room-id="' + roomId + '"]:checked',
                'edit_time_slots_' + roomId,
                'editRoomScheduleInputs' + roomId,
                buildScheduleMapFromArray(initialSchedule)
            );
        }

        $(document).on('change', '.add-schedule-day, #add_time_slots', renderAddRoomScheduleInputs);
        $(document).on('change', '.edit-schedule-day, .edit-time-slots', function () {
            const roomId = $(this).data('room-id');
            if (roomId) {
                renderEditRoomScheduleInputs(roomId);
            }
        });

        $(document).on('input change', '.schedule-time-input', function () {
            const isValid = /^([01]\d|2[0-3]):([0-5]\d)$/.test(this.value.trim());
            this.setCustomValidity(this.value.trim() === '' || isValid
                ? ''
                : 'Use HH:MM in 24-hour format, like 08:00 or 13:30.');
        });

        $('[id^="editRoomModal"]').on('shown.bs.modal', function () {
            const roomId = this.id.replace('editRoomModal', '');
            renderEditRoomScheduleInputs(roomId);
        });

        renderAddRoomScheduleInputs();

        // Add Room AJAX
        $('#addRoomForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function () {
                    $('#addRoomModal').modal('hide');
                    form[0].reset();
                    Swal.fire({
                        toast: true, icon: 'success',
                        title: 'Room created successfully!',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000, timerProgressBar: true
                    });
                    setTimeout(() => location.reload(), 2000);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Oops!', 'Error creating room.', 'error');
                }
            });
        });

        // Announcement AJAX
        $('.announcementForm').on('submit', function (e) {
            e.preventDefault();
            var form = $(this);
            var modal = form.closest('.modal');
            $.ajax({
                type: 'POST',
                url: form.attr('action'),
                data: form.serialize(),
                success: function () {
                    modal.modal('hide');
                    form[0].reset();
                    Swal.fire({
                        toast: true, icon: 'success',
                        title: 'Announcement posted successfully!',
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000, timerProgressBar: true
                    });
                    setTimeout(() => location.reload(), 2000);
                },
                error: function (xhr) {
                    console.error(xhr.responseText);
                    Swal.fire('Oops!', 'Error posting announcement.', 'error');
                }
            });
        });

        $('.btn-edit-announcement').on('click', function () {
            $('#editAnnouncementForm').attr('action', $(this).data('announcement-action'));
            $('#editAnnouncementTitle').val($(this).data('announcement-title'));
            $('#editAnnouncementContent').val($(this).data('announcement-content'));
        });

        // Archive Room
        $('.btn-archive-room').on('click', function () {
            let roomId = $(this).data('id');
            Swal.fire({
                title: 'Archive this room?',
                text: 'Archived rooms will disappear from professor and student lists, but the data will stay in the system.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d97706',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-archive"></i> Yes, archive it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/roomArchive/' + roomId,
                        data: { _token: (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || $('meta[name="csrf-token"]').attr('content') || '') },
                        success: function () {
                            Swal.fire({
                                toast: true, icon: 'success',
                                title: 'Room archived successfully!',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 2000);
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Oops!', 'Error archiving room.', 'error');
                        }
                    });
                }
            });
        });

        // Unarchive Room
        $('.btn-unarchive-room').on('click', function () {
            let roomId = $(this).data('id');
            Swal.fire({
                title: 'Restore this room?',
                text: 'This will bring the class back to the active list.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-undo"></i> Yes, restore it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/roomUnarchive/' + roomId,
                        data: { _token: (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || $('meta[name="csrf-token"]').attr('content') || '') },
                        success: function () {
                            Swal.fire({
                                toast: true, icon: 'success',
                                title: 'Room restored successfully!',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => window.location.href = '/professor/class', 1200);
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Oops!', 'Error restoring room.', 'error');
                        }
                    });
                }
            });
        });

        // Delete Room
        $('.btn-remove-room').on('click', function () {
            let roomId = $(this).data('id');
            Swal.fire({
                title: 'Remove this room?',
                text: 'This will permanently delete the room and all its records.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, delete it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: 'POST',
                        url: '/roomDelete/' + roomId,
                        data: { _token: (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || $('meta[name="csrf-token"]').attr('content') || '') },
                        success: function () {
                            Swal.fire({
                                toast: true, icon: 'success',
                                title: 'Room deleted successfully!',
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            setTimeout(() => location.reload(), 2000);
                        },
                        error: function (xhr) {
                            console.error(xhr.responseText);
                            Swal.fire('Oops!', 'Error deleting room.', 'error');
                        }
                    });
                }
            });
        });

        // Delete Room Template (Professor)
        $(document).on('click', '.btn-remove-template', function () {
            const actionUrl = $(this).data('action');

            Swal.fire({
                title: 'Remove this template?',
                text: 'This will permanently delete the template file record.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<i class="fa fa-trash"></i> Yes, remove it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = actionUrl;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || $('meta[name="csrf-token"]').attr('content') || '');

                    form.appendChild(csrf);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        });

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
            var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        });

        document.addEventListener('DOMContentLoaded', function () {
            var modalEl = document.getElementById('filePreviewModal');
            if (modalEl) {
                modalEl.addEventListener('hidden.bs.modal', function () {
                    var frame = document.getElementById('filePreviewFrame');
                    if (frame) frame.src = 'about:blank';
                });
            }
        });

    });
