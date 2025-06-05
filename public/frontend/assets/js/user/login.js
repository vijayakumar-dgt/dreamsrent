
(function($) {
    "use strict";
(async () => {
    await loadTranslationFile('web', 'auth, common');

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
            var errorId = element.attr("id") + "_error";
            $("#" + errorId).text(error.text());
        },
        highlight: function (element) {
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            $(element).removeClass("is-invalid").addClass("is-valid");
            var errorId = element.id + "_error";
            $("#" + errorId).text("");
        },
        submitHandler: function (form) {
            let formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $(".btn-outline-light").text(`${_l('web.auth.please_wait')}...`).prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/user/login",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (resp) {
                    if (resp.code === 200) {
                        $("#userLoginForm")[0].reset();
                        $(".form-control").removeClass("is-invalid is-valid");
                        showToast('success', resp.message);
                        if (resp.redirect_url) {
                            window.location.href = route('home');
                        }

                    }
                    $(".btn-outline-light").text(`${_l('web.auth.sign_in')}`).prop('disabled', false);
                },
                error: function (error) {
                    setTimeout(function () {
                        $(".btn-outline-light").text(`${_l('web.auth.sign_in')}`).prop('disabled', false);
                    }, 500);
                    $(".error-text").text("");
                    $(".form-control").removeClass("is-invalid is-valid");

                    if (error.responseJSON && error.responseJSON.code === 422) {
                        let errorMessages = [];
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                            errorMessages.push(val[0]);
                        });

                        showToast('error', errorMessages.join("<br>"));
                    } else {
                        showToast('error', error.responseJSON ? error.responseJSON.message : "An error occurred");
                    }
                }
            });
        }
    });
    $(document).ready(function () {
        $('.copy-login-details').on('click', function (event) {
            event.preventDefault(); // Prevent default anchor behavior

            const email = $(this).data('email');
            const password = $(this).data('password');

            $('#userLoginForm input[name="email"]').val(email);
            $('#userLoginForm input[name="password"]').val(password);
        });
    });
});

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

let emailTimerInterval, smsTimerInterval;
let emailTimerTime, smsTimerTime;

function startTimer(expireTime) {
    clearInterval(emailTimerInterval); // Clear any existing timer
    emailTimerTime = expireTime * 60; // Convert minutes to seconds

    setTimeout(() => {
        let otpTimerDisplay = document.getElementById("otp-timer");

        if (!otpTimerDisplay) {
            console.error("OTP Timer element not found!");
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

function sendEmail(email, emailData, userName, otp) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: "/api/mail/sendmail",
            type: "POST",
            dataType: "json",
            data: {
                otp_type: "email",
                to_email: email,
                notification_type: 2,
                type: 1,
                user_name: userName,
                otp: otp,
                subject: emailData.subject,
                content: emailData.content,
            },
            headers: {
                Authorization:
                    "Bearer " + localStorage.getItem("admin_token"),
                Accept: "application/json",
            },
            success: function (response) {
                resolve(response);
            },
            error: function (error) {
                reject(error);
            },
        });
    });
}

$(document).ready(function () {
    $(document).on("click", "#login_otp, .resendEmailOtp", function (event) {
        event.preventDefault(); // Prevent default anchor behavior

        const username = $('[name="email"]').val().trim();

        if (!username || !isValidEmail(username)) {
            showToast("error", "Please provide a valid email address.");
            return;
        }

        // showLoader();

        $.ajax({
            url: "/otp-settings",
            type: "POST",
            data: { email: username },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: function (data) {
                // hideLoader();

                const userName = data.name;
                const otpExpireTime = parseInt(data.otp_expire_time.split(" ")[0]); // Extract expiration time
                const otpDigitLimit = parseInt(data.otp_digit_limit); // Extract OTP digit limit
                const otp = data.otp; // Extract OTP
                const otpType = data.otp_type; // Extract OTP type
                const emailSubject = data.email_subject; // Extract email subject
                const emailContent = data.email_content; // Extract email content
                const username = $('[name="email"]').val().trim(); // Get email from input field

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

                // OTP Input Auto-Focus Logic
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

                // Send OTP via Email if OTP Type is 'email'
                if (otpType === "email") {
                    const emailData = {
                        subject: emailSubject,
                        content: `${emailContent}: ${otp}` // Include OTP in email content
                    };

                    sendEmail(username, emailData, "email", userName, otp)
                        .then(() => {
                            // Show OTP Modal only after successful email send
                            $("#otp-email-message").text(`${_l("web.auth.otp_sent_to_email")} ${username}`);
                            $("#otp-email-modal").modal("show");
                            startTimer(otpExpireTime);
                        })
                        .catch(() => {
                            showToast("error", _l("web.auth.failed_to_send_otp"));
                        });
                } else {
                    // Show OTP Modal immediately if not using email
                    $("#otp-email-message").text(`${_l("web.auth.otp_sent_to_email")} ${username}`);
                    $("#otp-email-modal").modal("show");
                    startTimer(otpExpireTime);
                }
            },
            error: function (xhr) {
                // hideLoader();
                const errorMessage = xhr.responseJSON?.error || _l("web.auth.failed_to_send_otp");
                showToast("error", errorMessage);
            }
        });
    });

    $("#verify-email-otp-btn").on("click", function () {
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
            requestData.email = email;
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
                    $("#email_id").val(email);
                } else {
                    $("#otp-email-modal").modal("hide");
                    $("#success_modal").modal("show");
                    window.location.href = "/";
                }
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON?.error || _l("web.auth.otp_required");
                showToast("error", errorMessage);
            },
            complete: function () {
                $(".verify-email-otp-btn").attr("disabled", false).html(_l("web.auth.verify_otp"));
            },
        });
    });
});

})();


})(jQuery);
