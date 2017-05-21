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
                    var form = document.createElement('form');
                    form.method = 'POST';
                    form.action = 'index.php?controller=catalogue&action=details';
                    $.each(data[0], function (key, value) {
                        var input = document.createElement('input');
                        input.type = "text";
                        input.name = key;
                        input.setAttribute("value", value);
                        form.appendChild(input);
                        console.log(input);
                    });
                    setTimeout(function () {
                        document.body.appendChild(form);
                        form.submit();
                    }, 500);
                }
                else {
                    gbooks.css("border", "2px solid red");
                }
            }
        });
    });
});