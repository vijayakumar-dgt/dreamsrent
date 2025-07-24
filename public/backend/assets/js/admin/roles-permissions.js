/* global $, loadTranslationFile, loadUserPermissions, FormData, document, showToast, _l, hasPermission */
(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, user_management");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();

        $("#roleForm").validate({
            rules: {
                role: {
                    required: true,
                    minlength: 3,
                    maxlength: 30
                }
            },
            messages: {
                role: {
                    required: _l("admin.user_management.role_required"),
                    minlength: _l("admin.user_management.role_minlength"),
                    maxlength: _l("admin.user_management.role_maxlength")
                }
            },
            errorPlacement: function (error, element) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element).next(".select2-container").addClass("is-invalid").removeClass("is-valid");
                }
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                if ($(element).hasClass("select2-hidden-accessible")) {
                    $(element).next(".select2-container").removeClass("is-invalid").addClass("is-valid");
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
                const formData = new FormData(form);
                if ($("#id").val() !== "") {
                    formData.append("id", $("#id").val());
                    formData.append("status", $("#status").is(":checked") ? 1 : 0);
                }

                $.ajax({
                    type: "POST",
                    url: "/admin/role/save",
                    data: formData,
                    enctype: "multipart/form-data",
                    processData: false,
                    contentType: false,
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                    },
                    beforeSend: function () {
                        $(".submitbtn").attr("disabled", true).html(
                            "<span class=\"spinner-border spinner-border-sm align-middle\" role=\"status\" aria-hidden=\"true\"></span> " + 
                            _l("admin.common.saving") + ".."
                        );
                    },
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                        $(".submitbtn").removeAttr("disabled").html(_l("admin.common.create_new"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#role_modal").modal("hide");
                            $("#roleTable").DataTable().ajax.reload();
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                        $(".submitbtn").removeAttr("disabled").html(_l("admin.common.create_new"));
                        if (error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(val[0]);
                            });
                        } else {
                            showToast("error", error.responseJSON.message);
                        }
                    }
                });
            }
        });
    });

    $("#add_role").on("click", function () {
        $(".modal-title").text(_l("admin.user_management.create_role"));
        $(".submitbtn").text(_l("admin.common.create_new"));
        $("#roleForm")[0].reset();
        $("#id").val("");
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        $("#statusDiv").addClass("d-none").parent().removeClass("justify-content-between").addClass("justify-content-end");
    });

    function initTable() {
        const table = $("#roleTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: true,
            ajax: {
                url: "/admin/role/list",
                type: "POST",
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
                },
                data: function (d) {
                    d.search = $("#search").val();
                },
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table, .table-footer").addClass("d-none");
                },
                complete: function () {
                    $(".table-loader, .input-loader, .label-loader").hide();
                    $(".real-table, .real-label, .real-input").removeClass("d-none");
                    if (table.rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                }
            },
            columns: [
                { data: "role_name" },
                { 
                    data: "created_at",
                    render: function (data, type, row) {
                        return row.created_date;
                    }
                },
                {
                    data: "status",
                    render: function (data, type, row) {
                        const statusClass = (row.status === 1) ? "badge-success-transparent" : "badge-danger-transparent";
                        const statusText = (row.status === 1) ? _l("admin.common.active") : _l("admin.common.inactive");
                        return `<span class="badge ${statusClass} d-inline-flex align-items-center badge-sm">
                            <i class="ti ti-point-filled me-1"></i>${statusText}</span>`;
                    }
                },
                {
                    data: "id",
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        let actions = "";

                        if (hasPermission(permissions, "roles_permissions", "edit")) {
                            actions += `
                                <li>
                                    <a class="dropdown-item rounded-1 editRole" href="javascript:void(0);" data-id="${row.id}">
                                        <i class="ti ti-edit me-1"></i>${_l("admin.common.edit")}
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item rounded-1" href="/admin/permissions/${row.encrypted_role_id}">
                                        <i class="ti ti-shield me-1"></i>${_l("admin.user_management.permissions")}
                                    </a>
                                </li>`;
                        }

                        if (hasPermission(permissions, "roles_permissions", "delete")) {
                            actions += `
                                <li>
                                    <a class="dropdown-item rounded-1 deleteRole" href="javascript:void(0);" data-id="${row.id}" data-bs-toggle="modal" data-bs-target="#delete_role">
                                        <i class="ti ti-trash me-1"></i>${_l("admin.common.delete")}
                                    </a>
                                </li>`;
                        }

                        if (!actions) return "";

                        return `<div class="dropdown">
                            <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-2">${actions}</ul>
                        </div>`;
                    },
                    visible: hasPermission(permissions, "roles_permissions", "edit") || hasPermission(permissions, "roles_permissions", "delete")
                }
            ],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
            language: {
                emptyTable: _l("admin.common.empty_table"),
                info: `${_l("admin.common.showing")} _START_ ${_l("admin.common.to")} _END_ ${_l("admin.common.of")} _TOTAL_ ${_l("admin.common.entries")}`,
                infoEmpty: `${_l("admin.common.showing")} 0 ${_l("admin.common.to")} 0 ${_l("admin.common.of")} 0 ${_l("admin.common.entries")}`,
                infoFiltered: `(${_l("admin.common.filtered_from")} _MAX_ ${_l("admin.common.total_entries")})`,
                lengthMenu: `${_l("admin.common.show")} _MENU_ ${_l("admin.common.entries")}`,
                search: `${_l("admin.common.search")}:`,
                zeroRecords: _l("admin.common.no_matching_records"),
                paginate: {
                    first: _l("admin.common.first"),
                    last: _l("admin.common.last"),
                    next: _l("admin.common.next"),
                    previous: _l("admin.common.previous")
                }
            },
            drawCallback: function () {
                $(".dataTables_info, .dataTables_wrapper .dataTables_paginate").addClass("d-none");
                const wrapper = $(this).closest(".dataTables_wrapper");
                const info = wrapper.find(".dataTables_info").clone(true);
                const pagination = wrapper.find(".dataTables_paginate").clone(true);
                $(".table-footer").empty().append(`
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div class="datatable-info">${info.html()}</div>
                        <div class="datatable-pagination">${pagination.html()}</div>
                    </div>`);
                $(".table-footer .dataTables_paginate").removeClass("d-none");
            }
        });
    }

    $(document).on("click", ".dataTables_paginate a", function () {
        $(".table-footer").find(".dataTables_paginate").removeClass("d-none");
    });

    $("#search").on("keyup", function () {
        $("#roleTable").DataTable().ajax.reload();
    });

    $("#roleDeleteForm").on("submit", function (e) {
        e.preventDefault();
        $.ajax({
            url: "/admin/role/delete",
            type: "POST",
            data: {
                id: $("#delete_id").val()
            },
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            },
            success: function (response) {
                if (response.code === 200) {
                    showToast("success", response.message);
                    $("#delete_role").modal("hide");
                    $("#roleTable").DataTable().ajax.reload();
                }
            },
            error: function (res) {
                if (res.responseJSON && res.responseJSON.code === 500) {
                    showToast("success", res.responseJSON.message);
                } else {
                    showToast("error", _l("admin.common.default_delete_error"));
                }
            }
        });
    });

    $(document).on("click", ".deleteRole", function () {
        const id = $(this).data("id");
        $("#delete_id").val(id);
    });

    $(document).on("click", ".editRole", function () {
        const id = $(this).data("id");
        $.ajax({
            type: "GET",
            url: `/admin/role/edit/${id}`,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                $("#roleForm")[0].reset();

                if (response.code === 200) {
                    const data = response.data;
                    $("#role").val(data.role_name);
                    $("#status").prop("checked", data.status === 1);
                    $("#id").val(data.id);

                    $("#role_modal .modal-title").text(_l("admin.user_management.edit_role"));
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv").removeClass("d-none")
                        .parent().removeClass("justify-content-end").addClass("justify-content-between");
                    $("#role_modal").modal("show");
                }
            }
        });
    });
}) ();