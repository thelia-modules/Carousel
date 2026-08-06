$(function () {
    // Adapted for the Bootstrap 5 / Twig back-office screen: the delete button opens the
    // confirmation modal via data-bs-toggle/data-bs-target (Bootstrap 5 attributes), we only
    // need to copy the slide id into the modal's hidden field before it submits.
    $('.carousel-image-delete').on('click', function () {
        $('#carousel-image-delete-id').val($(this).data('id'));
    });
});
