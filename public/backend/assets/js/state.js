(async () => {
    "use strict";

    await loadTranslationFile("admin", "cms,common");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        $("#country_id").select2({
            dropdownParent: $("#state_modal"),
            placeholder: _l("admin.common.select"),
        });

        initTable();
        $("#stateForm").validate({
            rules: {
                country_id: {
                    required: true,
                },
                name: {
                    required: true,
                },
            },
            messages: {
                country_id: {
                    required: _l("admin.cms.country_required"),
                },
                name: {
                    required: _l("admin.cms.state_required"),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    let errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    let errorId = element.attr("id") + "_error";
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
                let formData = new FormData(form);
                formData.append("status", $("#status").is(":checked") ? 1 : 0);

                $.ajax({
                    type: "POST",
                    url: "/admin/state/store",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#state_modal").modal("hide");
                            initTable();
                        }
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
                    },
                });
            },
        });

        $(document).on("click", "#edit-state", function () {
            let id = $(this).data("id");
            editState(id);
        });

        $(document).on("click", "#delete-state", function () {
            let id = $(this).data("id");
            delateState(id);
        });

        $(document).on("keyup", "#search", function () {
            setTimeout(function () {
                initTable();
            }, 500);
        });
        function initTable(status = '') {
            $.ajax({
                url: "/admin/state/datatable",
                type: "GET",
                data: {
                    search: $("#search").val(),
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
                    if ($("#stateTable").length === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
                success: function (response) {
                    let tableBody = "";
                    if ($.fn.DataTable.isDataTable("#stateTable")) {
                        $("#stateTable").DataTable().destroy();
                    }

                    if (response.code === 200 && response.data.length > 0) {
                        let data = response.data;

                        $.each(data, function (index, value) {
                            let countryCode =
                                value.country && value.country.code
                                    ? value.country.code.toLowerCase()
                                    : "";
                            let flagImage = `<img src="/backend/assets/img/flags/${countryCode}.svg" alt="Flag" width="20"
                            onerror="this.style.display='none'; this.parentNode.innerHTML='${
                                value.country && value.country.name ? value.country.name : ""
                            }';">`;
                            tableBody += `<tr>
                                    <td>${value.name}</td>
                                    <td>${flagImage} ${
                                value.country ? value.country.name : "N/A"
                            }</td>
                                    <td>
                                        <span class="badge ${
                                            value.status == 1
                                                ? "badge-success-transparent"
                                                : "badge-danger-transparent"
                                        } d-inline-flex align-items-center badge-sm">
                                            <i class="ti ti-point-filled me-1"></i>${
                                                value.status == 1
                                                    ? `${_l(
                                                          "admin.common.active"
                                                      )}`
                                                    : `${_l(
                                                          "admin.common.inactive"
                                                      )}`
                                            }
                                        </span>
                                    </td>
                                    ${
                                        hasPermission(
                                            permissions,
                                            "cms_locations",
                                            "edit"
                                        ) ||
                                        hasPermission(
                                            permissions,
                                            "cms_locations",
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
                                                        "cms_locations",
                                                        "edit"
                                                    )
                                                        ? `<li>
                                                    <button type="button" class="dropdown-item rounded-1" data-id="${
                                                        value.id
                                                    }" id="edit-state"><i class="ti ti-edit me-1"></i>${_l(
                                                              "admin.common.edit"
                                                          )}</button>
                                                </li>`
                                                        : ""
                                                }
                                                ${
                                                    hasPermission(
                                                        permissions,
                                                        "cms_locations",
                                                        "delete"
                                                    )
                                                        ? `<li>
                                                    <button type="button" class="dropdown-item rounded-1" data-id="${
                                                        value.id
                                                    }" id="delete-state" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l(
                                                              "admin.common.delete"
                                                          )}</button>
                                                </li>`
                                                        : ""
                                                }
                                            </ul>
                                        </div>
                                    </td>`
                                            : ""
                                    }
                                </tr>`;
                        });
                    } else {
                        tableBody += `
                                <tr>
                                    <td colspan="4" class="text-center">${_l(
                                        "admin.common.empty_table"
                                    )}</td>
                                </tr>`;
                        $(".table-footer").empty();
                    }

                    $("#stateTable tbody").html(tableBody);
                    if (response.data.length > 0) {
                        $("#stateTable").DataTable({
                            ordering: true,
                            searching: false,
                            pageLength: 10,
                            lengthChange: false,
                            drawCallback: function () {
                                customizeTableFooter($(this));
                            },
                            language: getDataTableLanguage(),
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

        $(document).on("click", ".selectStatus", function () {
            let selectedStatus = $(this).data("status");
            $(".selectStatus").removeClass("active");
            $(this).addClass("active");
            initTable(selectedStatus);
        });

        $(document).on("click", ".dataTables_paginate a", function () {
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        });

        $("#delateState").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/state/delete",
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

        $("#add_state").on("click", function () {
            $(".modal-title").text(_l("admin.cms.create_state"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#stateForm")[0].reset();
            $("#id").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
            $("#country_id").val("").trigger("change");
        });
    });

    function editState(id) {
        $.ajax({
            type: "GET",
            url: "/admin/state/edit/" + id,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if (response.code === 200) {
                    let data = response.data;
                    $("#name").val(data.name);
                    $("#country_id").val(data.country_id).trigger("change");
                    $("#status").prop("checked", data.status === 1);
                    $("#id").val(data.id);

                    $("#state_modal .modal-title").text(
                        _l("admin.cms.edit_state")
                    );
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                    $("#state_modal").modal("show");
                }
            },
        });
    }

    function delateState(id) {
        $("#delete_id").val(id);
    }
})();
