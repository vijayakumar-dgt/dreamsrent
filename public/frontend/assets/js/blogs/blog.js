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

        const fetchFilteredBlogs = async (params = {}) => {
            activeFilters = { ...params };
            const query = new URLSearchParams(activeFilters).toString();

            try {
                const response = await fetch(`/blogs?${query}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });

                const data = await response.json();
                $blogContainer.html(data.html);
                window.scrollTo({
                    top: $blogContainer.offset().top - 100,
                    behavior: "smooth",
                });
            } catch {
                showToast("error", _l("web.common.default_retrieve_error"));
            }
        };

        // Handle pagination clicks
        $(document).on("click", ".pagination a", function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            const page = url.searchParams.get("page");
            fetchFilteredBlogs({ ...activeFilters, page });
        });

        // Category filter clicks
        $(document).on("click", "[data-category]", function () {
            const category = $(this).data("category");
            fetchFilteredBlogs({ category });
        });

        // Search on Enter key
        if ($searchInput.length) {
            $searchInput.on("keypress", function (e) {
                if (e.key === "Enter") {
                    const search = $searchInput.val().trim();
                    fetchFilteredBlogs({ search });
                }
            });
        }

        // Review form submission
        if ($reviewForm.length) {
            $reviewForm.on("submit", function (e) {
                e.preventDefault();
                const formData = new FormData(this);

                if (!formData.get("comment")) {
                    showToast("error", _l("web.blog.all_fields_are_required"));
                    return;
                }

                $.ajax({
                    url: $reviewForm.attr("action"),
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": csrfToken },
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: () => {
                        showToast("success", _l("web.blog.review_added_successfully"));
                        this.reset();
                    },
                    error: (xhr) => {
                        const msg = xhr.responseJSON?.message || _l("web.common.something_went_wrong");
                        showToast("error", msg);
                    },
                });
            });
        }

        // Initial load binding (if needed)
        if ($(".pagination a").length) {
            const url = new URL(window.location.href);
            const page = url.searchParams.get("page");
            if (page) fetchFilteredBlogs({ page });
        }
    });
})();