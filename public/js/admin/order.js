$(function () {
  // Fonction de filtrage des commandes
  function filterOrders() {
    let search = $('#order-search').val().toLowerCase();
    let stateFilter = $('#order-state-filter').val();

    $('#entity-table tbody tr').each(function () {
      let reference = $(this).data('reference');
      let email = $(this).data('email');
      let state = $(this).data('state').toString();

      let matchSearch =
        reference.includes(search) ||
        email.includes(search);

      let matchState =
        stateFilter === '' || state === stateFilter;

      if (matchSearch && matchState) {
        $(this).show();
      } else {
        $(this).hide();
      }
    });
  }

  $('#order-search').on('keyup', filterOrders);
  $('#order-state-filter').on('change', filterOrders);

});