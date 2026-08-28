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

    if (menuToggle) {
        menuToggle.addEventListener('click', function () {
            const isMobile = window.innerWidth <= 900;
            if (isMobile) {
                if (sidebar) sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('collapsed');
                mainContent.classList.toggle('expanded');
                const isCollapsed = sidebar.classList.contains('collapsed');
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
        overlay.addEventListener('click', function () {
            if (sidebar) sidebar.classList.remove('mobile-open');
            overlay.classList.remove('active');
        });
    }

    // File upload zone label update
    document.getElementById('fileInput').addEventListener('change', function () {
        const label = document.getElementById('fileLabel');
        label.textContent = this.files.length > 0
            ? this.files[0].name
            : 'Click or drag a file here to upload';
    });

    const requirementPhaseSelect = document.getElementById('requirementPhaseSelect');
    const phaseDropdown = document.getElementById('phaseDropdown');
    const phaseDropdownTrigger = document.getElementById('phaseDropdownTrigger');
    const phaseDropdownTriggerText = document.getElementById('phaseDropdownTriggerText');
    const phaseDropdownOptions = phaseDropdown ? Array.from(phaseDropdown.querySelectorAll('.phase-dropdown-option')) : [];
    const requirementCategorySelect = document.getElementById('requirementCategorySelect');
    const categoryDropdown = document.getElementById('categoryDropdown');
    const categoryDropdownTrigger = document.getElementById('categoryDropdownTrigger');
    const categoryDropdownTriggerText = document.getElementById('categoryDropdownTriggerText');
    const categoryDropdownMenu = document.getElementById('categoryDropdownMenu');
    const phaseHelpText = document.getElementById('phaseHelpText');
    const cfg = window.studentFileReqConfig || {};
    const requirementCategoriesByPhase = {
        basic: cfg.basicCategories || [],
        other: cfg.otherCategories || [],
    };
    const otherRequirementsUnlocked = Boolean(cfg.otherRequirementsUnlocked);
    const missingBasicRequirementNames = cfg.missingBasicCategories || [];
    const hasSubmittedNotarizedMoa = Boolean(cfg.hasSubmittedNotarizedMoa);
    const submittedRequirementNames = new Set(
        (cfg.submittedRequirementNames || []).map(function (name) {
            return String(name || '').trim().toLowerCase();
        })
    );
    let categoryHoverBubble = null;

    function normalizeRequirementName(value) {
        return String(value || '').trim().toLowerCase();
    }

    function ensureCategoryHoverBubble() {
        if (categoryHoverBubble) {
            return categoryHoverBubble;
        }

        categoryHoverBubble = document.createElement('div');
        categoryHoverBubble.className = 'category-hover-bubble';
        document.body.appendChild(categoryHoverBubble);
        return categoryHoverBubble;
    }

    function hideCategoryHoverBubble() {
        if (!categoryHoverBubble) {
            return;
        }

        categoryHoverBubble.classList.remove('visible');
    }

    function showCategoryHoverBubble(target, message) {
        const bubble = ensureCategoryHoverBubble();
        bubble.textContent = message;
        bubble.classList.add('visible');

        const rect = target.getBoundingClientRect();
        const bubbleWidth = Math.min(320, window.innerWidth - 32);
        const bubbleHeight = bubble.offsetHeight || 56;

        let left = rect.left;
        if (left + bubbleWidth > window.innerWidth - 16) {
            left = window.innerWidth - bubbleWidth - 16;
        }
        left = Math.max(16, left);

        let top = rect.bottom + 10;
        if (top + bubbleHeight > window.innerHeight - 16) {
            top = rect.top - bubbleHeight - 10;
        }
        top = Math.max(16, top);

        bubble.style.left = left + 'px';
        bubble.style.top = top + 'px';
        bubble.style.width = bubbleWidth + 'px';
    }

    function setPhaseDropdownValue(value) {
        if (!requirementPhaseSelect || !phaseDropdownOptions.length) {
            return;
        }

        requirementPhaseSelect.value = value;

        phaseDropdownOptions.forEach(function (option) {
            const isActive = option.dataset.value === value;
            option.classList.toggle('active', isActive);
            if (isActive && phaseDropdownTriggerText) {
                const title = option.querySelector('.phase-dropdown-option-title');
                phaseDropdownTriggerText.textContent = title ? title.textContent.trim() : option.textContent.trim();
            }
        });
    }

    function closePhaseDropdown() {
        if (!phaseDropdown || !phaseDropdownTrigger) {
            return;
        }

        phaseDropdown.classList.remove('open');
        phaseDropdownTrigger.setAttribute('aria-expanded', 'false');
    }

    function openPhaseDropdown() {
        if (!phaseDropdown || !phaseDropdownTrigger) {
            return;
        }

        phaseDropdown.classList.add('open');
        phaseDropdownTrigger.setAttribute('aria-expanded', 'true');
    }

    function setCategoryDropdownValue(value, label) {
        if (!requirementCategorySelect || !categoryDropdownTriggerText) {
            return;
        }

        requirementCategorySelect.value = value || '';
        categoryDropdownTriggerText.textContent = label || 'Select a category';

        if (!categoryDropdownMenu) {
            return;
        }

        Array.from(categoryDropdownMenu.querySelectorAll('.phase-dropdown-option[data-value]')).forEach(function (option) {
            option.classList.toggle('active', option.dataset.value === (value || ''));
        });
    }

    function closeCategoryDropdown() {
        if (!categoryDropdown || !categoryDropdownTrigger) {
            return;
        }

        categoryDropdown.classList.remove('open');
        categoryDropdownTrigger.setAttribute('aria-expanded', 'false');
        hideCategoryHoverBubble();
    }

    function openCategoryDropdown() {
        if (!categoryDropdown || !categoryDropdownTrigger) {
            return;
        }

        categoryDropdown.classList.add('open');
        categoryDropdownTrigger.setAttribute('aria-expanded', 'true');
    }

    function updateRequirementCategoryOptions() {
        if (!requirementPhaseSelect || !requirementCategorySelect || !categoryDropdownMenu) {
            return;
        }

        const selectedPhase = requirementPhaseSelect.value || 'basic';
        const categories = requirementCategoriesByPhase[selectedPhase] || [];

        categoryDropdownMenu.innerHTML = '';

        if (!categories.length) {
            categoryDropdownMenu.innerHTML = ''
                + '<div class="phase-dropdown-option category-dropdown-option empty">'
                + '  <span class="phase-dropdown-option-label">'
                + '    <span class="phase-dropdown-option-title">No categories available</span>'
                + '    <span class="phase-dropdown-option-meta">Ask your professor to set up requirement categories for this phase.</span>'
                + '  </span>'
                + '</div>';
            setCategoryDropdownValue('', 'Select a category');
        } else {
            categories.forEach(function (category, index) {
                const option = document.createElement('button');
                option.type = 'button';
                option.className = 'phase-dropdown-option category-dropdown-option';
                option.dataset.value = category.fileName;
                const isAlreadySubmitted = submittedRequirementNames.has(normalizeRequirementName(category.fileName));

                option.innerHTML = ''
                    + '<span class="phase-dropdown-option-label">'
                    + '  <span class="phase-dropdown-option-title">' + category.fileName + '</span>'
                    + '</span>';

                if (isAlreadySubmitted) {
                    option.classList.add('locked');
                    option.setAttribute('aria-disabled', 'true');
                    option.addEventListener('mouseenter', function () {
                        showCategoryHoverBubble(
                            option,
                            'This requirement is already submitted. Remove the existing submission first before uploading another file for it.'
                        );
                    });
                    option.addEventListener('mouseleave', hideCategoryHoverBubble);
                    option.addEventListener('click', function (event) {
                        event.preventDefault();
                        event.stopPropagation();
                    });
                    option.innerHTML += ''
                        + '<span class="phase-dropdown-option-status">Submitted</span>';
                } else {
                    option.addEventListener('click', function () {
                        setCategoryDropdownValue(category.fileName, category.fileName);
                        closeCategoryDropdown();
                    });
                }

                categoryDropdownMenu.appendChild(option);

                if (index === 0) {
                    setCategoryDropdownValue('', 'Select a category');
                }
            });
        }

        if (!phaseHelpText) {
            return;
        }

        if (selectedPhase === 'other') {
            if (otherRequirementsUnlocked) {
                phaseHelpText.textContent = 'Other requirements are now unlocked. You can upload them here.';
            } else {
                const missingParts = [];
                if (missingBasicRequirementNames.length) {
                    missingParts.push('Basic requirements remaining: ' + missingBasicRequirementNames.join(', '));
                }
                if (!hasSubmittedNotarizedMoa) {
                    missingParts.push('Submit your Notarized MOA from the MOA page first.');
                }
                phaseHelpText.textContent = missingParts.join(' ');
            }
        } else {
            phaseHelpText.textContent = 'Upload your basic requirements first.';
        }
    }

    if (phaseDropdownTrigger && phaseDropdown) {
        phaseDropdownTrigger.addEventListener('click', function () {
            if (phaseDropdown.classList.contains('open')) {
                closePhaseDropdown();
            } else {
                openPhaseDropdown();
            }
        });

        phaseDropdownOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                const value = option.dataset.value;
                const isLocked = option.dataset.locked === 'true';

                if (isLocked) {
                    return;
                }

                setPhaseDropdownValue(value);
                closePhaseDropdown();
                updateRequirementCategoryOptions();
            });
        });

        document.addEventListener('click', function (event) {
            if (!phaseDropdown.contains(event.target)) {
                closePhaseDropdown();
            }
        });
    }

    if (categoryDropdownTrigger && categoryDropdown) {
        categoryDropdownTrigger.addEventListener('click', function () {
            if (categoryDropdown.classList.contains('open')) {
                closeCategoryDropdown();
            } else {
                openCategoryDropdown();
            }
        });

        document.addEventListener('click', function (event) {
            if (!categoryDropdown.contains(event.target)) {
                closeCategoryDropdown();
                hideCategoryHoverBubble();
            }
        });
    }

    if (requirementPhaseSelect) {
        setPhaseDropdownValue(requirementPhaseSelect.value || 'basic');
        updateRequirementCategoryOptions();
    }

    // SweetAlert remove confirmation
    function showDenialReason(fileName, denialReason) {
        Swal.fire({
            title: 'Denial Reason',
            html: '<div style="text-align:left; font-size:13px; line-height:1.6;">'
                + '<div style="font-weight:700; margin-bottom:8px; color:#1f2937;">' + fileName + '</div>'
                + '<div style="color:#4b5563;">' + denialReason + '</div>'
                + '</div>',
            icon: 'info',
            confirmButtonColor: '#dc2626',
            confirmButtonText: 'Close'
        });
    }

    function showRemoveConfirmation(fileId) {
        Swal.fire({
            title: 'Remove this requirement?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: '<i class="fa fa-trash"></i> Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                removeFileCategory(fileId);
            }
        });
    }

    function removeFileCategory(fileId) {
        $.ajax({
            type: 'POST',
            url: '/remove/filesReq/' + fileId,
            headers: {
                'X-CSRF-TOKEN': (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
            },
            data: {
                _token: (document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '')
            },
            success: function (res) {
                Swal.fire({
                    toast: true,
                    icon: 'success',
                    title: res.message || 'Requirement removed successfully.',
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 1800
                });
                setTimeout(() => location.reload(), 1500);
            },
            error: function (xhr) {
                var errMsg = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : 'Something went wrong while removing the requirement.';
                Swal.fire('Oops!', errMsg, 'error');
            }
        });
    }


