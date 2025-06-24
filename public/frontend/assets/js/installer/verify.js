/* global $, document, toastr, setTimeout, window */

(function () {
    "use strict";

    $(function () {
        $(document).on("submit", "#verify_form", async function (e) {
            e.preventDefault();
            toastr.clear();

            const $codeInput = $("#purchase_code");
            const code = $codeInput.val().trim();
            const $submitBtn = $("#submit_btn");
            const $form = $(this);

            if (!code) {
                toastr.warning("Purchase code is required");
                $codeInput.focus();
                return;
            }

            // Show loading state
            $submitBtn
                .html(
                    "Checking... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>"
                )
                .prop("disabled", true);

            try {
                const response = await $.ajax({
                    url: $form.attr("action"),
                    method: "POST",
                    data: { purchase_code: code },
                    dataType: "json"
                });

                if (response.success) {
                    toastr.success(response.message);
                    $submitBtn.addClass("btn-success").html("Redirecting...");
                    setTimeout(function () {
                        window.location.href = "/setup/requirements";
                    }, 1500);
                } else {
                    $codeInput.val("");
                    toastr.error(response.message);
                    setTimeout(function () {
                        window.location.reload();
                    }, 4000);
                }
            } catch (error) {
                $codeInput.val("");

                if (error.responseJSON) {
                    const { errors, message } = error.responseJSON;
                    if (errors) {
                        $.each(errors, function (key, value) {
                            if (Array.isArray(value)) {
                                toastr.error(value[0]);
                            } else {
                                toastr.error(value);
                            }
                        });
                    } else if (message) {
                        toastr.error(message);
                    } else {
                        toastr.error("An unknown server error occurred.");
                    }
                } else {
                    toastr.error("An unexpected error occurred. Please try again.");
                }
            } finally {
                $submitBtn
                    .html("Check")
                    .prop("disabled", false)
                    .removeClass("btn-success");
            }
        });
    });
})();
