$(document).ready(function () {
    $("#resetpasswordForm").validate({
        rules: {
            password: {
                required: true,
                minlength: 6,
            },
            password_confirmation: {
                required: true,
                equalTo: "#password",
            }
        },
        messages:{
            password: {
                required: 'Please enter password',
                minlength: 'Password must be at least 6 characters',
            },
            password_confirmation: {
                required: 'Please enter confirm password',
                equalTo: 'Password and confirm password must be same',
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
           let cylinderFormData = new FormData(form);
           $("#resetpasswordForm .submitbtn").text('Please Wait...');
           $("#resetpasswordForm .submitbtn").attr("disabled", true);
           $(".password-error-text").text('');
           $.ajax({
               type:"POST",
               url:"/forgot-password/update-password",
               data:cylinderFormData,
               processData: false,
               contentType: false,
               success:function(resp){
                   if (resp.code === 200) {
                       toastr.success(resp.message);
                   }
                   $("#resetpasswordForm .submitbtn").text("We're redirecting you...");
                   setTimeout(() => {
                       
                       window.location.href = resp.redirect_url;
                   }, 3000);
               },
               error:function(error){
                 $(".password-error-text").text(error.responseJSON.message);
                 $("#resetpasswordForm .submitbtn").text('Reset Password');
                 $("#resetpasswordForm .submitbtn").prop('disabled', false);
               }
           });
        }
    });
});