(function($) {
    "use strict";
var email = localStorage.getItem("email");
if (email) {
    $('#email').val(email);
}
$("#changePasswordForm").validate({
    rules: {
        current_password: {
            required: true,
            minlength: 6
        },
        confirm_password: {
            required: true,
            equalTo: "#current_password"
        }
    },
    messages: {
        current_password: {
            required: "Please enter your new password",
            minlength: "Password must be at least 6 characters"
        },
        confirm_password: {
            required: "Please confirm your new password",
            equalTo: "Passwords do not match"
        }
    },
    errorPlacement: function (error, element) {
        let errorId = element.attr("id") + "_error";
        $("#" + errorId).text(error.text());
    },
    highlight: function (element) {
        $(element).addClass("is-invalid").removeClass("is-valid");
    },
    unhighlight: function (element) {
        $(element).removeClass("is-invalid").addClass("is-valid");
        let errorId = $(element).attr("id") + "_error";
        $("#" + errorId).text("");
    },
    submitHandler: function (form) {
        const formData = new FormData(form);

        $(".btn-size").html(
            '<div class="spinner-border spinner-border-sm text-light" role="status"></div>'
        ).prop("disabled", true);

        $.ajax({
            type: "POST",
            url: "/user/reset-password-update",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr("content")
            },
            success: function (resp) {
                if (resp.code === 200) {
                    showToast("success", resp.message);
                    $("#changePasswordForm")[0].reset();
                    window.location.href = "/";
                    $(".form-control").removeClass("is-valid");
                } else {
                    showToast("error", resp.message || "Something went wrong");
                }
            },
            error: function (error) {
                $(".error-text").text("");
                $(".form-control").removeClass("is-invalid is-valid");

                if (error.responseJSON?.errors) {
                    $.each(error.responseJSON.errors, function (key, val) {
                        $("#" + key).addClass("is-invalid");
                        $("#" + key + "_error").text(val[0]);
                    });
                } else {
                    showToast("error", error.responseJSON?.message || "Server error");
                }
            },
            complete: function () {
                $(".btn-size").html("Save Changes").prop("disabled", false);
            }
        });
    }
});
})(jQuery);
