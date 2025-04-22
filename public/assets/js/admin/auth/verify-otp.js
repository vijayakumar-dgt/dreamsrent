$(document).ready(function(){
     $(".resend_otp_btn").addClass("disabled");
     
     startCountdown(59);
     function startCountdown(duration){
        let coundown = $(".timer").html(`<i class="ti ti-clock me-1"></i>` + " " + formatTime(duration));
        let timer = setInterval(function () {
            duration--;
            coundown.html(`<i class="ti ti-clock me-1"></i>` + " " + formatTime(duration));
   
            if(duration == 0){
                clearInterval(timer);
                $(".countdowndiv").addClass("d-none");
                $(".resend_otp_btn").removeClass("disabled");
            }
        }, 1000);
     }

     function formatTime(seconds){
        let minutes = Math.floor(seconds / 60);
        let remainingSeconds = seconds % 60;
        return `${String(minutes).padStart(2, '0')}:${String(remainingSeconds).padStart(2, '0')}`;
     }

     $(document).on("click", ".resend_otp_btn", function(e){
        e.preventDefault();
        if($(this).hasClass("disabled")){
        //    console.log("disabled");
            return;
        }
        $(".countdowndiv").removeClass("d-none");
        $(".resetpasswordbtn").prop("disabled", true);
        let token = window.location.href.split("token=")[1];
        $.ajax({
            type: "POST",
            url : "/forgot-password/resend-otp",
            data: {
                "_token": $('meta[name="csrf-token"]').attr('content'),
                "token" : token,
            },
            success: function(response){
                $(".resend_otp_btn").addClass("disabled");
                $(".resetpasswordbtn").prop("disabled", false);
                showToast("success", response.message);
                startCountdown(59);
            },
            error: function(error){
                showToast("error", error.responseJSON.message);
                $(".resend_otp_btn").removeClass("disabled");
                $(".resetpasswordbtn").prop("disabled", false);
                $(".countdowndiv").addClass("d-none");
            }
        });
        
     });

    // Auto move to the next input when a digit is entered
    $(document).on("keyup", ".otpinput", function (e) {
        if ($(this).val().length == 1) {
            $(this).next(".otpinput").focus();
        }
        $(".otp-error-text").text("");
    });

    // Handle backspace key to move back and clear the previous input
    $(document).on("keydown", ".otpinput", function (e) {
        if (e.key === "Backspace" && $(this).val().length === 0) {
            $(this).prev(".otpinput").focus().val('');
        }
        $(".otp-error-text").text("");
    });

    $(document).on("click", ".resetpasswordbtn", function(e){
        e.preventDefault();
        $(".resetpasswordbtn").prop("disabled", true);
        $(".resetpasswordbtn").html('<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>');
        //check is all input is filled
        let otp = [];
        $(".otpinput").each(function(){
            otp.push($(this).val());
        });
        if(otp.join("").length == 4){
            let token = window.location.href.split("token=")[1];
            let otp_input = otp.join("");
            $.ajax({
                type: "POST",
                url : "/forgot-password/confirm-otp",
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "token" : token,
                    "otp" : otp_input
                },
                success: function(response){
                    if(response.status == true && response.code == 200){
                        showToast('success', response.message);
                        $(".otp-error-text").text("");
                        $(".resetpasswordbtn").prop("disabled", true);
                        $(".resetpasswordbtn").text("We're redirecting you...");
                        setTimeout(function(){
                            window.location.href = response.redirect_url;                            
                        }, 3000);
                    }else{
                        $(".otp-error-text").text(response.message);
                        $(".resetpasswordbtn").prop("disabled", false);
                        $(".resetpasswordbtn").html('Reset Password');
                    }
                },
                error: function(error){
                    $(".otp-error-text").text(error.responseJSON.message);
                    $(".resetpasswordbtn").prop("disabled", false);
                    $(".resetpasswordbtn").html('Reset Password');
                }
                
            });
        }else{
            $(".otp-error-text").text("Please enter a valid OTP.");
            $(".resetpasswordbtn").prop("disabled", false);
            $(".resetpasswordbtn").html('Reset Password');
            return;
        }
    });
});