/* ==========================================================================
   Coordinator Companies Page Scripts
   Extracted from ojtCoordinator/companies.blade.php
   ========================================================================== */

// Sidebar toggle
const sidebar = document.getElementById('sidebar');
const mainContent = document.getElementById('mainContent');
const menuToggle = document.getElementById('menuToggle');
const overlay = document.getElementById('sidebarOverlay');

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

function setCheckedCourseValues(containerSelector, selectedCourses) {
    const normalizedCourses = new Set((selectedCourses || []).map(function (course) {
        return String(course || '').trim().toLowerCase();
    }).filter(Boolean));

    document.querySelectorAll(containerSelector + ' input[name="course[]"]').forEach(function (checkbox) {
        const option = checkbox.closest('.course-option');
        const checkboxValue = (checkbox.value || '').trim().toLowerCase();
        const optionName = (option && option.getAttribute('data-course-name') || '').trim().toLowerCase();
        const optionAcronym = (option && option.getAttribute('data-course-acronym') || '').trim().toLowerCase();
        const labelText = option ? option.textContent.replace(/\s+/g, ' ').trim().toLowerCase() : '';

        checkbox.checked = normalizedCourses.has(checkboxValue)
            || normalizedCourses.has(optionName)
            || normalizedCourses.has(optionAcronym)
            || (labelText ? normalizedCourses.has(labelText) : false);
    });
}

function filterCourseOptions(searchInputId, containerId) {
    const searchInput = document.getElementById(searchInputId);
    const container = document.getElementById(containerId);

    if (!searchInput || !container) {
        return;
    }

    const searchTerm = searchInput.value.trim().toLowerCase();

    container.querySelectorAll('.course-option').forEach(function (option) {
        const courseName = (option.getAttribute('data-course-name') || '').toLowerCase();
        const courseAcronym = (option.getAttribute('data-course-acronym') || '').toLowerCase();
        const visible = !searchTerm || courseName.includes(searchTerm) || courseAcronym.includes(searchTerm);
        option.style.display = visible ? '' : 'none';
    });
}

function setEditSchoolYearFields(startYear, endYear) {
    const startSelect = document.getElementById('edit_school_year_start');
    const endSelect = document.getElementById('edit_school_year_end');

    if (!startSelect || !endSelect) {
        return;
    }

    if (startYear) {
        startSelect.value = String(startYear);
    }

    const resolvedStartYear = parseInt(startSelect.value, 10);
    let resolvedEndYear = parseInt(endYear, 10);

    if (Number.isNaN(resolvedStartYear)) {
        return;
    }

    if (Number.isNaN(resolvedEndYear) || resolvedEndYear <= resolvedStartYear) {
        resolvedEndYear = resolvedStartYear + 1;
    }

    endSelect.innerHTML = '';

    const option = document.createElement('option');
    option.value = String(resolvedEndYear);
    option.textContent = String(resolvedEndYear);
    option.selected = true;
    endSelect.appendChild(option);
    endSelect.value = String(resolvedEndYear);
}

