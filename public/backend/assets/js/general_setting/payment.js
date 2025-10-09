(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, general_settings");

    $(document).ready(function () {
        initList();
        $("#PaypalSettingForm").validate({
            rules: {
                paypal_email: {
                    required: true,
                },
                paypal_key: {
                    required: true,
                },
                paypal_secret: {
                    required: true,
                },
            },
            messages: {
                paypal_email: {
                    required: _l(
                        "admin.general_settings.paypal_email_required"
                    ),
                },
                paypal_key: {
                    required: _l("admin.general_settings.paypal_key_required"),
                },
                paypal_secret: {
                    required: _l(
                        "admin.general_settings.paypal_secret_required"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
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

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/updatepaymentSettings",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_paypal").modal("hide");
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
                            toastr.error(error.responseJSON.message);
                        }
                    },
                });
            },
        });

        $("#StripeSettingForm").validate({
            rules: {
                stripe_email: {
                    required: true,
                },
                stripe_key: {
                    required: true,
                },
                stripe_secret: {
                    required: true,
                },
            },
            messages: {
                stripe_email: {
                    required: _l(
                        "admin.general_settings.stripe_email_required"
                    ),
                },
                stripe_key: {
                    required: _l("admin.general_settings.stripe_key_required"),
                },
                stripe_secret: {
                    required: _l(
                        "admin.general_settings.stripe_secret_required"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
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

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/updatepaymentSettings",
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#add_stripe").modal("hide");
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
                            toastr.error(error.responseJSON.message);
                        }
                    },
                });
            },
        });
    });
    
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
                    toastr.error(error.responseJSON.message);
                } else {
                    toastr.error(
                        "An error occurred while retrieving payment settings."
                    );
                }
            },
        });
    }
})();
