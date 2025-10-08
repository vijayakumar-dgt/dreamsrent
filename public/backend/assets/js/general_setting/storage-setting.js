(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        const localStorageSwitch = $("#local_storage");
        const awsStorageSwitch = $("#aws_storage");

        localStorageSwitch.on("change", function () {
            updateStorageSettings("local_storage", this.checked);
        });

        awsStorageSwitch.on("change", function () {
            updateStorageSettings("aws_storage", this.checked);
        });

        function updateStorageSettings(type, isEnabled) {
            const payload = {
                storage_type: type,
                status: isEnabled ? 1 : 0,
            };

            $.ajax({
                url: "/admin/settings/storageupdate",
                method: "POST",
                contentType: "application/json",
                data: JSON.stringify(payload),
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (data) {
                    if (data.success) {
                        showToast("success", data.message);
                        if (typeof loadStorageSettings === "function") {
                            loadStorageSettings();
                        }
                    } else {
                        showToast(
                            "error",
                            data.message ||
                                _l("admin.general_setting.fail_storage_setting")
                        );
                    }
                },
                error: function (xhr) {
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        showToast("error", xhr.responseJSON.message);
                    } else {
                        showToast(
                            "error",
                            _l("admin.general_setting.fail_storage_setting")
                        );
                    }
                },
            });
        }
    });

    $(document).ready(function () {
        $("#awsSettingForm").validate({
            rules: {
                aws_access_key: {
                    required: true,
                    minlength: 10,
                },
                aws_secret_key: {
                    required: true,
                    minlength: 10,
                },
                aws_bucket_name: {
                    required: true,
                },
                aws_region: {
                    required: true,
                },
                aws_base_url: {
                    required: true,
                    url: true,
                },
            },
            messages: {
                aws_access_key: {
                    required: _l("admin.general_settings.enter_aws_access_key"),
                    minlength: _l(
                        "admin.general_settings.enter_aws_access_key"
                    ),
                },
                aws_secret_key: {
                    required: _l("admin.general_settings.enter_aws_secret_key"),
                    minlength: _l(
                        "admin.general_settings.enter_aws_access_key"
                    ),
                },
                aws_bucket_name: {
                    required: _l(
                        "admin.general_settings.enter_aws_bucket_name"
                    ),
                },
                aws_region: {
                    required: _l("admin.general_settings.enter_aws_region"),
                },
                aws_base_url: {
                    required: _l("admin.general_settings.enter_aws_base_url"),
                    url: _l("admin.general_settings.enter_valid_url"),
                },
            },
            errorPlacement: function (error, element) {
                let errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
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
                let awsData = new FormData(form);
                $(".btn-primary")
                    .text(_l("admin.general_settings.please_wait"))
                    .prop("disabled", true);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/aws/store",
                    data: awsData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                    },
                    success: function (resp) {
                        if (resp.code === 200) {
                            loadStorageSettings();
                            showToast("success", resp.message);
                            $("#aws_settings").modal("hide"); // Close modal if needed
                            $(".btn-primary")
                                .text(_l("admin.common.submit"))
                                .prop("disabled", false);
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
                            showToast("error", error.responseJSON.message);
                        }

                        $(".btn-primary")
                            .text(_l("admin.common.submit"))
                            .prop("disabled", false);
                    },
                });
            },
        });
    });

    loadStorageSettings();

    function loadStorageSettings() {
        $.ajax({
            url: "/admin/settings/company/list",
            type: "POST",
            data: { group_id: 8 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach((setting) => {
                        const element = $("#" + setting.key);
                        if (
                            element.length &&
                            element.attr("type") === "checkbox"
                        ) {
                            element.prop("checked", setting.value === "1");
                        }
                    });

                    const awsSettings = {
                        aws_access_key: "",
                        aws_secret_key: "",
                        aws_bucket_name: "",
                        aws_region: "",
                        aws_base_url: "",
                    };

                    settings.forEach((setting) => {
                        if (awsSettings.hasOwnProperty(setting.key)) {
                            awsSettings[setting.key] = setting.value;
                        }
                    });

                    $("#aws_access_key").val(awsSettings.aws_access_key);
                    $("#aws_secret_key").val(awsSettings.aws_secret_key);
                    $("#aws_bucket_name").val(awsSettings.aws_bucket_name);
                    $("#aws_region").val(awsSettings.aws_region);
                    $("#aws_base_url").val(awsSettings.aws_base_url);
                }
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }
})();
