(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        loadEmailSettings();

        $("#php_mailer_form").validate({
            rules: {
                phpmail_from_email: {
                    required: true,
                    email: true,
                },
                phpmail_password: {
                    required: true,
                },
                phpmail_from_name: {
                    required: true,
                },
            },
            messages: {
                phpmail_from_email: {
                    required: _l(
                        "admin.general_settings.from_email_address_required"
                    ),
                    email: _l(
                        "admin.general_settings.from_email_address_valid"
                    ),
                },
                phpmail_password: {
                    required: _l(
                        "admin.general_settings.email_password_required"
                    ),
                },
                phpmail_from_name: {
                    required: _l(
                        "admin.general_settings.from_email_name_required"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    let errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    let errorId = element.attr("id") + "_error";
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
                formData.append("type", "phpmail");

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/email-settings-store",
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
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html($(".submitBtn").data("submit"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#phpmailersettings").modal("hide");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html($(".submitBtn").data("submit"));
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

        $("#smtp_form").validate({
            rules: {
                smtp_from_email: {
                    required: true,
                    email: true,
                    maxlength: 50,
                },
                smtp_password: {
                    required: true,
                },
                smtp_from_name: {
                    required: true,
                },
                smtp_host: {
                    required: true,
                },
                smtp_port: {
                    required: true,
                    maxlength: 6,
                },
            },
            messages: {
                smtp_from_email: {
                    required: _l(
                        "admin.general_settings.from_email_address_required"
                    ),
                    email: _l(
                        "admin.general_settings.from_email_address_valid"
                    ),
                    maxlength: "",
                },
                smtp_password: {
                    required: _l(
                        "admin.general_settings.email_password_required"
                    ),
                },
                smtp_from_name: {
                    required: _l(
                        "admin.general_settings.from_email_name_required"
                    ),
                },
                smtp_host: {
                    required: _l("admin.general_settings.email_host_required"),
                },
                smtp_port: {
                    required: _l("admin.general_settings.port_required"),
                    maxlength: _l("admin.general_settings.smtp_port_maxlength"),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    let errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    let errorId = element.attr("id") + "_error";
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
                formData.append("type", "smtp");

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/email-settings-store",
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
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.submit"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#smtpsettings").modal("hide");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.submit"));
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

        $("#sendgrid_form").validate({
            rules: {
                sendgrid_from_email: {
                    required: true,
                    email: true,
                },
                sendgrid_key: {
                    required: true,
                },
            },
            messages: {
                sendgrid_from_email: {
                    required: _l(
                        "admin.general_settings.from_email_address_required"
                    ),
                    email: _l(
                        "admin.general_settings.from_email_address_valid"
                    ),
                },
                sendgrid_key: {
                    required: _l(
                        "admin.general_settings.send_grid_key_required"
                    ),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    let errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    let errorId = element.attr("id") + "_error";
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
                formData.append("type", "sendgrid");

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/email-settings-store",
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
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.submit"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#sendgrid").modal("hide");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.submit"));
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

        $("#test_mail_form").validate({
            rules: {
                email_address: {
                    required: true,
                    email: true,
                },
            },
            messages: {
                email_address: {
                    required: _l(
                        "admin.general_settings.email_address_required"
                    ),
                    email: _l("admin.common.email_valid"),
                },
            },
            errorPlacement: function (error, element) {
                if (element.hasClass("select2-hidden-accessible")) {
                    let errorId = element.attr("id") + "_error";
                    $("#" + errorId).text(error.text());
                } else {
                    let errorId = element.attr("id") + "_error";
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
                    url: "/admin/settings/send-test-mail",
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
                            "admin.common.sending"
                        )}..
                    `);
                    },
                    success: function (resp) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.send"));
                        if (resp.code === 200) {
                            showToast("success", resp.message);
                            $("#testmail").modal("hide");
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $(".submitBtn")
                            .removeAttr("disabled")
                            .html(_l("admin.common.send"));
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
    });

    $(document).on("click", "#send_test_email_btn", function () {
        $("#test_mail_form").trigger("reset");
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    });

    $("#smtp_port").on("input", function () {
        let value = $(this).val();
        $(this).val(value.replace(/\D/g, ""));
    });

    $(document).on("click", ".configure-btn", function () {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");
    });

    $(document).on("change", ".status-switch", function () {
        const gateway = $(this).attr("name");
        const status = $(this).prop("checked") ? 1 : 0;

        $(this)
            .closest(".card")
            .find(".status-text")
            .html(
                `<i class="ti ti-point-filled text-success"></i> ${_l(
                    "admin.general_settings.connected"
                )}`
            );

        $.ajax({
            url: "/admin/settings/status-update",
            method: "POST",
            data: {
                gateway: gateway,
                status: status,
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    showToast("success", response.message);
                    loadEmailSettings();
                }
            },
            error: function (err) {
                if (err.responseJSON.code === 500) {
                    showToast("error", err.responseJSON.message);
                } else {
                    showToast("error", _l("admin.common.default_update_error"));
                }
            },
        });
    });

    function loadEmailSettings() {
        $.ajax({
            url: "/admin/settings/email-settings-list",
            type: "POST",
            data: { type: 1 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    const requiredKeys = [
                        "phpmail_from_email",
                        "phpmail_password",
                        "phpmail_from_name",
                        "phpmail_status",
                        "smtp_from_email",
                        "smtp_password",
                        "smtp_from_name",
                        "smtp_host",
                        "smtp_port",
                        "smtp_status",
                        "sendgrid_from_email",
                        "sendgrid_key",
                        "sendgrid_status",
                    ];

                    const filteredSettings = response.data.settings.filter(
                        (setting) => requiredKeys.includes(setting.key)
                    );

                    filteredSettings.forEach((setting) => {
                        if (setting.key === "phpmail_status") {
                            if (setting.value === "1") {
                                $("#phpmail_status").prop("checked", true);
                                $(".phpmail-status-text").html(
                                    `<i class="ti ti-point-filled text-success"></i> ${_l(
                                        "admin.general_settings.connected"
                                    )}`
                                );
                            } else {
                                $("#phpmail_status").prop("checked", false);
                                $(".phpmail-status-text").html(
                                    `<i class="ti ti-point-filled text-danger"></i> ${_l(
                                        "admin.general_settings.disconnected"
                                    )}`
                                );
                            }
                        } else if (setting.key === "smtp_status") {
                            if (setting.value === "1") {
                                $("#smtp_status").prop("checked", true);
                                $(".smtp-status-text").html(
                                    `<i class="ti ti-point-filled text-success"></i> ${_l(
                                        "admin.general_settings.connected"
                                    )}`
                                );
                            } else {
                                $("#smtp_status").prop("checked", false);
                                $(".smtp-status-text").html(
                                    `<i class="ti ti-point-filled text-danger"></i> ${_l(
                                        "admin.general_settings.disconnected"
                                    )}`
                                );
                            }
                        } else if (setting.key === "sendgrid_status") {
                            if (setting.value === "1") {
                                $("#sendgrid_status").prop("checked", true);
                                $(".sendgrid-status-text").html(
                                    `<i class="ti ti-point-filled text-success"></i> ${_l(
                                        "admin.general_settings.connected"
                                    )}`
                                );
                            } else {
                                $("#sendgrid_status").prop("checked", false);
                                $(".sendgrid-status-text").html(
                                    `<i class="ti ti-point-filled text-danger"></i> ${_l(
                                        "admin.general_settings.disconnected"
                                    )}`
                                );
                            }
                        } else {
                            $("#" + setting.key).val(setting.value);
                        }
                    });
                }
            },
            error: function (xhr) {
                if (xhr.responseJSON.code === 500) {
                    showToast("error", xhr.responseJSON.message);
                }
            },
            complete: function () {
                $(
                    ".label-loader, .input-loader, .card-loader, .button-loader"
                ).hide();
                $(
                    ".real-label, .real-input, .real-button, .real-card"
                ).removeClass("d-none");
            },
        });
    }
})();
