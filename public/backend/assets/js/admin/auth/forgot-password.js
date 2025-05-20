(async () => {
await loadTranslationFile('admin', 'general_settings, auth');

$(document).ready(function(){
    $(".submitbtn").attr("disabled", false);
    $("#resetpasswordForm").validate({
        rules: {
            email: {
                required: true,
                email: true,
            },
        },
        messages:{
            email: {
                required: _l('admin.auth.please_enter_email'),
                email: _l('admin.auth.please_enter_valid_email'),
            },
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
        onkeyup: function(element) {
            $(element).valid();
        },
        onchange: function(element) {
            $(element).valid();
        },
        submitHandler: function(form) {
           let _FormData = new FormData(form);
           $("#resetpasswordForm .submitbtn").text(_l('admin.general_settings.please_wait'));
           $("#resetpasswordForm .submitbtn").attr("disabled", true);
           $.ajax({
               type:"POST",
               url:"/forgot-password/send-otp",
               data:_FormData,
               processData: false,
               contentType: false,
               success:function(resp){
                   if (resp.code === 200) {
                       showToast('success', resp.message);
                       $("#resetpasswordForm .submitbtn").text(_l('admin.auth.we_are_redirecting_you'));
                        setTimeout(() => {
                            
                            window.location.href = '/forgot-password/verify-otp?token='+encodeURIComponent(resp.token);
                        }, 3000);
                   }else if(resp.code === 422){
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        showToast('error', resp.message);
                        $.each(resp.errors, function(key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
                        $("#resetpasswordForm .submitbtn").text(_l('admin.auth.reset_password'));
                        $("#resetpasswordForm .submitbtn").prop('disabled', false);
                   }
               },
               error:function(error){
                 $(".error-text").text("");
                 $(".form-control").removeClass("is-invalid is-valid");
                 if (error.responseJSON.code === 422) {
                    showToast('error', error.responseJSON.message);
                        $.each(error.responseJSON.errors, function(key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
                 } else {
                    showToast('error', error.responseJSON.message);
                 }
                 $("#resetpasswordForm .submitbtn").text(_l('admin.auth.reset_password'));
                 $("#resetpasswordForm .submitbtn").prop('disabled', false);
               }
           });
        }
    });


    $(document).on('click', '.submitbtn', function (e) {
        e.preventDefault();
        if ($("#resetpasswordForm").valid()) {
            $("#resetpasswordForm").submit();
        }
    });
});
}) ();