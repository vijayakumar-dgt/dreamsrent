/* global loadTranslationFile, document, showToast, _l, jQuery */
(function ($) {
    "use strict";

    (async function () {
        try {
            await loadTranslationFile("web", "user,common");

            $(document).on("click", ".submitbtn", function () {
                const csrfToken = $("meta[name='csrf-token']").attr("content");
                const $btn = $(this);

                const data = {
                    booking_confirmation: $("#booking").is(":checked") ? 1 : 0,
                    desktop_notifications: $("#desktop_notifications").is(":checked") ? 1 : 0,
                    email_notifications: $("#email_notifications").is(":checked") ? 1 : 0,
                    _token: csrfToken
                };

                $.ajax({
                    type: "POST",
                    url: "/user/update-notification-settings",
                    data: data,
                    beforeSend: function () {
                        $btn.prop("disabled", true).html(
                            `<span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l("web.common.saving")}..`
                        );
                    },
                    success: function (response) {
                        if (response.code === 200) {
                            showToast("success", response.message);
                        } else {
                            showToast("error", response.message || _l("web.common.default_error"));
                        }
                    },
                    error: function () {
                        showToast("error", _l("web.common.default_error"));
                    },
                    complete: function () {
                        $btn.prop("disabled", false).html(_l("web.user.save_changes"));
                    }
                });
            });
        } catch {
            showToast("error", _l("web.common.default_error"));
        }
    })();
})(jQuery);