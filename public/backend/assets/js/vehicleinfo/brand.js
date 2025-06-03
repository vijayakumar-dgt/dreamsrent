(async () => {
    "use strict";

    await loadTranslationFile("admin", "common, rentals");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initValidation();
        initEvents();
    });

    function initValidation() {
        $("#brandForm").validate({
            rules: {
                vehicle_category_id: {
                    required: true,
                },
                brand_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
                brand_image: {
                    required: () => $("#id").val() === "",
                    extension: "jpeg|jpg|png|svg",
                    filesize: 2048,
                    imageDimension: [180, 180],
                },
                brand_icon: {
                    required: () => $("#id").val() === "",
                    extension: "jpeg|jpg|png|svg",
                    filesize: 2048,
                    iconDimension: [25, 25],
                },
            },
            messages: {
                vehicle_category_id: {
                    required: _l("admin.rentals.category_required"),
                },
                brand_name: {
                    required: _l("admin.rentals.brand_name_required"),
                    minlength: _l("admin.rentals.brand_name_minlength"),
                    maxlength: _l("admin.rentals.brand_name_maxlength"),
                },
                brand_image: {
                    required: _l("admin.rentals.brand_image_required"),
                    extension: _l("admin.rentals.brand_image_format"),
                    filesize: _l("admin.rentals.brand_image_size", { size: 2 }),
                },
                brand_icon: {
                    required: _l("admin.rentals.brand_icon_required"),
                    extension: _l("admin.rentals.brand_icon_format"),
                    filesize: _l("admin.rentals.brand_icon_size", { size: 2 }),
                },
            },
            errorPlacement: function (error, element) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .addClass("is-invalid")
                        .removeClass("is-valid");
                }
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .removeClass("is-invalid")
                        .addClass("is-valid");
                }
                $("#" + element.id + "_error").text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: handleSubmit,
        });

        $.validator.addMethod(
            "filesize",
            (value, element, param) => {
                return (
                    element.files.length === 0 ||
                    element.files[0].size <= param * 1024
                );
            },
            "File size must be less than {0} KB."
        );

        $.validator.addMethod(
            "iconDimension",
            function (value, element) {
                if (element.files.length === 0) return true;

                let file = element.files[0];
                let img = new Image();
                let valid = false;

                let reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                };

                img.onload = function () {
                    valid =
                        img.width >= 10 &&
                        img.width <= 25 &&
                        img.height >= 10 &&
                        img.height <= 25;
                    $(element).data("valid-dimension", valid);
                    $(element).valid();
                };

                reader.readAsDataURL(file);

                return $(element).data("valid-dimension") !== false;
            },
            _l("admin.rentals.brand_icon_dimension")
        );

        $.validator.addMethod(
            "imageDimension",
            function (value, element) {
                if (element.files.length === 0) return true;

                let file = element.files[0];
                let img = new Image();
                let valid = false;

                let reader = new FileReader();
                reader.onload = function (e) {
                    img.src = e.target.result;
                };

                img.onload = function () {
                    valid = img.width >= 180 || img.height >= 180;
                    $(element).data("valid-dimension", valid);
                    $(element).valid();
                };

                reader.readAsDataURL(file);

                return $(element).data("valid-dimension") !== false;
            },
            _l("admin.common.image_pixel")
        );
    }

    function handleSubmit(form) {
        const formData = new FormData(form);
        const brandImage = $("#brand_image")[0].files[0];
        const brandIcon = $("#brand_icon")[0].files[0];

        if (brandImage) formData.append("brand_image", brandImage);
        if (brandIcon) formData.append("brand_icon", brandIcon);
        if ($("#id").val()) {
            formData.append("status", $("#status").is(":checked") ? 1 : 0);
        }

        $.ajax({
            type: "POST",
            url: "/admin/brand/save",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: () => {
                $(".submitbtn").attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status"></span> ${_l(
                        "admin.common.saving"
                    )}..
                `);
            },
            success: function (resp) {
                $(".submitbtn")
                    .prop("disabled", false)
                    .html(
                        $("#id").val()
                            ? _l("admin.common.save_changes")
                            : _l("admin.common.create_new")
                    );
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $("#brand_modal").modal("hide");
                    initTable();
                }
            },
            error: function (err) {
                $(".submitbtn")
                    .prop("disabled", false)
                    .html(
                        $("#id").val()
                            ? _l("admin.common.save_changes")
                            : _l("admin.common.create_new")
                    );
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (err.responseJSON?.code === 422) {
                    $.each(err.responseJSON.errors, (key, val) => {
                        $(`#${key}`).addClass("is-invalid");
                        $(`#${key}_error`).text(val[0]);
                    });
                } else {
                    showToast(
                        "error",
                        err.responseJSON?.message || "Unknown error occurred."
                    );
                }
            },
        });
    }

    function initEvents() {
        $("#brand_image, #brand_icon").on("change", function () {
            const input = this;
            const previewId =
                input.id === "brand_image" ? "#imagePreview" : "#iconPreview";
            const iconClass =
                input.id === "brand_image" ? ".upload_icon" : ".upload_icon_2";

            $(input).valid();
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) =>
                    $(previewId)
                        .attr("src", e.target.result)
                        .removeClass("d-none");
                reader.readAsDataURL(input.files[0]);
                $(iconClass).addClass("d-none");
            } else {
                $(previewId).addClass("d-none");
                $(iconClass).removeClass("d-none");
            }
        });

        $("#search").on("keyup", () =>
            $("#brandTable").DataTable().ajax.reload()
        );

        $(document).on("click", "#status_filter .dropdown-item", function () {
            const sortBy = $(this).data("status");
            $("#sort_by_status").val(sortBy);
            $("#current_sort_status").text(
                sortBy == 1
                    ? _l("admin.common.active")
                    : _l("admin.common.inactive")
            );
            $("#status_filter .dropdown-item").removeClass("active");
            $(this).addClass("active");
            $("#brandTable").DataTable().ajax.reload();
        });

        $("#add_brand").on("click", () => {
            $(".modal-title").text(_l("admin.rentals.create_brand"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#vehicle_category_id").val("").trigger("change");
            $("#brandForm")[0].reset();
            $("#id").val("");
            $(".form-control").removeClass("is-invalid is-valid");
            $(".error-text").text("");
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .addClass("justify-content-end")
                .removeClass("justify-content-between");
            $("#imagePreview, #iconPreview").addClass("d-none");
            $(".upload_icon, .upload_icon_2").removeClass("d-none");
        });

        $("#brandDeleteForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/brand/delete",
                type: "POST",
                data: { id: $("#delete_id").val() },
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $("#delete-modal").modal("hide");
                        initTable();
                    }
                },
                error: function (res) {
                    const msg =
                        res.responseJSON?.message ||
                        _l("admin.common.default_delete_error");
                    showToast(
                        res.responseJSON?.code === 500 ? "success" : "error",
                        msg
                    );
                },
            });
        });

        $(document).on("click", ".delete-brand", function () {
            $("#delete_id").val($(this).data("id"));
        });

        $(document).on("click", ".edit-brand", function () {
            const id = $(this).data("id");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#brandForm")[0].reset();
            $.get(`/admin/brand/edit/${id}`, function (response) {
                if (response.code === 200) {
                    const data = response.data;
                    $("#brandForm")[0].reset();
                    $("#brand_name").val(data.brand_name);
                    $("#vehicle_category_id")
                        .val(data.category_id)
                        .trigger("change");
                    $("#status").prop("checked", data.status == 1);
                    $("#id").val(data.id);
                    $("#language_id").val(data.language_id);
                    $("#brand_modal .modal-title").text(
                        _l("admin.rentals.edit_brand")
                    );
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");

                    if (data.brand_image) {
                        $("#imagePreview")
                            .attr("src", data.brand_image)
                            .removeClass("d-none");
                        $(".upload_icon").addClass("d-none");
                    }
                    if (data.brand_icon) {
                        $("#iconPreview")
                            .attr("src", data.brand_icon)
                            .removeClass("d-none");
                        $(".upload_icon_2").addClass("d-none");
                    }
                    $("#brand_modal").modal("show");
                }
            });
        });
    }

    function initTable() {
        $("#brandTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/brand/list",
                type: "POST",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                data: function (d) {
                    d.search = $("#search").val();
                    d.sort_by_status = $("#sort_by_status").val();
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete: function () {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass(
                        "d-none"
                    );
                    if ($("#brandTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                {
                    data: "brand_name",
                    render: function (data, type, row) {
                        return `<div class="d-flex align-items-center file-name-icon">
                                    <div class="avatar avatar-lg border">
                                        <img src="${
                                            row.brand_image
                                        }" class="img-fluid" alt="${_l(
                            "admin.common.image"
                        )}">
                                    </div>
                                    <div class="ms-2">
                                        <h6 class="fw-medium text-black">${
                                            row.brand_name
                                        }</h6>
                                    </div>
                                </div>`;
                    },
                },
                {
                    data: "status",
                    render: function (data, type, row) {
                        return `<span class="badge ${
                            row.status == 1
                                ? "badge-success-transparent"
                                : "badge-danger-transparent"
                        } d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${
                                        row.status == 1
                                            ? _l("admin.common.active")
                                            : _l("admin.common.inactive")
                                    }
                                </span>`;
                    },
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `<div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        ${
                                            hasPermission(
                                                permissions,
                                                "vehicle_attributes",
                                                "edit"
                                            )
                                                ? `<li>
                                                <button 
                                                    type="button" 
                                                    class="dropdown-item rounded-1 edit-brand" 
                                                    data-id="${row.id}">
                                                    <i class="ti ti-edit me-1"></i>${_l(
                                                        "admin.common.edit"
                                                    )}
                                                </button>
                                            </li>`
                                                : ""
                                        }
                                        ${
                                            hasPermission(
                                                permissions,
                                                "vehicle_attributes",
                                                "delete"
                                            )
                                                ? `<li>
                                                <button 
                                                    type="button" 
                                                    class="dropdown-item rounded-1 delete-brand"
                                                    data-id="${row.id}" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#delete-modal">
                                                    <i class="ti ti-trash me-1"></i>${_l(
                                                        "admin.common.delete"
                                                    )}
                                                </button>
                                            </li>`
                                                : ""
                                        }
                                    </ul>
                                </div>`;
                    },
                    visible:
                        hasPermission(
                            permissions,
                            "vehicle_attributes",
                            "edit"
                        ) ||
                        hasPermission(
                            permissions,
                            "vehicle_attributes",
                            "delete"
                        ),
                },
            ],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
            language: {
                emptyTable: _l("admin.common.empty_table"),
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
        });
    }
})();
