/**
 * Created by Xavier on 3/17/2017.
 */
$(document).ready(function () {

    var startDate = $('#bFrom').val(), // Start date
        endDate  = $('#bTo').val(),  // End date
        dateRange = [];   // array to hold the range

    var dateformat = 'yy-mm-dd';

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

    $("#from").datepicker({
        dateFormat: dateformat,
        minDate: 0,
        onSelect: function (date) {
            var to = $('#to');
            var startDate = $(this).datepicker('getDate');
            var minDate = $(this).datepicker('getDate');
            minDate.setDate(minDate.getDate() + 1);
            startDate.setDate(startDate.getDate() + +$('#availability').val());

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
                $('#booking').modal('hide');
                console.log(data);
                if (data == '1') {
                    swal({
                        title: 'Success',
                        text: 'Your booking was successful!.',
                        type: 'success',
                        confirmButtonText: 'Ok'
                    })}
                    else if (data == "user") {
                    swal({
                        title: 'Error',
                        text: 'You have already reserved this book!',
                        type: 'warning',
                        confirmButtonText: 'Ok'
                    })
                } else {
                    swal({
                        title: 'Error',
                        text: 'That date is already picked! Try a different one.',
                        type: 'error',
                        confirmButtonText: 'Ok'
                    })
                }
            }
        });
    });

    $('#submit_manager').click(function () {
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
                $('#addBook').modal('hide');
                console.log(data);
                data ?
                    swal({
                        title: 'Success',
                        text: 'Your booking was successful!.',
                        type: 'success',
                        confirmButtonText: 'Ok'
                    }) :
                    swal({
                        title: 'Error',
                        text: 'Something went wrong!',
                        type: 'error',
                        confirmButtonText: 'Ok'
                    })
            }
        });
    })
});