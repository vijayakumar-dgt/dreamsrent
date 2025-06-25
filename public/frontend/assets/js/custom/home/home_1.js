/* global $, loadTranslationFile, clearTimeout, setTimeout, document, showToast, _l */

(function () {
    "use strict";

    (async () => {
        await loadTranslationFile("web", "home");
        initLocationSearch();
    })();

    function initLocationSearch() {
        const $input = $("#pickup-location-input");
        const $suggestions = $("#pickup-suggestions");
        const $searchBtn = $(".searchbtn");
        let searchTimeout;
        const cache = {};

        $input.on("keyup", function () {
            const query = $(this).val().trim().toLowerCase();

            clearTimeout(searchTimeout);

            // Enable button if user clears the input (means no filter)
            if (query.length === 0) {
                $suggestions.hide();
                $searchBtn.prop("disabled", false);
                return;
            }

            // Disable button and hide suggestions if not enough characters
            if (query.length < 1) {
                $suggestions.hide();
                $searchBtn.prop("disabled", true);
                return;
            }

            if (cache[query]) {
                renderSuggestions(cache[query]);
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get("/search-locations", { query }, function (response) {
                    cache[query] = response.data;
                    renderSuggestions(response.data);
                });
            }, 300);
        });

        function renderSuggestions(data) {
            $suggestions.empty();

            if (data.length > 0) {
                data.forEach(location => {
                    const $li = $("<li></li>")
                        .attr("data-id", location.id)
                        .text(location.name);
                    $suggestions.append($li);
                });
                $searchBtn.prop("disabled", false);
            } else {
                $suggestions.append(`<li class="no-results">${_l("web.home.no_location_found")}</li>`);
                $searchBtn.prop("disabled", true);
            }

            $suggestions.show();
        }

        $(document).on("click", "#pickup-suggestions li", function () {
            if (!$(this).hasClass("no-results")) {
                $input.val($(this).text());
                $(".searchbtn").prop("disabled", false);
            }
            $suggestions.hide();
        });

        $(document).on("click", function (event) {
            if (!$(event.target).closest(".group-img").length) {
                $suggestions.hide();
            }
        });
    }

    $(document).on("click", ".wishlist-icon", function () {
        const id = $(this).data("id");

        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: {
                id: id,
                _token: $("meta[name=\"csrf-token\"]").attr("content")
            },
            dataType: "json",
            success: function (response) {
                showToast(response.status, response.message);
            },
            error: function () {
                showToast("error", "Something went wrong. Please try again.");
            }
        });
    });

})();
