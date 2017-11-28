
(function (window, $, App) {

    function MediaidsIndexController() {
        var startMonthPicker = $('input.monthpicker-companies'),
            endMonthPicker = $('input.monthpicker-companies-end'),
            startDatePicker = $('input.datepicker-companies'),
            endDatePicker = $('input.datepicker-companies-end');

        startDatePicker.attr("placeholder", getDefaultDate(1));
        endDatePicker.attr("placeholder", getDefaultDate(0));
        startMonthPicker.prop('disabled', true);
        endMonthPicker.prop('disabled', true);

        $(".optionsDateMonth").change(function(){
            if ($(this).val() == 1) {
                endMonthPicker.attr("placeholder", getDefaultDate(3));
                startMonthPicker.attr("placeholder", getDefaultDate(2));
                startDatePicker.attr("placeholder", "----年--月--日"); startDatePicker.val('');
                endDatePicker.attr("placeholder", "----年--月--日");  endDatePicker.val('');
                startDatePicker.prop('disabled', true);
                endDatePicker.prop('disabled', true);
                startMonthPicker.prop('disabled', false);
                endMonthPicker.prop('disabled', false);
            }
            else {
                endMonthPicker.attr("placeholder", "----年--月"); endMonthPicker.val('');
                startMonthPicker.attr("placeholder", "----年--月"); startMonthPicker.val('');
                endDatePicker.attr("placeholder", getDefaultDate(0));
                startDatePicker.attr("placeholder", getDefaultDate(1));
                startDatePicker.prop('disabled', false);
                endDatePicker.prop('disabled', false);
                startMonthPicker.prop('disabled', true);
                endMonthPicker.prop('disabled', true);
            }
        });

        $("#submit-csv").click(function (e) {
            var radio = $('input:radio[name=optionsRadios]:checked').val();
            if (radio == 0) {
                var startD = startDatePicker.val() ? convertDate(startDatePicker.val()) : convertDate(startDatePicker.attr("placeholder")),
                    endD = endDatePicker.val() ? convertDate(endDatePicker.val()) : convertDate(endDatePicker.attr("placeholder"));
                if (dayDiff(startD, endD) > 31 ) {
                    $(".day-condition-csv").addClass('validate-csv');
                    $(".month-condition-csv").removeClass('validate-csv');
                    e.preventDefault();
                }
                else {
                    $(".day-condition-csv").removeClass('validate-csv');
                }
            }
            else {
                var startM = startMonthPicker.val() ? convertDate(startMonthPicker.val() + '01日' ) : convertDate(startMonthPicker.attr("placeholder") + '01日'),
                    endM = endMonthPicker.val() ? convertDate(endMonthPicker.val() + '01日' ) : convertDate(endMonthPicker.attr("placeholder") + '01日' );
                if (monthDiff(startM, endM) > 2 ) {
                    $(".month-condition-csv").addClass('validate-csv');
                    $(".day-condition-csv").removeClass('validate-csv');
                    e.preventDefault();
                }
                else {
                    $(".month-condition-csv").removeClass('validate-csv');
                }
            }

        });

        function dayDiff(first, second) {
            first = new Date(first);
            second = new Date(second);
            return Math.round((second-first)/(1000*60*60*24));
        }

        function monthDiff(first, second) {
            first = new Date(first);
            second = new Date(second);
            return Math.round(second.getMonth() - first.getMonth()
                + (12 * (second.getFullYear() - first.getFullYear())));
        }

        $('.disabled-input').on('keydown',function(e)
        {
            var key = e.charCode || e.keyCode;
            if (key == 122 || key == 27 )
            {}
            else
                e.preventDefault();
        });

        function getDefaultDate(type) {
            var d = new Date();
            var month = d.getMonth()+1;
            var day = d.getDate();
            var year = d.getFullYear();

            d.setMonth( d.getMonth() - 2 );
            var monthStartMonth = d.getMonth()+1;
            var yearStartMonth = d.getFullYear();

            switch (type) {
                case 1: //startDate
                    return year + '年' + (month < 10 ? '0' : '') + month + '月' + '01日';
                    break;
                case 2: //startMonth
                    return yearStartMonth + '年' + (monthStartMonth < 10 ? '0' : '') + monthStartMonth + '月';
                    break;
                case 3: //endMonth
                    return year + '年' + (month < 10 ? '0' : '') + month + '月';
                    break;
                default: //endDate
                    return year + '年' + (month < 10 ? '0' : '') + month + '月' + (day < 10 ? '0' : '') + day + '日';
                    break;
            }
        }

        //check-all
        $("#select-all").change(function(){
            $(".select-company").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-users-type").change(function(){
            $(".select-users").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-orders-type").change(function(){
            $(".select-orders").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-stores-type").change(function(){
            $(".select-stores").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-settings-type").change(function(){
            $(".select-settings").prop('checked', $(this).prop("checked"));
        });

        $("#select-all-checkbox-csv").change(function(){
            $(".select-all-checkbox").prop('checked', $(this).prop("checked"));
        });

        $('#menu-current-view').click(function(){
            $('#tab-current-view a').trigger('click');
        });


        $('.statistic-view .year-month').each(function(){
            var colSpan=1;
            while( $(this).text() == $(this).next().text() ){
                $(this).next().remove();
                colSpan++;
            }
            $(this).attr('colSpan',colSpan);
        });

        //Companies Datepicker

        function convertDate(date) {
            if (!date) {
                return false;
            }
            var dateConvert = date.replace(/年|月/g, "-").replace(/日/g, ""),
                comp = dateConvert.split('-'),
                m = parseInt(comp[1], 10),
                d = parseInt(comp[2], 10),
                y = parseInt(comp[0], 10),
                date = new Date(y,m-1,d);

            if (date.getFullYear() == y && date.getMonth() + 1 == m && date.getDate() == d) {
                return dateConvert;
            }
        }
    }

    App.registerController('MediaidsIndexController', MediaidsIndexController);

})(window, window.jQuery, window.App);