(async () => {
    "use strict";
    $(document).ready(function () {
        listAddonModules();

        $(".install_btn").on("click", function () {
            let selectedPlugin = $('input[name="selected_plugin"]:checked');

            if (!selectedPlugin.val()) {
                showToast(
                    "error",
                    _l("admin.general_settings.select_plugin_proceeding")
                );
                return;
            }

            let selectedRow = selectedPlugin.closest("tr");

            let moduleName = selectedRow.find(".name").text().trim();
            let moduleVersion = selectedRow.find(".version").text().trim();
            let modulePrice = selectedRow
                .find(".price")
                .text()
                .trim()
                .replace("$", "");
            let gitLink = selectedPlugin.val();

            $("#module_name").val(moduleName);
            $("#module_version").val(moduleVersion);
            $("#module_price").val(modulePrice);
            $("#git_link").val(gitLink);

            $("#add_plugin").modal("hide");

            $("#purchase_plugin").modal("show");
        });
    });

    function listAddonModules() {
        $.ajax({
            url: "/admin/settings/addon-module-list",
            type: "POST",
            dataType: "json",
            data: {
                order_by: "asc",
                sort_by: "id",
            },
            headers: {
                Authorization: "Bearer " + localStorage.getItem("admin_token"),
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    let addons = response.data;
                    let tableBody = "";

                    if (addons.length === 0) {
                        $("#addonModuleTable").DataTable().destroy();
                        tableBody += `
                        <tr>
                            <td colspan="6" class="text-center">${$(
                                "#addonModuleTable"
                            ).data("empty")}</td>
                        </tr>`;
                    } else {
                        addons.forEach((addon, index) => {
                            tableBody += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${addon.name} (${addon.version})</td>
                                <td>
                                    <img src="${
                                        addon.module_image
                                    }" alt="Image">
                                </td>
                                <td>$${addon.price}</td>
                                ${
                                    $("#has_permission").data("edit") == 1
                                        ? `<td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input addon_status" ${
                                            addon.status == 1 ? "checked" : ""
                                        } type="checkbox" role="switch" id="switch-sm" data-id="${
                                              addon.id
                                          }">
                                    </div>
                                </td>`
                                        : ""
                                }
                                <td>
                                    <div class="d-flex align-items-center">
                                        <a href="/reload/${addon.name}">
                                            <i class="ti ti-refresh fs-20"></i></a>
                                    </div>
                                </td>
                            </tr>
                        `;
                        });
                    }

                    $("#addonModuleTable tbody").html(tableBody);
                    $("#loader-table").addClass("d-none");
                    $(".label-loader, .input-loader").hide();
                    $(
                        "#addonModuleTable, .real-label, .real-input"
                    ).removeClass("d-none");

                    if (
                        addons.length != 0 &&
                        !$.fn.DataTable.isDataTable("#addonModuleTable")
                    ) {
                        $("#addonModuleTable").DataTable({
                            ordering: true,
                            language: datatableLang,
                        });
                    }
                }
            },
            error: function (error) {
                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else {
                        toastr.error(
                            _l("admin.general_settings.retrive_error")
                        );
                    }
                }
            },
        });
    }

    function listNewAddonModules() {
        $.ajax({
            url: "/admin/settings/new-addon-modules",
            type: "POST",
            dataType: "json",
            data: {
                order_by: "asc",
                sort_by: "id",
            },
            headers: {
                Authorization: "Bearer " + localStorage.getItem("admin_token"),
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },

            success: function (response) {
                if (response.code === 200) {
                }
            },
            error: function (error) {
                if (error.status === 422) {
                    var errors = error.responseJSON.errors;
                    if (errors) {
                        $.each(errors, function (key, messages) {
                            toastr.error(messages[0]);
                        });
                    } else {
                        toastr.error(
                            _l("admin.general_settings.retrive_error")
                        );
                    }
                }
            },
        });
    }

    $(document).on("change", ".addon_status", function () {
        let id = $(this).data("id");
        let status = $(this).is(":checked") ? 1 : 0;

        var data = {
            id: id,
            status: status,
        };

        $.ajax({
            url: "/admin/settings/change-addon-status",
            type: "POST",
            data: data,
            headers: {
                Authorization: "Bearer " + localStorage.getItem("admin_token"),
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    toastr.success(response.message);
                    listAddonModules();
                    location.reload();
                }
            },
            error: function (error) {
                toastr.error(_l("admin.general_settings.retrive_error"));
            },
        });
    });
    $(document).on("click", "#installed_addon", function () {
    $("#newAddonModuleTable").addClass("d-none");
    $("#addonModuleTable").removeClass("d-none");
    if ($.fn.DataTable.isDataTable("#newAddonModuleTable")) {
        $("#newAddonModuleTable").DataTable().destroy();
    }
    if ($.fn.DataTable.isDataTable("#addonModuleTable")) {
        $("#addonModuleTable").DataTable().destroy();
    }
    $("#addonModuleTable tbody").empty();
    $("#loader-table").removeClass("d-none");
    $(".label-loader, .input-loader").show();
    $("#addonModuleTable, .real-label, .real-input").addClass("d-none");

    listAddonModules();
});

$(document).on("click", "#new_addon", function () {
    $("#addonModuleTable").addClass("d-none");
    $("#newAddonModuleTable").removeClass("d-none");
    if ($.fn.DataTable.isDataTable("#addonModuleTable")) {
        $("#addonModuleTable").DataTable().destroy();
    }
    if ($.fn.DataTable.isDataTable("#newAddonModuleTable")) {
        $("#newAddonModuleTable").DataTable().destroy();
    }
    $("#newAddonModuleTable tbody").empty();
    $("#loader-table").removeClass("d-none");
    $(".label-loader, .input-loader").show();
    $("#newAddonModuleTable, .real-label, .real-input").addClass("d-none");
    listNewAddonModules();
});

$(document).on("click", ".purchase_btn", function () {
    $("#module_name").val($(this).data("module"));
    $("#module_version").val($(this).data("version"));
    $("#module_price").val($(this).data("price"));
    $("#git_link").val($(this).data("git_link"));

    $("#purchase_modal").modal("show");
});

$(document).on("click", ".purchase_confirm_btn", function (event) {
    event.preventDefault();

    var formData = new FormData();
    formData.append("module_name", $("#module_name").val());
    formData.append("module_version", $("#module_version").val());
    formData.append("module_price", $("#module_price").val());
    formData.append("git_link", $("#git_link").val());

    $.ajax({
        url: "/admin/settings/purchase-module",
        type: "POST",
        data: formData,
        contentType: false,
        processData: false,
        cache: false,
        headers: {
            Authorization: "Bearer " + localStorage.getItem("admin_token"),
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        beforeSend: function () {
            $(".purchase_confirm_btn")
                .attr("disabled", true)
                .html(
                    '<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>'
                );
        },
        success: function (response) {
            $(".error-text").text("");
            $(".purchase_confirm_btn")
                .removeAttr("disabled")
                .html(
                    $(".purchase_confirm_btn").data(
                        _l("admin.general_settings.save")
                    )
                );
            $(".form-control").removeClass("is-invalid is-valid");
            $(".select2-container").removeClass("is-invalid is-valid");
            $("#purchase_modal").modal("hide");
            if (response.code === 200) {
                toastr.success(response.message);
                listNewAddonModules();
                location.reload();
            }
        },
        error: function (error) {
            $(".error-text").text("");
            $(".purchase_confirm_btn")
                .removeAttr("disabled")
                .html(
                    $(".purchase_confirm_btn").data(
                        _l("admin.general_settings.save")
                    )
                );
            $(".form-control").removeClass("is-invalid is-valid");
            $(".select2-container").removeClass("is-invalid is-valid");
            if (error.responseJSON.code === 422) {
                $.each(error.responseJSON.errors, function (key, val) {
                    $("#" + key).addClass("is-invalid");
                    $("#" + key + "_error").text(val[0]);
                });
            } else {
                toastr.error(error.responseJSON.message);
            }
        },
    });
});
})();


