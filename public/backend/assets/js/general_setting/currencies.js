(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");
    const permissions = await loadUserPermissions();
    let table;

    $(document).ready(function () {
        initTable();

        $("#currencyForm").validate({
            rules: {
                currency_name: {
                    required: true,
                    minlength: 3,
                    maxlength: 50,
                },
                code: {
                    required: true,
                },
                symbol: {
                    required: true,
                    maxlength: 20,
                },
            },
            messages: {
                currency_name: {
                    required: _l("admin.general_settings.enter_currency_name"),
                },
                code: {
                    required: _l("admin.general_settings.enter_currency_code"),
                },
                symbol: {
                    required: _l(
                        "admin.general_settings.enter_currency_symbol"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                let errorId = element.id + "_error";
                $("#" + errorId).text("");
            },
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let _formData = new FormData(form);
                $("#currencyForm .submitbtn").text(
                    _l("admin.general_settings.please_wait")
                );
                $("#currencyForm .submitbtn").attr("disabled", true);
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/save_currency",
                    data: _formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_currency").modal("hide");
                        } else {
                            showToast("error", resp.message);
                        }
                        $("#currencyForm .submitbtn").text(
                            $("#id").val()
                                ? _l("admin.common.save_changes")
                                : _l("admin.common.create_new")
                        );
                        $("#currencyForm .submitbtn").prop("disabled", false);
                        table.ajax.reload();
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
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
                        $("#currencyForm .submitbtn").text(
                            $("#id").val()
                                ? _l("admin.common.save_changes")
                                : _l("admin.common.create_new")
                        );
                        $("#currencyForm .submitbtn").prop("disabled", false);
                    },
                });
            },
        });
    });

    function initTable() {
        table = $("#currencyTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/settings/get_currencies",
                type: "POST",
                data: function (d) {
                    d._token = $('meta[name="csrf-token"]').attr("content");
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

                    if ($("#currencyTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            order: [["1", "desc"]],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
            aoColumns: [
                {
                    data: "currency_name",
                    render: function (data, type, row) {
                        return `<p class="text-gray-9 fw-semibold fs-14">${row.currency_name}</p>`;
                    },
                },
                {
                    data: "code",
                    render: function (data, type, row) {
                        return `<p class="text-gray-9">${row.code}</p>`;
                    },
                },
                {
                    data: "symbol",
                    render: function (data, type, row) {
                        return `<p class="text-gray-9">${row.symbol}</p>`;
                    },
                },
                {
                    data: "status",
                    render: function (data, type, row) {
                        return `<span class="badge ${
                            row.status == 1
                                ? `badge-success-transparent`
                                : `badge-danger-transparent`
                        }  d-inline-flex align-items-center badge-sm">
                                         <i class="ti ti-point-filled me-1"></i>${
                                             row.status == 1
                                                 ? _l("admin.common.active")
                                                 : _l("admin.common.inactive")
                                         }
                                 </span>`;
                    },
                },
                {
                    data: null,
                    render: function (data, type, row) {
                        return `<div class="dropdown">
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
                                                <a class="dropdown-item rounded-1" href="javascript:void(${
                                                    row.id
                                                });" id="editcurrency" data-id="${
                                                      row.id
                                                  }"><i class="ti ti-edit me-1"></i>${_l(
                                                      "admin.common.edit"
                                                  )}</a>
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
                                                <a class="dropdown-item rounded-1" href="javascript:void(${
                                                    row.id
                                                });" id="deleteCurrency" data-id="${
                                                      row.id
                                                  }" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l(
                                                      "admin.common.delete"
                                                  )}</a>
                                            </li>`
                                                : ""
                                        }
                                        </ul>
                                    </div>`;
                    },
                    visible:
                        hasPermission(
                            permissions,
                            "finance_settings",
                            "edit"
                        ) ||
                        hasPermission(
                            permissions,
                            "finance_settings",
                            "delete"
                        ),
                },
            ],
            drawCallback: function () {
                customizeTableFooter($(this));
            },
            language: getDataTableLanguage(),
        });
    }

    $(document).on("click", "#add_new_currency", function () {
        $("#currencyForm")[0].reset();
        $("#currencyForm #id").val("");
        $("#currencyForm .submitbtn").text(_l("admin.common.create_new"));
        $("#currencyForm .submitbtn").prop("disabled", false);
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        $("#status_div").addClass("d-none");
        if ($("#modalfootdiv").hasClass("justify-content-between")) {
            $("#modalfootdiv").removeClass("justify-content-between");
            $("#modalfootdiv").addClass("justify-content-end");
        }
    });

    $(document).on("click", "#editcurrency", function () {
        let id = $(this).data("id");
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        $.ajax({
            type: "GET",
            url: "/admin/settings/edit_currency/" + id,
            success: function (response) {
                if (response && response.code === 200) {
                    $("#currencyForm #currency_name").val(
                        response.data.currency_name
                    );
                    $("#currencyForm #code").val(response.data.code);
                    $("#currencyForm #symbol").val(response.data.symbol);
                    $("#currencyForm #id").val(response.data.id);
                    $("#currencyForm .submitbtn").text(
                        _l("admin.common.save_changes")
                    );
                    $("#currencyForm .submitbtn").prop("disabled", false);
                    $("#status_div").removeClass("d-none");
                    $("#add_currency").modal("show");
                    if ($("#modalfootdiv").hasClass("justify-content-end")) {
                        $("#modalfootdiv").addClass("justify-content-between");
                        $("#modalfootdiv").removeClass("justify-content-end");
                    }
                    if (response.data.status) {
                        $("#currencyForm #status").prop("checked", true);
                    } else {
                        $("#currencyForm #status").prop("checked", false);
                    }
                }
            },
        });
    });

    $(document).on("click", "#deleteCurrency", function () {
        let delete_id = $(this).data("id");
        $("#deleteCurrencyForm #id").val(delete_id);
    });

    $("#deleteCurrencyForm").on("submit", function (e) {
        e.preventDefault();
        $("#deleteCurrencyForm .submitbtn").prop("disabled", true);
        $("#deleteCurrencyForm .submitbtn").text(
            _l("admin.general_settings.please_wait")
        );
        $.ajax({
            type: "POST",
            url: "/admin/settings/delete-currency",
            data: $("#deleteCurrencyForm").serialize(),
            success: function (response) {
                $("#deleteCurrencyForm .submitbtn").prop("disabled", false);
                $("#deleteCurrencyForm .submitbtn").text(
                    _l("admin.common.yes_delete")
                );
                $("#delete-modal").modal("hide");
                table.ajax.reload();
            },
            error: function (error) {
                showToast("error", error.responseJSON.message);
                $("#deleteCurrencyForm .submitbtn").prop("disabled", false);
                $("#deleteCurrencyForm .submitbtn").text(
                    _l("admin.common.yes_delete")
                );
                $("#delete-modal").modal("hide");
            },
        });
    });
})();