$(document).ready(function () {

    // DataTable - keep server-side ordering by school year
    $('#companyTable').DataTable({
        order: [],
        scrollX: true,
        scrollCollapse: true,
        autoWidth: false,
        columnDefs: [{ targets: 0, visible: false }]
    });

    // Send modal populate
    $(document).on('click', '.btn-open-send', function () {
        const fileId = $(this).data('file-id');
        const companyName = $(this).data('company-name');
        $('#send-file-id').val(fileId);
        $('#send-company-name-input').val(companyName);
        $('#send-company-name').text(companyName);
    });

    const assignStudentsUrl = (window.companiesConfig && window.companiesConfig.assignStudentsUrl) || '/moa/assignable-students';
    const assignableStudentState = {
        mode: null,
        selectedNames: new Set(),
        students: [],
        loading: false,
        searchTimer: null
    };

    const assignTargets = {
        add: {
            courseField: '#moaCourseSelect',
            schoolYearStartField: '#schoolYearStart',
            schoolYearEndField: '#schoolYearEnd',
            summaryField: '#moaAssignedStudentsSummary',
            inputsField: '#moaAssignedStudentInputs'
        },
        edit: {
            courseField: '#editMoaCourseSelect',
            schoolYearStartField: '#edit_school_year_start',
            schoolYearEndField: '#edit_school_year_end',
            summaryField: '#editMoaAssignedStudentsSummary',
            inputsField: '#editMoaAssignedStudentInputs'
        }
    };

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCheckedCourseValues(containerSelector) {
        return Array.from(document.querySelectorAll(containerSelector + ' input[name="course[]"]:checked'))
            .map(function (input) {
                return (input.value || '').trim();
            })
            .filter(Boolean);
    }

    function setCheckedCourseValues(containerSelector, selectedCourses) {
        const normalizedCourses = new Set((selectedCourses || []).map(function (course) {
            return String(course || '').trim().toLowerCase();
        }).filter(Boolean));

        document.querySelectorAll(containerSelector + ' input[name="course[]"]').forEach(function (checkbox) {
            const option = checkbox.closest('.course-option');
            const checkboxValue = (checkbox.value || '').trim().toLowerCase();
            const optionName = (option && option.getAttribute('data-course-name') || '').trim().toLowerCase();
            const optionAcronym = (option && option.getAttribute('data-course-acronym') || '').trim().toLowerCase();
            const labelText = option ? option.textContent.replace(/\s+/g, ' ').trim().toLowerCase() : '';

            checkbox.checked = normalizedCourses.has(checkboxValue)
                || normalizedCourses.has(optionName)
                || normalizedCourses.has(optionAcronym)
                || (labelText ? normalizedCourses.has(labelText) : false);
        });
    }

    function filterCourseOptions(searchInputId, containerId) {
        const searchInput = document.getElementById(searchInputId);
        const container = document.getElementById(containerId);

        if (!searchInput || !container) {
            return;
        }

        const searchTerm = searchInput.value.trim().toLowerCase();

        container.querySelectorAll('.course-option').forEach(function (option) {
            const courseName = (option.getAttribute('data-course-name') || '').toLowerCase();
            const courseAcronym = (option.getAttribute('data-course-acronym') || '').toLowerCase();
            const visible = !searchTerm || courseName.includes(searchTerm) || courseAcronym.includes(searchTerm);
            option.style.display = visible ? '' : 'none';
        });
    }

    function syncSchoolYearEnd(startId, endId, selectedEndYear = null) {
        const startSelect = document.getElementById(startId);
        const endSelect = document.getElementById(endId);

        if (!startSelect || !endSelect || !startSelect.value) {
            return;
        }

        const startYear = parseInt(startSelect.value, 10);

        if (Number.isNaN(startYear)) {
            return;
        }

        const parsedEndYear = selectedEndYear ? parseInt(selectedEndYear, 10) : NaN;
        const endYear = Number.isNaN(parsedEndYear) || parsedEndYear <= startYear
            ? startYear + 1
            : parsedEndYear;
        endSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = String(endYear);
        option.textContent = String(endYear);
        option.selected = true;
        endSelect.appendChild(option);
        endSelect.value = String(endYear);
    }

    function setEditSchoolYearFields(startYear, endYear) {
        const startSelect = document.getElementById('edit_school_year_start');
        const endSelect = document.getElementById('edit_school_year_end');

        if (!startSelect || !endSelect) {
            return;
        }

        if (startYear) {
            startSelect.value = String(startYear);
        }

        const resolvedStartYear = parseInt(startSelect.value, 10);
        let resolvedEndYear = parseInt(endYear, 10);

        if (Number.isNaN(resolvedStartYear)) {
            return;
        }

        if (Number.isNaN(resolvedEndYear) || resolvedEndYear <= resolvedStartYear) {
            resolvedEndYear = resolvedStartYear + 1;
        }

        endSelect.innerHTML = '';

        const option = document.createElement('option');
        option.value = String(resolvedEndYear);
        option.textContent = String(resolvedEndYear);
        option.selected = true;
        endSelect.appendChild(option);
        endSelect.value = String(resolvedEndYear);
    }

    function getTargetConfig(mode) {
        return assignTargets[mode] || null;
    }

    function getTargetValues(mode) {
        const target = getTargetConfig(mode);
        if (!target) {
            return { course: '', schoolYear: '' };
        }

        const courseValues = getCheckedCourseValues(target.courseField);
        const startYear = ($(target.schoolYearStartField).val() || '').trim();
        const endYear = ($(target.schoolYearEndField).val() || '').trim();

        return {
            course: courseValues[0] || '',
            schoolYear: startYear && endYear ? startYear + '-' + endYear : ''
        };
    }

    function syncTargetFields(mode, course, schoolYear) {
        const target = getTargetConfig(mode);
        if (!target) return;

        if ($(target.schoolYearStartField).length && $(target.schoolYearEndField).length) {
            const parts = String(schoolYear || '').split('-');
            $(target.schoolYearStartField).val(parts[0] || '');
            $(target.schoolYearEndField).val(parts[1] || '');
        }
    }

    function renderAssignedSummary(mode, selectedNames) {
        const target = getTargetConfig(mode);
        if (!target) return;

        const summary = document.querySelector(target.summaryField);
        const inputs = document.querySelector(target.inputsField);
        const names = Array.from(selectedNames || []).filter(Boolean);

        if (summary) {
            summary.textContent = names.length
                ? names.length + ' student' + (names.length === 1 ? '' : 's') + ' selected.'
                : 'No students assigned yet.';
        }

        if (inputs) {
            inputs.innerHTML = names.map(function (name) {
                return '<input type="hidden" name="student_names[]" value="' + escapeHtml(name) + '">';
            }).join('');
        }
    }

    function renderAssignableSelectedChips() {
        const chips = document.getElementById('assignStudentsSelectedChips');
        const info = document.getElementById('assignStudentsSelectedInfo');
        if (!chips || !info) return;

        const names = Array.from(assignableStudentState.selectedNames);
        info.textContent = names.length
            ? names.length + ' student' + (names.length === 1 ? '' : 's') + ' selected.'
            : 'No students selected yet.';

        chips.innerHTML = names.length
            ? names.map(function (name) {
                return '<button type="button" class="student-pill" data-selected-name="' + escapeHtml(name) + '" style="border:none; cursor:pointer; display:inline-flex; align-items:center; gap:6px;">'
                    + '<i class="fa fa-times" style="font-size:10px;"></i>'
                    + escapeHtml(name)
                    + '</button>';
            }).join('')
            : '<span style="font-size:12px; color:#6b7280;">Selected students will appear here.</span>';
    }

    function getActiveAssignFilters() {
        return {
            course: ($('#assignStudentsCourse').val() || '').trim(),
            schoolYear: ($('#assignStudentsSchoolYear').val() || '').trim(),
            search: ($('#assignStudentsSearch').val() || '').trim()
        };
    }

    function setAssignStatus(message, isError) {
        const status = document.getElementById('assignStudentsStatus');
        if (!status) return;

        if (!message) {
            status.style.display = 'none';
            status.textContent = '';
            return;
        }

        status.style.display = 'block';
        status.textContent = message;
        status.style.borderColor = isError ? '#fca5a5' : '#f3b3b3';
        status.style.background = isError ? '#fef2f2' : '#fff7f7';
    }

    function renderAssignableStudentsList() {
        const list = document.getElementById('assignStudentsList');
        const { course } = getActiveAssignFilters();

        if (!list) return;

        if (assignableStudentState.loading) {
            list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">Loading students...</div>';
            return;
        }

        if (!course) {
            list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">Choose a course to load matching students.</div>';
            setAssignStatus('', false);
            return;
        }

        if (!assignableStudentState.students.length) {
            list.innerHTML = '<div style="padding:18px; text-align:center; color:#6b7280; border:1px dashed #f3b3b3; border-radius:12px; background:#fff;">No students found for the selected filters.</div>';
            setAssignStatus('No students matched the current filters.', false);
            return;
        }

        setAssignStatus('Loaded ' + assignableStudentState.students.length + ' student' + (assignableStudentState.students.length === 1 ? '' : 's') + ' for the current filters.', false);

        list.innerHTML = assignableStudentState.students.map(function (student) {
            const checked = assignableStudentState.selectedNames.has(student.full_name) ? 'checked' : '';
            return '<label style="display:flex; align-items:flex-start; gap:12px; padding:14px 15px; border:1px solid #f1d5d5; border-radius:14px; background:#fff; cursor:pointer; transition:all .15s ease;">'
                + '<input type="checkbox" class="assign-student-checkbox" data-student-name="' + escapeHtml(student.full_name) + '" ' + checked + ' style="margin-top:4px; width:18px; height:18px;">'
                + '<div style="flex:1; min-width:0;">'
                + '<div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap;">'
                + '<span style="font-size:14px; font-weight:700; color:#111827;">' + escapeHtml(student.full_name) + '</span>'
                + '<span style="font-size:11px; padding:3px 8px; border-radius:999px; background:#eff6ff; color:#2563eb; font-weight:700;">' + escapeHtml(student.student_num || 'No ID') + '</span>'
                + '</div>'
                + '<div style="font-size:12px; color:#6b7280; margin-top:4px;">'
                + escapeHtml(student.course || 'No course') + ' • '
                + escapeHtml(student.year_and_section || 'No section') + ' • '
                + escapeHtml(student.school_year || 'No school year')
                + '</div>'
                + '</div>'
                + '</label>';
        }).join('');

        renderAssignableSelectedChips();
    }

    function fetchAssignableStudents() {
        const { course, schoolYear, search } = getActiveAssignFilters();

        assignableStudentState.loading = true;
        renderAssignableStudentsList();

        $.getJSON(assignStudentsUrl, {
            course: course,
            school_year: schoolYear,
            search: search
        }).done(function (response) {
            assignableStudentState.students = Array.isArray(response.students) ? response.students : [];
        }).fail(function () {
            assignableStudentState.students = [];
            setAssignStatus('Unable to load students right now. Please try again.', true);
        }).always(function () {
            assignableStudentState.loading = false;
            renderAssignableStudentsList();
        });
    }

    function resetAssignableSelection() {
        assignableStudentState.selectedNames.clear();
        renderAssignableSelectedChips();
        renderAssignableStudentsList();
    }

    function openAssignStudentsModal(mode) {
        assignableStudentState.mode = mode;

        const target = getTargetConfig(mode);
        if (!target) return;

        const values = getTargetValues(mode);
        $('#assignStudentsCourse').val(values.course);
        $('#assignStudentsSchoolYear').val(values.schoolYear);
        $('#assignStudentsSearch').val('');

        const existingNames = Array.from(document.querySelectorAll(target.inputsField + ' input[name="student_names[]"]'))
            .map(function (input) { return (input.value || '').trim(); })
            .filter(Boolean);
        assignableStudentState.selectedNames = new Set(existingNames);

        renderAssignedSummary(mode, assignableStudentState.selectedNames);
        renderAssignableSelectedChips();
        setAssignStatus('', false);

        $('#assignStudentsModal').modal('show');
        fetchAssignableStudents();
    }

    function applyAssignableStudents() {
        if (!assignableStudentState.mode) {
            return;
        }

        const values = getActiveAssignFilters();
        syncTargetFields(assignableStudentState.mode, values.course, values.schoolYear);
        renderAssignedSummary(assignableStudentState.mode, assignableStudentState.selectedNames);

        $('#assignStudentsModal').modal('hide');
    }

    $(document).on('click', '#openAssignStudentsModal', function () {
        openAssignStudentsModal('add');
    });

    $(document).on('click', '#openEditAssignStudentsModal', function () {
        openAssignStudentsModal('edit');
    });

    $(document).on('change', '#assignStudentsCourse, #assignStudentsSchoolYear', function () {
        if (!assignableStudentState.mode) {
            return;
        }

        syncTargetFields(assignableStudentState.mode, $('#assignStudentsCourse').val(), $('#assignStudentsSchoolYear').val());
        resetAssignableSelection();
        fetchAssignableStudents();
    });

    $(document).on('input', '#assignStudentsSearch', function () {
        clearTimeout(assignableStudentState.searchTimer);
        assignableStudentState.searchTimer = setTimeout(function () {
            fetchAssignableStudents();
        }, 250);
    });

    $(document).on('change', '.assign-student-checkbox', function () {
        const studentName = ($(this).data('student-name') || '').trim();
        if (!studentName) {
            return;
        }

        if (this.checked) {
            assignableStudentState.selectedNames.add(studentName);
        } else {
            assignableStudentState.selectedNames.delete(studentName);
        }

        renderAssignableSelectedChips();
    });

    $(document).on('click', '#assignStudentsApply', function () {
        applyAssignableStudents();
    });

    $(document).on('input', '#moaCourseSearch', function () {
        filterCourseOptions('moaCourseSearch', 'moaCourseSelect');
    });

    $(document).on('input', '#editMoaCourseSearch', function () {
        filterCourseOptions('editMoaCourseSearch', 'editMoaCourseSelect');
    });

    syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
    syncSchoolYearEnd('edit_school_year_start', 'edit_school_year_end');

    $('#schoolYearStart').on('change', function () {
        syncSchoolYearEnd('schoolYearStart', 'schoolYearEnd');
    });

    $('#edit_school_year_start').on('change', function () {
        syncSchoolYearEnd('edit_school_year_start', 'edit_school_year_end');
    });

    $(document).on('click', '#assignStudentsClear', function () {
        resetAssignableSelection();
    });

    $(document).on('click', '#assignStudentsSelectedChips [data-selected-name]', function () {
        const studentName = ($(this).data('selected-name') || '').trim();
        if (!studentName) return;

        assignableStudentState.selectedNames.delete(studentName);
        renderAssignableSelectedChips();

        document.querySelectorAll('.assign-student-checkbox').forEach(function (checkbox) {
            if ((checkbox.getAttribute('data-student-name') || '').trim() === studentName) {
                checkbox.checked = false;
            }
        });
    });

    // Form submission debounce

        $('form[action$="/companyCreate"], #editCompanyForm').on('submit', function (e) {
    if (this.dataset.submitting === 'true') {
        e.preventDefault();
        return;
    }

    this.dataset.submitting = 'true';

    const submitButton = this.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = this.id === 'editCompanyForm'
            ? '<i class="fa fa-spinner fa-spin me-1"></i> Saving...'
            : '<i class="fa fa-spinner fa-spin me-1"></i> Uploading...';
    }
});

    });

