(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");

    $(document).ready(function () {
        // Initialize validation
        const { rules, messages } = getPrefixValidationRules();

        $("#prefixesSettingForm").validate({
            rules,
            messages,
            errorPlacement: (error, element) => {
                $("#" + element.attr("id") + "_error").text(error.text());
            },
            highlight: (element) => $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $("#" + element.id + "_error").text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: (form) => {
                const formData = new FormData(form);
                formData.set("group_id", 3);

                $(".submitBtn").attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
                `);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/update-prefixes",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                })
                .done(handleAjaxResponse)
                .fail(handleAjaxError);
            },
        });

        loadPrefixesSettings();
    });

    // Helper function to get validation rules/messages
    function getPrefixValidationRules() {
        const fields = [
            "reservation_prefix",
            "quotation_prefix",
            "enquiry_prefix",
            "company_prefix",
            "inspection_prefix",
            "invoice_prefix",
            "report_prefix",
            "customer_prefix",
        ];

        const rules = {};
        const messages = {};

        fields.forEach((field) => {
            rules[field] = {
                required: true,
                maxlength: 10,
                minlength: 2,
                pattern: /^[A-Za-z-]+$/,
            };
            messages[field] = {
                required: _l(`admin.general_settings.${field}_required`),
                minlength: _l(`admin.general_settings.${field}_minlength`),
                maxlength: _l(`admin.general_settings.${field}_maxlength`),
                pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
            };
        });

        return { rules, messages };
    }

    // Helper function to handle AJAX response
    function handleAjaxResponse(resp) {
        $(".submitBtn").removeAttr("disabled").html(_l("admin.common.save_changes"));
        if (resp.code === 200) showToast("success", resp.message);
    }

    // Helper function to handle AJAX errors
    function handleAjaxError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
        $(".submitBtn").removeAttr("disabled").html(_l("admin.common.save_changes"));

        if (error.responseJSON.code === 422) {
            $.each(error.responseJSON.errors, (key, val) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        } else {
            showToast("error", error.responseJSON.message);
        }
    }

    function loadPrefixesSettings() {
        $.ajax({
            url: "/admin/settings/list",
            type: "POST",
            data: { group_id: 3 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach((setting) => {
                        const element = $("#" + setting.key);
                        if (element.length) {
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
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }
})();
