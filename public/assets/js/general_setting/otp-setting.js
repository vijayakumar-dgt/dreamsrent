(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

    $(".label-loader, .input-loader").hide();
$('.real-label, .real-input').removeClass('d-none');
$(document).ready(function () {
    $("#otpSettingForm").validate({
        rules: {
            "otp_type[]": {
                required: true
            },
            otp_digit_limit: {
                required: true,
                digits: true
            },
            otp_expire_time: {
                required: true
            },
            login: {
                required: false
            },
            register: {
                required: false
            }
        },
        messages: {
            "otp_type[]": {
                required:  _l('admin.general_settings.select_otp_type'),
            },
            otp_digit_limit: {
                required:  _l('admin.general_settings.otp_digit_limit'),
                digits: _l('admin.general_settings.only_numbers_allowed')
            },
            otp_expire_time: {
                required: _l('admin.general_settings.otp_expiry_time')
            }
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
            let otpData = new FormData(form);


            otpData.set("login", $("#login").is(":checked") ? "1" : "0");
            otpData.set("register", $("#register").is(":checked") ? "1" : "0");



            $.ajax({
                type: "POST",
                url: "/admin/settings/otp/update",
                data: otpData,
                processData: false,
                contentType: false,
                headers: {
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
                },
                beforeSend: function () {
                    $('.submitBtn').attr('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                    `);
                },
                complete: function () {
                    $('.submitBtn').attr('disabled', false).html(_l('admin.common.save_changes'));
                },

                success: function (resp) {
                    if (resp.code === 200) {
                        loadOtpSettings();
                        showToast("success", resp.message);

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
                        showToast("error", error.responseJSON.message);
                    }


                }
            });
        }
    });

    loadOtpSettings();

    function loadOtpSettings() {
        $.ajax({
            url: '/admin/settings/company/list',
            type: "POST",
            data: { 'group_id': 15 },
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach(setting => {
                        const element = $("#" + setting.key);

                        if (element.length) {
                            if (element.is(":checkbox")) {

                                element.prop("checked", setting.value == 1);
                            } else if (element.is("select")) {

                                const optionExists = element.find(`option[value="${setting.value}"]`).length > 0;

                                if (!optionExists) {
                                    element.append(`<option value="${setting.value}">${setting.value}</option>`);
                                }


                                element.val(setting.value).trigger('change');
                            } else {

                                element.val(setting.value);
                            }
                        }
                    });
                }
            },

            error: function (xhr) {
                showToast('error', _l('admin.common.default_retrieve_error'));
            },
            complete: function () {
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-input").removeClass("d-none");
            }
        });
    }
});

})();


