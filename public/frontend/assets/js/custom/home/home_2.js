(function () {
    "use strict";

    (async () => {
        await loadTranslationFile('web', 'home');
        initDualLocationSearch();
    })();

    function initDualLocationSearch() {
        setupLocationSearch("#pickup-location-input", "#pickup-suggestions");
        setupLocationSearch("#drop-location-input", "#drop-suggestions");
    }

    function setupLocationSearch(inputSelector, suggestionSelector) {
        const $input = $(inputSelector);
        const $suggestions = $(suggestionSelector);
        const $searchBtn = $(".searchbtn");
        const cache = {};
        let searchTimeout;

        $input.on("keyup", function () {
            const query = $input.val().trim().toLowerCase();
            clearTimeout(searchTimeout);

            if (query.length === 0) {
                $suggestions.hide();
                $searchBtn.prop("disabled", false);
                return;
            }

            if (query.length < 1) {
                $suggestions.hide();
                $searchBtn.prop("disabled", true);
                return;
            }

            if (cache[query]) {
                renderSuggestions($suggestions, cache[query], $input, $searchBtn);
                return;
            }

            searchTimeout = setTimeout(() => {
                $.get("/search-locations", { query }, function (response) {
                    cache[query] = response.data;
                    renderSuggestions($suggestions, response.data, $input, $searchBtn);
                });
            }, 300);
        });

        $(document).on("click", suggestionSelector + " li", function () {
            if (!$(this).hasClass("no-results")) {
                $input.val($(this).text());
                $searchBtn.prop("disabled", false);
            }
            $suggestions.hide();
        });
    }

    function renderSuggestions($container, data, $input, $searchBtn) {
        $container.empty();

        if (data.length > 0) {
            data.forEach(location => {
                const $li = $('<li></li>').text(location.name); // Safe way
                $container.append($li);
            });
            $searchBtn.prop("disabled", false);
        } else {
            $container.append(`<li class="no-results">${_l('web.home.no_location_found')}</li>`);
            $searchBtn.prop("disabled", true);
        }

        $container.show();
    }

    $(document).on("click", function (event) {
        if (!$(event.target).closest(".group-img").length) {
            $("#pickup-suggestions").hide();
            $("#drop-suggestions").hide();
        }
    });

    $(document).on("click", ".wishlist-icon", function () {
        const id = $(this).data("id");

        $.ajax({
            type: "POST",
            url: "/user/add-to-wishlist",
            data: {
                id: id,
                _token: $('meta[name="csrf-token"]').attr("content")
            },
            dataType: "json",
            success: function (response) {
                showToast(response.status, response.message);
            },
            error: function (error) {
                console.error(error);
            }
        });
    });

      
    window.addEventListener('scroll', () => {
        const header = document.querySelector('.theme-2-header');
        if (window.scrollY > 50) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
})();
