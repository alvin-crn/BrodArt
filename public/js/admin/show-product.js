$(function () {
  // ============ Gestion preview photos ============
  const files = Array(10).fill(null); // Tableau pour stocker les nouvelles images (uploadées)
  const deletedContainer = $('#deleted-images-container'); // Container où on ajoute les hidden inputs pour les images supprimées

  // === Gestion du preview pour les nouvelles images ===
  $('.img-input').on('change', function (e) {
    const index = $(this).data('index');
    const file = e.target.files[0] || null;
    files[index] = file;
    renderPreviews();
  });

  function renderPreviews() {
    $('.img-preview[data-existing="0"]').remove(); // Supprime uniquement les previews des nouvelles images (data-existing="0")

    files.forEach((file, i) => {
      if (!file) return;

      const reader = new FileReader();
      reader.onload = function (e) {
        $('#images-preview-container').append(`
                    <div class="img-preview" data-existing="0" data-index="${i}">
                        <img src="${e.target.result}" alt="Image ${i + 1}">
                        <button type="button" class="remove-image" data-index="${i}">✕</button>
                    </div>
                `);
      };
      reader.readAsDataURL(file);
    });
  }

  // === Supprimer une image existante ou une nouvelle image ===
  $(document).on('click', '.img-preview .remove-image', function () {
    const $previewImg = $(this).closest('.img-preview');
    console.log($previewImg);
    if ($previewImg.data('existing') == 1) {
      // Image existante => ajouter hidden input pour suppression
      const prop = $(this).data('prop') || $previewImg.data('prop');
      deletedContainer.append(`<input type="hidden" name="deleted_images[]" value="${prop}">`);
    } else {
      // Nouvelle image => enlever du tableau files et réinitialiser l'input
      const index = $(this).data('index');
      files[index] = null;
      $(`.img-input[data-index="${index}"]`).val('');
    }

    // Supprime visuellement la preview
    $previewImg.remove();
  });

  // ============ Gestion taille et stock ============
  // === Ajouter une nouvelle taille ===
  $('#add-size').on('click', function () {
    const $options = $('#sizes-options').html();

    const $row = $(`
            <div class="size-row" data-existing="0">
                <select name="new-sizes[]">
                    ${$options}
                </select>
                <input type="number" name="new-stocks[]" min="0" placeholder="Stock">
                <button type="button" class="remove-size">✕</button>
            </div>
        `);
    $('#sizes-container').append($row);
  });

  // === Supprimer une taille ===
  $(document).on('click', '.remove-size', function () {
    const $row = $(this).closest('.size-row');

    if ($row.data('existing') == 1) {
      // Taille existante => créer un input hidden pour indiquer la suppression
      const id = $row.data('id');
      $('#deleted-sizes-container').append(
        `<input type="hidden" name="deleted_sizes[]" value="${id}">`
      );
    }

    $row.remove();
  });
});