(async () => {
    "use strict";

    await loadTranslationFile("admin", "general_settings, auth");

    $(document).ready(function () {
        $(document).ready(function () {
            $(".copy-login-details").on("click", function (event) {
                event.preventDefault(); // Prevent default anchor behavior

                const email = $(this).data("email");
                const password = $(this).data("password");

                $('#loginForm input[name="email"]').val(email);
                $('#loginForm input[name="password"]').val(password);
            });
        });
        $(".submitbtn").attr("disabled", false);
        $("#loginForm").validate({
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 50,
                },
                password: {
                    required: true,
                    minlength: 6,
                    maxlength: 30,
                },
            },
            messages: {
                email: {
                    required: _l("admin.auth.please_enter_email"),
                    email: _l("admin.auth.please_enter_valid_email"),
                },
                password: {
                    required: _l("admin.auth.please_enter_password"),
                    minlength: _l("admin.general_settings.password_min"),
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
                let _formData = new FormData(form);
                $("#loginForm .submitbtn").html(
                    '<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span>'
                );
                $("#loginForm .submitbtn").attr("disabled", true);
                $("#error").text("");
                $.ajax({
                    type: "POST",
                    url: "/admin/verify-login",
                    data: _formData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        if (resp.status) {
                            window.location.href = resp.redirect_url;
                        } else {
                            showToast("error", resp.message);
                            $("#loginForm .submitbtn").text(
                                _l("admin.auth.login")
                            );
                            $("#loginForm .submitbtn").prop("disabled", false);
                        }
                    },
                    error: function (error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        if (error.responseJSON.code === 422) {
                            $.each(
                                error.responseJSON.errors,
                                function (key, val) {
                                    $("#" + key).addClass("is-invalid");
                                    $("#" + key + "_error").text(val[0]);
                                }
                            );
                        } else {
                            $("#error").text(error.responseJSON.message);
                        }
                        $("#loginForm .submitbtn").text(
                            _l("admin.auth.login")
                        );
                        $("#loginForm .submitbtn").prop("disabled", false);
                    },
                });
            },
        });

        $(document).on("keyup", "#password", function () {
            $("#error").text("");
        });

        $(document).on("click", "#toggle-password", function () {
            $(this).toggleClass("fa-eye fa-eye-slash");
            var input = $("#password");
            input.attr("type") === "password"
                ? input.attr("type", "text")
                : input.attr("type", "password");
        });
    });
})();
