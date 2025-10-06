/* global $, loadTranslationFile, document, _l, showToast, localStorage, window, clearInterval, setTimeout, setInterval */
(async () => {
    "use strict";
    await loadTranslationFile("web", "user,common,auth");
    let emailTimerInterval;
    let emailTimerTime;

    $(document).ready(function () {
        // Validate and send OTP
        $(document).on("click", "#forgot_otp, .resendEmailOtpForgot", function (event) {
            event.preventDefault();
            const username = $("[name='email']").val().trim();

            if (!username || !isValidEmail(username)) {
                const errorMessage = _l("web.auth.invalid_email") || "Please provide a valid email address.";
                showToast("error", errorMessage);
                return;
            }

            sendOtp(username, "forgot");
        });

        $("#verify-email-forgot-otp-btn").on("click", function () {
            const email = $("[name='email']").val();
            const forgotEmail = $("[name='forgot_email']").val();
            const otpDigitLimit = $(".inputcontainer input").length;
            const loginType = "forgot_email";

            const otp = [];
            for (let i = 1; i <= otpDigitLimit; i++) {
                const digit = $(`#digit-${i}`).val();
                otp.push(digit);
            }
            const otpString = otp.join("");

            const requestData = { otp: otpString };

            if (email || forgotEmail) {
                requestData.forgot_email = email || forgotEmail;
                requestData.login_type = loginType;
            }

            $.ajax({
                url: "/verify-otp",
                type: "POST",
                data: requestData,
                headers: {
                    "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")
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
                        const resEmail = response.email;
                        localStorage.setItem("email", resEmail);
                        window.location.href = "/user/reset-password";
                        $("#email_id").val(resEmail);
                    } else {
                        $("#otp-email-modal").modal("hide");
                        $("#success_modal").modal("show");
                        window.location.href = "/";
                    }
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.error || _l("web.auth.otp_required") || "OTP Required";
                    showToast("error", errorMessage);
                },
                complete: function () {
                    $(".verify-email-otp-btn").attr("disabled", false).html(_l("web.auth.verify_otp") || "Verify OTP");
                }
            });
        });
    });

    function isValidEmail(email) {
        const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        return emailRegex.test(email);
    }

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

                otpTimerDisplay.textContent =
                    String(minutes).padStart(2, "0") + ":" + String(seconds).padStart(2, "0");

                if (emailTimerTime <= 0) {
                    clearInterval(emailTimerInterval);
                    otpTimerDisplay.textContent = "00:00";
                } else {
                    emailTimerTime--;
                }
            }, 1000);
        }, 500);
    }

    // Function to send OTP via AJAX
    function sendOtp(email, type) {
        $.ajax({
            url: "/otp-settings",
            type: "POST",
            data: { email, type },
            headers: { "X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content") },
            beforeSend: function () { handleOtpBeforeSend(); },
            success: function (data) { handleOtpSuccess(data, email); },
            error: function (xhr) { handleOtpError(xhr); },
            complete: function () { handleOtpComplete(); }
        });
    }

    // Handle OTP AJAX beforeSend
    function handleOtpBeforeSend() {
        $("#forgot_otp").attr("disabled", false).html(
            `<span class="spinner-border spinner-border-sm align-middle" role="status"></span> ${_l("web.user.plz_wait")}`
        );
    }

    // Handle OTP AJAX complete
    function handleOtpComplete() {
        $("#forgot_otp").attr("disabled", false).html(_l("web.auth.reset_password_title") || "Reset Password");
    }

    // Handle OTP success
    function handleOtpSuccess(data, email) {
        const otpExpireTime = parseInt(data.otp_expire_time.split(" ")[0], 10);
        const otpDigitLimit = parseInt(data.otp_digit_limit, 10);

        generateOtpInputs(otpDigitLimit);
        setupOtpInputHandlers();
        showOtpModal(email, otpExpireTime);
    }

    // Generate OTP input fields
    function generateOtpInputs(digitLimit) {
        const inputContainer = $(".inputcontainer");
        inputContainer.empty();

        let inputsHtml = '<div class="d-flex align-items-center mb-3">';
        for (let i = 1; i <= digitLimit; i++) {
            const nextId = `digit-${i + 1}`;
            const prevId = `digit-${i - 1}`;
            inputsHtml += `
                <input type="text"
                    class="rounded w-100 py-sm-3 py-2 text-center fs-26 fw-bold me-3 digit-${i}"
                    id="digit-${i}"
                    name="digit-${i}"
                    data-next="${nextId}"
                    data-previous="${prevId}"
                    maxlength="1">
            `;
        }
        inputsHtml += "</div>";
        inputContainer.append(inputsHtml);
    }

    // Setup OTP input navigation
    function setupOtpInputHandlers() {
        const container = $(".inputcontainer");

        container.off("input").on("input", "input", function () {
            if (this.value.length >= 1) {
                const next = $(this).data("next");
                if (next) $("#" + next).focus();
            }
        });

        container.off("keydown").on("keydown", "input", function (e) {
            if (e.key === "Backspace" && this.value === "") {
                const prev = $(this).data("previous");
                if (prev) $("#" + prev).focus();
            }
        });

        container.off("click").on("click", "input", function () {
            $(this).select();
        });
    }

    // Show OTP modal and start timer
    function showOtpModal(email, otpExpireTime) {
        const successMessage = _l("web.auth.otp_sent_to_email", { username: email }) || "OTP sent to your Email Address";
        $("#otp-email-message").text(successMessage);
        $("#otp-email-modal").modal("show");
        startTimer(otpExpireTime);
    }

    // Handle OTP AJAX error
    function handleOtpError(xhr) {
        const errorMessage = xhr.responseJSON?.error || _l("web.auth.failed_fetch_otp") || "Failed to fetch OTP settings. Please try again.";
        showToast("error", errorMessage);
    }
})();