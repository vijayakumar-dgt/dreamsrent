(async () => {
    "use strict";
    await loadTranslationFile("admin", "blog, common");

    $(document).ready(function () {
        $(".summernote").summernote({
            height: 300,
            placeholder: _l("admin.cms.enter_your_description"),
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });

        $(".blog-delete").on("click", function () {
            let blogId = $(this).data("id");
            $("#delete_blog_id").val(blogId);
        });

        const blogContainer = document.getElementById("blogList");
        const gridViewBtn = document.getElementById("gridViewBtn");
        const listViewBtn = document.getElementById("listViewBtn");

        // Only proceed if all required elements exist
        if (blogContainer && gridViewBtn && listViewBtn) {
            gridViewBtn.addEventListener("click", function () {
                blogContainer.classList.remove("list-view");
                blogContainer.classList.add("grid-view");

                gridViewBtn.classList.add("bg-primary", "text-white");
                listViewBtn.classList.remove("bg-primary", "text-white");
            });

            listViewBtn.addEventListener("click", function () {
                blogContainer.classList.remove("grid-view");
                blogContainer.classList.add("list-view");

                listViewBtn.classList.add("bg-primary", "text-white");
                gridViewBtn.classList.remove("bg-primary", "text-white");
            });
        }

        // Initialize both "add" and "edit" previews
        setupImagePreview("featured_image_add", "selectedFileNameAdd", ".preview-image-add");
        setupImagePreview("imageInput", "selectedFileName", ".preview-image");

        loadBlogs();
    });

    // Helper: Validate image dimensions
    function validateImageDimensions(img, requiredWidth, requiredHeight) {
        return img.width === requiredWidth && img.height === requiredHeight;
    }

    // Helper: Handle invalid image
    function handleInvalidImage(inputEl, fileNameDisplayEl, previewEl) {
        showToast(
            "error",
            _l("admin.blog.image_dimensions_must_be_exactly_900_600_pixels")
        );
        inputEl.value = "";
        if (fileNameDisplayEl) fileNameDisplayEl.textContent = _l("admin.blog.no_file_chosen");
        if (previewEl) previewEl.setAttribute("src", "");
    }

    // Helper: Preview valid image
    function showImagePreview(file, readerEvent, fileNameDisplayEl, previewEl) {
        if (fileNameDisplayEl) fileNameDisplayEl.textContent = file.name;
        if (previewEl) previewEl.setAttribute("src", readerEvent.target.result);
    }

    // Helper: Validate image and show preview or error
    function validateAndPreviewImage(file, readerEvent, inputEl, fileNameDisplayEl, previewEl) {
        const img = new Image();
        img.onload = () => {
            if (validateImageDimensions(img, 900, 600)) {
                showImagePreview(file, readerEvent, fileNameDisplayEl, previewEl);
            } else {
                handleInvalidImage(inputEl, fileNameDisplayEl, previewEl);
            }
        };
        img.src = readerEvent.target.result;
    }

    // Generic function for image preview setup
    function setupImagePreview(inputId, fileNameDisplayId, previewSelector) {
        const inputEl = document.getElementById(inputId);
        const fileNameDisplayEl = document.getElementById(fileNameDisplayId);
        const previewEl = document.querySelector(previewSelector);

        if (!inputEl) return;

        const handleFileChange = (event) => {
            const file = event.target?.files?.[0];
            if (!file?.type.startsWith("image/")) return;

            const reader = new FileReader();
            reader.onload = (readerEvent) => {
                validateAndPreviewImage(file, readerEvent, inputEl, fileNameDisplayEl, previewEl);
            };
            reader.readAsDataURL(file);
        };

        inputEl.addEventListener("change", handleFileChange);
    }

    // Helper: Filter and sort blogs
    function filterAndSortBlogs(allBlogs, selectedCategories, searchKeyword, currentSort) {
        let filtered = allBlogs.filter((blog) => {
            const matchesCategory =
                selectedCategories.length === 0 ||
                selectedCategories.includes(blog.dataset.category);
            const matchesSearch = blog.dataset.title
                .toLowerCase()
                .includes(searchKeyword.toLowerCase());
            return matchesCategory && matchesSearch;
        });

        switch (currentSort) {
            case "asc":
                filtered.sort((a, b) => a.dataset.title.localeCompare(b.dataset.title));
                break;
            case "desc":
                filtered.sort((a, b) => b.dataset.title.localeCompare(a.dataset.title));
                break;
            case "latest":
                filtered.sort((a, b) => new Date(b.dataset.date) - new Date(a.dataset.date));
                break;
            case "last_month": {
                const lastMonth = new Date();
                lastMonth.setMonth(lastMonth.getMonth() - 1);
                filtered = filtered.filter(
                    (item) => new Date(item.dataset.date) >= lastMonth
                );
                break;
            }
            case "last_7_days": {
                const last7Days = new Date();
                last7Days.setDate(last7Days.getDate() - 7);
                filtered = filtered.filter(
                    (item) => new Date(item.dataset.date) >= last7Days
                );
                break;
            }
        }
        return filtered;
    }

    // Helper: Render blogs to DOM
    function renderBlogs(blogList, filteredBlogs, visibleCount, loadMoreBtn) {
        if (!blogList) return;
        blogList.innerHTML = "";

        // Show message if no blogs found
        if (filteredBlogs.length === 0) {
            const message = document.createElement("h6");
            message.className = "no-blogs-message text-center py-4";
            message.textContent = _l('admin.blog.no_blog_found');
            blogList.appendChild(message);

            // Hide "Load More" button parent
            loadMoreBtn?.parentElement?.classList.add("d-none");
            return;
        }

        const blogsToShow = filteredBlogs.slice(0, visibleCount);
        blogsToShow.forEach((blog) => blogList.appendChild(blog));

        const parentDiv = loadMoreBtn?.parentElement;
        if (visibleCount >= filteredBlogs.length) {
            parentDiv?.classList.add("d-none");
        } else {
            parentDiv?.classList.remove("d-none");
        }
    }

    // Helper: Apply filters + render
    function applyFiltersAndRender({
        allBlogs,
        selectedCategories,
        searchKeyword,
        currentSort,
        visibleCount,
        blogList,
        loadMoreBtn,
    }) {
        const filteredBlogs = filterAndSortBlogs(
            allBlogs,
            selectedCategories,
            searchKeyword,
            currentSort
        );
        renderBlogs(blogList, filteredBlogs, visibleCount.value, loadMoreBtn);
        return filteredBlogs;
    }

    // Main function
    function loadBlogs() {
        let currentSort = "latest";
        let selectedCategories = [];
        let searchKeyword = "";
        let visibleCount = { value: 6 };

        const blogList = document.getElementById("blogList");
        const sortDropdownItems = document.querySelectorAll(".dropdown-item-blog");
        const categoryCheckboxes = document.querySelectorAll(".category-checkbox");
        const searchInput = document.getElementById("searchInputBlog");
        const loadMoreBtn = document.querySelector(".load-btn");
        const selectedFilterTextCategoryWrapper = document.getElementById("selectedFilterTextCategory");
        const selectedFilterTextCategory = selectedFilterTextCategoryWrapper?.querySelector("span") ?? null;

        const allBlogs = blogList ? Array.from(blogList.querySelectorAll(".blog-item")) : [];

        // Helper: update filtered blogs and render
        const updateAndRender = () => {
            return applyFiltersAndRender({
                allBlogs,
                selectedCategories,
                searchKeyword,
                currentSort,
                visibleCount,
                blogList,
                loadMoreBtn,
            });
        };

        let filteredBlogs = updateAndRender();

        // Helper: handle sort change
        const handleSortChange = (item) => {
            currentSort = item.getAttribute("data-filter");
            if (selectedFilterTextCategory) selectedFilterTextCategory.innerText = item.innerText;
            visibleCount.value = 6;
            filteredBlogs = updateAndRender();
        };

        // Helper: handle category change
        const handleCategoryChange = () => {
            selectedCategories = Array.from(categoryCheckboxes)
                .filter((cb) => cb.checked)
                .map((cb) => cb.value);
            visibleCount.value = 6;
            filteredBlogs = updateAndRender();
        };

        // Helper: handle search input
        const handleSearchInput = (event) => {
            searchKeyword = event.target.value;
            visibleCount.value = 6;
            filteredBlogs = updateAndRender();
        };

        // Helper: handle load more
        const handleLoadMore = () => {
            visibleCount.value += 6;
            renderBlogs(blogList, filteredBlogs, visibleCount.value, loadMoreBtn);
        };

        // 🔹 Attach event listeners
        sortDropdownItems.forEach((item) => item.addEventListener("click", () => handleSortChange(item)));
        categoryCheckboxes.forEach((checkbox) => checkbox.addEventListener("change", handleCategoryChange));
        if (searchInput) searchInput.addEventListener("input", handleSearchInput);
        if (loadMoreBtn) loadMoreBtn.addEventListener("click", handleLoadMore);
    }

    if ($(".blogCategoryTable").length > 0) {
        $(".blogCategoryTable").DataTable({
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            drawCallback: function () {
                customizeTableFooter($(this));
            },
            language: getDataTableLanguage(),
            initComplete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass(
                    "d-none"
                );
                if ($(".blogCategoryTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
        });
    }

    $(document).on("click", "#blog-edit", function () {
        let blogId = $(this).data("id");
        window.location.href = "/admin/content/blogs/" + blogId;
    });

    $(document).on('click', '#delete_blogs .btn-primary', function () {
        let blogId = $("#delete_blog_id").val();

        $.ajax({
            url: "/admin/content/blog/" + blogId,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            success: function (response) {
                showToast("success", _l("admin.blog.blog_deleted!"));
                location.reload();
                $("#delete_blogs").modal("hide");

                $('a[data-id="' + blogId + '"]')
                    .closest(".blog-img")
                    .remove();
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });

    $(document).on("click", "#saveBlogBtn", function () {
        let formData = new FormData($("#editBlogForm")[0]);
        let blogId = $('input[name="blog_id"]').val();

        $.ajax({
            url: `/admin/content/blog/${blogId}`,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            headers: {
                "X-CSRF-TOKEN": $('input[name="_token"]').val(),
            },
            success: function (response) {
                showToast("success", _l("admin.blog.blog_post_updated!"));
                window.location.href = "/admin/content/blogs";
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });

    $(document).on("click", "#create_blog_btn", function () {
        const image = document.getElementById("featured_image_add").files[0];
        const title = $("#blog_title").val().trim();
        const language = $("#blog_language").val().trim();
        const category = $("#blog_category").val();
        const tag = $("#blog_tags").val();
        const description = $("#editor").val().trim();

        if (!image) {
            showToast("error", _l("admin.blog.please_upload_an_image"));
            return;
        }
        if (!title) {
            showToast("error", _l("admin.blog.please_enter_a_blog_title"));
            return;
        }
        if (!language) {
            showToast("error", _l("admin.blog.please_select_a_language"));
            return;
        }
        if (!category) {
            showToast("error", _l("admin.blog.please_select_a_category"));
            return;
        }
        if (!tag || tag.length === 0) {
            showToast("error", _l("admin.blog.please_select_at_least_one_tag"));
            return;
        }
        if (!description) {
            showToast("error", _l("admin.blog.please_enter_a_description"));
            return;
        }

        const formData = new FormData();
        formData.append("image", image);
        formData.append("title", title);
        formData.append("language", language);
        formData.append("category_id", category);
        tag.forEach((tagId) => {
            formData.append("tag_id[]", tagId);
        });
        formData.append("description", description);

        $.ajax({
            url: "/admin/content/blog-store",
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                showToast("success", _l("admin.blog.blog_post_created!"));
                window.location.href = "/admin/content/blogs";
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });
})();
