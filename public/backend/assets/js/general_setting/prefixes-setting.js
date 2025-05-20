(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");

    $(document).ready(function () {
        $("#prefixesSettingForm").validate({
            rules: {
                reservation_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                quotation_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                enquiry_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                company_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                inspection_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                invoice_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                report_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
                customer_prefix: {
                    required: true,
                    maxlength: 10,
                    minlength: 2,
                    pattern: /^[A-Za-z-]+$/,
                },
            },
            messages: {
                reservation_prefix: {
                    required: _l(
                        "admin.general_settings.reservation_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.reservation_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.reservation_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                quotation_prefix: {
                    required: _l(
                        "admin.general_settings.quotation_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.quotation_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.quotation_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                enquiry_prefix: {
                    required: _l(
                        "admin.general_settings.enquiry_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.enquiry_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.enquiry_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                company_prefix: {
                    required: _l(
                        "admin.general_settings.company_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.company_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.company_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                inspection_prefix: {
                    required: _l(
                        "admin.general_settings.inspection_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.inspection_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.inspection_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                invoice_prefix: {
                    required: _l(
                        "admin.general_settings.invoice_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.invoice_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.invoice_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                report_prefix: {
                    required: _l(
                        "admin.general_settings.report_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.report_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.report_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
                },
                customer_prefix: {
                    required: _l(
                        "admin.general_settings.customer_prefix_required"
                    ),
                    minlength: _l(
                        "admin.general_settings.customer_prefix_minlength"
                    ),
                    maxlength: _l(
                        "admin.general_settings.customer_prefix_maxlength"
                    ),
                    pattern: _l("admin.general_settings.alpha_hyphen_allowed"),
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
                let formData = new FormData(form);
                formData.set("group_id", 3);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/update-prefixes",
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    beforeSend: function () {
                        $(".submitBtn").attr("disabled", true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l(
                            "admin.common.saving"
                        )}..
                    `);
                    },
                    success: function (resp) {
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.save_changes"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.save_changes"));

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

        loadPrefixesSettings();
    });
})();

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
                showToast("error", _l("admin.common.default_retrieve_error"));
            }
        },
        complete: function () {
            $(".label-loader, .input-loader, .card-loader").hide();
            $(".real-label, .real-input, .real-card").removeClass("d-none");
        },
    });
}
