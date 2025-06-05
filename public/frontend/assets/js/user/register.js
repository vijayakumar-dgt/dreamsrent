(function($) {
    "use strict";
(async () => {
    await loadTranslationFile('web', 'auth, common');
$(document).ready(function () {
    let emailExists = false;
    let userRegisterData;

    $("#email").on("keyup", function () {
        var email = $(this).val().trim();
        var emailError = $("#email_error");


        emailError.text("");

        if (email.length > 0 && !validateEmail(email)) {
            emailError.text(_l("web.auth.valid_email"));
            emailExists = true;
            return;
        }

        if (email.length > 0) {
            $.ajax({
                type: "POST",
                url: "/validate-email",
                data: {
                    email: email,
                    _token: $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (resp) {
                    if (resp.exists) {
                        emailError.text(_l("web.auth.email_exists"));
                        emailExists = true;
                    } else {
                        emailError.text("");
                        emailExists = false;
                    }
                },
                error: function () {
                    emailError.text(_l("web.auth.email_exists"));
                    emailExists = true;
                },
            });
        }
    });

    function validateEmail(email) {
        var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }   

    function startTimer(duration) {
        let timer = duration;
        const display = document.getElementById("otp-reg-timer");

        const interval = setInterval(() => {
            const minutes = String(Math.floor(timer / 60)).padStart(2, "0");
            const seconds = String(timer % 60).padStart(2, "0");

            if (display) {
                display.textContent = `${minutes}:${seconds}`;
            }

            if (--timer < 0) {
                clearInterval(interval);
                display.textContent = "00:00";
            }
        }, 1000);
    }

    $("#verify-email-red-otp-btn").on("click", function () {
        const otpDigitLimit = $(".inputcontainerreg input").length;

        const otp = [];
        for (let i = 1; i <= otpDigitLimit; i++) {
            const digit = $(`#digit-${i}`).val();
            otp.push(digit);
        }
        const otpString = otp.join("");

        const payload = {
            otp: otpString,
            login_type: "register",
            ...userRegisterData, // Include name, phone_number, email, password
        };

        $.ajax({
            url: "/verify-otp",
            type: "POST",
            data: payload,
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            beforeSend: function () {
                $(".verify-email-reg-otp-btn").attr("disabled", true);
                $(".verify-email-reg-otp-btn").html(
                    '<div class="spinner-border text-light" role="status"></div>'
                );
            },
            success: function (response) {
                $("#otp-email-reg-modal").modal("hide");
                $("#reg_success_modal").modal("show");

                setTimeout(function () {
                    location.reload();
                }, 500);
            },
            error: function (xhr) {
                const errorMessage = xhr.responseJSON.error || "OTP Required";
                $("#error_email_reg_message").text(errorMessage);
            },
            complete: function () {
                // Reset the button and remove the spinner
                $(".verify-email-reg-otp-btn").attr("disabled", false);
                $(".verify-email-reg-otp-btn").html("Verify OTP");
            },
        });
    });


    $("#userRegisterForm").validate({
        rules: {           
            first_name: {
                required: true,
                minlength: 3,
                pattern: /^[A-Za-z]+$/
            },
            last_name: {
                required: true,
                minlength: 3,
                pattern: /^[A-Za-z]+$/
            },
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
            first_name: {
                required: _l("web.auth.first_name_required"),
                minlength: _l("web.auth.first_name_minlength"),
                pattern: _l("web.auth.first_name_alphabets")
            },
            last_name: {
                required: _l("web.auth.last_name_required"),
                minlength: _l("web.auth.last_name_minlength"),
                pattern: _l("web.auth.last_name_alphabets")
            },
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
            if (emailExists) {
                $("#email_error").text(_l("web.auth.email_exists"));
                return false;
            }

            let formData = new FormData(form);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

            $(".btn-outline-light").text(`${_l('web.auth.please_wait')}...`).prop('disabled', true);

            $.ajax({
                type: "POST",
                url: "/user/register",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.register_status == 0) {
                        $("#userRegisterForm")[0].reset();
                        $(".form-control").removeClass("is-invalid is-valid");           
                        if (response.redirect_url) {
                                    window.location.href = route('home');
                                    return; 
                        }                   
                        
                    } else if (response.register_status === "1") {
                        $("#register-modal").modal("hide");

                        userRegisterData = {
                            name: response.name,
                            phone_number: response.phone_number,
                            email: response.email,
                            password: response.password,
                            first_name: response.first_name,
                            last_name: response.last_name,
                        };

                        const userName = response.email;
                        const otp = response.otp;
                        const otpDigitLimit = parseInt(response.otp_digit_limit || 4);
                        const expiresAt = new Date(response.expires_at);
                        const now = new Date();
                        const otpExpireTime = Math.floor((expiresAt - now) / 1000);

                        const inputContainer = $(".inputcontainerreg");
                        inputContainer.empty();

                        let inputsHtml = '<div class="d-flex align-items-center justify-content-center mb-3">';
                        for (let i = 1; i <= otpDigitLimit; i++) {
                            const nextId = `digit-${i + 1}`;
                            const prevId = `digit-${i - 1}`;
                            inputsHtml += `
                                <input type="text"
                                    class="rounded w-100 py-sm-3 py-2 text-center fs-26 fw-bold me-2 digit-${i}"
                                    id="digit-${i}"
                                    name="digit-${i}"
                                    data-next="${nextId}"
                                    data-previous="${prevId}"
                                    maxlength="1"
                                >
                            `;
                        }
                        inputsHtml += "</div>";

                        inputContainer.append(inputsHtml);

                        inputContainer.on("input", "input", function () {
                            const maxLength = $(this).attr("maxlength") || 1;
                            if (this.value.length >= maxLength) {
                                const next = $(this).data("next");
                                if (next) $("#" + next).focus();
                            }
                        });

                        inputContainer.on("keydown", "input", function (e) {
                            if (e.key === "Backspace" && this.value === "") {
                                const prev = $(this).data("previous");
                                if (prev) $("#" + prev).focus();
                            }
                        });

                        inputContainer.on("click", "input", function () {
                            $(this).select();
                        });

                        if (response.otp_type === "email") {
                            const otpEmailMessage = document.getElementById("otp-email-message");
                            if (otpEmailMessage) {
                                otpEmailMessage.textContent = `${_l('web.auth.otp_sent_to_email')} ${userName}`;
                            }

                            $("#otp-email-reg-modal").modal("show");
                            startTimer(otpExpireTime);
                        }
                    }



                    $(".btn-outline-light").text(_l('web.auth.sign_in')).prop('disabled', false);
                },
                error: function (error) {
                    setTimeout(function () {
                        $(".btn-outline-light").text(_l('web.auth.sign_up')).prop('disabled', false);
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
});
}) ();

})(jQuery);
