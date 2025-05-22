(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, rentals");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initFormValidation();
        initEvents();
        initSelect2();
    });

    function initSelect2() {
        $("#brand_id").select2({
            dropdownParent: $("#car_model_modal"),
        });
    }

    function initFormValidation() {
        $("#carModelForm").validate({
            rules: {
                model_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
                brand_id: {
                    required: true,
                },
                total_cars: {
                    required: true,
                },
            },
            messages: {
                model_name: {
                    required: _l("admin.rentals.model_name_required"),
                    minlength: _l("admin.rentals.model_name_minlength"),
                    maxlength: _l("admin.rentals.model_name_maxlength"),
                },
                brand_id: {
                    required: _l("admin.rentals.brand_required"),
                },
                total_cars: {
                    required: _l("admin.rentals.total_vehicles_required"),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    var errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                }
            },
            highlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .addClass("is-invalid")
                        .removeClass("is-valid");
                }
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element)
                        .next(".select2-container")
                        .removeClass("is-invalid")
                        .addClass("is-valid");
                }
                $(element).removeClass("is-invalid").addClass("is-valid");
                var errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData();
                formData.append("model_name", $("#model_name").val());
                formData.append("total_cars", $("#total_cars").val());
                formData.append("brand_id", $("#brand_id").val());
                formData.append("language_id", $("#language_id").val());

                if ($("#id").val() != "") {
                    formData.append("id", $("#id").val());
                    formData.append(
                        "status",
                        $("#status").is(":checked") ? 1 : 0
                    );
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/vehicle-model/save",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    beforeSend: function () {
                        $(".submitbtn").attr("disabled", true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                                "admin.common.saving"
                            )}..
                        `);
                    },
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass(
                            "is-invalid is-valid"
                        );
                        $(".submitbtn")
                            .removeAttr("disabled")
                            .html(
                                $("#id").val()
                                    ? _l("admin.common.save_changes")
                                    : _l("admin.common.create_new")
                            );
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#car_model_modal").modal("hide");
                            initTable();
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass(
                            "is-invalid is-valid"
                        );
                        $(".submitbtn")
                            .removeAttr("disabled")
                            .html(
                                $("#id").val()
                                    ? _l("admin.common.save_changes")
                                    : _l("admin.common.create_new")
                            );
                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                    },
                });
            },
        });
    }

    function initTable() {
        $("#carModelTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/vehicle-model/list",
                type: "GET",
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
                    if ($("#carModelTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                {
                    data: "model_name",
                    render: function (data, type, row) {
                        return `<h6 class="fw-medium text-black">${data}</h6>`;
                    },
                },
                { data: "brand_name", name: "brand_name" },
                {
                    data: "status",
                    name: "status",
                    render: function (data, type, row) {
                        let badgeClass =
                            data == 1
                                ? "badge-success-transparent"
                                : "badge-danger-transparent";
                        return `
                            <span class="badge ${badgeClass} d-inline-flex align-items-center badge-sm">
                                <i class="ti ti-point-filled me-1"></i>${
                                    data == 1
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
                        return `
                            <div class="dropdown">
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
                                            ? ` <li>
                                        <button 
                                            type="button" 
                                            class="dropdown-item rounded-1 edit-car-model" 
                                            data-id="${data}">
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
                                            class="dropdown-item rounded-1 delete-car-model" 
                                            data-id="${data}" 
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

    function initEvents() {
        $(document).on("click", ".dataTables_paginate a", function () {
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        });

        $("#search").on("keyup", function () {
            $("#carModelTable").DataTable().ajax.reload();
        });

        $(document).on("click", "#status_filter .dropdown-item", function () {
            let sortBy = $(this).data("status");
            $("#sort_by_status").val(sortBy);
            if (sortBy == 1) {
                $("#current_sort_status").text(_l("admin.common.active"));
            } else {
                $("#current_sort_status").text(_l("admin.common.inactive"));
            }
            $("#status_filter .dropdown-item").removeClass("active");
            $(this).addClass("active");
            $("#carModelTable").DataTable().ajax.reload();
        });

        $("#add_car_model").on("click", function () {
            $(".modal-title").text(_l("admin.rentals.create_vehicle_model"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#carModelForm")[0].reset();
            $("#id").val("");
            $("#brand_id").val("").trigger("change");
            $(".error-text").text("");
            $(".form-control, .select2-container").removeClass(
                "is-invalid is-valid"
            );
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
        });

        $("#total_cars").on("input", function () {
            $(this).val(
                $(this)
                    .val()
                    .replace(/[^0-9]/g, "")
            );
        });

        $("#deleteCarModel").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/vehicle-model/delete",
                type: "POST",
                data: {
                    id: $("#delete_id").val(),
                },
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
                    if (res.responseJSON.code === 500) {
                        showToast("success", res.responseJSON.message);
                    } else {
                        showToast(
                            "error",
                            _l("admin.common.default_delete_error")
                        );
                    }
                },
            });
        });

        $(document).on("click", ".edit-car-model", function () {
            const id = $(this).data("id");
            editCarModel(id);
        });

        $(document).on("click", ".delete-car-model", function () {
            const id = $(this).data("id");
            $("#delete_id").val(id);
        });
    }

    function editCarModel(id) {
        $.ajax({
            type: "GET",
            url: "/admin/vehicle-model/edit/" + id,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass(
                    "is-invalid is-valid"
                );
                if (response.code === 200) {
                    let data = response.data;
                    $("#model_name").val(data.model_name);
                    $("#total_cars").val(data.total_cars);
                    $("#brand_id").val(data.brand_id).trigger("change");
                    $("#status").prop("checked", data.status == 1);
                    $("#id").val(data.id);
                    $("#language_id").val(data.language_id);

                    $("#car_model_modal .modal-title").text(
                        _l("admin.rentals.edit_vehicle_model")
                    );
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                    $("#car_model_modal").modal("show");
                }
            },
        });
    }
})();
