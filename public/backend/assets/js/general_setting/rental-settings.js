(async () => {
    "use strict";
    await loadTranslationFile("admin", "general_settings,common");

    $(document).ready(function () {
        // Initialize validation
        $("#rentalSettingForm").validate({
            rules: {},
            messages: {},
            errorPlacement: (error, element) => {
                $("#" + element.attr("id") + "Error").text(error.text()).removeClass("d-none");
            },
            highlight: (element) => $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $("#" + element.id + "Error").text("").addClass("d-none");
            },
            submitHandler: (form) => {
                const rentalData = new FormData(form);
                setCheckboxValues(rentalData, "#rentalSettingForm");

                const submitBtn = $(".submitBtn");
                submitBtn.attr("disabled", true).html(`
                    <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("admin.common.saving")}..
                `);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/rental/update",
                    data: rentalData,
                    processData: false,
                    contentType: false,
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                    },
                })
                .done(handleRentalSuccess)
                .fail(handleRentalError)
                .always(() => {
                    submitBtn.attr("disabled", false).html(_l("admin.common.save_changes"));
                });
            },
        });

        loadRentalSettings();
    });

    // Helper: Handle AJAX success
    function handleRentalSuccess(resp) {
        if (resp.code === 200) {
            loadRentalSettings();
            showToast("success", resp.message);
        }
    }

    // Helper: Handle AJAX error
    function handleRentalError(error) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON.code === 422) {
            $.each(error.responseJSON.errors, (key, val) => {
                $("#" + key).addClass("is-invalid");
                $("#" + key + "Error").text(val[0]).removeClass("d-none");
            });
        } else {
            showToast("error", error.responseJSON.message);
        }
    }

    // Helper: Prepare checkbox values
    function setCheckboxValues(formData, formSelector) {
        $(`${formSelector} input[type='checkbox']`).each(function () {
            formData.set($(this).attr("name"), $(this).is(":checked") ? "1" : "0");
        });
    }

    function loadRentalSettings() {
        $.ajax({
            url: "/admin/settings/company/list",
            type: "POST",
            data: { group_id: 20 },
            headers: {
                Accept: "application/json",
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                    "content"
                ),
            },
            success: function (response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach((setting) => {
                        const element = $("#" + setting.key);

                        if (element.length) {
                            if (element.is(":checkbox")) {
                                element.prop("checked", setting.value == 1);
                            } else if (element.is("select")) {
                                const optionExists =
                                    element.find(
                                        `option[value="${setting.value}"]`
                                    ).length > 0;

                                if (!optionExists) {
                                    element.append(
                                        $("<option>", {
                                            value: setting.value,
                                            text: setting.value,
                                        })
                                    );
                                }

                                element
                                    .val(setting.value)
                                    .trigger("change");
                            } else {
                                element.val(setting.value);
                            }
                        }
                    });
                }
            },
            error: function (xhr) {
                showToast(
                    "error",
                    _l("admin.common.default_retrieve_error")
                );
            },
            complete: function () {
                $(".label-loader, .input-loader, .card-loader").hide();
                $(".real-label, .real-input, .real-card").removeClass(
                    "d-none"
                );
            },
        });
    }
})();
