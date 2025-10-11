(async () => {
    "use strict";
    await loadTranslationFile("admin", "rentals,common");
    const permissions = await loadUserPermissions();
    let currentStatus = "";

    $(document).ready(function () {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initFormValidation() {
        $("#carColorForm").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
                value: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: _l("admin.rentals.color_name_required"),
                    minlength: _l("admin.rentals.color_name_minlength"),
                    maxlength: _l("admin.rentals.color_name_maxlength"),
                },
                value: {
                    required: _l("admin.rentals.color_code_required"),
                },
            },
            errorPlacement: function (error, element) {
                    const errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
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
                const errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let formData = new FormData(form);
                formData.append("status", $("#status").is(":checked") ? 1 : 0);

                $.ajax({
                    type: "POST",
                    url: "/admin/vehicle-color/store",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: disableSubmitButton,
                    complete: resetSubmitButton,
                    success: handleSuccessResponse,
                    error: handleErrorResponse,
                });
            },
        });
    }

    function disableSubmitButton() {
        $(".submitbtn")
            .attr("disabled", true)
            .html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                ${_l("admin.common.saving")}..
            `);
    }

    function resetSubmitButton() {
        const buttonText = $("#id").val()
            ? _l("admin.common.save_changes")
            : _l("admin.common.create_new");

        $(".submitbtn").attr("disabled", false).html(buttonText);
    }

    function handleSuccessResponse(resp) {
        resetValidation();

        if (resp.code === 200) {
            showToast("success", resp.message);
            $("#car_color_modal").modal("hide");
            initTable();
        }
    }

    function handleErrorResponse(error) {
        resetValidation();

        const response = error.responseJSON;
        if (!response) return showToast("error", "Unexpected error occurred.");

        if (response.code === 422) {
            displayValidationErrors(response.errors);
        } else {
            showToast("error", response.message);
        }
    }

    function resetValidation() {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    }

    function displayValidationErrors(errors) {
        Object.entries(errors).forEach(([key, messages]) => {
            $("#" + key).addClass("is-invalid");
            $("#" + key + "_error").text(messages[0]);
        });
    }

    function initTable(search = "", status = "") {
        $(".table-loader").show();
        $(".input-loader").show();
        $(".real-table, .real-data").addClass("d-none");
        $.ajax({
            url: "/admin/vehicle-color/datatable",
            type: "GET",
            data: {
                search: search,
                status: status,
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
                if ($("#carColorTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#carColorTable")) {
                    $("#carColorTable").DataTable().destroy();
                }

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        let statusBadgeClass = value.status == 1
                            ? "badge-success-transparent"
                            : "badge-danger-transparent";

                        let statusText = value.status == 1
                            ? _l("admin.common.active")
                            : _l("admin.common.inactive");

                        let actionButtons = "";

                        const canEdit = hasPermission(permissions, "vehicle_attributes", "edit");
                        const canDelete = hasPermission(permissions, "vehicle_attributes", "delete");

                        if (canEdit || canDelete) {
                            actionButtons += `<td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        ${canEdit ? `
                                            <li>
                                                <button type="button" class="dropdown-item rounded-1" data-id="${value.id}" id="edit-color">
                                                    <i class="ti ti-edit me-1"></i>${_l("admin.common.edit")}
                                                </button>
                                            </li>` : ""}
                                        ${canDelete ? `
                                            <li>
                                                <button type="button" class="dropdown-item rounded-1 delete-color" data-id="${value.id}" data-bs-toggle="modal" data-bs-target="#delete-modal">
                                                    <i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}
                                                </button>
                                            </li>` : ""}
                                    </ul>
                                </div>
                            </td>`;
                        }

                        tableBody += `<tr>
                            <td>${
                                value.name.length > 24
                                    ? value.name.substring(0, 24) + "..."
                                    : value.name
                            }</td>
                            <td>
                                <div class="d-inline-flex gap-2 align-items-center">
                                    <div class="coloredsquare border" style="background-color: ${value.value};"></div>
                                    <span>${value.value}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge ${statusBadgeClass} d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>${statusText}
                                </span>
                            </td>
                            ${actionButtons}
                        </tr>`;
                    });
                } else {
                    tableBody += `
                        <tr>
                            <td colspan="5" class="text-center">${_l("admin.common.empty_table")}</td>
                        </tr>`;
                    $(".table-footer").empty();
                }

                $("#carColorTable tbody").html(tableBody);
                if (response.data.length > 0) {
                    $("#carColorTable").DataTable({
                        ordering: true,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(
                                ".dataTables_wrapper .dataTables_paginate"
                            ).addClass("d-none");
                            let tableWrapper = $(this).closest(
                                ".dataTables_wrapper"
                            );
                            let info = tableWrapper.find(".dataTables_info");
                            let pagination = tableWrapper.find(
                                ".dataTables_paginate"
                            );
                            $(".table-footer")
                                .empty()
                                .append(
                                    $(
                                        '<div class="d-flex justify-content-between align-items-center w-100"></div>'
                                    )
                                        .append(
                                            $(
                                                '<div class="datatable-info"></div>'
                                            ).append(info.clone(true))
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
                    });
                }
            },
            error: function (error) {
                if (error.responseJSON.code === 500) {
                    showToast("error", error.responseJSON.message);
                } else {
                    showToast(
                        "error",
                        _l("admin.common.default_retrieve_error")
                    );
                }
            },
        });
    }

    function initEvents() {
        $(document).on("click", ".dataTables_paginate a", function () {
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        });

        $(document).on("click", ".delete-color", function () {
            let id = $(this).data("id");
            console.log(id);

            $("#delete_id").val(id);
        });

        $("#delateCarColorForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/vehicle-color/delete",
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

        $("#add_car_color").on("click", function () {
            $(".modal-title").text(_l("admin.rentals.create_vehicle_color"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#carColorForm")[0].reset();
            $("#id").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
        });

        $(document).on("click", "#edit-color", function () {
            let id = $(this).data("id");
            editCarColor(id);
        });

        $("#search").on("input", function () {
            let searchQuery = $(this).val().trim();
            initTable(searchQuery, currentStatus);
        });

        $(".statusfilter").on("click", function () {
            $(".statusfilter").removeClass("active");
            $(this).addClass("active");
            currentStatus = $(this).data("status");
            $("#status_text").text($(this).text());
            let searchQuery = $("#search").val().trim();
            initTable(searchQuery, currentStatus);
        });
    }

    function editCarColor(id) {
        $.ajax({
            type: "GET",
            url: "/admin/vehicle-color/edit/" + id,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if (response.code === 200) {
                    let data = response.data;
                    $("#name").val(data.name);
                    $("#value").val(data.value);
                    $("#status").prop("checked", data.status === 1);
                    $("#id").val(data.id);
                    $("#language_id").val(data.language_id);

                    $("#car_color_modal .modal-title").text(_l("admin.rentals.edit_vehicle_color"));
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                    $("#car_color_modal").modal("show");
                }
            },
        });
    }
})();
