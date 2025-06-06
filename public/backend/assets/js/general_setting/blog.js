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
    });
    if ($(".blogCategoryTable").length > 0) {
        $(".blogCategoryTable").DataTable({
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            drawCallback: function () {
                $(".dataTables_info").addClass("d-none");
                $(".dataTables_wrapper .dataTables_paginate").addClass(
                    "d-none"
                );

                var tableWrapper = $(this).closest(".dataTables_wrapper");
                var info = tableWrapper.find(".dataTables_info");
                var pagination = tableWrapper.find(".dataTables_paginate");

                $(".table-footer")
                    .empty()
                    .append(
                        $(
                            '<div class="d-flex justify-content-between align-items-center w-100"></div>'
                        )
                            .append(
                                $('<div class="datatable-info"></div>').append(
                                    info.clone(true)
                                )
                            )
                            .append(
                                $(
                                    '<div class="datatable-pagination"></div>'
                                ).append(pagination.clone(true))
                            )
                    );
                $(".table-footer")
                    .find(".dataTables_paginate")
                    .removeClass("d-none");
            },
            language: {
                emptyTable: _l("admin.common.no_matching_records"),
                info:
                    _l("admin.common.showing") +
                    " _START_ " +
                    _l("admin.common.to") +
                    " _END_ " +
                    _l("admin.common.of") +
                    " _TOTAL_ " +
                    _l("admin.common.entries"),
                infoEmpty:
                    _l("admin.common.showing") +
                    " 0 " +
                    _l("admin.common.to") +
                    " 0 " +
                    _l("admin.common.of") +
                    " 0 " +
                    _l("admin.common.entries"),
                infoFiltered:
                    "(" +
                    _l("admin.common.filtered_from") +
                    " _MAX_ " +
                    _l("admin.common.total_entries") +
                    ")",
                lengthMenu:
                    _l("admin.common.show") +
                    " _MENU_ " +
                    _l("admin.common.entries"),
                search: _l("admin.common.search") + ":",
                zeroRecords: _l("admin.common.empty_table"),
                paginate: {
                    first: _l("admin.common.first"),
                    last: _l("admin.common.last"),
                    next: _l("admin.common.next"),
                    previous: _l("admin.common.previous"),
                },
            },
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

    $(document).ready(function () {
        $(".blog-delete").on("click", function () {
            var blogId = $(this).data("id");
            $("#delete_blog_id").val(blogId);
        });
        $(document).on('click', '#delete_blogs .btn-primary', function () {
            var blogId = $("#delete_blog_id").val();

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
    });
    $(document).ready(function () {
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
    });

    $(document).on("click", "#blog-edit", function () {
        let blogId = $(this).data("id");
        window.location.href = "/admin/content/blogs/" + blogId;
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

    $(document).ready(function () {
        const inputAdd = document.getElementById("featured_image_add");
        const fileNameDisplayAdd = document.getElementById(
            "selectedFileNameAdd"
        );
        const preview = document.querySelector(".preview-image-add") ?? "";

        if (inputAdd) {
            inputAdd.addEventListener("change", function (event) {
                const file = event.target.files[0];

                if (file && file.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const img = new Image();
                        img.onload = function () {
                            if (img.width === 900 && img.height === 600) {
                                fileNameDisplayAdd.textContent = file.name;
                                if (preview) {
                                    preview.src = e.target.result;
                                }
                            } else {
                                showToast(
                                    "error",
                                    _l(
                                        "admin.blog.image_dimensions_must_be_exactly_900_600_pixels"
                                    )
                                );
                                inputAdd.value = "";
                                fileNameDisplayAdd.textContent = _l(
                                    "admin.blog.no_file_chosen"
                                );
                                if (preview) preview.src = "";
                            }
                        };
                        img.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        }
    });

    $(document).ready(function () {
        const input = document.getElementById("imageInput");
        const fileNameDisplay = document.getElementById("selectedFileName");
        const previewContainer = document.querySelector(".preview-image");

        if (input) {
            input.addEventListener("change", function (event) {
                const file = event.target.files[0];

                if (file && file.type.startsWith("image/")) {
                    const reader = new FileReader();

                    reader.onload = function (e) {
                        const img = new Image();
                        img.onload = function () {
                            if (img.width === 900 && img.height === 600) {
                                fileNameDisplay.textContent = file.name;

                                previewContainer.innerHTML = "";

                                const newImage = document.createElement("img");
                                newImage.src = e.target.result;
                                newImage.classList.add(
                                    "rounded-2",
                                    "img-fluid"
                                );
                                previewContainer.appendChild(newImage);
                            } else {
                                showToast(
                                    "error",
                                    _l(
                                        "admin.blog.image_dimensions_must_be_exactly_900_600_pixels"
                                    )
                                );
                                input.value = "";
                                fileNameDisplay.textContent = _l(
                                    "admin.blog.no_file_chosen"
                                );
                                previewContainer.innerHTML = "";
                            }
                        };
                        img.src = e.target.result;
                    };

                    reader.readAsDataURL(file);
                }
            });
        }
    });

    document.addEventListener("DOMContentLoaded", function () {
        const itemsPerPage = 9;
        const blogItems = document.querySelectorAll("#blogList .blog-item");
        const loadMoreBtn = document.querySelector(".load-btn");
        let currentIndex = 0;

        function showItems() {
            for (
                let i = currentIndex;
                i < currentIndex + itemsPerPage && i < blogItems.length;
                i++
            ) {
                blogItems[i].style.display = "block";
            }
            currentIndex += itemsPerPage;

            if (currentIndex >= blogItems.length && loadMoreBtn) {
                loadMoreBtn.style.display = "none";
            }
        }

        if (blogItems.length <= itemsPerPage && loadMoreBtn) {
            loadMoreBtn.style.display = "none";
        }

        blogItems.forEach((item) => (item.style.display = "none"));
        showItems();

        if (loadMoreBtn) {
            loadMoreBtn.addEventListener("click", showItems);
        }
    });

    $(document).ready(function () {
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

        let currentSort = "latest";
        let selectedCategories = [];
        let searchKeyword = "";
        let filteredBlogs = [];
        let visibleCount = 6;

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
            if (visibleCount >= filteredBlogs.length) {
                if (loadMoreBtn) {
                    loadMoreBtn.style.display = "none";
                }
            } else {
                if (loadMoreBtn) {
                    loadMoreBtn.style.display = "inline-block";
                }
            }
        }

        function applyFiltersAndRender() {
            visibleCount = 6;
            filterAndSortBlogs();
            renderBlogs();
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
    });
})();
