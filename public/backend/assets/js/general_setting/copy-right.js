(async () => {
    "use strict";
    await loadTranslationFile("admin", "cms,common");

    $(document).ready(function () {
        $("#language").on("change", function () {
            loadCopyRightSettings($(this).val());
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

        // Initialize the form
        initCopyRightForm();

        loadCopyRightSettings();
    });

    // Initialize form validation
    function initCopyRightForm() {
        $("#copyRightForm").validate({
            rules: getCopyRightRules(),
            messages: getCopyRightMessages(),
            errorPlacement: handleCopyRightErrorPlacement,
            highlight: handleHighlight,
            unhighlight: handleUnhighlight,
            onkeyup: (el) => $(el).valid(),
            onchange: (el) => $(el).valid(),
            submitHandler: submitCopyRightForm,
        });
    }

    // Validation rules
    function getCopyRightRules() {
        return {
            copy_right_description: { required: true, minlength: 10 },
            language: { required: true },
        };
    }

    // Validation messages
    function getCopyRightMessages() {
        return {
            copy_right_description: {
                required: _l("admin.cms.description_required"),
                minlength: _l("admin.cms.description_minlength"),
            },
            language: {
                required: _l("admin.cms.language_required"),
            },
        };
    }

    // Error placement
    function handleCopyRightErrorPlacement(error, element) {
        const errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    }

    // Highlight invalid fields
    function handleHighlight(element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    }

    // Unhighlight valid fields
    function handleUnhighlight(element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        $("#" + element.id + "_error").text("");
    }

    // Submit form via AJAX
    function submitCopyRightForm(form) {
        const copyRightData = new FormData(form);

        $.ajax({
            type: "POST",
            url: "/admin/copyright/update",
            data: copyRightData,
            processData: false,
            contentType: false,
            headers: getAjaxHeaders(),
            beforeSend: showCopyRightSavingState,
            complete: hideCopyRightSavingState,
            success: handleCopyRightSuccess,
            error: handleCopyRightError,
        });
    }

    // AJAX headers
    function getAjaxHeaders() {
        return {
            Accept: "application/json",
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        };
    }

    // Show loader
    function showCopyRightSavingState() {
        $(".submitbtn").attr("disabled", true).html(`
            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
        `);
    }

    // Hide loader
    function hideCopyRightSavingState() {
        $(".submitbtn").attr("disabled", false).html(_l("admin.common.save_changes"));
    }

    // Success handler
    function handleCopyRightSuccess(resp) {
        if (resp.code === 200) {
            loadCopyRightSettings();
            showToast("success", resp.message);
        }
    }

    // Error handler
    function handleCopyRightError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON?.code === 422) {
            Object.entries(error.responseJSON.errors).forEach(([key, val]) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "_error").text(val[0]);
            });
        } else {
            showToast("error", error.responseJSON?.message || "Something went wrong");
        }
    }

    function loadCopyRightSettings(languageId = null) {
        const selectedLanguageId = languageId || $("#language").val();
        $.ajax({
            url: "/admin/copyright/list",
            type: "POST",
            data: {
                group_id: 20,
                language_id: selectedLanguageId,
            },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (response) {
                const setting = response.data;

                if (setting === null) {
                    $("#copy_right_description").summernote("code", "");
                    $("#profile_photo_preview").attr("src", "").hide();
                    return;
                }

                if (setting.key === "maintenance_image" && setting.value) {
                    const imageUrl = `/storage/${setting.value}`;
                    $("#profile_photo_preview").attr("src", imageUrl).show();
                }

                if (setting.key === `copy_right_${setting.language_id}`) {
                    $("#copy_right_description").summernote(
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
                showToast("error", _l("admin.common.default_retrieve_error"));
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass("d-none");
            },
        });
    }
})();
