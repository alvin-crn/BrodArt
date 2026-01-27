$(function () {
  function filterUsers() {
    const search = $('#user-search').val().toLowerCase();
    const filterAdmin = $('#filter-admin').is(':checked');
    const filterBlocked = $('#filter-blocked').is(':checked');
    const filterBlacklist = $('#filter-blacklist').is(':checked');

    $('#users-table tbody tr').each(function () {
      const $row = $(this);

      const email = $row.data('email');
      const firstname = $row.data('firstname');
      const lastname = $row.data('lastname');

      const isAdmin = $row.data('admin') === 1;
      const isBlocked = $row.data('desactived') === 1;
      const isBlacklisted = $row.data('blacklist') === 1;

      let visible = true;

      /* Recherche */
      if (
        search &&
        !email.includes(search) &&
        !firstname.includes(search) &&
        !lastname.includes(search)
      ) {
        visible = false;
      }

      /* Filtres */
      if (filterAdmin && !isAdmin) visible = false;
      if (filterBlocked && !isBlocked) visible = false;
      if (filterBlacklist && !isBlacklisted) visible = false;

      $row.toggle(visible);
    });
  }

  $('#user-search').on('keyup', filterUsers);
  $('#filter-admin, #filter-blocked, #filter-blacklist').on('change', filterUsers);
});