function confirmRemove(companyId, companyName) {
    Swal.fire({
        title: 'Remove MOA?',
        html: 'This will permanently delete <strong>' + companyName + '</strong> and all associated data.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Remove',
        cancelButtonText: 'Cancel',
    }).then(function (result) {
        if (result.isConfirmed) {
            document.getElementById('remove-form-' + companyId).submit();
        }
    });
}

function parseSchoolYearParts(value) {
    const matches = String(value || '').match(/\d{4}/g) || [];

    if (matches.length < 2) {
        return ['', ''];
    }

    let startYear = parseInt(matches[0], 10);
    let endYear = parseInt(matches[1], 10);

    if (!Number.isNaN(startYear) && !Number.isNaN(endYear) && endYear < startYear) {
        const swap = startYear;
        startYear = endYear;
        endYear = swap;
    }

    return [
        Number.isNaN(startYear) ? '' : String(startYear),
        Number.isNaN(endYear) ? '' : String(endYear)
    ];
}

function parseJsonDataset(value, fallback) {
    if (!value) {
        return fallback;
    }

    try {
        return JSON.parse(value);
    } catch (error) {
        return fallback;
    }
}

function getEditPayload(dataset) {
    const payload = parseJsonDataset(dataset.editPayload, null);

    if (!payload || typeof payload !== 'object' || Array.isArray(payload)) {
        return {};
    }

    return payload;
}

