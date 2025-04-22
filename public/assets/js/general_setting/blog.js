(async () => {
    await loadTranslationFile('admin', 'blog, common');

    if ($('.blogCategoryTable').length > 0) {
        $('.blogCategoryTable').DataTable({
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            "drawCallback": function () {
                $(".dataTables_info").addClass('d-none');
                $(".dataTables_wrapper .dataTables_paginate").addClass('d-none');
    
                var tableWrapper = $(this).closest('.dataTables_wrapper');
                var info = tableWrapper.find('.dataTables_info');
                var pagination = tableWrapper.find('.dataTables_paginate');
    
                $('.table-footer').empty()
                    .append($('<div class="d-flex justify-content-between align-items-center w-100"></div>')
                        .append($('<div class="datatable-info"></div>').append(info.clone(true)))
                        .append($('<div class="datatable-pagination"></div>').append(pagination.clone(true)))
                    );
                $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
            },
            language: {
                emptyTable: _l("admin.common.no_matching_records"),
                info: _l("admin.common.showing") + " _START_ " + _l("admin.common.to") + " _END_ " + _l("admin.common.of") + " _TOTAL_ " + _l("admin.common.entries"),
                infoEmpty: _l("admin.common.showing") + " 0 " + _l("admin.common.to") + " 0 " + _l("admin.common.of") + " 0 " + _l("admin.common.entries"),
                infoFiltered: "(" + _l("admin.common.filtered_from") + " _MAX_ " + _l("admin.common.total_entries") + ")",
                lengthMenu: _l("admin.common.show") + " _MENU_ " + _l("admin.common.entries"),
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
                $(".real-table, .real-label, .real-input").removeClass("d-none");
                if ($(".blogCategoryTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            }
        });
    }
    

$(document).ready(function () {
    $(document).ready(function () {
        $('.summernote').summernote({
            height: 300,
            placeholder: _l('admin.cms.enter_your_description'),
            toolbar: [
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });
    // Setup CSRF token for all AJAX
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    // Create Category
    $("#create_category_btn").click(function () {
        
    const title = $("#add_category_name").val().trim();

    // Validate
    if (!title) {
        showToast("error", _l('admin.blog.please_enter_the_name'));
        return;
    }
        $.ajax({
            url: "/admin/content/categories",
            type: "POST",
            data: {
                name: $("#add_category_name").val(),
                language_id: $("#add_language").val(),
            },
            success: function (response) {
                showToast("success",  _l('admin.blog.blog_category_created!'));
                location.reload(); // Or update list dynamically
                $("#add_Category").modal("hide");
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });

    // Open Edit Modal and Fill Data
    $(document).on("click", ".open-edit-modal", function () {
        let id = $(this).data("id");
        let name = $(this).data("name");
        let status = $(this).data("status");

        $("#edit_category_id").val(id);
        $("#edit_category_name").val(name);
        $("#edit_category_status").prop("checked", status == 1);
        $("#edit_Category").modal("show");
    });

    // Update Category
    $("#update_category_btn").click(function () {
        const title = $("#edit_category_name").val().trim();

        // Validate
        if (!title) {
            showToast("error", _l('admin.blog.please_enter_the_name'));
            return;
        }

        let id = $("#edit_category_id").val();

        $.ajax({
            url: "/admin/content/categories/" + id,
            type: "POST", // use POST here
            data: {
                _token: $('meta[name="csrf-token"]').attr("content"), // csrf
                _method: "PUT", // spoof PUT method
                name: $("#edit_category_name").val(),
                status: $("#edit_category_status").is(":checked") ? 1 : 0,
            },
            success: function (response) {
                showToast("success", _l('admin.blog.blog_category_updated!'));
                location.reload();
                $("#edit_Category").modal("hide");
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });

    // Open Delete Modal
    $(document).on("click", ".open-delete-modal", function () {
        let id = $(this).data("id");
        $("#delete_category_id").val(id);
        $("#delete_Category").modal("show");
    });

    // Delete Category
    $("#delete_category_btn").click(function () {
        let id = $("#delete_category_id").val();
        $.ajax({
            url: "/admin/content/categories/" + id,
            type: "POST",
            success: function (response) {
                showToast("success",  _l('admin.blog.blog_category_deleted!'));
                location.reload();
                $("#delete_Category").modal("hide");
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
        });
    });
});

$("#create_blog_btn").click(function () {
    // Get form values
    const image = document.getElementById("featured_image").files[0];
    const title = $("#blog_title").val().trim();
    const language = $("#blog_language").val().trim();
    const category = $("#blog_category").val();
    const tag = $("#blog_tags").val();
    const description = $("#editor").val().trim();

    // Validate
    if (!image) {
        showToast("error", _l('admin.blog.please_upload_an_image'));
        return;
    }
    if (!title) {
        showToast("error", _l('admin.blog.please_enter_a_blog_title'));
        return;
    }
    if (!language) {
        showToast("error", _l('admin.blog.please_select_a_language'));
        return;
    }
    if (!category) {
        showToast("error", _l('admin.blog.please_select_a_category'));
        return;
    }
    if (!tag || tag.length === 0) {
        showToast("error", _l('admin.blog.please_select_at_least_one_tag'));
        return;
    }
    if (!description) {
        showToast("error", _l('admin.blog.please_enter_a_description'));
        return;
    }

    // If validation passes, append to FormData
    const formData = new FormData();
    formData.append("image", image);
    formData.append("title", title);
    formData.append("language", language);
    formData.append("category_id", category);
    tag.forEach(tagId => {
        formData.append("tag_id[]", tagId);
    });
    formData.append("description", description);

    $.ajax({
        url: "/admin/content/blog-store", // update if different
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
            showToast("success", _l('admin.blog.blog_post_created!'));
            window.location.href = "/admin/content/blogs";
        },
        error: function (xhr) {
            showToast("error", xhr.responseJSON.message);
        },
    });
});

$(document).ready(function () {
    // Set blog ID in modal
    $(".blog-delete").on("click", function () {
        var blogId = $(this).data("id");
        $("#delete_blog_id").val(blogId);
    });

    // AJAX delete
    $("#confirmDelete").on("click", function () {
        var blogId = $("#delete_blog_id").val();

        $.ajax({
            url: "/admin/content/blog/" + blogId,
            type: "POST",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                showToast("success", _l('admin.blog.blog_deleted!'));
                location.reload(); // Or update list dynamically
                $("#delete_blogs").modal("hide");
                // Optionally remove blog from DOM
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

$(document).on("click", "#blog-edit", function () {
    let blogId = $(this).data("id");
    window.location.href = "/admin/content/blogs/" + blogId;
});

$("#saveBlogBtn").click(function () {
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
            showToast("success", _l('admin.blog.blog_post_updated!'));
            window.location.href = "/admin/content/blogs";
        },
        error: function (xhr) {
            showToast("error", xhr.responseJSON.message);
        },
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const blogList = document.getElementById("blogList");
    const allBlogItems = Array.from(document.querySelectorAll(".blog-item"));
    const checkboxes = document.querySelectorAll(".category-checkbox");
    const sortLinks = document.querySelectorAll("#sortDropdown .dropdown-item");
    const selectedFilterSpan = document.getElementById("selectedFilter");

    const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", applyFilters);

    let currentSort = "latest";

    function getSelectedCategories() {
        return Array.from(checkboxes)
            .filter((cb) => cb.checked)
            .map((cb) => cb.value);
    }

    function applyFilters() {
        const selectedCategories = getSelectedCategories();
        const searchText = searchInput.value.toLowerCase().trim();

        let filtered = [...allBlogItems];

        // 🔍 Filter by category
        if (selectedCategories.length > 0) {
            filtered = filtered.filter((item) =>
                selectedCategories.includes(String(item.dataset.category))
            );
        }

        // 🔍 Filter by search text
        if (searchText !== "") {
            filtered = filtered.filter((item) => {
                const title = item.dataset.title?.toLowerCase() || "";
                return title.includes(searchText);
            });
        }

        // 🔁 Sort (same as before)
        if (currentSort === "asc") {
            filtered.sort(
                (a, b) => new Date(a.dataset.date) - new Date(b.dataset.date)
            );
        } else if (currentSort === "desc") {
            filtered.sort(
                (a, b) => new Date(b.dataset.date) - new Date(a.dataset.date)
            );
        } else if (currentSort === "last_month") {
            const lastMonth = new Date();
            lastMonth.setMonth(lastMonth.getMonth() - 1);
            filtered = filtered.filter(
                (item) => new Date(item.dataset.date) >= lastMonth
            );
        } else if (currentSort === "last_7_days") {
            const last7 = new Date();
            last7.setDate(last7.getDate() - 7);
            filtered = filtered.filter(
                (item) => new Date(item.dataset.date) >= last7
            );
        } else {
            filtered.sort(
                (a, b) => new Date(b.dataset.date) - new Date(a.dataset.date)
            );
        }

        // 🧼 Render filtered list
        blogList.innerHTML = "";
        filtered.forEach((item) => blogList.appendChild(item));
    }

    // Bind checkbox change
    checkboxes.forEach((cb) => {
        cb.addEventListener("change", applyFilters);
    });

    // Bind sort dropdown
    sortLinks.forEach((link) => {
        link.addEventListener("click", function () {
            currentSort = this.dataset.filter;
            if (selectedFilterSpan)
                selectedFilterSpan.textContent = this.textContent;
            applyFilters();
        });
    });

    // Initial render
    applyFilters();
});

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("imageInput");
    const fileNameDisplay = document.getElementById("selectedFileName");
    const preview = document.querySelector(".preview-image");

    input.addEventListener("change", function (event) {
        const file = event.target.files[0];

        // Show selected file name
        if (file) {
            fileNameDisplay.textContent = file.name;
        } else {
            fileNameDisplay.textContent = "No file chosen";
        }

        // Preview image
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                if (preview) {
                    preview.src = e.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const input = document.getElementById("featured_image");
    const fileNameDisplay = document.getElementById("selectedFileName");
    const previewContainer = document.querySelector(".preview-image");

    input.addEventListener("change", function (event) {
        const file = event.target.files[0];

        // Show selected file name
        if (file) {
            fileNameDisplay.textContent = file.name;
        } else {
            fileNameDisplay.textContent = "No file chosen";
        }

        // Preview image
        if (file && file.type.startsWith("image/")) {
            const reader = new FileReader();
            reader.onload = function (e) {
                // Clear existing image
                previewContainer.innerHTML = "";

                // Create new image
                const newImage = document.createElement("img");
                newImage.src = e.target.result;
                newImage.classList.add("rounded-2", "img-fluid");

                // Append new image
                previewContainer.appendChild(newImage);
            };
            reader.readAsDataURL(file);
        }
    });
});

const blogContainer = document.getElementById("blogList");
const gridViewBtn = document.getElementById("gridViewBtn");
const listViewBtn = document.getElementById("listViewBtn");

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

document.addEventListener("DOMContentLoaded", function () {
    const itemsPerPage = 9;
    const blogItems = document.querySelectorAll("#blogList .blog-item");
    const loadMoreBtn = document.querySelector(".load-btn");
    let currentIndex = 0;

    function showItems() {
        for (let i = currentIndex; i < currentIndex + itemsPerPage && i < blogItems.length; i++) {
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

    // Initially hide all and show first batch
    blogItems.forEach(item => item.style.display = "none");
    showItems();

    if (loadMoreBtn) {
        loadMoreBtn.addEventListener("click", showItems);
    }
});

})();
