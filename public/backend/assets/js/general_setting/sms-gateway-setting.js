(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        loadSMSSetting();
       

        $(document).ready(function () {
            $("#addNexmoForm").validate({
                rules: {
                    nexmo_api_key: {
                        required: true,
                        minlength: 6,
                        maxlength: 32,
                    },
                    nexmo_secret_key: {
                        required: true,
                        minlength: 8,
                        maxlength: 64,
                    },
                    nexmo_sender_id: {
                        required: true,
                        minlength: 3,
                        maxlength: 11,
                    },
                },
                messages: {
                    nexmo_api_key: {
                        required: _l(
                            "admin.general_settings.enter_nexmo_api_key"
                        ),
                        minlength: _l(
                            "admin.general_settings.nexmo_key_characters"
                        ),
                        maxlength: _l("admin.general_settings.api_key_exceed"),
                    },
                    nexmo_api_secret: {
                        required: _l(
                            "admin.general_settings.enter_nexmo_api_secret"
                        ),
                        minlength: _l(
                            "admin.general_settings.nexmo_secret_characters"
                        ),
                        maxlength: _l(
                            "admin.general_settings.api_secret_exceed"
                        ),
                    },
                    nexmo_sender_id: {
                        required: _l("admin.general_settings.enter_sender_id"),
                        minlength: _l(
                            "admin.general_settings.sender_id_characters"
                        ),
                        maxlength: _l(
                            "admin.general_settings.sender_id_exceed"
                        ),
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
                    let nexmoData = new FormData(form);
                    $(".btn-primary")
                        .text("Please Wait...")
                        .prop("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "/admin/settings/sms-store",
                        data: nexmoData,
                        processData: false,
                        contentType: false,
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (resp) {
                            loadSMSSetting();
                            if (resp.code === 200) {
                                showToast("success", resp.message);
                                $("#add_nexmo").modal("hide");
                                $(".btn-primary")
                                    .text("Save Changes")
                                    .prop("disabled", false);
                            }
                        },
                        error: function (error) {
                            $(".error-text").text("");
                            $(".form-control").removeClass(
                                "is-invalid is-valid"
                            );

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

                            $(".btn-primary")
                                .text(_l("admin.common.save_changes"))
                                .prop("disabled", false);
                        },
                    });
                },
            });

            $("#addTwilioForm").validate({
                rules: {
                    twilio_api_key: {
                        required: true,
                        minlength: 6,
                        maxlength: 64,
                    },
                    twilio_secret_key: {
                        required: true,
                        minlength: 8,
                        maxlength: 64,
                    },
                    twilio_sender_id: {
                        required: true,
                        minlength: 3,
                        maxlength: 15,
                    },
                },
                messages: {
                    twilio_api_key: {
                        required: _l(
                            "admin.general_settings.twilio_api_key_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twilio_api_key_minlength"
                        ),
                        maxlength: _l(
                            "admin.twilio_api_key_maxlength.enter_nexmo_api_key"
                        ),
                    },
                    twilio_secret_key: {
                        required: _l(
                            "admin.general_settings.twilio_secret_key_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twilio_secret_key_minlength"
                        ),
                        maxlength: _l(
                            "admin.general_settings.twilio_secret_key_maxlength"
                        ),
                    },
                    twilio_sender_id: {
                        required: _l(
                            "admin.general_settings.twilio_sender_id_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twilio_sender_id_minlength"
                        ),
                        maxlength: _l(
                            "admin.general_settings.twilio_sender_id_maxlength"
                        ),
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
                    let twilioData = new FormData(form);
                    $(".btn-primary")
                        .text(_l("admin.common.please_wait"))
                        .prop("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "/admin/settings/sms-store",
                        data: twilioData,
                        processData: false,
                        contentType: false,
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (resp) {
                            loadSMSSetting();
                            if (resp.code === 200) {
                                showToast("success", resp.message);
                                $(".btn-primary")
                                    .text(_l("admin.common.save_changes"))
                                    .prop("disabled", false);
                                $("#add_twilio").modal("hide");
                                $("#addTwilioForm")[0].reset();
                                $(".form-control").removeClass(
                                    "is-invalid is-valid"
                                );
                                $(".error-text").text("");
                            }
                        },
                        error: function (error) {
                            $(".error-text").text("");
                            $(".form-control").removeClass(
                                "is-invalid is-valid"
                            );

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

                            $(".btn-primary")
                                .text(_l("admin.common.save_changes"))
                                .prop("disabled", false);
                        },
                    });
                },
            });

            $("#add2FactorForm").validate({
                rules: {
                    twofactor_api_key: {
                        required: true,
                        minlength: 6,
                        maxlength: 64,
                    },
                    twofactor_secret_key: {
                        required: true,
                        minlength: 8,
                        maxlength: 64,
                    },
                    twofactor_sender_id: {
                        required: true,
                        minlength: 3,
                        maxlength: 15,
                    },
                },
                messages: {
                    twofactor_api_key: {
                        required: _l(
                            "admin.general_settings.twofactor_api_key_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twofactor_api_key_minlength"
                        ),
                        maxlength: _l(
                            "admin.general_settings.twofactor_api_key_maxlength"
                        ),
                    },
                    twofactor_secret_key: {
                        required: _l(
                            "admin.general_settings.twofactor_secret_key_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twofactor_secret_key_minlength"
                        ),
                        maxlength: _l(
                            "admin.general_settings.twofactor_secret_key_maxlength"
                        ),
                    },
                    twofactor_sender_id: {
                        required: _l(
                            "admin.general_settings.twofactor_sender_id_required"
                        ),
                        minlength: _l(
                            "admin.general_settings.twofactor_sender_id_minlength"
                        ),
                        maxlength: _l(
                            "admin.general_settings.twofactor_sender_id_maxlength"
                        ),
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
                    let twoFactorData = new FormData(form);
                    $(".btn-primary")
                        .text(_l("admin.common.please_wait"))
                        .prop("disabled", true);

                    $.ajax({
                        type: "POST",
                        url: "/admin/settings/sms-store",
                        data: twoFactorData,
                        processData: false,
                        contentType: false,
                        headers: {
                            Accept: "application/json",
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                "content"
                            ),
                        },
                        success: function (resp) {
                            loadSMSSetting();
                            if (resp.code === 200) {
                                showToast("success", resp.message);
                                $(".btn-primary")
                                    .text(_l("admin.common.save_changes"))
                                    .prop("disabled", false);
                                $("#add_2factor").modal("hide");
                                $("#add2FactorForm")[0].reset();
                                $(".form-control").removeClass(
                                    "is-invalid is-valid"
                                );
                                $(".error-text").text("");
                            }
                        },
                        error: function (error) {
                            $(".error-text").text("");
                            $(".form-control").removeClass(
                                "is-invalid is-valid"
                            );

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

                            $(".btn-primary")
                                .text(_l("admin.common.save_changes"))
                                .prop("disabled", false);
                        },
                    });
                },
            });
             $(".gateway-switch").on("change", function () {
            const gateway = $(this).attr("name");
            const status = $(this).prop("checked") ? 1 : 0;

            $.ajax({
                url: "/admin/settings/status-update",
                method: "POST",
                data: {
                    gateway: gateway,
                    status: status,
                },
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    loadSMSSetting();
                },
                error: function (err) {
                    showToast("error", _l("admin.common.default_update_error"));
                },
            });
        });
        });

        function loadSMSSetting() {
            $.ajax({
                url: "/admin/settings/sms-list",
                type: "POST",
                data: { type: 2 },
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {
                    if (response.code === 200) {
                        const requiredKeys = [
                            "nexmo_api_key",
                            "nexmo_secret_key",
                            "nexmo_sender_id",
                            "nexmo_status",
                            "twofactor_api_key",
                            "twofactor_secret_key",
                            "twofactor_sender_id",
                            "twofactor_status",
                            "twilio_api_key",
                            "twilio_secret_key",
                            "twilio_sender_id",
                            "twilio_status",
                        ];

                        const filteredSettings = response.data.settings.filter(
                            (setting) => requiredKeys.includes(setting.key)
                        );

                        filteredSettings.forEach((setting) => {
                            if (setting.key === "nexmo_status") {
                                if (setting.value === "1") {
                                    $("#nexmo-switch").prop("checked", true);
                                } else {
                                    $("#nexmo-switch").prop("checked", false);
                                }
                            } else {
                                $("#" + setting.key).val(setting.value);
                            }
                        });

                        filteredSettings.forEach((setting) => {
                            if (setting.key === "twofactor_status") {
                                if (setting.value === "1") {
                                    $("#twofactor-switch").prop(
                                        "checked",
                                        true
                                    );
                                } else {
                                    $("#twofactor-switch").prop(
                                        "checked",
                                        false
                                    );
                                }
                            } else {
                                $("#" + setting.key).val(setting.value);
                            }
                        });
                        filteredSettings.forEach((setting) => {
                            if (setting.key === "twilio_status") {
                                if (setting.value === "1") {
                                    $("#twilio-switch").prop("checked", true);
                                } else {
                                    $("#twilio-switch").prop("checked", false);
                                }
                            } else {
                                $("#" + setting.key).val(setting.value);
                            }
                        });
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON.code === 404) {
                        toastr.error(xhr.responseJSON.message);
                    }
                },
                complete: function () {
                    $(".label-loader, .input-loader, .card-loader").hide();
                    $(".real-label, .real-input, .real-card").removeClass(
                        "d-none"
                    );
                },
            });
        }
    });
})();
