(async () => {
    "use strict";

    await loadTranslationFile("web", "common,blog");

    $(function () {
        const $blogContainer = $("#blogListContainer");
        let activeFilters = {};

        const fetchFilteredBlogs = async (params = {}) => {
            activeFilters = { ...params };
            const query = new URLSearchParams(params).toString();

            try {
                const response = await fetch(`/blogs?${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                const data = await response.json();
                $blogContainer.html(data.html);
                bindPaginationLinks();

                window.scrollTo({
                    top: $blogContainer.offset().top - 100,
                    behavior: "smooth",
                });
            } catch (error) {
                console.error(_l('web.common.default_retrieve_error'));
            }
        };

        const bindPaginationLinks = () => {
            $(".pagination a").off("click").on("click", function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get("page");
                fetchFilteredBlogs({ ...activeFilters, page });
            });
        };

        // Category filter
        $("[data-category]").off("click").on("click", function () {
            const category = $(this).data("category");
            fetchFilteredBlogs({ category });
        });

        // Search input
        const $searchInput = $("#blogSearch");
        if ($searchInput.length) {
            $searchInput.off("keypress").on("keypress", function (e) {
                if (e.key === "Enter") {
                    const search = $searchInput.val().trim();
                    fetchFilteredBlogs({ search });
                }
            });
        }

        // Initial pagination binding
        bindPaginationLinks();

        // Review form
        const $reviewForm = $("#blogReviewForm");
        if ($reviewForm.length) {
            $reviewForm.off("submit").on("submit", function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                if (
                    !formData.get("comment")
                ) {
                    showToast("error", _l("web.blog.all_fields_are_required"));
                    return;
                }

                $.ajax({
                    url: $reviewForm.attr("action"),
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        showToast("success", _l('web.blog.review_added_successfully'));
                        $reviewForm[0].reset();
                    },
                    error: xhr => {
                        if (xhr.responseJSON?.message) {
                            showToast("error", xhr.responseJSON.message);
                        } else {
                            showToast("error", _l('web.common.something_went_wrong'));
                        }
                    },
                });
            });
        }
    });
})();
