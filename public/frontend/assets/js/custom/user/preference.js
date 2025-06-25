/* global location, loadTranslationFile, jQuery, setTimeout, document, showToast */

(function ($) {
    "use strict";

    (async () => {
        await loadTranslationFile("web", "home, common");

        let isInitialLoad = true;

        $(document).ready(() => {
            $(".custom-select2").select2();
            fetchPreference();
        });

        $(document).on("change", "#language_id", function () {
            if (!isInitialLoad) {
                updatePreference({ language_id: $(this).val() });
            }
        });

        $(document).on("change", "#region_id", function () {
            if (!isInitialLoad) {
                updatePreference({ region_id: $(this).val() });
            }
        });

        const fetchPreference = async () => {
            try {
                const response = await $.ajax({
                    type: "POST",
                    url: "/user/get-preferences",
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                    }
                });

                if (response.code === 200 && response.data) {
                    $("#language_id").val(response.data.language_id).trigger("change");
                    $("#region_id").val(response.data.region_id).trigger("change");
                    isInitialLoad = false;
                }
            } catch (error) {
                showToast("error", `Error fetching preferences: ${error?.message || "Unknown error"}`);
            }
        };

        const updatePreference = async (formData = {}) => {
            try {
                const response = await $.ajax({
                    type: "POST",
                    url: "/user/preference/update",
                    data: formData,
                    dataType: "json",
                    headers: {
                        Accept: "application/json",
                        "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
                    }
                });

                clearErrors();

                if (response.code === 200) {
                    showToast("success", response.message);
                    setTimeout(() => location.reload(), 3000);
                }
            } catch (error) {
                clearErrors();
                const errorData = error?.responseJSON;

                if (errorData?.code === 422 && errorData.errors) {
                    Object.entries(errorData.errors).forEach(([key, val]) => {
                        $(`#${key}`).addClass("is-invalid");
                        $(`#${key}_error`).text(val[0]);
                    });
                } else {
                    showToast("error", errorData?.message || "An error occurred");
                }
            }
        };

        const clearErrors = () => {
            $(".error-text").text("");
            $(".form-control").removeClass("is-invalid is-valid");
        };
    })();
})(jQuery);