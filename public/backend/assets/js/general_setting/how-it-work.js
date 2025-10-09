(async () => {
    "use strict";
    await loadTranslationFile("admin", "cms,common");

    $(document).ready(function () {
        $("#language").on("change", function () {
            loadHowItWorksSettings($(this).val());
        });

        $(".summernote").summernote({
            height: 300,
            placeholder: _l("admin.cms.enter_your_description"),
            toolbar: [
                ["style", ["bold", "italic", "underline", "clear"]],
                ["para", ["ul", "ol", "paragraph"]],
                ["insert", ["link", "picture", "video"]],
                ["view", ["fullscreen", "codeview", "help"]],
            ],
        });

        // Initialize form
        initHowItWorkForm();

        loadHowItWorksSettings();
    });

    function initHowItWorkForm() {
        const formSelector = "#howItWorkForm";
        const submitBtnSelector = `${formSelector} .submitbtn`;
        const csrfToken = $('meta[name="csrf-token"]').attr("content");

        const handleErrorResponse = (error) => {
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
        };

        const handleSuccessResponse = (resp) => {
            if (resp.code === 200) {
                loadHowItWorksSettings();
                showToast("success", resp.message);
            }
        };

        const ajaxSubmit = (formData) => {
            $.ajax({
                type: "POST",
                url: "/admin/how-it-works/update",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken,
                },
                beforeSend: () => {
                    $(submitBtnSelector).attr("disabled", true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
                    `);
                },
                complete: () => {
                    $(submitBtnSelector).attr("disabled", false).html(_l("admin.common.save_changes"));
                },
                success: handleSuccessResponse,
                error: handleErrorResponse,
            });
        };

        const resetFieldErrors = (element) => {
            $(element).removeClass("is-invalid").addClass("is-valid");
            $("#" + element.id + "_error").text("");
        };

        $(formSelector).validate({
            rules: {
                maintenance_description: {
                    required: true,
                    minlength: 10,
                },
            },
            messages: {
                maintenance_description: {
                    required: _l("admin.cms.description_required"),
                    minlength: _l("admin.cms.description_minlength"),
                },
            },
            errorPlacement: (error, element) => {
                $("#" + element.attr("id") + "_error").text(error.text());
            },
            highlight: (element) => $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: resetFieldErrors,
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: (form) => {
                const formData = new FormData(form);
                ajaxSubmit(formData);
            },
        });
    }

    function loadHowItWorksSettings(languageId = null) {
        const selectedLanguageId = languageId || $("#language").val();

        $.ajax({
            url: "/admin/how-it-works/list",
            type: "POST",
            data: {
                group_id: 10,
                language_id: selectedLanguageId,
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                const setting = response.data;

                if (setting === null) {
                    $("#howitwork_description").summernote("code", "");
                    $("#profile_photo_preview").attr("src", "").hide();
                    return;
                }

                if (setting.key === "maintenance_image" && setting.value) {
                    const imageUrl = `/storage/${setting.value}`;
                    $("#profile_photo_preview").attr("src", imageUrl).show();
                }

                if (setting.key === `how_it_works_${setting.language_id}`) {
                    $("#howitwork_description").summernote(
                        "code",
                        setting.value
                    );
                } else {
                    const element = $("#" + setting.key);
                    if (element.length) {
                        element.val(setting.value);
                    }
                }
            },
            error: function (xhr) {
                showToast("error", xhr.responseJSON.message);
            },
            complete: function () {
                $(".label-loader, .input-loader").hide();
                $(".real-label, .real-input").removeClass("d-none");
            },
        });
    }
})();
