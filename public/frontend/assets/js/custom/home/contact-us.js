/* global $, loadTranslationFile, window, showToast, _l, FormData */

(async () => {
    "use strict";

    await loadTranslationFile("web", "home, common");

    const $userPhoneInput = $("#contact_phone");
    const $intlPhoneInput = $("#international_phone_number");
    const $userProfileForm = $("#contactForm");
    const $submitBtn = $("#contactForm .submitbtn");

    let iti;

    if ($userPhoneInput.length && $userProfileForm.length) {
        iti = window.intlTelInput($userPhoneInput[0], {
            utilsScript: `${window.location.origin}/frontend/assets/plugins/intltelinput/js/utils.js`,
            separateDialCode: true,
        });

        $userPhoneInput.addClass("iti");
        $userPhoneInput.parent().addClass("intl-tel-input");

        const updateIntlNumber = () => {
            $intlPhoneInput.val(iti.getNumber());
        };

        $userPhoneInput.on("keyup countrychange", updateIntlNumber);
    }

    $userProfileForm.validate({
        rules: {
            contact_name: {
                required: true,
                minlength: 3,
                maxlength: 30,
            },
            contact_email: {
                required: true,
                email: true,
                maxlength: 50,
            },
            contact_phone: {
                required: true,
                minlength: 10,
                maxlength: 15,
            },
            contact_comments: {
                required: true,
                minlength: 3,
            },
        },
        messages: {
            contact_name: {
                required: _l("web.home.name_required"),
                minlength: _l("web.common.minlength_3", { min: 3 }),
                maxlength: _l("web.common.maxlength_30", { max: 30 }),
            },
            contact_email: {
                required: _l("web.home.email_required"),
                email: _l("web.home.valid_email"),
                maxlength: _l("web.home.email_max_length"),
            },
            contact_phone: {
                required: _l("web.home.phone_number_required"),
                minlength: _l("web.home.phone_number_minlength"),
                maxlength: _l("web.home.phone_number_maxlength"),
            },
            contact_comments: {
                required: _l("web.home.message_required"),
                minlength: _l("web.home.message_minlength"),
            },
        },
        errorPlacement: function (error, element) {
            $("#" + element.attr("id") + "_error").text(error.text());
        },
        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
            $("#" + element.id + "_error").text("");
        },
        onkeyup: function (element) {
            $(element).valid();
        },
        onchange: function (element) {
            $(element).valid();
        },
        submitHandler: function () {
            const formData = new FormData();
            formData.append("name", $("#contact_name").val());
            formData.append("email", $("#contact_email").val());
            formData.append("phone_number", $intlPhoneInput.val());
            formData.append("message", $("#contact_comments").val());
            formData.append("_token", $('meta[name="csrf-token"]').attr("content"));

            $submitBtn
                .html(`<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("web.common.saving")}..`)
                .attr("disabled", true);

            $.ajax({
                type: "POST",
                url: "/api/contact-message/save",
                data: formData,
                processData: false,
                contentType: false,
                success: function (resp) {
                    if (resp.code === 200) {
                        showToast("success", resp.message);
                    }
                    $userProfileForm[0].reset();
                    $submitBtn.text("Send Enquiry").prop("disabled", false);
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");

                    if (error.responseJSON?.code === 422) {
                        $.each(error.responseJSON.errors, function (key, val) {
                            const $field = $("#" + key);
                            $field.addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
                    } else {
                        showToast("error", error.responseJSON?.message || "Something went wrong");
                    }

                    $submitBtn.text("Send Enquiry").prop("disabled", false);
                },
            });
        },
    });
})();