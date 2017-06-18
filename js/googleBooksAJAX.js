/**
 * Created by Xavier on 5/3/2017.
 */
$(document).ready(function () {
    $('#submit').click(function () {
        var gbooks = $('#googleBooks');
        $.ajax({
            type: "GET",
            url: "ajax/checkISBN.php",
            dataType: "json",
            data: {
                isbn: gbooks.val()
            },
            success: function (data) {
                if (data) {
                    console.log(data[0].id);
                    window.location.replace("index.php?controller=catalogue&action=details&id="+data[0].id);
                }
                else {
                    gbooks.css("border", "2px solid red");
                    toastr.options = {
                        "closeButton": false,
                        "debug": false,
                        "newestOnTop": true,
                        "progressBar": false,
                        "positionClass": "toast-top-right",
                        "preventDuplicates": false,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "3000",
                        "timeOut": "5000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };
                    toastr.error('Couldn\'t find the specified ISBN.');
                }
            }
        });
    });
});