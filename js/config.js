/**
 * Created by Xavier on 4/26/2017.
 */
$(document).ready(function () {
    $('.add_param').click(function () {
        var parent = $(this).siblings('.list');
        var copia = parent.find('li').last().clone();
        var input = document.createElement('input');
        copia.find("input[name='oldKey']").remove();
        copia.find("input[name='key']").attr("value", "");
        copia.find("input[name='value']").attr("value", "");
        copia.find(".modify").text("Add param.");
        parent.append(copia);
    });
});

function updateParams(form) {
    var jsonForm = toJson(form);
    $.ajax({
        type: "POST",
        url: "index.php?controller=manager&action=updateParams",
        datatype: "json",
        data: {
            paramKey: jsonForm.paramKey,
            key: jsonForm.key,
            value: jsonForm.value,
            oldKey: jsonForm.oldKey,
            oldValue: jsonForm.oldValue
        },
        success: function (data) {
            console.log(data);
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
            data ?
                toastr.success('Changes to '+jsonForm.paramKey+' parameters have been saved.') :
                toastr.error('Something went wrong while updating '+jsonForm.paramKey+' parameters.');
        }
    })
}

function toJson(formArray) {

    var returnArray = {};
    for (var i = 0; i < formArray.length; i++) {
        returnArray[formArray[i]['name']] = formArray[i]['value'];
    }
    return returnArray;
}