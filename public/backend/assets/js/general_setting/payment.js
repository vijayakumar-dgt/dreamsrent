(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");

    $(document).ready(function () {
        initList();
        // Initialize forms
        initPaymentFormValidation("PaypalSettingForm", "add_paypal", ["paypal_email", "paypal_key", "paypal_secret"]);
        initPaymentFormValidation("StripeSettingForm", "add_stripe", ["stripe_email", "stripe_key", "stripe_secret"]);
    });

    // Generic function to initialize payment form validation
    function initPaymentFormValidation(formId, modalId, fields) {
        $(`#${formId}`).validate({
            rules: generateRules(fields),
            messages: generateMessages(fields),
            errorPlacement: handleErrorPlacement,
            highlight: handleHighlight,
            unhighlight: handleUnhighlight,
            onkeyup: (el) => $(el).valid(),
            onchange: (el) => $(el).valid(),
            submitHandler: (form) => submitPaymentForm(form, modalId),
        });
    }

    // Helper: Generate rules from fields
    function generateRules(fields) {
        const rules = {};
        fields.forEach((field) => {
            rules[field] = { required: true };
        });
        return rules;
    }

    // Helper: Generate messages from fields
    function generateMessages(fields) {
        const messages = {};
        fields.forEach((field) => {
            const key = `admin.general_settings.${field}_required`;
            messages[field] = { required: _l(key) };
        });
        return messages;
    }

    // Helper: Error placement
    function handleErrorPlacement(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    // Helper: Highlight invalid fields
    function handleHighlight(element) {
        const $el = $(element);
        if ($el.hasClass("select2-hidden-accessible")) {
            $el.next(".select2-container").addClass("is-invalid").removeClass("is-valid");
        }
        $el.addClass("is-invalid").removeClass("is-valid");
    }

    // Helper: Unhighlight valid fields
    function handleUnhighlight(element) {
        const $el = $(element);
        if ($el.hasClass("select2-hidden-accessible")) {
            $el.next(".select2-container").removeClass("is-invalid").addClass("is-valid");
        }
        $el.removeClass("is-invalid").addClass("is-valid");
        $("#" + element.id + "_error").text("");
    }

    // Helper: Submit form via AJAX
    function submitPaymentForm(form, modalId) {
        const formData = new FormData(form);

        $.ajax({
            type: "POST",
            url: "/admin/settings/updatepaymentSettings",
            data: formData,
            processData: false,
            contentType: false,
            success: (resp) => handlePaymentSuccess(resp, modalId),
            error: handlePaymentError,
        });
    }

    // Helper: Handle successful response
    function handlePaymentSuccess(resp, modalId) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (resp.code === 200) {
            showToast("success", resp.message);
            $(`#${modalId}`).modal("hide");
        }
    }

    // Helper: Handle AJAX error
    function handlePaymentError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON?.code === 422) {
            Object.entries(error.responseJSON.errors).forEach(([key, val]) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        } else {
            showToast('error', error.responseJSON?.message || "Something went wrong");
        }
    }

    $(document).on("change", ".checkStatus", function () {
        let status = $(this).is(":checked") ? 1 : 0;
        let key = $(this).attr("name");

        $.ajax({
            url: "/admin/settings/updatepaymentStatus",
            type: "POST",
            data: {
                key: key,
                value: status,
                group_id: 13,
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.success) {
                    showToast(
                        "success",
                        "Payment status updated successfully!"
                    );
                    initList();
                } else {
                    showToast("error", "Failed to update payment status.");
                }
            },
            error: function () {
                showToast("error", "Something went wrong! Please try again.");
            },
        });
    });

    function initList() {
        $.ajax({
            url: "/admin/settings/payment-list",
            type: "GET",
            success: function (response) {
                if (response.code === 200 && response.data) {
                    response.data.forEach(function (item) {
                        let element = $("#" + item.key);

                        if (element.length) {
                            if (element.attr("type") === "checkbox") {
                                let isChecked = item.value == "1";
                                element.prop("checked", isChecked);

                                let statusSpan = $(
                                    "." + item.key.replace("_status", "In")
                                );
                                if (statusSpan.length) {
                                    if (isChecked) {
                                        statusSpan.html(
                                            '<i class="ti ti-point-filled text-success me-1"></i>Connected'
                                        );
                                    } else {
                                        statusSpan.html(
                                            '<i class="ti ti-point-filled text-dark me-1"></i>Not Connected'
                                        );
                                    }
                                }
                            } else {
                                element.val(item.value);
                            }
                        }
                    });
                }
                $(".table-loader").hide();
                $(".label-loader, .input-loader, .card-loader").hide();
                $(
                    ".real-label, .real-table, .real-data, .real-card"
                ).removeClass("d-none");
            },
            error: function (error) {
                if (error.responseJSON && error.responseJSON.code === 500) {
                    showToast('error', error.responseJSON.message);
                } else {
                    showToast('error',
                        "An error occurred while retrieving payment settings."
                    );
                }
            },
        });
    }
})();
