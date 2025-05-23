(async () => {
    "use strict";

    await loadTranslationFile("admin", "common, general_settings");
    const permissions = await loadUserPermissions();

    // -------------------- Initialization --------------------

    $(document).ready(() => {
        initializeTaxRateForm();
        initializeTaxGroupForm();
        bindTaxRateHandlers();
        bindTaxGroupHandlers();

        // Initial data load
        loadTaxRates();
        loadTaxGroups();
        getTaxRates();

        // Restrict tax_rate input
        $("#tax_rate").on("input", function () {
            $(this).val(
                $(this)
                    .val()
                    .replace(/[^0-9.]/g, "")
                    .replace(/(\..*)\./g, "$1")
            );
        });

        // Open add modal reset
        $("#add_tax_rate").on("click", () => {
            resetTaxRateForm();
        });

        $("#add_tax_group").on("click", () => {
            resetTaxGroupForm();
        });
    });

    // -------------------- Form Validations --------------------

    function initializeTaxRateForm() {
        $("#tax_rate_form").validate({
            rules: {
                tax_name: { required: true, minlength: 3, maxlength: 30 },
                tax_rate: { required: true },
            },
            messages: {
                tax_name: {
                    required: _l("admin.general_settings.tax_name_required"),
                    minlength: _l("admin.general_settings.tax_name_minlength"),
                    maxlength: _l("admin.general_settings.tax_name_maxlength"),
                },
                tax_rate: {
                    required: _l("admin.general_settings.tax_rate_required"),
                },
            },
            errorPlacement: placeError,
            highlight: highlightError,
            unhighlight: clearError,
            onkeyup: validateOnEvent,
            onchange: validateOnEvent,
            submitHandler: submitTaxRateForm,
        });
    }

    function initializeTaxGroupForm() {
        $("#tax_group_form").validate({
            rules: {
                tax_group_name: { required: true, minlength: 3, maxlength: 30 },
                "sub_tax[]": { required: true },
            },
            messages: {
                tax_group_name: {
                    required: _l(
                        "admin.general_settings.tax_group_name_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.tax_group_name_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.tax_group_name_maxlength"
                    ),
                },
                "sub_tax[]": {
                    required: _l("admin.general_settings.sub_taxes_required"),
                },
            },
            errorPlacement: placeError,
            highlight: highlightError,
            unhighlight: clearError,
            onkeyup: validateOnEvent,
            onchange: validateOnEvent,
            submitHandler: submitTaxGroupForm,
        });
    }

    function placeError(error, element) {
        $("#" + element.attr("id") + "_error").text(error.text());
    }

    function highlightError(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
        if ($(element).hasClass("select2-hidden-accessible")) {
            $(element).next(".select2-container").addClass("is-invalid");
        }
    }

    function clearError(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        $("#" + element.id + "_error").text("");
        if ($(element).hasClass("select2-hidden-accessible")) {
            $(element).next(".select2-container").removeClass("is-invalid");
        }
    }

    function validateOnEvent(element) {
        $(element).valid();
    }

    // -------------------- Submit Handlers --------------------

    function submitTaxRateForm(form) {
        const formData = new FormData(form);
        if ($("#id").val()) {
            formData.set("status", $("#status").is(":checked") ? 1 : 0);
        }

        $.ajax({
            type: "POST",
            url: "/admin/settings/tax-rate/store",
            data: formData,
            processData: false,
            contentType: false,
            headers: setHeaders(),
            beforeSend: () => showButtonLoader(".submitBtn"),
            success: (resp) => {
                resetValidation(".form-control, .form-check-input");
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $("#tax_rate_modal").modal("hide");
                    loadTaxRates();
                    getTaxRates();
                }
            },
            error: handleAjaxError(".form-control, .form-check-input"),
            complete: () => hideButtonLoader(".submitBtn", $("#id").val()),
        });
    }

    function submitTaxGroupForm(form) {
        const formData = new FormData(form);
        if ($("#tax_group_id").val()) {
            formData.set("status", $("#group_status").is(":checked") ? 1 : 0);
            formData.set("id", $("#tax_group_id").val());
        }

        $.ajax({
            type: "POST",
            url: "/admin/settings/tax-group/store",
            data: formData,
            processData: false,
            contentType: false,
            headers: setHeaders(),
            beforeSend: () => showButtonLoader(".submitBtn"),
            success: (resp) => {
                resetValidation(
                    ".form-control, .form-check-input, .select2-container"
                );
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $("#tax_group_modal").modal("hide");
                    loadTaxGroups();
                }
            },
            error: handleAjaxError(
                ".form-control, .form-check-input, .select2-container"
            ),
            complete: () =>
                hideButtonLoader(".submitBtn", $("#tax_group_id").val()),
        });
    }

    // -------------------- Handlers --------------------

    function bindTaxRateHandlers() {
        $(document).on("click", ".edit_tax_rate", function () {
            const id = $(this).data("id");
            $.get(`/admin/settings/tax-rate/edit/${id}`, (res) => {
                if (res.code === 200) {
                    const { id, tax_name, tax_rate, status } = res.data;
                    $("#id").val(id);
                    $("#tax_name").val(tax_name);
                    $("#tax_rate").val(tax_rate);
                    $("#status").prop("checked", status === 1);
                    $("#tax_rate_modal").modal("show");
                    $(".submitBtn").text(_l("admin.common.save_changes"));
                    $("#tax_rate_modal .statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                }
            });
        });

        $(document).on("click", ".delete_tax_rate_btn", function () {
            const id = $(this).data("id");
            $("#delete_tax_rate_id").val(id);
        });

        $("#delete_tax_rate_form").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/settings/tax-rate/delete",
                type: "POST",
                data: {
                    id: $("#delete_tax_rate_id").val(),
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
                        $("#delete_tax_rate").modal("hide");
                        loadTaxRates();
                        getTaxRates();
                    }
                },
                error: function (res) {
                    if (res.responseJSON.code === 500) {
                        showToast("error", res.responseJSON.message);
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

    function bindTaxGroupHandlers() {
        $(document).on("click", ".edit_tax_group", function () {
            const id = $(this).data("id");
            $.get(`/admin/settings/tax-group/edit/${id}`, (res) => {
                if (res.code === 200) {
                    const { id, tax_name, status, tax_rates } = res.data;
                    $("#tax_group_id").val(id);
                    $("#tax_group_name").val(tax_name);
                    $("#group_status").prop("checked", status === 1);
                    $("#sub_tax")
                        .val(tax_rates.map((t) => t.id))
                        .trigger("change");
                    $("#tax_group_modal").modal("show");
                    $(".submitBtn").text(_l("admin.common.save_changes"));
                    $("#tax_group_modal .statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                }
            });
        });

        $(document).on("click", ".delete_tax_group", function () {
            const id = $(this).data("id");
            $("#delete_tax_group_id").val(id);
        });

        $("#delete_tax_group_form").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/settings/tax-group/delete",
                type: "POST",
                data: {
                    id: $("#delete_tax_group_id").val(),
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
                        $("#delete_tax_group").modal("hide");
                        loadTaxGroups();
                    }
                },
                error: function (res) {
                    if (res.responseJSON.code === 500) {
                        showToast("error", res.responseJSON.message);
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

    // -------------------- Utility --------------------

    function setHeaders() {
        return {
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        };
    }

    function showButtonLoader(selector) {
        $(selector).attr("disabled", true).html(`
            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                "admin.common.saving"
            )}..
        `);
    }

    function hideButtonLoader(selector, isEdit) {
        $(selector)
            .attr("disabled", false)
            .text(
                isEdit
                    ? _l("admin.common.save_changes")
                    : _l("admin.common.create_new")
            );
    }

    function resetValidation(selector) {
        $(".error-text").text("");
        $(selector).removeClass("is-invalid is-valid");
    }

    function handleAjaxError(selector) {
        return (error) => {
            resetValidation(selector);
            if (error.responseJSON?.code === 422) {
                $.each(error.responseJSON.errors, function (key, val) {
                    $("#" + key).addClass("is-invalid");
                    $("#" + key + "_error").text(val[0]);
                });
            } else {
                showToast(
                    "error",
                    error.responseJSON?.message || "Unknown Error"
                );
            }
        };
    }

    function handleGenericError(error) {
        showToast(
            "error",
            error.responseJSON?.message ||
                _l("admin.common.default_delete_error")
        );
    }

    function resetTaxRateForm() {
        $("#tax_rate_form")[0].reset();
        $("#id").val("");
        $(".submitBtn").text(_l("admin.common.create_new"));
        $(".form-control, .form-check-input").removeClass(
            "is-invalid is-valid"
        );
        $("#tax_rate_modal .modal-title").text(
            _l("admin.general_settings.create_tax_rate")
        );
        $("#tax_rate_modal .statusDiv")
            .addClass("d-none")
            .parent()
            .removeClass("justify-content-between")
            .addClass("justify-content-end");
    }

    function resetTaxGroupForm() {
        $("#tax_group_form")[0].reset();
        $("#tax_group_id").val("");
        $("#sub_tax").val("").trigger("change");
        $(".submitBtn").text(_l("admin.common.create_new"));
        $(".form-control, .form-check-input, .select2-container").removeClass(
            "is-invalid is-valid"
        );
        $("#tax_group_modal .modal-title").text(
            _l("admin.general_settings.create_tax_group")
        );
        $("#tax_group_modal .statusDiv")
            .addClass("d-none")
            .parent()
            .removeClass("justify-content-between")
            .addClass("justify-content-end");
    }

    // -------------------- Data Loaders --------------------

    function getTaxRates() {
        $.get("/admin/settings/get-tax-rates", function (res) {
            if (res.code === 200) {
                let options = res.data.map(
                    (t) => `<option value="${t.id}">${t.tax_name}</option>`
                );
                $("#sub_tax").html(options.join(""));
            }
        });
    }

    function loadTaxRates() {
        $.ajax({
            url: "/admin/settings/tax-rate/list",
            type: "GET",
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#taxRateTable")) {
                    $("#taxRateTable").DataTable().destroy();
                }

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        tableBody += `
                    <tr>
                        <td>
                            <p class="text-gray-9 fw-semibold fs-14">${
                                value.tax_name
                            }</p>
                        </td>
                        <td>
                            <p class="text-gray-9">${value.tax_rate}%</p>
                        </td>
                        <td>
                            <p class="text-gray-9">${value.created_on}</p>
                        </td>
                        ${
                            hasPermission(
                                permissions,
                                "finance_settings",
                                "edit"
                            ) ||
                            hasPermission(
                                permissions,
                                "finance_settings",
                                "delete"
                            )
                                ? `<td>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                  ${
                                      hasPermission(
                                          permissions,
                                          "finance_settings",
                                          "edit"
                                      )
                                          ? `<li>
                                        <button type="button" class="dropdown-item rounded-1 edit_tax_rate" data-id="${
                                            value.id
                                        }">
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
                                              "finance_settings",
                                              "delete"
                                          )
                                              ? `<li>
                                        <button type="button" class="dropdown-item rounded-1 delete_tax_rate_btn" data-id="${
                                            value.id
                                        }" data-bs-toggle="modal" data-bs-target="#delete_tax_rate">
                                            <i class="ti ti-trash me-1"></i>${_l(
                                                "admin.common.delete"
                                            )}
                                        </button>
                                    </li>`
                                              : ""
                                      }
                                </ul>
                            </div>
                        </td>`
                                : ""
                        }
                    </tr>  `;
                    });
                } else {
                    tableBody += `
                        <tr>
                            <td colspan="6" class="text-center">${_l(
                                "admin.common.empty_table"
                            )}</td>
                        </tr>`;
                    $(".table-footer").empty();
                }
                $("#taxRateTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $("#taxRateTable").DataTable({
                        ordering: true,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
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
                            zeroRecords: _l("admin.common.no_matching_records"),
                            paginate: {
                                first: _l("admin.common.first"),
                                last: _l("admin.common.last"),
                                next: _l("admin.common.next"),
                                previous: _l("admin.common.previous"),
                            },
                        },
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(
                                ".dataTables_wrapper .dataTables_paginate"
                            ).addClass("d-none");

                            var tableWrapper = $(this).closest(
                                ".dataTables_wrapper"
                            );
                            var info = tableWrapper.find(".dataTables_info");
                            var pagination = tableWrapper.find(
                                ".dataTables_paginate"
                            );

                            $(".first-table .table-footer")
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
                            $(".first-table .table-footer")
                                .find(".dataTables_paginate")
                                .removeClass("d-none");
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
            complete: function () {
                $(".first-table")
                    .find(
                        ".table-loader, .input-loader, .label-loader, .button-loader"
                    )
                    .hide();
                $(".first-table")
                    .find(
                        ".real-table, .table-footer, .real-label, .real-input, .real-button"
                    )
                    .removeClass("d-none");
            },
        });
    }

    function loadTaxGroups() {
        $.ajax({
            url: "/admin/settings/tax-group/list",
            type: "GET",
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#taxGroupTable")) {
                    $("#taxGroupTable").DataTable().destroy();
                }

                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        tableBody += `
                    <tr>
                        <td>
                            <p class="text-gray-9 fw-semibold fs-14">${
                                value.tax_name
                            }</p>
                        </td>
                        <td>
                            <p class="text-gray-9">${value.total_tax_rate}%</p>
                        </td>
                        <td>
                            <p class="text-gray-9">${value.created_on}</p>
                        </td>
                            ${
                                hasPermission(
                                    permissions,
                                    "finance_settings",
                                    "edit"
                                ) ||
                                hasPermission(
                                    permissions,
                                    "finance_settings",
                                    "delete"
                                )
                                    ? `<td>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                ${
                                    hasPermission(
                                        permissions,
                                        "finance_settings",
                                        "edit"
                                    )
                                        ? `<li>
                                        <button type="button" class="dropdown-item rounded-1 edit_tax_group" data-id="${
                                            value.id
                                        }">
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
                                            "finance_settings",
                                            "delete"
                                        )
                                            ? `<li>
                                        <button type="button" class="dropdown-item rounded-1 delete_tax_group" data-id="${
                                            value.id
                                        }" data-bs-toggle="modal" data-bs-target="#delete_tax_group">
                                            <i class="ti ti-trash me-1"></i>${_l(
                                                "admin.common.delete"
                                            )}
                                        </button>
                                    </li>`
                                            : ""
                                    }
                                </ul>
                            </div>
                        </td>`
                                    : ""
                            }
                    </tr>  `;
                    });
                } else {
                    tableBody += `
                        <tr>
                            <td colspan="6" class="text-center">${_l(
                                "admin.common.empty_table"
                            )}</td>
                        </tr>`;
                    $(".second-table .table-footer").empty();
                }
                $("#taxGroupTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $("#taxGroupTable").DataTable({
                        ordering: true,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
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
                            zeroRecords: _l("admin.common.no_matching_records"),
                            paginate: {
                                first: _l("admin.common.first"),
                                last: _l("admin.common.last"),
                                next: _l("admin.common.next"),
                                previous: _l("admin.common.previous"),
                            },
                        },
                        drawCallback: function () {
                            $(".dataTables_info").addClass("d-none");
                            $(
                                ".dataTables_wrapper .dataTables_paginate"
                            ).addClass("d-none");

                            var tableWrapper = $(this).closest(
                                ".dataTables_wrapper"
                            );
                            var info = tableWrapper.find(".dataTables_info");
                            var pagination = tableWrapper.find(
                                ".dataTables_paginate"
                            );

                            $(".second-table .table-footer")
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
                            $(".second-table .table-footer")
                                .find(".dataTables_paginate")
                                .removeClass("d-none");
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
            complete: function () {
                $(".second-table")
                    .find(
                        ".table-loader, .input-loader, .label-loader, .button-loader"
                    )
                    .hide();
                $(".second-table")
                    .find(
                        ".real-table, .table-footer, .real-label, .real-input, .real-button"
                    )
                    .removeClass("d-none");
            },
        });
    }
})();
