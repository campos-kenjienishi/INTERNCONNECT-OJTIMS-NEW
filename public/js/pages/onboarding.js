/* Onboarding Scripts */

    function goToStep2() {
        // No required fields in step 1 for onboarding (all disabled)
        // Switch steps
        const step2 = document.getElementById('step2');
        step2.classList.remove('going-back');
        document.getElementById('step1').classList.remove('active');
        step2.classList.add('active');
        // Update indicators
        const dot1 = document.getElementById('dot1');
        dot1.classList.remove('active');
        dot1.classList.add('done');
        dot1.innerHTML = '<i class="fa fa-check" style="font-size:12px;"></i>';
        document.getElementById('dot2').classList.add('active');
        document.getElementById('line1').classList.add('done');
        document.getElementById('label1').classList.remove('active');
        document.body.scrollLeft = 0;
        document.documentElement.scrollLeft = 0;
        window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
        if ($.fn.select2) {
            $('select').trigger('change.select2');
        }
    }
    function goToStep1() {
        const step2 = document.getElementById('step2');
        step2.classList.remove('active');
        document.getElementById('step1').classList.add('active');
        const dot1 = document.getElementById('dot1');
        dot1.classList.add('active');
        dot1.classList.remove('done');
        dot1.innerHTML = '1';
        document.getElementById('dot2').classList.remove('active');
        document.getElementById('line1').classList.remove('done');
        document.getElementById('label1').classList.add('active');
        document.getElementById('label1').classList.remove('done');
        document.getElementById('label2').classList.remove('active');
        document.body.scrollLeft = 0;
        document.documentElement.scrollLeft = 0;
        window.scrollTo({ top: 0, left: 0, behavior: 'smooth' });
    }

    $(document).ready(function() {
        if ($.fn.select2) {
            $('select[name="adviser_name"]').select2({
                placeholder: 'Search or Select Professor...',
                allowClear: true,
                width: '100%'
            });
            $('select[name="course"]').select2({
                placeholder: 'Select Course...',
                width: '100%'
            });
            $('select[name="semester"]').select2({
                placeholder: 'Select Semester...',
                allowClear: true,
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
            $('select[name="academic_year_start"]').select2({
                placeholder: 'Start Year',
                allowClear: true,
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
            $('#academic_year_end_display').select2({
                placeholder: 'End Year',
                minimumResultsForSearch: Infinity,
                width: '100%',
                disabled: true
            });

            $('#academic_year_start').on('change', function() {
                var startVal = parseInt($(this).val());
                if (!isNaN(startVal)) {
                    var nextYear = startVal + 1;
                    $('#academic_year_end').val(nextYear);
                    $('#academic_year_end_display').val(nextYear).trigger('change.select2');
                }
            });

            // Set search input placeholder when dropdown opens
            $(document).on('select2:open', function(e) {
                var searchField = document.querySelector('.select2-container--open .select2-search__field');
                if (searchField) {
                    var selectName = $(e.target).attr('name') || '';
                    if (selectName.indexOf('course') !== -1) {
                        searchField.setAttribute('placeholder', 'Search course name...');
                    } else if (selectName.indexOf('adviser') !== -1) {
                        searchField.setAttribute('placeholder', 'Search professor name...');
                    } else {
                        searchField.setAttribute('placeholder', 'Type to search...');
                    }
                    searchField.focus();
                }
            });
        }
    });