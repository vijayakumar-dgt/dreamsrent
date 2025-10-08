(async () => {
    "use strict";
    await loadTranslationFile("admin", "cms,common");
    const permissions = await loadUserPermissions();
    let selectedStatus = "";

    $(document).ready(function () {
        initTable();

        $("#state_id").select2({
            dropdownParent: $("#city_modal"),
            placeholder: _l("admin.common.select"),
        });

        $("#cityForm").validate({
            rules: {
                state_id: {
                    required: true,
                },
                name: {
                    required: true,
                },
            },
            messages: {
                state_id: {
                    required: _l("admin.cms.state_required"),
                },
                name: {
                    required: _l("admin.cms.city_required"),
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
                    url: "/admin/city/store",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#city_modal").modal("hide");
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

        $(document).on("keyup", "#search", function () {
            setTimeout(function () {
                initTable();
            }, 500);
        });

        $(document).on("click", "#edit-city", function () {
            let id = $(this).data("id");
            editCity(id);
        });

        $(document).on("click", "#delete-city", function () {
            let id = $(this).data("id");
            delateCity(id);
        });

        $(document).on("click", ".dataTables_paginate a", function () {
            $(".table-footer")
                .find(".dataTables_paginate")
                .removeClass("d-none");
        });

        $("#delateCity").on("submit", function (e) {
            e.preventDefault();
            $.ajax({
                url: "/admin/city/delete",
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

        $("#add_city").on("click", function () {
            $(".modal-title").text(_l("admin.cms.create_city"));
            $(".submitbtn").text(_l("admin.common.create_new"));
            $("#cityForm")[0].reset();
            $("#id").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
            $("#state_id").val("").trigger("change");
        });

        $(document).on("click", ".selectStatus", function () {
            selectedStatus = $(this).data("status");
            $(".selectStatus").removeClass("active");
            $(this).addClass("active");
            initTable(selectedStatus);
        });
    });

    function initTable() {
        $("#cityTable").DataTable({
            serverSide: true,
            destroy: true,
            processing: false,
            ajax: {
                url: "/admin/city/datatable",
                type: "GET",
                data: function (d) {
                    d.search = $("#search").val();
                    d.status = selectedStatus;
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
                    if ($("#cityTable").DataTable().rows().count() === 0) {
                        $(".table-footer").addClass("d-none");
                    } else {
                        $(".table-footer").removeClass("d-none");
                    }
                },
            },
            columns: [
                { data: "name", name: "name" },
                {
                    data: "state.name",
                    orderable: false,
                    defaultContent: "N/A",
                },
                {
                    data: "state.country.name",
                    orderable: false,
                    render: function (data, type, row) {
                        if (row.state && row.state.country) {
                            let countryCode =
                                row.state.country.code.toLowerCase();
                            return `<img src="/backend/assets/img/flags/${countryCode}.svg" alt="${row.state.country.name} Flag" width="20"
                                onerror="this.style.display='none'; this.parentNode.innerHTML='${row.state.country.name}';"> ${row.state.country.name}`;
                        } else {
                            return "N/A";
                        }
                    },
                },
                {
                    data: "status",
                    orderable: false,
                    render: function (data, type, row) {
                        return `
                                <span class="badge ${
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
                        let actions = "";
                        if (
                            hasPermission(permissions, "cms_locations", "edit")
                        ) {
                            actions += `<li><button type="button" class="dropdown-item rounded-1" data-id="${
                                row.id
                            }" id="edit-city" ><i class="ti ti-edit me-1"></i>${_l(
                                "admin.common.edit"
                            )}</button></li>`;
                        }
                        if (
                            hasPermission(
                                permissions,
                                "cms_locations",
                                "delete"
                            )
                        ) {
                            actions += `<li><button type="button" class="dropdown-item rounded-1" data-id="${
                                row.id
                            }" id="delete-city" data-bs-toggle="modal" data-bs-target="#delete-modal"><i class="ti ti-trash me-1"></i>${_l(
                                "admin.common.delete"
                            )}</button></li>`;
                        }

                        if (actions !== "") {
                            return `<div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">${actions}</ul>
                                </div>`;
                        } else {
                            return "";
                        }
                    },
                },
            ],
            ordering: true,
            searching: false,
            pageLength: 10,
            lengthChange: false,
            responsive: false,
            autoWidth: false,
            drawCallback: function () {
                customizeTableFooter($(this));
            },
            language: getDataTableLanguage(),
        });
    }

    function editCity(id) {
        $.ajax({
            type: "GET",
            url: "/admin/city/edit/" + id,
            success: function (response) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");
                if (response.code === 200) {
                    let data = response.data;
                    $("#name").val(data.name);
                    $("#state_id").val(data.state_id).trigger("change");
                    $("#status").prop("checked", data.status === 1);
                    $("#id").val(data.id);

                    $("#city_modal .modal-title").text(
                        _l("admin.cms.edit_city")
                    );
                    $(".submitbtn").text(_l("admin.common.save_changes"));
                    $("#statusDiv")
                        .removeClass("d-none")
                        .parent()
                        .removeClass("justify-content-end")
                        .addClass("justify-content-between");
                    $("#city_modal").modal("show");
                }
            },
        });
    }

    function delateCity(id) {
        $("#delete_id").val(id);
    }
})();
