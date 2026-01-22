$(function () {
    $.ajax({
        url: "/ajax/categories",
        method: "GET",
        dataType: "json",
        success: function (categories) {
            let html = '';

            categories.forEach(function (category) {
                html += `
                    <li>
                        <a href="/categorie/${category.slug}">
                            ${category.name}
                        </a>
                    </li>
                `;
            });

            $('#all-products').after(html);
        },
        error: function () {
            console.error("Erreur lors du chargement des catégories");
        },
    });
});
