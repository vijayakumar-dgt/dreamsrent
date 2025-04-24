document.addEventListener("DOMContentLoaded", function () {
    const blogContainer = document.getElementById("blogListContainer");
    let activeFilters = {};

    function fetchFilteredBlogs(params = {}) {
        activeFilters = { ...params }; // update filters
        const query = new URLSearchParams(params).toString();

        fetch(`/blogs?${query}`, {
            headers: {
                "X-Requested-With": "XMLHttpRequest",
            },
        })
            .then((response) => response.json())
            .then((data) => {
                blogContainer.innerHTML = data.html;
                bindPaginationLinks(); // re-bind after updating HTML

                window.scrollTo({
                    top: blogContainer.offsetTop - 100, // adjust offset as needed
                    behavior: "smooth",
                });
            })
            .catch((error) => console.error("Error fetching blogs:", error));
    }

    function bindPaginationLinks() {
        document.querySelectorAll(".pagination a").forEach((link) => {
            link.addEventListener("click", function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                const page = url.searchParams.get("page");
                fetchFilteredBlogs({ ...activeFilters, page });
            });
        });
    }

    // Initial bindings for category and search
    document.querySelectorAll("[data-category]").forEach((button) => {
        button.addEventListener("click", function () {
            const category = this.dataset.category;
            fetchFilteredBlogs({ category });
        });
    });

    const searchInput = document.getElementById("blogSearch");
    if (searchInput) {
        searchInput.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                const search = this.value.trim();
                fetchFilteredBlogs({ search });
            }
        });
    }

    // Bind pagination on initial load
    bindPaginationLinks();
});

document.addEventListener("DOMContentLoaded", function () {
    const reviewForm = document.getElementById("blogReviewForm");
    if (reviewForm) {
        reviewForm.addEventListener("submit", function (e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);

        if (
            !formData.get("name") ||
            !formData.get("email") ||
            !formData.get("comment")
        ) {
            showToast("error", "All fields are required!");
            return;
        }

        $.ajax({
            url: form.getAttribute("action"),
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                showToast("success", "Review Added!");
                e.preventDefault();
                form.reset();
            },
            error: function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    showToast("error", xhr.responseJSON.message);
                } else {
                    showToast("error", "Something went wrong");
                }
            },
        });
    });
}
});
