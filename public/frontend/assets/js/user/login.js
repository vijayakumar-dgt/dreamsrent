/* global loadTranslationFile, document, _l, FormData, showToast, window, setTimeout, clearInterval, setInterval, jQuery */
(function($) {
    "use strict";
(async () => {
    await loadTranslationFile("web", "auth, common");

$(document).ready(function () {
    $(".submitbtn").attr("disabled", false);
    $("#userLoginForm").validate({
        rules: {
            email: {
                required: true,
                email: true
            },
            password: {
                required: true,
                minlength: 6
            }
        },
        messages: {
            email: {
                required: _l("web.auth.email_required"),
                email: _l("web.auth.valid_email")
            },
            password: {
                required: _l("web.auth.password_required"),
                minlength: _l("web.auth.password_minlength")
            }
        },
        errorPlacement: function (error, element) {
            const errorId = element.attr("id") + "_error";
            $("#" + errorId).text(error.text());
        },
        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
            const errorId = element.id + "_error";
            $("#" + errorId).text("");
        },
        submitHandler: function (form) {
            const formData = new FormData(form);
            const csrfToken = $("meta[name=\"csrf-token\"]").attr("content");
            formData.append("_token", csrfToken);

            $(".btn-outline-light").text(_l("web.auth.please_wait") + "...").prop("disabled", true);

            $.ajax({
                type: "POST",
                url: "/user/login",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                success: function (resp) {
                    $(".btn-outline-light").text(_l("web.auth.sign_in")).prop("disabled", false);
                    if (resp.code === 200) {
                        $("#userLoginForm")[0].reset();
                        $(".form-control").removeClass("is-invalid is-valid");
                        showToast("success", resp.message);
                        const BASE_URL = window.location.origin;
                        const redirectUrl = resp.redirect_url;

                        if (redirectUrl && redirectUrl.startsWith("/") && !redirectUrl.startsWith("//")) {
                            window.location.href = BASE_URL + redirectUrl;
                        } else {
                            window.location.href = BASE_URL + "/";
                        }
                    }
                },
                error: function (error) {
                    setTimeout(function () {
                        $(".btn-outline-light").text(_l("web.auth.sign_in")).prop("disabled", false);
                    }, 500);

                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");

                    if (error.responseJSON && error.responseJSON.code === 422) {
                        const errorMessages = [];
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                            errorMessages.push(val[0]);
                        });
                        showToast("error", errorMessages.join("<br>"));
                    } else {
                        const message = error.responseJSON ? error.responseJSON.message : "An error occurred";
                        showToast("error", message);
                    }
                }
            });
        }
    });

    $(document).ready(function () {
        $(".copy-login-details").on("click", function (event) {
            event.preventDefault();

            const email = $(this).data("email");
            const password = $(this).data("password");

            $("#userLoginForm input[name=\"email\"]").val(email);
            $("#userLoginForm input[name=\"password\"]").val(password);
        });
    });
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

let emailTimerInterval;
let emailTimerTime;

function startTimer(expireTime) {
    clearInterval(emailTimerInterval);
    emailTimerTime = expireTime * 60;

    setTimeout(function () {
        const otpTimerDisplay = document.getElementById("otp-timer");

        if (!otpTimerDisplay) {
            showToast("error", "OTP Timer element not found!");
            return;
        }

        emailTimerInterval = setInterval(function () {
            const minutes = Math.floor(emailTimerTime / 60);
            const seconds = emailTimerTime % 60;

            otpTimerDisplay.textContent = String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0");

            if (emailTimerTime <= 0) {
                clearInterval(emailTimerInterval);
                otpTimerDisplay.textContent = "00:00";
            } else {
                emailTimerTime--;
            }
        }, 1000);
    }, 500);
}

$(document).ready(function () {
    $(document).on("click", "#login_otp, .resendEmailOtp", function (event) {
        event.preventDefault();

        const username = $("[name=\"email\"]").val().trim();

        if (!username || !isValidEmail(username)) {
            showToast("error", "Please provide a valid email address.");
            return;
        }

        $.ajax({
            url: "/otp-settings",
            type: "POST",
            data: { email: username },
            headers: {
                "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
            },
            success: function (data) {
                if (data.code !== 200) {
                    showToast("error", data.error || _l("web.auth.failed_to_send_otp"));
                    return;
                }

                const otpExpireTime = parseInt(data.otp_expire_time.split(" ")[0], 10);
                const otpDigitLimit = parseInt(data.otp_digit_limit, 10);
                const inputContainer = $(".inputcontainer");

                inputContainer.empty();

                let inputsHtml = "<div class=\"d-flex align-items-center mb-3\">";
                for (let i = 1; i <= otpDigitLimit; i++) {
                    const nextId = "digit-" + (i + 1);
                    const prevId = "digit-" + (i - 1);
                    inputsHtml +=
                        "<input type=\"text\" " +
                        "class=\"rounded w-100 py-sm-3 py-2 text-center fs-26 fw-bold me-3 digit-" + i + "\" " +
                        "id=\"digit-" + i + "\" " +
                        "name=\"digit-" + i + "\" " +
                        "data-next=\"" + nextId + "\" " +
                        "data-previous=\"" + prevId + "\" " +
                        "maxlength=\"1\">";
                }
                inputsHtml += "</div>";
                inputContainer.append(inputsHtml);

                $(".inputcontainer").off("input").on("input", "input", function () {
                    if (this.value.length >= 1) {
                        const next = $(this).data("next");
                        if (next) {
                            $("#" + next).focus();
                        }
                    }
                });

                $(".inputcontainer").off("keydown").on("keydown", "input", function (e) {
                    if (e.key === "Backspace" && this.value === "") {
                        const prev = $(this).data("previous");
                        if (prev) {
                            $("#" + prev).focus();
                        }
                    }
                });

                $(".inputcontainer").off("click").on("click", "input", function () {
                    $(this).select();
                });

                $("#otp-email-message").text(_l("web.auth.otp_sent_to_email") + " " + username);
                $("#otp-email-modal").modal("show");
                startTimer(otpExpireTime);
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : _l("web.auth.failed_to_send_otp");
                showToast("error", errorMessage);
            }
        });
    });

    $("#verify-email-otp-btn").on("click", function () {
        const email = $("[name=\"email\"]").val();
        const forgotEmail = $("[name=\"forgot_email\"]").val();
        const otpDigitLimit = $(".inputcontainer input").length;
        const otp = [];

        for (let i = 1; i <= otpDigitLimit; i++) {
            const digit = $("#digit-" + i).val();
            otp.push(digit);
        }

        const otpString = otp.join("");
        const requestData = { otp: otpString };

        if (email) {
            requestData.email = email;
        } else if (forgotEmail) {
            requestData.forgot_email = forgotEmail;
            requestData.login_type = "forgot_email";
        }

        $.ajax({
            url: "/verify-otp",
            type: "POST",
            data: requestData,
            headers: {
                "X-CSRF-TOKEN": $("meta[name=\"csrf-token\"]").attr("content")
            },
            beforeSend: function () {
                $(".verify-email-otp-btn").attr("disabled", true).html(
                    "<div class=\"spinner-border text-light\" role=\"status\"></div>"
                );
            },
            success: function (response) {
                if (response.data === "done") {
                    $("#otp-email-modal").modal("hide");
                    $("#reset-password").modal("show");
                    const responseEmail = response.email;
                    $("#email_id").val(responseEmail);
                } else {
                    $("#otp-email-modal").modal("hide");
                    $("#success_modal").modal("show");
                    window.location.href = "/";
                }
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON && xhr.responseJSON.error ? xhr.responseJSON.error : _l("web.auth.otp_required");
                showToast("error", errorMessage);
            },
            complete: function () {
                $(".verify-email-otp-btn").attr("disabled", false).html(_l("web.auth.verify_otp"));
            }
        });
    });
});

})();


})(jQuery);
