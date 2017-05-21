/**
 * Created by Xavier on 3/17/2017.
 */
$(document).ready(function () {

    var startDate = $('#bFrom').val(), // Start date
        endDate  = $('#bTo').val(),  // End date
        dateRange = [];   // array to hold the range

    var dateformat = 'yy-mm-dd';

    // if (startDate != $('#bFrom').val() && endDate != $('#bTo').val()) {
        for (var d = new Date(startDate); d <= new Date(endDate); d.setDate(d.getDate() + 1)) {
            dateRange.push($.datepicker.formatDate(dateformat, d));
        }
        $('#from, #to').datepicker("option", "beforeShowDay", function (date) {
            if($.inArray($.datepicker.formatDate(dateformat, date ), dateRange) > -1)
            {
                return [false,"","Booked out"];
            }
            else
            {
                return [true,'',"available"];
            }
        });
    // }

    $("#from").datepicker({
        dateFormat: dateformat,
        minDate: 0,
        onSelect: function (date) {
            var to = $('#to');
            var startDate = $(this).datepicker('getDate');
            var minDate = $(this).datepicker('getDate');
            minDate.setDate(minDate.getDate() + 1);
            startDate.setDate(startDate.getDate() + +$('#protection').val());

            to.datepicker('option', 'maxDate', startDate);
            to.datepicker('option', 'minDate', minDate);
            to.datepicker('setDate', minDate);
        }
    });
    $('#to').datepicker({
        dateFormat: dateformat,
        minDate: 0
    });

    $('#submit').click(function () {
        $('#modal-success').hide();
        $('#modal-error').hide();
        $.ajax({
            type: "POST",
            url: "index.php?controller=catalogue&action=booking",
            datatype: "json",
            data: {
                from: $('#from').val(),
                to: $('#to').val(),
                id: $('#id').val()
            },
            success: function (data) {
                console.log(data);
                $('#booking').modal('hide');
                // $('#dismiss').trigger();
                if (data == 'OK') {
                    var modal = $('#modal-success');
                    modal.append("You must return before or at <strong>"+$('#to').val()+"</strong>.");
                    modal.toggle("Seems that date is already picked, try another one.");
                }
                else $('#modal-error').toggle();
            }
        });
    });

    $('#submit_manager').click(function () {
        $('#modal-success').hide();
        $('#modal-error').hide();
        $.ajax({
            type: "POST",
            url: "index.php?controller=manager&action=newBooking",
            datatype: "json",
            data: {
                from: $('#from').val(),
                to: $('#to').val(),
                book: $('#book').val(),
                user: $('#user').val()
            },
            success: function (data) {
                console.log(data);
                $('#booking').modal('hide');
                // $('#dismiss').trigger();
                if (data == 'OK') {
                    var modal = $('#modal-success');
                    modal.append("Return date is due before or at <strong>"+$('#to').val()+"</strong>.");
                    modal.toggle("Seems that date is already picked, try another one.");
                }
                else $('#modal-error').toggle();
            }
        });
    })
});