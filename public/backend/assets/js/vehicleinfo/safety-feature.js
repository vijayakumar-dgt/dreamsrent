(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, rentals");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initFormValidation() {
        $("#safetyFeatureForm").validate({
            rules: {
                feature: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
            },
            messages: {
                feature: {
                    required: _l("admin.rentals.feature_required"),
                    minlength: _l("admin.rentals.feature_minlength"),
                    maxlength: _l("admin.rentals.feature_maxlength"),
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
                if ($("#id").val() != "") {
                    formData.set("id", $("#id").val());
                    formData.set(
                        "status",
                        $("#status").is(":checked") ? 1 : 0
                    );
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/safety-feature/save",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                    beforeSend: handleSafetyBeforeSend,
                    success: handleSafetySuccess,
                    error: handleSafetyError,
                });
            },
        });
    }

    function handleSafetyBeforeSend() {
        $(".submitbtn")
            .attr("disabled", true)
            .html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                ${_l("admin.common.saving")}..
            `);
    }

    function handleSafetySuccess(resp) {
        resetSafetyValidation();
        resetSafetySubmitButton();

        if (resp.code === 200) {
            showToast("success", resp.message);
            $("#safety_feature_modal").modal("hide");
            $("#safetyFeatureTable").DataTable().ajax.reload();
        }
    }

    function handleSafetyError(error) {
        resetSafetyValidation();
        resetSafetySubmitButton();

        const response = error.responseJSON;
        if (!response) return showToast("error", "Unexpected error occurred.");

        if (response.code === 422) {
            displaySafetyValidationErrors(response.errors);
        } else {
            showToast("error", response.message);
        }
    }

    function resetSafetyValidation() {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    }

    function resetSafetySubmitButton() {
        const buttonText = $("#id").val()
            ? _l("admin.common.save_changes")
            : _l("admin.common.create_new");

        $(".submitbtn").removeAttr("disabled").html(buttonText);
    }

    function displaySafetyValidationErrors(errors) {
        Object.entries(errors).forEach(([key, val]) => {
            $("#" + key).addClass("is-invalid");
            $("#" + key + "_error").text(val[0]);
        });
    }

    function initTable() {
        $("#safetyFeatureTable").DataTable({
            processing: true,
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/safety-feature/list",
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
                    if (
                        $("#safetyFeatureTable").DataTable().rows().count() ===
                        0
                    ) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                {
                    data: "feature",
                    render: function (data, type, row) {
                        return `<h6 class="fw-medium text-black">${data}</h6>`;
                    },
                },
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
                                            ? `<li>
                                        <button
                                            type="button"
                                            class="dropdown-item rounded-1 edit-safety-feature-btn"
                                            data-id="${data}"
                                        >
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
                                            class="dropdown-item rounded-1 delete-safety-feature-btn"
                                            data-id="${data}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#delete-modal"
                                        >
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

                let tableWrapper = $(this).closest(".dataTables_wrapper");
                let info = tableWrapper.find(".dataTables_info");
                let pagination = tableWrapper.find(".dataTables_paginate");

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
        $("#search").on("keyup", function () {
            $("#safetyFeatureTable").DataTable().ajax.reload();
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
            $("#safetyFeatureTable").DataTable().ajax.reload();
        });

        $(document).on("click", ".dataTables_paginate a", function () {
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        });

        $("#add_safety_feature").on("click", function () {
            $(".modal-title").text(_l("admin.rentals.create_safety_feature"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#safetyFeatureForm")[0].reset();
            $("#id").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
        });

        $(document).on("click", ".edit-safety-feature-btn", function () {
            const id = $(this).data("id");
            editSafetyFeature(id);
        });
        $(document).on("click", ".delete-safety-feature-btn", function () {
            const id = $(this).data("id");
            $("#delete_id").val(id);
        });

        $("#deleteSafetyFeatureForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/safety-feature/delete",
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
                        $("#safetyFeatureTable").DataTable().ajax.reload();
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
    }

    function editSafetyFeature(id) {
        $.ajax({
            type: "GET",
            url: "/admin/safety-feature/edit/" + id,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                $("#safetyFeatureForm")[0].reset();

                if (response.code === 200) {
                    let data = response.data;
                    $("#feature").val(data.feature);
                    $("#status").prop("checked", data.status == 1);
                    $("#id").val(data.id);
                    $("#language_id").val(data.language_id);

                    $("#safety_feature_modal .modal-title").text(
                        _l("admin.rentals.edit_safety_feature")
                    );
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                    $("#safety_feature_modal").modal("show");
                }
            },
        });
    }
})();
