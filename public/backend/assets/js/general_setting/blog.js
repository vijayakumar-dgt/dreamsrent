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

        // Add Image Preview
        const inputAdd = document.getElementById("featured_image_add");
        const fileNameDisplayAdd = document.getElementById(
            "selectedFileNameAdd"
        );
        const preview = document.querySelector(".preview-image-add") ?? "";

        if (inputAdd) {
            inputAdd?.addEventListener("change", (event) => {
                const file = event.target.files?.[0];

                if (file?.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            if (img.width === 900 && img.height === 600) {
                                fileNameDisplayAdd.textContent = file.name;
                                preview?.setAttribute("src", e.target.result);
                            } else {
                                showToast(
                                    "error",
                                    _l(
                                        "admin.blog.image_dimensions_must_be_exactly_900_600_pixels"
                                    )
                                );
                                inputAdd.value = "";
                                fileNameDisplayAdd.textContent = _l("admin.blog.no_file_chosen");
                                preview?.setAttribute("src", "");
                            }
                        };
                        img.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        }

        // Edit Image Preview
        const inputEdit = document.getElementById("imageInput");
        const fileNameDisplay = document.getElementById("selectedFileName");
        const previewContainer = document.querySelector(".preview-image");

        if (inputEdit) {
            console.log(inputEdit);
            inputEdit?.addEventListener("change", (event) => {

                const file = event.target.files?.[0];

                if (file?.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            if (img.width === 900 && img.height === 600) {
                                fileNameDisplay.textContent = file.name;
                                previewContainer?.setAttribute("src", e.target.result);
                            } else {
                                showToast(
                                    "error",
                                    _l(
                                        "admin.blog.image_dimensions_must_be_exactly_900_600_pixels"
                                    )
                                );
                                inputEdit.value = "";
                                fileNameDisplay.textContent = _l("admin.blog.no_file_chosen");
                                previewContainer?.setAttribute("src", "");
                            }
                        };
                        img.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        }

        loadBlogs();
    });

    function loadBlogs() {
        let currentSort = "latest";
        let selectedCategories = [];
        let searchKeyword = "";
        let filteredBlogs = [];
        let visibleCount = 6;

        const blogList = document.getElementById("blogList");
        let allBlogs = [];

        if (blogList) {
            allBlogs = Array.from(blogList.querySelectorAll(".blog-item"));
        }
        const sortDropdownItems = document.querySelectorAll(
            ".dropdown-item-blog"
        );
        const categoryCheckboxes =
            document.querySelectorAll(".category-checkbox");
        const searchInput = document.getElementById("searchInputBlog");
        const loadMoreBtn = document.querySelector(".load-btn");
        const selectedFilterTextCategoryWrapper = document.getElementById(
            "selectedFilterTextCategory"
        );
        let selectedFilterTextCategory = null;

        if (selectedFilterTextCategoryWrapper) {
            selectedFilterTextCategory =
                selectedFilterTextCategoryWrapper.querySelector("span");
        }

        sortDropdownItems.forEach((item) => {
            item.addEventListener("click", function () {
                currentSort = this.getAttribute("data-filter");
                selectedFilterTextCategory.innerText = this.innerText;
                applyFiltersAndRender();
            });
        });

        categoryCheckboxes.forEach((checkbox) => {
            checkbox.addEventListener("change", function () {
                selectedCategories = Array.from(categoryCheckboxes)
                    .filter((cb) => cb.checked)
                    .map((cb) => cb.value);
                applyFiltersAndRender();
            });
        });

        if (searchInput) {
            searchInput.addEventListener("input", function () {
                searchKeyword = this.value;
                applyFiltersAndRender();
            });
        }

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener("click", function () {
                visibleCount += 6;
                renderBlogs();
            });
        }

        applyFiltersAndRender();

        function filterAndSortBlogs() {
            filteredBlogs = allBlogs.filter((blog) => {
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
                    filteredBlogs.sort((a, b) =>
                        a.dataset.title.localeCompare(b.dataset.title)
                    );
                    break;
                case "desc":
                    filteredBlogs.sort((a, b) =>
                        b.dataset.title.localeCompare(a.dataset.title)
                    );
                    break;
                case "latest":
                    filteredBlogs.sort(
                        (a, b) =>
                            new Date(b.dataset.date) - new Date(a.dataset.date)
                    );
                    break;
                case "last_month":
                    const lastMonth = new Date();
                    lastMonth.setMonth(lastMonth.getMonth() - 1);
                    filteredBlogs = filteredBlogs.filter(
                        (item) => new Date(item.dataset.date) >= lastMonth
                    );
                    break;
                case "last_7_days":
                    const last7Days = new Date();
                    last7Days.setDate(last7Days.getDate() - 7);
                    filteredBlogs = filteredBlogs.filter(
                        (item) => new Date(item.dataset.date) >= last7Days
                    );
                    break;
            }
        }

        function renderBlogs() {
            if (blogList) {
                blogList.innerHTML = "";
            }
            const blogsToShow = filteredBlogs.slice(0, visibleCount);
            blogsToShow.forEach((blog) => blogList.appendChild(blog));
            const parentDiv = loadMoreBtn?.parentElement; // get the parent div

            if (visibleCount >= filteredBlogs.length) {
                parentDiv?.classList.add("d-none"); // hide parent div
            } else {
                parentDiv?.classList.remove("d-none"); // show parent div
            }
        }

        function applyFiltersAndRender() {
            visibleCount = 6;
            filterAndSortBlogs();
            renderBlogs();
        }
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
