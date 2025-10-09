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
        $("#tagForm").validate({
            rules: {
                tag: {
                    required: true,
                    minlength: 3,
                    maxlength: 30,
                },
            },
            messages: {
                tag: {
                    required: _l("admin.rentals.tag_required"),
                    minlength: _l("admin.rentals.tag_minlength"),
                    maxlength: _l("admin.rentals.tag_maxlength"),
                },
            },
            errorPlacement: function (error, element) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
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
                let tagFormData = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: "/admin/store_tag",
                    data: tagFormData,
                    processData: false,
                    contentType: false,
                    beforeSend: handleTagBeforeSend,
                    success: handleTagSuccess,
                    error: handleTagError,
                    complete: handleTagComplete,
                });
            },
        });
    }

    function handleTagBeforeSend() {
        $(".submitbtn")
            .attr("disabled", true)
            .html(`
                <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>
                ${_l("admin.common.saving")}...
            `);
    }

    function handleTagSuccess(resp) {
        if (resp.code === 200) {
            showToast("success", resp.message);
            $("#add_tag").modal("hide");
            initTable();
        }
    }

    function handleTagError(error) {
        resetTagValidation();

        const response = error.responseJSON;
        if (!response) return showToast("error", "Unexpected error occurred.");

        if (response.code === 422) {
            displayTagValidationErrors(response.errors);
        } else {
            showToast("error", response.message);
        }
    }

    function handleTagComplete() {
        const buttonText = $("#id").val()
            ? _l("admin.common.save_changes")
            : _l("admin.common.create_new");

        $(".submitbtn").attr("disabled", false).html(buttonText);
    }

    function resetTagValidation() {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    }

    function displayTagValidationErrors(errors) {
        Object.entries(errors).forEach(([key, val]) => {
            $("#" + key).addClass("is-invalid");
            $("#" + key + "_error").text(val[0]);
        });
    }

    function initEvents() {
        $(document).on("click", "#add_new_tag", function () {
            $("#add_tag .modal-title").text(_l("admin.rentals.create_tag"));
            $("#add_tag .submitbtn").text(_l("admin.common.create_new"));
            $("#status_div")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
            $("#tagForm")[0].reset();
            $("#tagForm #id").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
        });

        $(document).on("click", ".statusfilter", function () {
            let statusFilter = $(this).data("status");
            if (statusFilter == 1) {
                $("#status_text").text(_l("admin.common.active"));
            } else if (statusFilter == 0) {
                $("#status_text").text(_l("admin.common.inactive"));
            } else {
                $("#status_text").text(_l("admin.common.status"));
            }
            initTable(statusFilter);
        });

        $(document).on("click", "#editTag", function () {
            let id = $(this).data("id");
            $.ajax({
                type: "GET",
                url: "/admin/get_tag/" + id,
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#add_tag #tag").val(data.tag);
                        $("#add_tag #id").val(data.id);
                        $("#add_tag #status").prop(
                            "checked",
                            data.status === 1
                        );
                        $("#add_tag .modal-title").text(
                            _l("admin.rentals.edit_tag")
                        );
                        $("#add_tag .submitbtn").text(
                            _l("admin.common.save_changes")
                        );
                        $("#status_div")
                            .removeClass("d-none")
                            .parent()
                            .removeClass("justify-content-end")
                            .addClass("justify-content-between");
                        $("#add_tag").modal("show");
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                    } else {
                        showToast("error", response.message);
                    }
                },
                error: function (error) {
                    showToast("error", error.responseJSON.message);
                },
            });
        });

        $(document).on("click", "#deleteTag", function () {
            let id = $(this).data("id");
            $("#delete_id").val(id);
        });

        $("#keyword").on("keyup", function () {
            setTimeout(function () {
                initTable();
            }, 300);
        });

        $("#deleteTagForm").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/admin/delete_tag",
                data: $(this).serialize(),
                success: function (response) {
                    if (response.code === 200) {
                        showToast("success", response.message);
                        $("#delete-modal").modal("hide");
                        initTable();
                    } else {
                        showToast("error", response.message);
                        $("#delete-modal").modal("hide");
                    }
                },
                error: function (error) {
                    showToast("error", error.responseJSON.message);
                    $("#delete-modal").modal("hide");
                },
            });
        });
    }

    function initTable(statusFilter = null) {
        let keyword = $("#keyword").val();
        $.ajax({
            url: "/admin/get_tags",
            type: "GET",
            data: {
                status: statusFilter,
                keyword: keyword,
            },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            success: function (response) {
                let tableBody = "";
                if ($.fn.DataTable.isDataTable("#tagTable")) {
                    $("#tagTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        tableBody += `<tr>
                            <td>
                                <h6 class="fw-medium text-black">${
                                    value.tag
                                }</h6>
                            </td>
                            <td>
                                <span class="badge ${
                                    value.status == 1
                                        ? `badge-success-transparent`
                                        : `badge-danger-transparent`
                                } d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>
                                    ${
                                        value.status == 1
                                            ? `${_l("admin.common.active")}`
                                            : `${_l("admin.common.inactive")}`
                                    }
                                </span>
                            </td>
                            ${
                                hasPermission(
                                    permissions,
                                    "vehicle_attributes",
                                    "edit"
                                ) ||
                                hasPermission(
                                    permissions,
                                    "vehicle_attributes",
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
                                                "vehicle_attributes",
                                                "edit"
                                            )
                                                ? `<li>
                                            <button
                                                type="button"
                                                class="dropdown-item rounded-1 edit-tag"
                                                id="editTag"
                                                data-id="${value.id}">
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
                                                class="dropdown-item rounded-1 delete-tag"
                                                id="deleteTag"
                                                data-id="${value.id}"
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
                                </div>
                            </td>`
                                    : ""
                            }
                        </tr>`;
                    });
                } else {
                    tableBody += `<tr>
                                    <td colspan="4" class="text-center">${_l(
                                        "admin.common.empty_table"
                                    )}</td>
                                </tr>`;
                    $(".table-footer").empty();
                }

                $("#tagTable tbody").html(tableBody);

                if (response.data.length > 0) {
                    $("#tagTable").DataTable({
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
                            zeroRecords: _l("admin.common.no_matching_records"),
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
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass(
                    "d-none"
                );

                if ($("#tagTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            error: function (error) {
                showToast("error", error.responseJSON.message);
            },
        });
    }
})();
