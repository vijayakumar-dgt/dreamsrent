(async () => {
    "use strict";
    await loadTranslationFile("admin", "common, auth");
    $(document).ready(function () {
        $("#resetpasswordForm").validate({
            rules: {
                password: {
                    required: true,
                    minlength: 8,
                },
                password_confirmation: {
                    required: true,
                    equalTo: "#password",
                },
            },
            messages: {
                password: {
                    required: _l('admin.common.password_required'),
                    minlength: _l("admin.common.password_minlength")
                },
                password_confirmation: {
                    required: _l('admin.common.confirm_password_required'),
                    equalTo: _l('admin.common.confirm_password_equal_to')
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
            onkeyup: function (element) {
                $(element).valid();
            },
            onchange: function (element) {
                $(element).valid();
            },
            submitHandler: function (form) {
                let cylinderFormData = new FormData(form);
                $("#resetpasswordForm .submitbtn").text(_l('admin.common.please_wait'));
                $("#resetpasswordForm .submitbtn").attr("disabled", true);
                $(".password-error-text").text("");
                $.ajax({
                    type: "POST",
                    url: "/forgot-password/update-password",
                    data: cylinderFormData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.code === 200) {
                            toastr.success(resp.message);
                        }
                        $("#resetpasswordForm .submitbtn").text(_l('admin.auth.we_are_redirecting_you'));
                        setTimeout(() => {
                            window.location.href = resp.redirect_url;
                        }, 3000);
                    },
                    error: function (error) {
                        $(".password-error-text").text(
                            error.responseJSON.message
                        );
                        $("#resetpasswordForm .submitbtn").text(_l('admin.common.reset_password'));
                        $("#resetpasswordForm .submitbtn").prop(
                            "disabled",
                            false
                        );
                    },
                });
            },
        });
    });
})();
