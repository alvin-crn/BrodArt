$(function() {
    $('#entity-search').on('keyup', function() {
        let search = $(this).val().toLowerCase();

        $('#entity-table tbody tr').each(function() {
            let name = $(this).data('name');
            if (name.includes(search)) {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
});