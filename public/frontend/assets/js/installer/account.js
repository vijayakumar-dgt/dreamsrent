/* global $, window, toastr, setTimeout */

(function () {
    "use strict";

    $(function () {
        const $form = $("#account_form");
        const $submitBtn = $("#submit_btn");
        const csrfToken = $("meta[name='csrf-token']").attr("content");

        $form.on("submit", async function (e) {
            e.preventDefault();
            toastr.clear();

            const name = $.trim($("#name").val());
            const email = $.trim($("#email").val());
            const password = $("#password").val();
            const confirmPassword = $("#confirm_password").val();

            if (!name) {
                return showWarning("#name", "Name is required");
            }

            if (!email) {
                return showWarning("#email", "Email is required");
            }

            if (!isValidEmail(email)) {
                return showWarning("#email", "Invalid email format");
            }

            if (!password) {
                return showWarning("#password", "Password is required");
            }

            if (password.length < 8) {
                return showWarning("#password", "Password must be at least 8 characters");
            }

            if (password !== confirmPassword) {
                return showWarning("#confirm_password", "Passwords must match");
            }

            toggleLoading(true);

            try {
                const response = await $.ajax({
                    url: "/setup/account-submit",
                    method: "POST",
                    dataType: "json",
                    data: {
                        name,
                        email,
                        password,
                        confirm_password: confirmPassword,
                        _token: csrfToken
                    }
                });

                if (response.success) {
                    toastr.success(response.message || "Account created successfully");
                    $submitBtn.addClass("btn-success").html("Redirecting...");
                    setTimeout(function () {
                        window.location.href = "/setup/configuration";
                    }, 1500);
                } else {
                    toastr.error(response.message || "Something went wrong");
                    resetButton();
                }
            } catch (err) {
                resetButton();

                const errors = err?.responseJSON?.errors;
                if (errors) {
                    Object.values(errors).forEach(function (messages) {
                        if (messages.length > 0) {
                            toastr.error(messages[0]);
                        }
                    });
                } else {
                    toastr.error("Unexpected error. Please try again.");
                }
            }
        });

        function toggleLoading(isLoading) {
            if (isLoading) {
                $submitBtn
                    .html("Creating... <span class=\"spinner-border spinner-border-sm\" role=\"status\" aria-hidden=\"true\"></span>")
                    .prop("disabled", true);
            } else {
                $submitBtn.prop("disabled", false).html("Create Account");
            }
        }

        function resetButton() {
            toggleLoading(false);
        }

        function showWarning(selector, message) {
            toastr.warning(message);
            $(selector).focus();
        }

        function isValidEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }
    });
})();