function normalizeCourseList(value) {
    return String(value || '')
        .split(/[\r\n,;|\/]+/)
        .map(function (course) {
            return course.trim();
        })
        .filter(function (course) {
            return course.length > 0;
        });
}

function normalizeNameList(value) {
    return String(value || '')
        .split(/[\r\n,;]+/)
        .map(function (name) {
            return name.trim();
        })
        .filter(function (name) {
            return name.length > 0;
        });
}

function normalizeDateInput(value) {
    if (!value) {
        return '';
    }

    const raw = String(value).trim();
    const isoMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})/);
    if (isoMatch) {
        return isoMatch[1] + '-' + isoMatch[2] + '-' + isoMatch[3];
    }

    const slashMatch = raw.match(/^(\d{2})\/(\d{2})\/(\d{4})/);
    if (slashMatch) {
        return slashMatch[3] + '-' + slashMatch[2] + '-' + slashMatch[1];
    }

    const parsed = new Date(raw);
    if (!Number.isNaN(parsed.getTime())) {
        const year = String(parsed.getFullYear());
        const month = String(parsed.getMonth() + 1).padStart(2, '0');
        const day = String(parsed.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    return '';
}

function openEditCompanyModal(button) {
    const dataset = button && button.dataset ? button.dataset : {};
    const payload = getEditPayload(dataset);
    const form = document.getElementById('editCompanyForm');
    const currentFile = dataset.fileName || '';
    const row = button && button.closest ? button.closest('tr') : null;
    const cells = row ? row.querySelectorAll('td') : [];

    const rowSchoolYearText = cells[4] ? cells[4].textContent.trim() : '';
    const rowCourseValues = cells[5]
        ? Array.from(cells[5].querySelectorAll('.course-pill'))
            .map(function (pill) {
                return (pill.textContent || '').trim();
            })
            .filter(Boolean)
        : [];
    const rowStudentValues = cells[6]
        ? Array.from(cells[6].querySelectorAll('.student-pill'))
            .map(function (pill) {
                return (pill.textContent || '').trim();
            })
            .filter(Boolean)
        : [];

    if (!form) {
        return;
    }

    form.action = '/company/' + (dataset.companyId || '');
    $('#edit_company_name').val(payload.company_name || dataset.companyName || '');
    $('#edit_company_address').val(payload.company_address || dataset.companyAddress || '');
    $('#edit_company_rep').val(payload.company_rep || dataset.companyRep || '');
    $('#edit_company_no').val(payload.company_no || dataset.companyNo || '');
    $('#edit_company_email').val(payload.company_email || dataset.companyEmail || '');

    const schoolYearParts = parseSchoolYearParts(
        payload.school_year
        || dataset.schoolYearRaw
        || dataset.schoolYearNormalized
        || dataset.schoolYear
        || rowSchoolYearText
        || ''
    );
    const schoolYearStart = payload.school_year_start || schoolYearParts[0] || dataset.schoolYearStart || '';
    const schoolYearEnd = payload.school_year_end || schoolYearParts[1] || dataset.schoolYearEnd || '';

    setEditSchoolYearFields(schoolYearStart, schoolYearEnd);
    $('#editDateNotarized').val(normalizeDateInput(payload.date_notarized || dataset.dateNotarized || ''));
    $('#editValidUntil').val(normalizeDateInput(payload.valid_until || dataset.validUntil || ''));

    const selectedCourses = normalizeCourseList(payload.course_values && payload.course_values.length ? payload.course_values.join(', ') : (dataset.courseRaw || ''));
    const selectedCoursesFallback = rowCourseValues.length
        ? rowCourseValues
        : parseJsonDataset(dataset.courseValues, []);
    setCheckedCourseValues('#editMoaCourseSelect', selectedCourses.length ? selectedCourses : selectedCoursesFallback);
    const manualStudents = normalizeNameList((payload.manual_students && payload.manual_students.length ? payload.manual_students.join(', ') : (dataset.manualStudentsRaw || '')));
    const manualStudentsFallback = parseJsonDataset(dataset.manualStudents, []);
    $('#editManualStudentInput').val((manualStudents.length ? manualStudents : (manualStudentsFallback || [])).join(', '));

    const courseSearch = document.getElementById('editMoaCourseSearch');
    if (courseSearch) {
        courseSearch.value = '';
    }

    filterCourseOptions('editMoaCourseSearch', 'editMoaCourseSelect');

    const selectedStudents = normalizeNameList((payload.selected_students && payload.selected_students.length ? payload.selected_students.join(', ') : (dataset.selectedStudentsRaw || '')));
    const selectedStudentsFallback = rowStudentValues.length
        ? rowStudentValues
        : parseJsonDataset(dataset.selectedStudents, []);
    const resolvedSelectedStudents = selectedStudents.length ? selectedStudents : (selectedStudentsFallback || []);
    const selectedSet = new Set(resolvedSelectedStudents);
    const editAssignedInputs = document.getElementById('editMoaAssignedStudentInputs');
    const editAssignedSummary = document.getElementById('editMoaAssignedStudentsSummary');

    if (editAssignedInputs) {
        editAssignedInputs.innerHTML = '';
        resolvedSelectedStudents.forEach(function (studentName) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'student_names[]';
            input.value = studentName;
            editAssignedInputs.appendChild(input);
        });
    }

    if (editAssignedSummary) {
        editAssignedSummary.textContent = selectedSet.size
            ? selectedSet.size + ' student' + (selectedSet.size === 1 ? '' : 's') + ' selected.'
            : 'No students assigned yet.';
    }

    const currentFileNode = document.getElementById('editMoaCurrentFile');
    if (currentFileNode) {
        currentFileNode.textContent = currentFile
            ? 'Current file: ' + currentFile + '. Leave the file empty if you only need to update the company details.'
            : 'Leave the file empty if you only need to update the company details.';
    }

    const fileInput = document.getElementById('editMoaFileInput');
    const fileLabel = document.getElementById('editMoaFileLabel');
    if (fileInput) {
        fileInput.value = '';
    }
    if (fileLabel) {
        fileLabel.textContent = 'Leave empty to keep the current notarized MOA PDF';
    }
}

// Print functions
function openViewModal(routeUrl) {
    document.getElementById('viewIframe').src = routeUrl;
    $('#viewModal').modal('show');
}

function printUploadedMoa(fileUrl) {
    if (!fileUrl) return;

    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    iframe.src = fileUrl;
    document.body.appendChild(iframe);

    iframe.onload = function () {
        try {
            const pdfWindow = iframe.contentWindow;
            pdfWindow.focus();
            pdfWindow.print();
        } catch (error) {
            window.open(fileUrl, '_blank');
        } finally {
            setTimeout(function () {
                if (iframe.parentNode) {
                    iframe.parentNode.removeChild(iframe);
                }
            }, 1000);
        }
    };
}

function printRegularPreview() {
    const iframe = document.getElementById('viewIframe');
    if (iframe.contentDocument.readyState === 'complete') {
        iframe.contentWindow.print();
    } else {
        iframe.onload = function () { iframe.contentWindow.print(); };
    }
}
// Dark mode toggle
