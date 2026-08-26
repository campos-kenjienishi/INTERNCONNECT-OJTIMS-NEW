/* ==========================================================================
   Coordinator Student Requirements Page Scripts
   Extracted from ojtCoordinator/studentRequirements.blade.php
   ========================================================================== */

    function showDenialReason(catName, reason) {
        Swal.fire({
            title: `Denied Requirement: ${catName}`,
            text: reason || 'No denial reason provided.',
            icon: 'error',
            confirmButtonColor: '#dc2626'
        });
    }

    function openPdfPreviewModal(url, title, downloadUrl) {
        document.getElementById('pdfPreviewTitle').innerText = title || 'Document Preview';
        document.getElementById('pdfPreviewIframe').src = url;
        document.getElementById('pdfDownloadLink').href = downloadUrl || url;

        const modal = new bootstrap.Modal(document.getElementById('pdfPreviewModal'));
        modal.show();
    }

    function openStudentFolderModal(data) {
        document.getElementById('modalFolderName').innerText = data.student_name + "'s Requirements";
        document.getElementById('modalFolderSub').innerText = (data.student_num ? data.student_num + ' • ' : '') + data.course + ' (Adviser: ' + data.adviser + ')';

        const listContainer = document.getElementById('modalFilesList');
        listContainer.innerHTML = '';

        const categories = data.categories || {};
        const catKeys = Object.keys(categories);

        if (catKeys.length === 0) {
            listContainer.innerHTML = `
                <div style="text-align: center; padding: 30px; color: #94a3b8;">
                    <i class="fa fa-folder-open" style="font-size: 36px; margin-bottom: 10px; opacity: 0.5;"></i>
                    <p style="font-size: 14px; font-weight: 600; margin: 0;">No basic requirement categories found.</p>
                </div>
            `;
        } else {
            catKeys.forEach(catName => {
                const info = categories[catName];
                let badgeHtml = '';
                let actionHtml = '';

                if (info.is_inhouse_waived) {
                    badgeHtml = '<span class="req-badge req-approved" style="background:#d1fae5; color:#047857; border-color:#a7f3d0;"><i class="fa fa-university me-1"></i> Waived (In-House OJT)</span>';
                    actionHtml = '<span style="font-size:12px; color:#047857; font-weight:600;"><i class="fa fa-check-circle me-1"></i> School In-House OJT</span>';
                } else if (info.submitted || info.file_id) {
                    badgeHtml = '<span class="req-badge req-approved"><i class="fa fa-check-circle me-1"></i> Uploaded</span>';
                    actionHtml = `
                        <button type="button" onclick="openPdfPreviewModal('/coordinator/requirements/view/${info.file_id}', '${catName.replace(/'/g, "\\'")}', '/coordinator/requirements/download/${info.file_id}')" class="btn-action view-personal">
                            <i class="fa fa-eye"></i> View PDF
                        </button>
                        <a href="/coordinator/requirements/download/${info.file_id}" class="btn-action view-personal" style="background:#f1f5f9; border-color:#cbd5e1; color:#475569; text-decoration:none;">
                            <i class="fa fa-download"></i> Download
                        </a>
                    `;
                } else {
                    badgeHtml = '<span class="req-badge req-missing"><i class="fa fa-minus-circle me-1"></i> Missing</span>';
                    actionHtml = '<span style="font-size:12px; color:#94a3b8; font-style:italic;">Not Uploaded</span>';
                }

                listContainer.innerHTML += `
                    <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 14px 18px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                        <div>
                            <div style="font-size: 14px; font-weight: 700; color: #0f172a; display: flex; align-items: center; gap: 8px;">
                                <i class="fa fa-file-pdf" style="color: #dc2626; font-size: 16px;"></i> ${catName}
                            </div>
                            <div style="font-size: 12px; color: #64748b; margin-top: 2px;">
                                ${info.file_name ? info.file_name : 'No file attached'}
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            ${badgeHtml}
                            ${actionHtml}
                        </div>
                    </div>
                `;
            });
        }

        const modal = new bootstrap.Modal(document.getElementById('studentFilesModal'));
        modal.show();
    }

    function confirmToggleInhouse(studentId, studentName, isRevoke) {
        if (isRevoke) {
            Swal.fire({
                title: 'Revoke In-House OJT Status?',
                text: 'This will remove the School In-House OJT waiver for ' + studentName + ' and require an external notarized MOA again.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa fa-undo me-1"></i> Yes, Revoke Waiver',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inhouse-form-' + studentId).submit();
                }
            });
        } else {
            Swal.fire({
                title: 'Grant School In-House OJT Waiver?',
                text: 'This will waive the external notarized MOA requirement for ' + studentName + ' and unlock all requirement submission slots.',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0284c7',
                cancelButtonColor: '#64748b',
                confirmButtonText: '<i class="fa fa-university me-1"></i> Yes, Grant Waiver',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('inhouse-form-' + studentId).submit();
                }
            });
        }
    }

    $(document).ready(function() {
        $('#requirementsMatrixTable').DataTable({
            scrollX: true,
            order: []
        });

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const menuToggle = document.getElementById('menuToggle');

        if (menuToggle && sidebar && mainContent) {
            menuToggle.addEventListener('click', function () {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
            });
        }
    });
