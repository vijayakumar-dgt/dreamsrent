/* global $, document, toastr, setTimeout, window */

(function () {
    "use strict";

    $(function () {
        $(document).on("submit", "#config_form", async function (e) {
            e.preventDefault();

            toastr.clear();

            const $configAppName = $("#config_app_name");
            const $submitBtn = $("#submit_btn");
            const csrfToken = $("meta[name='csrf-token']").attr("content");

            const configAppName = $configAppName.val().trim();

            if (!configAppName) {
                toastr.warning("App Name is required");
                $configAppName.focus();
                return;
            }

            // Show loading
            $submitBtn
                .html(
                    "Saving... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>"
                )
                .prop("disabled", true);

            try {
                const response = await $.ajax({
                    url: "/setup/configuration-submit",
                    method: "POST",
                    dataType: "json",
                    data: {
                        config_app_name: configAppName,
                        _token: csrfToken
                    }
                });

                if (response.success) {
                    toastr.success(response.message);
                    $submitBtn
                        .addClass("btn-success")
                        .html("Redirecting...");
                    setTimeout(function () {
                        window.location.href = "/setup/complete";
                    }, 1500);
                } else {
                    toastr.error(response.message || "Something went wrong");
                    $submitBtn.prop("disabled", false).html("Save Config");
                }
            } catch (error) {
                $submitBtn.prop("disabled", false).html("Save Config");

                if (
                    error.responseJSON &&
                    error.responseJSON.errors
                ) {
                    $.each(error.responseJSON.errors, function (key, messages) {
                        if (Array.isArray(messages) && messages.length > 0) {
                            toastr.error(messages[0]);
                        }
                    });
                } else {
                    toastr.error("Unexpected error. Please try again.");
                }
            }
        });
    });
})();