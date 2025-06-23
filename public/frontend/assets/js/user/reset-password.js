/* global $, loadTranslationFile, showToast, _l, localStorage, FormData, window */
(async () => {
    "use strict";

    // Load translation keys from the specified namespaces
    await loadTranslationFile('web', 'user,common,auth');

    $(function () {
        const email = localStorage.getItem("email");
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
                    required: _l("web.auth.current_password_required"),
                    minlength: _l("web.auth.password_min_length")
                },
                confirm_password: {
                    required: _l("web.auth.confirm_password_required"),
                    equalTo: _l("web.auth.passwords_do_not_match")
                }
            },
            errorPlacement: function (error, element) {
                const errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function (element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function (element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
                const errorId = $(element).attr("id") + "_error";
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
                            showToast("error", resp.message || _l("web.common.something_went_wrong"));
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
                            showToast("error", error.responseJSON?.message || _l("web.common.server_error"));
                        }
                    },
                    complete: function () {
                        $(".btn-size").html(_l("web.auth.save_changes")).prop("disabled", false);
                    }
                });
            }
        });
    });
})();
