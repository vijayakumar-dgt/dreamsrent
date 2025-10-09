(async () => {
    "use strict";
    await loadTranslationFile("admin", "blog, common");
    
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
                if ($(".blogCategoryTable").DataTable().rows().count() === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
        });
    }

    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $(document).on("click", "#create_category_btn", function () {
            const title = $("#add_category_name").val().trim();
            if (!title) {
                showToast("error", _l("admin.blog.please_enter_the_name"));
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
                    showToast(
                        "success",
                        _l("admin.blog.blog_category_created!")
                    );
                    location.reload();
                    $("#add_Category").modal("hide");
                },
                error: function (xhr) {
                    showToast("error", xhr.responseJSON.message);
                },
            });
        });

        $(document).on("click", ".open-edit-modal", function () {
            let id = $(this).data("id");
            let name = $(this).data("name");
            let status = $(this).data("status");

            $("#edit_category_id").val(id);
            $("#edit_category_name").val(name);
            $("#edit_category_status").prop("checked", status == 1);
            $("#edit_Category").modal("show");
        });

        $(document).on("click", "#update_category_btn", function () {
            const title = $("#edit_category_name").val().trim();
            if (!title) {
                showToast("error", _l("admin.blog.please_enter_the_name"));
                return;
            }
            let id = $("#edit_category_id").val();
            $.ajax({
                url: "/admin/content/categories/" + id,
                type: "POST",
                data: {
                    _token: $('meta[name="csrf-token"]').attr("content"),
                    _method: "PUT",
                    name: $("#edit_category_name").val(),
                    status: $("#edit_category_status").is(":checked") ? 1 : 0,
                },
                success: function (response) {
                    showToast(
                        "success",
                        _l("admin.blog.blog_category_updated!")
                    );
                    location.reload();
                    $("#edit_Category").modal("hide");
                },
                error: function (xhr) {
                    showToast("error", xhr.responseJSON.message);
                },
            });
        });

        $(document).on("click", ".open-delete-modal", function () {
            let id = $(this).data("id");
            $("#delete_category_id").val(id);
            $("#delete_Category").modal("show");
        });

        $(document).on('click', '#delete_Category', function () {
            let id = $("#delete_category_id").val();
            $.ajax({
                url: "/admin/content/categories/" + id,
                type: "POST",
                success: function (response) {
                    showToast(
                        "success",
                        _l("admin.blog.blog_category_deleted!")
                    );
                    location.reload();
                    $("#delete_Category").modal("hide");
                },
                error: function (xhr) {
                    showToast("error", xhr.responseJSON.message);
                },
            });
        });
    });

    const searchInput = document.getElementById("searchInputCategory");
    const tableRows = document.querySelectorAll(".blogCategoryTable tbody tr");

    searchInput.addEventListener("input", function () {
        const query = this.value.toLowerCase();

        tableRows.forEach((row) => {
            const rowText = row.textContent.toLowerCase();

            if (rowText.includes(query)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    const originalRows = Array.from(
        document.querySelectorAll(".blogCategoryTable tbody tr")
    );
    const tbody = document.querySelector(".blogCategoryTable tbody");

    document.querySelectorAll(".sort-option-category").forEach(function (item) {
        item.addEventListener("click", function () {
            const sortType = this.getAttribute("data-sort");
            const dropdownLabel = document
                .getElementById("selectedFilterTextCategory")
                .querySelector("span");
            dropdownLabel.textContent = this.textContent.trim();

            tbody.innerHTML = "";
            originalRows.forEach((row) =>
                tbody.appendChild(row.cloneNode(true))
            );

            const rows = Array.from(tbody.querySelectorAll("tr"));
            let resultRows = [...rows];

            if (sortType === "asc") {
                resultRows.sort((a, b) =>
                    a.dataset.name.localeCompare(b.dataset.name)
                );
            } else if (sortType === "desc") {
                resultRows.sort((a, b) =>
                    b.dataset.name.localeCompare(a.dataset.name)
                );
            } else if (sortType === "latest") {
                resultRows.sort(
                    (a, b) =>
                        new Date(b.dataset.created) -
                        new Date(a.dataset.created)
                );
            } else if (sortType === "last_7_days") {
                const cutoff = new Date();
                cutoff.setDate(cutoff.getDate() - 7);
                resultRows = resultRows.filter(
                    (row) => new Date(row.dataset.created) >= cutoff
                );
            } else if (sortType === "last_month") {
                const now = new Date();
                const start = new Date(
                    now.getFullYear(),
                    now.getMonth() - 1,
                    1
                );
                const end = new Date(now.getFullYear(), now.getMonth(), 0);
                resultRows = resultRows.filter((row) => {
                    const created = new Date(row.dataset.created);
                    return created >= start && created <= end;
                });
            }

            tbody.innerHTML = "";
            resultRows.forEach((row) => tbody.appendChild(row));
        });
    });
})();
