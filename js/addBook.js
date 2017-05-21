/**
 * Created by Xavier on 5/21/2017.
 */
$(document).ready(function($) {
    $("#addBookForm").submit(function(e) {
        $.ajax({
            type: "POST",
            url: $("#addBookForm").attr('action'),
            data: $("#addBookForm").serialize(), // serializes the form's elements.
            success: function(data) {
                console.log(data);
                $('#addBook').modal('toggle');
                if (data) {
                    swal({
                        title: 'Success',
                        text: 'The book was added to the database.',
                        type: 'success',
                        confirmButtonText: 'Ok'
                    })
                } else {
                    swal({
                        title: 'Error',
                        text: 'Something went wrong!',
                        type: 'error',
                        confirmButtonText: 'Ok'
                    })
                }
            }
        });
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });
});