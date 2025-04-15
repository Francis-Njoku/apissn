/**
 * Created by Toshiba on 10/27/2018.
 */

/**$(document).ready(function(){
    $("#myModal").on("show.bs.modal", function(){
        // Place the returned HTML into the selected element
        // $(this).find(".modal-body").load("remote.php");
        $('#id').val($(this).data('id'));
        $('#category').val($(this).data('category'));
        $('#dscrption').val($(this).data('description'));
        $('#status').val($(this).data('status'));
    });
}); **/

$(document).ready(function(){
    $("#myModal").on("show.bs.modal", function(event){
        // Get the button that triggered the modal
        var button = $(event.relatedTarget);

        // Extract value from the custom data-* attribute
        var titleData = button.data("title");
        var categoryData = button.data("category");
        var statusData = button.data("status");
        var idData = button.data("id");        
        var descriptionData = button.data("description");
        $(this).find(".modal-title").text(titleData);
        $(this).find(".category").val(categoryData); 
        $(this).find(".idi").val(idData);
        $(this).find(".status").val(statusData);
        $(this).find(".descriptionid").attr(descriptionData);
        $('#status').val($(this).data('status'));
        document.getElementById("description").value = descriptionData;
    });
});



// AJAX CRUD operations -->

// add a new post
$(document).on('click', '.add-modal', function() {
    $('.modal-title').text('Add');
    $('#addModal').modal('show');
    });
$('.modal-footer').on('click', '.add', function() {
    $.ajax({
        type: 'POST',
        url: 'posts',
        data: {
            '_token': $('input[name=_token]').val(),
            'title': $('#title_add').val(),
            'content': $('#content_add').val()
        },
        success: function(data) {
            $('.errorTitle').addClass('hidden');
            $('.errorContent').addClass('hidden');

            if ((data.errors)) {
                setTimeout(function () {
                    $('#addModal').modal('show');
                    toastr.error('Validation error!', 'Error Alert', {timeOut: 5000});
                }, 500);

                if (data.errors.title) {
                    $('.errorTitle').removeClass('hidden');
                    $('.errorTitle').text(data.errors.title);
                }
                if (data.errors.content) {
                    $('.errorContent').removeClass('hidden');
                    $('.errorContent').text(data.errors.content);
                }
            } else {
                toastr.success('Successfully added Post!', 'Success Alert', {timeOut: 5000});
                $('#postTable').prepend("<tr class='item" + data.id + "'><td class='col1'>" + data.id + "</td><td>" + data.title + "</td><td>" + data.content + "</td><td class='text-center'><input type='checkbox' class='new_published' data-id='" + data.id + " '></td><td>Just now!</td><td><button class='show-modal btn btn-success' data-id='" + data.id + "' data-title='" + data.title + "' data-content='" + data.content + "'><span class='glyphicon glyphicon-eye-open'></span> Show</button> <button class='edit-modal btn btn-info' data-id='" + data.id + "' data-title='" + data.title + "' data-content='" + data.content + "'><span class='glyphicon glyphicon-edit'></span> Edit</button> <button class='delete-modal btn btn-danger' data-id='" + data.id + "' data-title='" + data.title + "' data-content='" + data.content + "'><span class='glyphicon glyphicon-trash'></span> Delete</button></td></tr>");

            }
        },
    });
    });







// Forum
$(document).on('click', '.update-forum-modal', function() {
    $('#id').val($(this).data('id'));
    $('#title').val($(this).data('title'));
    $('#category').val($(this).data('category'));
    $('#dscrption').val($(this).data('description'));
    $('#status').val($(this).data('status'));
    id = $('#id').val();
    $('#UpdateForumModal').modal('show');
});


// delete a post
$(document).on('click', '.delete-modal', function() {
    $('.modal-title').text('Delete');
    $('#id_delete').val($(this).data('id'));
    $('#title_delete').val($(this).data('title'));
    $('#deleteModal').modal('show');
    id = $('#id_delete').val();
    });
$('.modal-footer').on('click', '.delete', function() {
    $.ajax({
        type: 'DELETE',
        url: 'posts/' + id,
        data: {
            '_token': $('input[name=_token]').val(),
        },
        success: function(data) {
            toastr.success('Successfully deleted Post!', 'Success Alert', {timeOut: 5000});
            $('.item' + data['id']).remove();
            $('.col1').each(function (index) {
                $(this).html(index+1);
            });
        }
    });
    });


