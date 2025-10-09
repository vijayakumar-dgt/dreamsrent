(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        $("#language").on("change", function () {
            loadCookiesSettings($(this).val());
        });
        $(".summernote").summernote({
            height: 150,
            placeholder: `${_l(
                "admin.general_settings.type_your_content_here"
            )}`,
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["font", ["strikethrough", "superscript", "subscript"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });

        // Initialize form
        initCookiesSettingForm();

        loadCookiesSettings();
    });

    // Initialize cookies settings form validation
    function initCookiesSettingForm() {
        $("#cookiesSettingForm").validate({
            rules: getCookiesRules(),
            messages: getCookiesMessages(),
            errorPlacement: handleErrorPlacement,
            highlight: handleHighlight,
            unhighlight: handleUnhighlight,
            onkeyup: (el) => $(el).valid(),
            onchange: (el) => $(el).valid(),
            submitHandler: submitCookiesForm,
        });
    }

    // Rules for form fields
    function getCookiesRules() {
        return {
            cookiesContentText: { required: true, maxlength: 60 },
            cookiesPosition: { required: true },
            agreeButtonText: { required: true, minlength: 2 },
            declineButtonText: { required: true, minlength: 2 },
            showDeclineButton: { required: false },
            cookiesPageLink: { required: true, url: true },
        };
    }

    // Messages for form fields
    function getCookiesMessages() {
        return {
            cookiesContentText: {
                required: _l("admin.general_settings.enter_cookies_content_text"),
                maxlength: _l("admin.general_settings.cookies_content_length"),
            },
            cookiesPosition: {
                required: _l("admin.general_settings.select_cookies_position"),
            },
            agreeButtonText: {
                required: _l("admin.general_settings.enter_agree_button_text"),
                minlength: _l("admin.general_settings.agree_button_characters"),
            },
            declineButtonText: {
                required: _l("admin.general_settings.enter_decline_button_text"),
                minlength: _l("admin.general_settings.decline_button_characters"),
            },
            cookiesPageLink: {
                required: _l("admin.general_settings.cookies_page_link"),
                url: _l("admin.general_settings.enter_valid_url"),
            },
        };
    }

    // Error placement
    function handleErrorPlacement(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    // Highlight invalid fields
    function handleHighlight(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    }

    // Unhighlight valid fields
    function handleUnhighlight(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        $("#" + element.id + "_error").text("");
    }

    // Submit form via AJAX
    function submitCookiesForm(form) {
        const cookiesData = new FormData(form);

        $.ajax({
            type: "POST",
            url: "/admin/settings/cookies/store",
            data: cookiesData,
            processData: false,
            contentType: false,
            headers: getAjaxHeaders(),
            beforeSend: showSavingState,
            complete: hideSavingState,
            success: handleCookiesSuccess,
            error: handleCookiesError,
        });
    }

    // Common AJAX headers
    function getAjaxHeaders() {
        return {
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        };
    }

    // Show loader on submit
    function showSavingState() {
        $(".btn-primary").attr("disabled", true).html(`
            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
        `);
    }

    // Hide loader on complete
    function hideSavingState() {
        $(".btn-primary").attr("disabled", false).html(_l("admin.common.save_changes"));
    }

    // Handle successful response
    function handleCookiesSuccess(resp) {
        if (resp.code === 200) {
            loadCookiesSettings();
            showToast("success", resp.message);
        }
    }

    // Handle AJAX error
    function handleCookiesError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON?.code === 422) {
            Object.entries(error.responseJSON.errors).forEach(([key, val]) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        } else {
            showToast("error", error.responseJSON?.message || "Something went wrong");
        }
    }

    function loadCookiesSettings(languageId = null) {
        $.ajax({
            url: "/admin/settings/cookies/list",
            type: "POST",
            data: {
                group_id: 7,
                language_id: languageId,
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    Object.keys(settings).forEach(function (key) {
                        const baseKey = key.split("_")[0]; // strip _1, _2, etc.
                        const value = settings[key];

                        if (baseKey === "cookiesContentText") {
                            $("#cookiesContentText").summernote("code", value);
                        } else {
                            const element = $("#" + baseKey);
                            if (element.length) {
                                if (element.attr("type") === "checkbox") {
                                    element.prop("checked", value === "1");
                                } else if (element.is("select")) {
                                    element.val(value).trigger("change");
                                } else {
                                    element.val(value);
                                }
                            }
                        }
                    });
                }
            },
            error: function (xhr) {
                showToast("error", _l("admin.common.default_retrieve_error"));
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }
})();
