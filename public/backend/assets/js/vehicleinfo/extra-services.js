(async () => {
    "use strict";
    await loadTranslationFile("admin", "rentals,common");
    const permissions = await loadUserPermissions();

    $(document).ready(function () {
        initTable();
        initFormValidation();
        initEvents();
    });

    function initFormValidation() {
        $("#extraServiceForm").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 30,
                    minlength: 3,
                },
                icon: {
                    required: function () {
                        return $("#id").val() === "";
                    },
                    extension: "jpeg|jpg|png|svg",
                    filesize: 2048,
                    imageDimension: [20, 20],
                },
                image: {
                    required: function () {
                        return $("#id").val() === "";
                    },
                    extension: "jpeg|jpg|png|svg",
                    filesize: 2048,
                    extraImageDimension: [180, 180],
                },
                description: {
                    required: true,
                },
            },
            messages: {
                name: {
                    required: _l("admin.rentals.name_required"),
                    maxlength: _l("admin.rentals.name_maxlength"),
                    minlength: _l("admin.rentals.name_minlength"),
                },
                icon: {
                    required: _l("admin.rentals.icon_required"),
                    extension: _l("admin.rentals.icon_extension"),
                    filesize: _l("admin.common.image_size", { size: 2 }),
                },
                image: {
                    required: _l("admin.common.image_required"),
                    extension: _l("admin.rentals.extra_service_image_format"),
                    filesize: _l("admin.common.image_size", { size: 2 }),
                },
                description: {
                    required: _l("admin.rentals.description_required"),
                },
            },
            errorPlacement: function (error, element) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
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
                let ExtraServiceFormData = new FormData(form);
                $.ajax({
                    type: "POST",
                    url: "/admin/store_extra_service",
                    data: ExtraServiceFormData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $(".submitbtn").attr("disabled", true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                                "admin.common.saving"
                            )}..
                        `);
                    },
                    complete: function () {
                        $(".submitbtn")
                            .attr("disabled", false)
                            .html(
                                $("#id").val()
                                    ? _l("admin.common.save_changes")
                                    : _l("admin.common.create_new")
                            );
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_extra_service").modal("hide");
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

        $.validator.addMethod(
            "filesize",
            function (value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param * 1024;
            },
            "file size should be less than {0} bytes"
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
                    valid =
                        img.width >= 100 &&
                        img.width <= 100 &&
                        img.height >= 100 &&
                        img.height <= 100;
                    $(element).data("valid-dimension", valid);
                    $(element).valid();
                };

                reader.readAsDataURL(file);

                return $(element).data("valid-dimension") !== false;
            },
            _l("admin.rentals.extra_service_icon_dimension")
        );

        $.validator.addMethod(
            "extraImageDimension",
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
                        img.width >= 180 &&
                        img.width <= 180 &&
                        img.height >= 180 &&
                        img.height <= 180;
                    $(element).data("valid-dimension", valid);
                    $(element).valid();
                };

                reader.readAsDataURL(file);

                return $(element).data("valid-dimension") !== false;
            },
            _l("admin.rentals.extra_service_image_dimension")
        );
    }

    function initEvents() {
        $(document).on("click", ".status_option", function () {
            $(".status_option").removeClass("active");
            $(this).addClass("active");
            if ($(this).hasClass("active")) {
                $(".status_label").text($(this).data("label"));
            }
            initTable();
        });

        $(document).on("keyup", "#keyword", function () {
            let keyword = $(this).val();
            initTable();
        });

        $(document).on("submit", "#deleteExtraService", function (e) {
            e.preventDefault();
            let serviceFormData = new FormData(this);
            serviceFormData.append(
                "_token",
                $('meta[name="csrf-token"]').attr("content")
            );
            $.ajax({
                type: "POST",
                url: "/admin/delete_extra_service",
                data: serviceFormData,
                processData: false,
                contentType: false,
                beforeSend: function () {
                    $("#deleteExtraService .submitbtn")
                        .attr("disabled", true)
                        .html(_l("admin.common.please_wait"));
                },
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
                complete: function () {
                    $("#deleteExtraService .submitbtn")
                        .attr("disabled", false)
                        .html(_l("admin.common.delete"));
                },
            });
        });

        $(document).on("click", "#add_new_extra_service", function () {
            $("#add_extra_service .modal-title").text(
                _l("admin.rentals.create_extra_service")
            );
            $("#add_extra_service .submitbtn").text(
                _l("admin.common.create_new")
            );
            $("#extraServiceForm")[0].reset();
            $("#extraServiceForm #id").val("");
            $("#extraServiceForm #icon").val("");
            $("#extraServiceForm #image").val("");
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
            $(".icon_asterisk").show();
            $("#icon_preview").addClass("d-none");
            $(".icon_placeholder").show();
            $("#image_preview").addClass("d-none");
            $(".image_placeholder").show();
            $("#statusDiv")
                .addClass("d-none")
                .parent()
                .removeClass("justify-content-between")
                .addClass("justify-content-end");
        });

        $(document).on("click", "#editExtraservice", function () {
            let id = $(this).data("id");
            $.ajax({
                type: "GET",
                url: "/admin/get_extra_service/" + id,
                success: function (response) {
                    if (response.code === 200) {
                        let data = response.data;
                        $("#add_extra_service #name").val(data.name);
                        $("#add_extra_service #id").val(data.id);
                        $("#add_extra_service #language_id").val(
                            data.language_id
                        );
                        if (data.icon && data.icon != null) {
                            $("#icon_preview")
                                .attr("src", data.icon)
                                .removeClass("d-none");
                            $(".icon_placeholder").hide();
                        } else {
                            $("#icon_preview").addClass("d-none");
                            $(".icon_placeholder").show();
                        }
                        if (data.image && data.image != null) {
                            $("#image_preview")
                                .attr("src", data.image)
                                .removeClass("d-none");
                            $(".image_placeholder").hide();
                        } else {
                            $("#image_preview").addClass("d-none");
                            $(".image_placeholder").show();
                        }
                        $("#add_extra_service #description").val(
                            data.description
                        );
                        if (data.status === 1) {
                            $("#add_extra_service #status").prop(
                                "checked",
                                true
                            );
                        } else {
                            $("#add_extra_service #status").prop(
                                "checked",
                                false
                            );
                        }
                        $("#add_extra_service .modal-title").text(
                            _l("admin.rentals.edit_extra_service")
                        );
                        $("#add_extra_service .submitbtn").text(
                            _l("admin.common.save_changes")
                        );
                        $("#add_extra_service #icon").val("");
                        $("#add_extra_service #image").val("");
                        $("#add_extra_service").modal("show");
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".icon_asterisk").hide();
                        $("#statusDiv")
                            .removeClass("d-none")
                            .parent()
                            .removeClass("justify-content-end")
                            .addClass("justify-content-between");
                    }
                },
            });
        });

        $(document).on("click", "#deleteService", function () {
            let id = $(this).data("id");
            $("#delete_id").val(id);
        });

        $(document).on("change", "#icon", function () {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $("#icon_preview").attr("src", e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
                $("#icon_preview").removeClass("d-none");
                $(".icon_placeholder").hide();
            } else {
                $("#icon_preview").addClass("d-none");
                $(".icon_placeholder").show();
            }
        });

        $(document).on("change", "#image", function () {
            if (this.files && this.files[0]) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    $("#image_preview").attr("src", e.target.result);
                };
                reader.readAsDataURL(this.files[0]);
                $("#image_preview").removeClass("d-none");
                $(".image_placeholder").hide();
            } else {
                $("#image_preview").addClass("d-none");
                $(".image_placeholder").show();
            }
        });
    }

    function initTable() {
        let keyword = $("#keyword").val();
        let status;
        $(".status_option").each(function () {
            if ($(this).hasClass("active")) {
                status = $(this).data("id");
            }
        });
        $.ajax({
            url: "/admin/get_extra_services",
            type: "GET",
            data: { keyword: keyword, status: status },
            beforeSend: function () {
                $(".table-loader").show();
                $(".real-table, .table-footer").addClass("d-none");
            },
            complete: function () {
                $(".table-loader, .input-loader, .label-loader").hide();
                $(".real-table, .real-label, .real-input").removeClass(
                    "d-none"
                );
                if ($("#carTransmissionTable").length === 0) {
                    $(".table-footer").addClass("d-none");
                } else {
                    $(".table-footer").removeClass("d-none");
                }
            },
            success: function (response) {
                let tableBody = "";

                if ($.fn.DataTable.isDataTable("#ExtraServiceTable")) {
                    $("#ExtraServiceTable").DataTable().destroy();
                }
                if (response.code === 200 && response.data.length > 0) {
                    let data = response.data;

                    $.each(data, function (index, value) {
                        var desc = value.description;
                        if (
                            value.description &&
                            value.description.length > 65
                        ) {
                            desc = value.description.substring(0, 65) + "...";
                        }
                        tableBody += `<tr>
                                <td><h6 class="fw-medium">${
                                    value.name
                                }</h6></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg">
                                            <img src="${
                                                value.icon
                                            }" class="img-fluid" alt="${_l(
                            "admin.common.image"
                        )}">
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg">
                                            <img src="${
                                                value.image
                                            }" class="img-fluid" alt="${_l(
                            "admin.common.image"
                        )}">
                                        </div>
                                    </div>
                                </td>
                                <td>${desc}</td>
                                <td> 
                                    <span class="badge ${
                                        value.status == 1
                                            ? `badge-success-transparent`
                                            : `badge-danger-transparent`
                                    }  d-inline-flex align-items-center badge-sm">
                                        <i class="ti ti-point-filled me-1"></i>${
                                            value.status == 1
                                                ? `${_l("admin.common.active")}`
                                                : `${_l(
                                                      "admin.common.inactive"
                                                  )}`
                                        }
                                    </span>
                                </td>
                               ${
                                   hasPermission(
                                       permissions,
                                       "extra_service",
                                       "edit"
                                   ) ||
                                   hasPermission(
                                       permissions,
                                       "extra_service",
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
                                                   "extra_service",
                                                   "edit"
                                               )
                                                   ? `<li>
                                                <button type="button" class="dropdown-item rounded-1" id="editExtraservice" data-id="${
                                                    value.id
                                                }"><i class="ti ti-edit me-1"></i>${_l(
                                                         "admin.common.edit"
                                                     )}</button>
                                            </li>`
                                                   : ""
                                           }
                                            ${
                                                hasPermission(
                                                    permissions,
                                                    "extra_service",
                                                    "delete"
                                                )
                                                    ? `<li>
                                                <button type="button" class="dropdown-item rounded-1" id="deleteService" data-id="${
                                                    value.id
                                                }" data-bs-toggle="modal" data-bs-target="#delete-modal">
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
                    tableBody += `
                                <tr>
                                    <td colspan="7" class="text-center">${_l(
                                        "admin.common.empty_table"
                                    )}</td>
                                </tr>`;
                    $(".table-footer").empty();
                }
                $("#ExtraServiceTable tbody").html(tableBody);
                if (response.data.length > 0) {
                    $("#ExtraServiceTable").DataTable({
                        ordering: false,
                        searching: false,
                        pageLength: 10,
                        lengthChange: false,
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
                showToast("error", error.responseJSON.message);
            },
        });
    }
})();
