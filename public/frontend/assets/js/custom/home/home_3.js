/* global $, loadTranslationFile, document, showToast*/

(async () => {
    "use strict";
    await loadTranslationFile("web", "home");

    $(document).on("click", ".wishlist-icon", function () {
        const id = $(this).data("id");
        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr("content"),
            },
            dataType: "json",
            success: function (response) {
                showToast(response.status, response.message);
            },
            error: function () {
                showToast("error", "Something went wrong. Please try again.");
            },
        });
    });
})();
