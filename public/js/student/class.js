/* Student Scripts */

    $(document).ready(function() {
        if ($.fn.select2) {
            $('select[name="adviser_name"]').select2({
                placeholder: 'Select your Professor',
                allowClear: true,
                width: '100%'
            });
        }
    });

