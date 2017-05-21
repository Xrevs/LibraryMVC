/**
 * Created by Xavier on 4/26/2017.
 */
$(document).ready(function () {
    $('.add_param').click(function () {
        var parent = $(this).siblings('.list');
        parent.find('li').last().clone().appendTo(parent);
    });
    $('.modify').click(function () {
        var input = $(this);
        $.ajax({
            type: "POST",
            url: "index.php?controller=manager&action=modConf",
            datatype: "json",
            data: {
                key: $('input[name=key]').val(),
                value: $('input[name=value]').val(),
                oldKey: $('input[name=oldKey]').val(),
                oldValue: $('input[name=oldValue]').val()
            },
            success: function (data) {
                console.log(data);
                var success = $('#modal-success');
                var error = $('#modal-error');
                success.hide();
                error.hide();

                if (data == 'OK') {
                    $('input[name=oldKey]').val($('input[name=key]').val());
                    $('input[name=oldValue]').val($('input[name=value]').val());
                    success.append("Parameters have changed.");
                    success.toggle();
                }
                else {
                    error.append("Something went wrong.");
                    error.toggle();
                }
            }
        })
    });
});