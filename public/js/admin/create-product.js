$(function () {
  // --- Gestion des previews d'images --- 
  // Quand un input file change
  $('.img-input').on('change', function () {
    const input = this;
    const index = $(this).data('index');
    const file = input.files[0];

    if (!file) {
      removePreview(index);
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      removePreview(index); // Supprimer l'ancienne preview si elle existe

      $('#images-preview-container').append(`
        <div class="img-preview" data-index="${index}">
          <img src="${e.target.result}" alt="Image ${index + 1}">
          <button type="button" class="remove-image" data-index="${index}">✕</button>
        </div>
      `);
    };

    reader.readAsDataURL(file);
  });

  // Click sur le bouton supprimer
  $(document).on('click', '.remove-image', function () {
    const index = $(this).data('index');
    removePreview(index); // Supprimer preview

    // Vider l'input correspondant
    $('.img-input').filter(function () {
      return $(this).data('index') === index;
    }).val('');
  });

  // Fonction pour supprimer une preview par index
  function removePreview(index) {
    $('#images-preview-container .img-preview').filter(function () {
      return $(this).data('index') === index;
    }).remove();
  }

  // Ajouter une nouvelle taille
  $('#add-size').on('click', function () {
    // clone les options du select caché
    const $options = $('#sizes-options').html();

    const $row = $(`
        <div class="size-row">
            <select name="sizes[]">
                ${$options}
            </select>
            <input type="number" name="stocks[]" min="0" placeholder="Stock">
            <button type="button" class="remove-size">✕</button>
        </div>
    `);
    $('#sizes-container').append($row);
  });

  // Supprimer une taille
  $(document).on('click', '.remove-size', function () {
    $(this).closest('.size-row').remove();
  });
});