/* global $, loadTranslationFile, document, _l, showToast, localStorage, window, clearInterval, setTimeout, setInterval */
(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common,auth');

    $(document).ready(function () {
        
        let emailTimerInterval;
        let emailTimerTime;
        $(document).on("click", "#forgot_otp, .resendEmailOtpForgot", function (event) {
            event.preventDefault();

            const username = $('[name="email"]').val().trim();

            if (!username || !isValidEmail(username)) {                
                const errorMessage = _l('web.auth.invalid_email') || "Please provide a valid email address.";
                showToast("error", errorMessage);
                return;
            }

            $.ajax({
                url: "/otp-settings",
                type: "POST",
                data: { email: username, type: "forgot" },
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (data) {
                    const otpExpireTime = parseInt(data.otp_expire_time.split(" ")[0]);
                    const otpDigitLimit = parseInt(data.otp_digit_limit);
                    const username = $('[name="email"]').val().trim();

                    const inputContainer = $(".inputcontainer");
                    inputContainer.empty();

                    let inputsHtml = '<div class="d-flex align-items-center mb-3">';
                    for (let i = 1; i <= otpDigitLimit; i++) {
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

                    const successMessage = _l('web.auth.otp_sent_to_email', { username }) || "OTP sent to your Email Address";
                    $("#otp-email-message").text(successMessage);
                    $("#otp-email-modal").modal("show");
                    startTimer(otpExpireTime);
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.error || _l('web.auth.failed_fetch_otp') || "Failed to fetch OTP settings. Please try again.";
                    showToast("error", errorMessage);
                }
            });
        });

        $("#verify-email-forgot-otp-btn").on("click", function () {
            const email = $('[name="email"]').val();
            const otpDigitLimit = $(".inputcontainer input").length;
            const forgot_email = $('[name="forgot_email"]').val();
            const login_type = "forgot_email";

            const otp = [];
            for (let i = 1; i <= otpDigitLimit; i++) {
                const digit = $(`#digit-${i}`).val();
                otp.push(digit);
            }
            const otpString = otp.join("");

            let requestData = { otp: otpString };

            if (email) {
                requestData.forgot_email = email;
                requestData.login_type = login_type;
            } else if (forgot_email) {
                requestData.forgot_email = forgot_email;
                requestData.login_type = login_type;
            }

            $.ajax({
                url: "/verify-otp",
                type: "POST",
                data: requestData,
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                beforeSend: function () {
                    $(".verify-email-otp-btn").attr("disabled", true).html(
                        '<div class="spinner-border text-light" role="status"></div>'
                    );
                },
                success: function (response) {
                    if (response.data === "done") {
                        $("#otp-email-modal").modal("hide");
                        $("#reset-password").modal("show");
                        let email = response.email;
                        localStorage.setItem('email', email);
                        window.location.href = "/user/reset-password";

                        $("#email_id").val(email);
                    } else {
                        $("#otp-email-modal").modal("hide");
                        $("#success_modal").modal("show");
                        window.location.href = "/";
                    }
                },
                error: function (xhr) {
                    const errorMessage = xhr.responseJSON?.error || _l('web.auth.otp_required') || "OTP Required";
                    showToast("error", errorMessage);
                },
                complete: function () {
                    $(".verify-email-otp-btn").attr("disabled", false).html(_l('web.auth.verify_otp') || "Verify OTP");
                },
            });
        });

        function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
        }
        function startTimer(expireTime) {
            clearInterval(emailTimerInterval); // Clear any existing timer
            emailTimerTime = expireTime * 60; // Convert minutes to seconds

            setTimeout(() => {
                let otpTimerDisplay = document.getElementById("otp-timer");

                if (!otpTimerDisplay) {
                    showToast("error", "OTP Timer element not found!")
                    return;
                }

                emailTimerInterval = setInterval(() => {
                    let minutes = Math.floor(emailTimerTime / 60);
                    let seconds = emailTimerTime % 60;

                    otpTimerDisplay.textContent = `${String(minutes).padStart(2, "0")}:${String(seconds).padStart(2, "0")}`;

                    if (emailTimerTime <= 0) {
                        clearInterval(emailTimerInterval);
                        otpTimerDisplay.textContent = "00:00"; // Timer finished
                    } else {
                        emailTimerTime--;
                    }
                }, 1000);
            }, 500); // Ensures modal and elements are visible
        }       
    });
})();




