/* global $, document, setTimeout, window, toastr */

(function () {
    "use strict";

    $(document).ready(function () {
        const $form = $("#account_form");
        const $submitBtn = $("#submit_btn");

        $form.on("submit", async function (e) {
            e.preventDefault();
            toastr.clear();

            const name = $("#name").val().trim();
            const email = $("#email").val().trim();
            const password = $("#password").val().trim();
            const confirmPassword = $("#confirm_password").val().trim();
            const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");

            // Validate Inputs
            if (!name) {
                toastr.warning("Name is required");
                $("#name").focus();
                return;
            }

            if (!email) {
                toastr.warning("Email is required");
                $("#email").focus();
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                toastr.warning("Invalid email format");
                $("#email").focus();
                return;
            }

            if (!password) {
                toastr.warning("Password is required");
                $("#password").focus();
                return;
            }

            if (password.length < 8) {
                toastr.warning("Password must be at least 8 characters");
                $("#password").focus();
                return;
            }

            if (password !== confirmPassword) {
                toastr.warning("Password and Confirm Password must match");
                $("#confirm_password").focus();
                return;
            }

            // Loading state
            $submitBtn
                .html(
                    "Creating... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>"
                )
                .prop("disabled", true);

            try {
                const response = await $.ajax({
                    url: "/setup/account-submit",
                    method: "POST",
                    dataType: "json",
                    data: {
                        name: name,
                        email: email,
                        password: password,
                        confirm_password: confirmPassword,
                        _token: csrfToken
                    }
                });

                if (response.success) {
                    toastr.success(response.message);
                    $submitBtn.addClass("btn-success").html("Redirecting...");
                    setTimeout(() => {
                        window.location.href = "/setup/configuration";
                    }, 1500);
                } else {
                    toastr.error(response.message || "Something went wrong");
                    $submitBtn.prop("disabled", false).html("Create Account");
                }
            } catch (error) {
                $submitBtn.prop("disabled", false).html("Create Account");

                if (error.responseJSON?.errors) {
                    $.each(error.responseJSON.errors, function (key, messages) {
                        if (messages.length > 0) {
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