(async () => {
    "use strict";
    await loadTranslationFile('admin', 'common, general_settings');

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
                required: _l('admin.general_settings.paypal_email_required')
            },
            paypal_key: {
                required:_l('admin.general_settings.paypal_key_required')
            },
            paypal_secret: {
                required: _l('admin.general_settings.paypal_secret_required')
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
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
                required: _l('admin.general_settings.stripe_email_required')
            },
            stripe_key: {
                required: _l('admin.general_settings.stripe_key_required')
            },
            stripe_secret: {
                required: _l('admin.general_settings.stripe_secret_required')
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
                    } else {
                        toastr.error(error.responseJSON.message);
                    }
                },
            });
        },
    });

    $("#BraintreeSettingForm").validate({
        rules: {
            braintree_email: {
                required: true,
            },
            braintree_key: {
                required: true,
            },
            braintree_secret: {
                required: true,
            },
        },
        messages: {
            braintree_email: {
                required: _l('admin.general_settings.braintree_email_required')
            },
            braintree_key: {
                required: _l('admin.general_settings.braintree_tree_required')
            },
            braintree_secret: {
                required: _l('admin.general_settings.braintree_secret_required')
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_braintree").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#RazorpaySettingForm").validate({
        rules: {
            razorpay_email: {
                required: true,
            },
            razorpay_key: {
                required: true,
            },
            razorpay_secret: {
                required: true,
            },
        },
        messages: {
            razorpay_email: {
                required: "Razorpay email field is required",
            },
            razorpay_key: {
                required: "Razorpay key field is required",
            },
            razorpay_secret: {
                required: "Razorpay secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_razorpay").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#TwoCheckoutSettingForm").validate({
        rules: {
            twocheckout_email: {
                required: true,
            },
            twocheckout_key: {
                required: true,
            },
            twocheckout_secret: {
                required: true,
            },
        },
        messages: {
            twocheckout_email: {
                required: "2Checkout email field is required",
            },
            twocheckout_key: {
                required: "2Checkout key field is required",
            },
            twocheckout_secret: {
                required: "2Checkout secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_2checkout").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#SkrillSettingForm").validate({
        rules: {
            skrill_email: {
                required: true,
            },
            skrill_key: {
                required: true,
            },
            skrill_secret: {
                required: true,
            },
        },
        messages: {
            skrill_email: {
                required: "Skrill email field is required",
            },
            skrill_key: {
                required: "Skrill key field is required",
            },
            skrill_secret: {
                required: "Skrill secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_skrill").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#PayUSettingForm").validate({
        rules: {
            payu_email: {
                required: true,
            },
            payu_key: {
                required: true,
            },
            payu_secret: {
                required: true,
            },
        },
        messages: {
            payu_email: {
                required: "PayU email field is required",
            },
            payu_key: {
                required: "PayU key field is required",
            },
            payu_secret: {
                required: "PayU secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_payu").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#ApplePaySettingForm").validate({
        rules: {
            applepay_email: {
                required: true,
            },
            applepay_key: {
                required: true,
            },
            applepay_secret: {
                required: true,
            },
        },
        messages: {
            applepay_email: {
                required: "Apple Pay email field is required",
            },
            applepay_key: {
                required: "Apple Pay key field is required",
            },
            applepay_secret: {
                required: "Apple Pay secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_applepay").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#PayoneerSettingForm").validate({
        rules: {
            payoneer_email: {
                required: true,
            },
            payoneer_key: {
                required: true,
            },
            payoneer_secret: {
                required: true,
            },
        },
        messages: {
            payoneer_email: {
                required: "Payoneer email field is required",
            },
            payoneer_key: {
                required: "Payoneer key field is required",
            },
            payoneer_secret: {
                required: "Payoneer secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_payoneer").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#MercadoPagoSettingForm").validate({
        rules: {
            mercadopago_email: {
                required: true,
            },
            mercadopago_key: {
                required: true,
            },
            mercadopago_secret: {
                required: true,
            },
        },
        messages: {
            mercadopago_email: {
                required: "Mercado Pago email field is required",
            },
            mercadopago_key: {
                required: "Mercado Pago key field is required",
            },
            mercadopago_secret: {
                required: "Mercado Pago secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_mercadopago").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#PaymentSettingForm").validate({
        rules: {
            payment_email: {
                required: true,
            },
            payment_key: {
                required: true,
            },
            payment_secret: {
                required: true,
            },
        },
        messages: {
            payment_email: {
                required: "Payment email field is required",
            },
            payment_key: {
                required: "Payment key field is required",
            },
            payment_secret: {
                required: "Payment secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_payment").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#MidtransSettingForm").validate({
        rules: {
            midtrans_email: {
                required: true,
            },
            midtrans_key: {
                required: true,
            },
            midtrans_secret: {
                required: true,
            },
        },
        messages: {
            midtrans_email: {
                required: "Midtrans email field is required",
            },
            midtrans_key: {
                required: "Midtrans key field is required",
            },
            midtrans_secret: {
                required: "Midtrans secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_midtrans").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#PyTorchSettingForm").validate({
        rules: {
            pytorch_email: {
                required: true,
            },
            pytorch_key: {
                required: true,
            },
            pytorch_secret: {
                required: true,
            },
        },
        messages: {
            pytorch_email: {
                required: "PyTorch email field is required",
            },
            pytorch_key: {
                required: "PyTorch key field is required",
            },
            pytorch_secret: {
                required: "PyTorch secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_pytorch").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#BankSettingForm").validate({
        rules: {
            bank_email: {
                required: true,
            },
            bank_key: {
                required: true,
            },
            bank_secret: {
                required: true,
            },
        },
        messages: {
            bank_email: {
                required: "Bank email field is required",
            },
            bank_key: {
                required: "Bank key field is required",
            },
            bank_secret: {
                required: "Bank secret field is required",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_bank").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });

    $("#CODSettingForm").validate({
        rules: {
            cod_email: {
                required: true,
                email: false,
            },
            cod_fee: {
                required: true,
                number: true,
            },
            cod_notes: {
                maxlength: 500,
            },
        },
        messages: {
            cod_email: {
                required: "Cash on Delivery email field is required",
                email: "Please enter a valid email address",
            },
            cod_fee: {
                required: "COD fee field is required",
                number: "COD fee must be a valid number",
            },
            cod_notes: {
                maxlength: "Notes cannot exceed 500 characters",
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
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
                        $("#add_cod").modal("hide");
                    }
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");
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
        },
    });
});

})();

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
                showToast("success", "Payment status updated successfully!");
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
            $(".real-label, .real-table, .real-data, .real-card").removeClass("d-none");
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
