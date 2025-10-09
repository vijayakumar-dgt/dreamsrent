(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");
    $(document).ready(function () {
        $("#ai_configuration_form").validate({
            rules: {
                ai_api_key: {
                    required: true,
                    minlength: 10,
                },
            },
            messages: {
                ai_api_key: {
                    required: _l("admin.general_settings.api_key_required"),
                    minlength: _l("admin.general_settings.api_key_minlength"),
                },
            },
            errorPlacement: placeError,
            highlight: highlightElement,
            unhighlight: unhighlightElement,
            onkeyup: validateElement,
            onchange: validateElement,
            submitHandler: submitAIConfigurationForm,
        });

        loadSettings();

    });

    function loadSettings() {
        $.ajax({
            url: "/admin/settings/list",
            type: "POST",
            data: { group_id: 4 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach((setting) => {
                        const element = $("#" + setting.key);

                        if (
                            setting.key === "ai_global_status" ||
                            setting.key === "ai_admin_status" ||
                            setting.key === "ai_user_status"
                        ) {
                            element.prop(
                                "checked",
                                parseInt(setting.value) === 1
                            );
                        } else {
                            element.val(setting.value);
                        }
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
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-input").removeClass("d-none");
            },
        });
    }

    // --- Helper Functions ---

    function placeError(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    function highlightElement(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    }

    function unhighlightElement(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        const errorId = element.id + "_error";
        $("#" + errorId).text("");
    }

    function validateElement(element) {
        $(element).valid();
    }

    function submitAIConfigurationForm(form) {
        const formData = getFormData(form);

        $.ajax({
            type: "POST",
            url: "/admin/settings/update-ai-configuration",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: disableSubmitButton,
            success: handleSuccess,
            error: handleError,
        });
    }

    // --- Form Data Preparation ---
    function getFormData(form) {
        const formData = new FormData(form);
        formData.set("group_id", 4);
        formData.set("ai_global_status", $("#ai_global_status").is(":checked") ? 1 : 0);
        formData.set("ai_admin_status", $("#ai_admin_status").is(":checked") ? 1 : 0);
        formData.set("ai_user_status", $("#ai_user_status").is(":checked") ? 1 : 0);
        return formData;
    }

    // --- AJAX Helpers ---
    function disableSubmitButton() {
        $(".submitBtn").attr("disabled", true).html(`
            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
        `);
    }

    function enableSubmitButton() {
        $(".submitBtn").removeAttr("disabled").html(_l("admin.common.save_changes"));
    }

    function handleSuccess(resp) {
        enableSubmitButton();
        if (resp.code === 200) {
            showToast("success", resp.message);
        }
    }

    function handleError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        enableSubmitButton();

        if (error.responseJSON.code === 422) {
            displayValidationErrors(error.responseJSON.errors);
        } else {
            showToast("error", error.responseJSON.message);
        }
    }

    function displayValidationErrors(errors) {
        $.each(errors, function (key, val) {
            $("#" + key).addClass("is-invalid");
            $("#" + key + "_error").text(val[0]);
        });
    }
})();
