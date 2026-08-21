/* Student Scripts */

    function openUnlockRequestModal(type, isOwner) {
        const select = document.getElementById('modalRequestType');
        if (select) {
            const editOpt = select.querySelector('option[value="edit"]');
            const unlinkOpt = select.querySelector('option[value="unlink"]');
            const switchOpt = select.querySelector('option[value="switch_external"]');

            if (type === 'switch_external') {
                if (editOpt) editOpt.style.display = 'none';
                if (unlinkOpt) unlinkOpt.style.display = 'none';
                if (switchOpt) switchOpt.style.display = 'block';
                select.value = 'switch_external';
            } else if (isOwner === false) {
                if (editOpt) editOpt.style.display = 'none';
                if (unlinkOpt) unlinkOpt.style.display = 'block';
                if (switchOpt) switchOpt.style.display = 'none';
                select.value = 'unlink';
            } else {
                if (editOpt) editOpt.style.display = 'block';
                if (unlinkOpt) unlinkOpt.style.display = 'block';
                if (switchOpt) switchOpt.style.display = 'none';
                select.value = type || 'edit';
            }
        }
        const modalEl = document.getElementById('requestUnlockModal');
        if (modalEl) {
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    }