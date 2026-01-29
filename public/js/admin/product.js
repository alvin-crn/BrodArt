$(function () {
  $('#product-search').on('keyup', function () {
    const value = $(this).val().toLowerCase();

    $('#products-table tbody tr').each(function () {
      const name = $(this).data('name');
      const category = $(this).data('category');

      if (
        name.includes(value) ||
        category.includes(value)
      ) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  });
});