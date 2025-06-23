/* global $, document, loadTranslationFile, URLSearchParams, window, showToast, _l, FormData, fetch, URL */

(async () => {
    "use strict";

    await loadTranslationFile("web", "common,blog");

    $(function () {
        const $blogContainer = $("#blogListContainer");
        const $searchInput = $("#blogSearch");
        const $reviewForm = $("#blogReviewForm");
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        let activeFilters = {};

        const fetchFilteredBlogs = async (filters = {}) => {
            activeFilters = { ...filters };
            const queryString = new URLSearchParams(activeFilters).toString();

            try {
                const response = await fetch(`/blogs?${queryString}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });

                const result = await response.json();
                $blogContainer.html(result.html);

                $("html, body").animate({
                    scrollTop: $blogContainer.offset().top - 100,
                }, 500);
            } catch {
                showToast("error", _l("web.common.default_retrieve_error"));
            }
        };

        // Event: Pagination click
        $(document).on("click", ".pagination a", function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get("page");
            fetchFilteredBlogs({ ...activeFilters, page });
        });

        // Event: Category filter click
        $(document).on("click", "[data-category]", function () {
            const category = $(this).data("category");
            fetchFilteredBlogs({ category });
        });

        // Event: Search input (Enter key)
        if ($searchInput.length) {
            $searchInput.on("keypress", function (e) {
                if (e.key === "Enter") {
                    const search = $searchInput.val().trim();
                    fetchFilteredBlogs({ search });
                }
            });
        }

        // Event: Submit review form
        if ($reviewForm.length) {
            $reviewForm.on("submit", function (e) {
                e.preventDefault();
                const form = this;
                const formData = new FormData(form);

                const comment = formData.get("comment")?.trim();
                if (!comment) {
                    showToast("error", _l("web.blog.all_fields_are_required"));
                    return;
                }

                $.ajax({
                    url: form.action,
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        showToast("success", _l("web.blog.review_added_successfully"));
                        form.reset();
                    },
                    error: (xhr) => {
                        const msg = xhr.responseJSON?.message || _l("web.common.something_went_wrong");
                        showToast("error", msg);
                    },
                });
            });
        }

        // Initial pagination-bound load
        const currentPage = new URL(window.location.href).searchParams.get("page");
        if (currentPage) fetchFilteredBlogs({ page: currentPage });
    });
})();