$(function () {
    $(".btn-burger").click(function () {
        $(this).find(".icon-bar").toggleClass("cross");
        $(".nav-content").toggleClass("isOpen");
        $(".btn-burger").toggleClass("menu-open");
        $(".bg-opacity").toggleClass("active");
    });

    $(".btn-burger-filter").click(function () {
        $(this).find(".icon-bar-filter").toggleClass("cross-filter");
        $(".collumn-filter").toggleClass("show-filter");
    });

    $(".login_button").click(function () {
        $(".loginchoise").toggleClass("isOpen");
    });

    $(".photo-slider").click(function () {
        $(".photo-container-zoom").toggleClass("active");
    });

    $(".btn-exit").click(function () {
        $(".photo-container-zoom").toggleClass("active");
    });

    $(".btn-add").click(function () {
        $(".form-add-pic").toggleClass("active");
    });

    $(".btn-close").click(function () {
        $(".form-add-pic").toggleClass("active");
    });

    // Nombre d'article dans le panier
    $.ajax({
        url: "/ajax/categories",
        method: "GET",
        dataType: "json",
        success: function (categories) {
            let html = "";

            categories.forEach(function (category) {
                html += `
                    <li>
                        <a href="/categorie/${category.slug}">
                            ${category.name}
                        </a>
                    </li>
                `;
            });

            $("#all-products").after(html);
        },
        error: function () {
            console.error("Erreur lors du chargement des catégories");
        },
    });
});
