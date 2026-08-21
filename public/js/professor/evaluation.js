/* Professor Evaluation Scripts */

        (function () {
            if (typeof window.jQuery === 'undefined' || typeof jQuery.fn.DataTable === 'undefined' || !document.getElementById('classStatusTable')) {
                return;
            }

            const classStatusTable = $('#classStatusTable').DataTable({
                dom: 't<"history-bottom"ip>',
                pageLength: 10,
                lengthMenu: [[10, 25, 50], [10, 25, 50]],
                autoWidth: false,
                language: {
                    emptyTable: 'No classes found for your account.'
                },
                columnDefs: [
                    { targets: [4], orderable: false, searchable: false }
                ]
            });

            $('#classStatusPerPage').on('change', function () {
                classStatusTable.page.len(Number(this.value)).draw();
            });

            $('#classStatusSearch').on('input', function () {
                classStatusTable.search(this.value).draw();
            });
        })();

        (function () {
            const evaluationAiData = (window.professorEvalConfig?.evaluationAiData || []);
            const generateInsightBtn = document.getElementById('generateEvaluationInsightBtn');
            const insightPanel = document.getElementById('evaluationInsightPanel');
            const insightCloseBtn = document.getElementById('evaluationInsightCloseBtn');
            const insightStatus = document.getElementById('evaluationAiStatus');
            const insightIntro = document.getElementById('evaluationInsightIntro');
            const insightResult = document.getElementById('evaluationAiResult');
            const insightSummary = document.getElementById('evaluationAiSummary');
            const insightFindings = document.getElementById('evaluationAiFindings');
            const insightWatchouts = document.getElementById('evaluationAiWatchouts');
            const insightActions = document.getElementById('evaluationAiActions');

            function renderList(target, items, emptyText) {
                if (!target) return;
                target.innerHTML = '';
                const list = Array.isArray(items) && items.length ? items : [emptyText];
                list.forEach(function (item) {
                    const li = document.createElement('li');
                    li.textContent = item;
                    target.appendChild(li);
                });
            }

            function renderEvaluationInsight(data) {
                if (!insightResult || !insightSummary) return;

                insightSummary.textContent = data.summary || 'No AI insight was returned.';
                renderList(insightFindings, data.key_findings, 'No key findings available.');
                renderList(insightWatchouts, data.watchouts, 'No major watchouts detected.');
                renderList(insightActions, data.recommendations, 'No actions suggested.');
                insightResult.style.display = 'block';
                if (insightIntro) insightIntro.style.display = 'none';
            }

            if (generateInsightBtn) {
                generateInsightBtn.addEventListener('click', function () {
                    generateInsightBtn.disabled = true;
                    if (insightPanel) {
                        insightPanel.style.display = 'block';
                    }
                    if (insightStatus) {
                        insightStatus.textContent = 'Generating AI insight...';
                        insightStatus.style.display = 'block';
                    }

                    fetch((window.professorEvalConfig?.aiInsightUrl || '/reports/ai/insight'), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': (window.professorEvalConfig?.csrfToken || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
                        },
                        body: JSON.stringify(evaluationAiData)
                    })
                        .then(function (response) {
                            if (!response.ok) throw new Error('AI insight request failed.');
                            return response.json();
                        })
                        .then(function (data) {
                            renderEvaluationInsight(data);
                            if (insightStatus) {
                                insightStatus.textContent = data.source === 'fallback'
                                    ? ((data.availability && data.availability.message) ? data.availability.message + ' Internal insight shown.' : 'Gemini is unavailable or rate-limited. Internal insight shown.')
                                    : 'AI insight generated.';
                            }
                        })
                        .catch(function () {
                            if (insightStatus) {
                                insightStatus.textContent = 'AI insight could not be generated right now. Please try again later.';
                                insightStatus.style.display = 'block';
                            }
                        })
                        .finally(function () {
                            generateInsightBtn.disabled = false;
                        });
                });
            }

            if (insightCloseBtn && insightPanel) {
                insightCloseBtn.addEventListener('click', function () {
                    insightPanel.style.display = 'none';
                });
            }

            const questionList = document.getElementById('ratingQuestionList');
            const addButton = document.getElementById('addQuestionBlockBtn');
            const template = document.getElementById('questionBlockTemplate');
            const questionBlockNotice = document.getElementById('questionBlockNotice');
            const removeQuestionConfirm = document.getElementById('removeQuestionConfirm');
            const removeQuestionCancelBtn = document.getElementById('removeQuestionCancelBtn');
            const removeQuestionConfirmBtn = document.getElementById('removeQuestionConfirmBtn');
            const removeQuestionPreviewSection = document.getElementById('removeQuestionPreviewSection');
            const removeQuestionPreviewLabel = document.getElementById('removeQuestionPreviewLabel');
            let questionNoticeTimeout = null;
            let pendingRemoveRow = null;

            if (!questionList || !addButton || !template) {
                return;
            }

            function makeKey() {
                return 'new_' + Date.now() + '_' + Math.random().toString(36).slice(2, 8);
            }

            function showQuestionBlockNotice(message) {
                if (!questionBlockNotice) return;

                questionBlockNotice.textContent = message;
                questionBlockNotice.style.display = 'block';

                if (questionNoticeTimeout) {
                    clearTimeout(questionNoticeTimeout);
                }

                questionNoticeTimeout = window.setTimeout(function () {
                    questionBlockNotice.style.display = 'none';
                }, 2500);
            }

            function closeRemoveConfirm() {
                if (!removeQuestionConfirm) return;

                removeQuestionConfirm.classList.remove('open');
                removeQuestionConfirm.setAttribute('aria-hidden', 'true');
                document.body.classList.remove('modal-open');
                pendingRemoveRow = null;
            }

            function openRemoveConfirm(row) {
                if (!removeQuestionConfirm) return;

                const sectionInput = row ? row.querySelector('input[name="item_sections[]"]') : null;
                const labelInput = row ? row.querySelector('input[name="item_labels[]"]') : null;
                const sectionText = sectionInput && sectionInput.value.trim() ? sectionInput.value.trim() : 'No section entered';
                const labelText = labelInput && labelInput.value.trim() ? labelInput.value.trim() : 'No question entered';

                pendingRemoveRow = row;
                if (removeQuestionPreviewSection) {
                    removeQuestionPreviewSection.textContent = sectionText;
                }
                if (removeQuestionPreviewLabel) {
                    removeQuestionPreviewLabel.textContent = labelText;
                }
                removeQuestionConfirm.classList.add('open');
                removeQuestionConfirm.setAttribute('aria-hidden', 'false');
                document.body.classList.add('modal-open');

                if (removeQuestionConfirmBtn) {
                    removeQuestionConfirmBtn.focus();
                }
            }

            function bindRemoveButtons() {
                questionList.querySelectorAll('.remove-question-btn').forEach(function (button) {
                    button.onclick = function () {
                        const rows = questionList.querySelectorAll('.template-question-row');
                        if (rows.length <= 1) {
                            alert('At least one question block is required.');
                            return;
                        }
                        openRemoveConfirm(button.closest('.template-question-row'));
                    };
                });
            }

            addButton.addEventListener('click', function () {
                const key = makeKey();
                const html = template.innerHTML.replace(/__KEY__/g, key);
                questionList.insertAdjacentHTML('beforeend', html);
                bindRemoveButtons();
                showQuestionBlockNotice('Question block added.');
            });

            bindRemoveButtons();

            if (removeQuestionCancelBtn) {
                removeQuestionCancelBtn.addEventListener('click', closeRemoveConfirm);
            }

            if (removeQuestionConfirmBtn) {
                removeQuestionConfirmBtn.addEventListener('click', function () {
                    if (pendingRemoveRow) {
                        pendingRemoveRow.remove();
                    }

                    closeRemoveConfirm();
                });
            }

            if (removeQuestionConfirm) {
                removeQuestionConfirm.addEventListener('click', function (event) {
                    if (event.target === removeQuestionConfirm) {
                        closeRemoveConfirm();
                    }
                });
            }

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && removeQuestionConfirm && removeQuestionConfirm.classList.contains('open')) {
                    closeRemoveConfirm();
                }
            });
        })();
    