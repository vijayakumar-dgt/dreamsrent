/* global $, loadTranslationFile, document, showToast, _l, FormData, window */
(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, user_management");

    $(document).on("click", ".select_all_permission", function () {
        const parentModule = $(this).data("parent_module");
        $("." + parentModule).prop("checked", $(this).prop("checked"));
    });

    $("#permissionForm").submit(function (e) {
        e.preventDefault();

        const formData = new FormData();
        const roleId = $("#role_id").val();
        const processedModules = new Set();
        let index = 0;

        formData.append("role_id", roleId);

        $("table tbody tr").each(function () {
            const row = $(this);
            const moduleId = row.find("td:eq(0)").data("module_id");

            if (!moduleId || processedModules.has(moduleId)) {
                return;
            }

            processedModules.add(moduleId);
            const permissionId = row.find("td:eq(0)").data("permission_id") || "";

            formData.append(`permissions[${index}][id]`, permissionId);
            formData.append(`permissions[${index}][module_id]`, moduleId);
            formData.append(`permissions[${index}][create]`, row.find(".perm-create").is(":checked") ? 1 : 0);
            formData.append(`permissions[${index}][edit]`, row.find(".perm-edit").is(":checked") ? 1 : 0);
            formData.append(`permissions[${index}][delete]`, row.find(".perm-delete").is(":checked") ? 1 : 0);
            formData.append(`permissions[${index}][view]`, row.find(".perm-view").is(":checked") ? 1 : 0);
            formData.append(`permissions[${index}][allow_all]`, row.find(".perm-allow-all").is(":checked") ? 1 : 0);

            index++;
        });

        $.ajax({
            type: "POST",
            url: "/admin/permission/update",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
            },
            beforeSend: function () {
                $(".submitbtn").prop("disabled", true).html(
                    `<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..`
                );
            },
            success: function (resp) {
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                $(".submitbtn").prop("disabled", false).html(_l("admin.common.submit"));

                if (resp.code === 200) {
                    showToast("success", resp.message);
                    window.location.href = "/admin/roles-permissions";
                }
            },
            error: function (error) {
                $(".error-text").text("");
                $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                $(".submitbtn").prop("disabled", false).html(_l("admin.common.submit"));

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
    });

    $(document).on("change", ".perm-allow-all", function () {
        const row = $(this).closest("tr");
        const isChecked = $(this).is(":checked");

        row.find(".perm-create, .perm-view, .perm-edit, .perm-delete").prop("checked", isChecked);
    });

    $(document).on("change", ".perm-create, .perm-view, .perm-edit, .perm-delete", function () {
        const row = $(this).closest("tr");
        const totalPermissions = row.find(".perm-create, .perm-view, .perm-edit, .perm-delete").length;
        const checkedPermissions = row.find(".perm-create:checked, .perm-view:checked, .perm-edit:checked, .perm-delete:checked").length;

        row.find(".perm-allow-all").prop("checked", totalPermissions === checkedPermissions);
    });
}) ();