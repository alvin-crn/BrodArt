$(function () {
    $.ajax({
        url: "/ajax/cart/count",
        method: "GET",
        dataType: "json",
        success: function (response) {
            if (response.count > 0) {
                $(".cart-count").text(response.count).show();
            }
        },
        error: function () {
            console.error("Erreur chargement compteur panier");
        },
    });
});
