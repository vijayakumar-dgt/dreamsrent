(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

$(document).ready(function() {
    $("#notificationsSettingForm").validate({
        rules: {
            notificationPreference: {
                required: true
            }
        },
        messages: {
            notificationPreference: {
                required: _l('admin.general_settings.please_select_notification_preference'),
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
        onkeyup: function (element) {
            $(element).valid();
        },
        onchange: function (element) {
            $(element).valid();
        },
        submitHandler: function (form) {
            let formData = new FormData(form);

            const notificationPreference = $('input[name="notificationPreference"]:checked').attr('id');
            let preferenceValue = '';

            if (notificationPreference === 'notifyAll') {
                preferenceValue = 'all';
            } else if (notificationPreference === 'notifyMentions') {
                preferenceValue = 'mentions';
            } else if (notificationPreference === 'notifyNothing') {
                preferenceValue = 'nothing';
            }

            formData.set('notificationPreference', preferenceValue);

            const booleanFields = [
                'desktopNotifications',
                'unreadBadge',
                'bookingUpdates',
                'paymentNotifications',
                'userTenantNotifications',
                'vehicleManagement',
                'discountOffers'
            ];

            booleanFields.forEach(field => {
                const value = formData.get(field);
                formData.set(field, value === 'on' ? 1 : 0);
            });

            $.ajax({
                type: "POST",
                url: "/admin/settings/notifications/store",
                data: formData,
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },


                success: function (resp) {
                    $(".error-text").text("");
                    $(".form-control, .form-check-input").removeClass("is-invalid is-valid");

                    if (resp.code === 200) {
                        showToast('success', resp.message);
                    }
                    loadNotificationSettings();
                },
                error: function (error) {
                    $(".error-text").text("");
                    $(".form-control, .form-check-input").removeClass("is-invalid is-valid");

                    if (error.responseJSON.code === 422) {
                        $.each(error.responseJSON.errors, function (key, val) {
                            $("#" + key).addClass("is-invalid");
                            $("#" + key + "_error").text(val[0]);
                        });
                    } else {
                       showToast('error', error.responseJSON.message);
                    }
                }
            });
        }
    });
    loadNotificationSettings();

    function loadNotificationSettings() {
        $.ajax({
            url: '/admin/settings/company/list',
            type: 'POST',
            data: { 'group_id': 2 },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.code === 200) {
                    const settings = response.data;

                    settings.forEach(setting => {
                        const element = $('#' + setting.key);

                        if (setting.key === 'notificationPreference') {
                            if (setting.value.toLowerCase() === 'all') {
                                $('#notifyAll').prop('checked', true);
                            } else if (setting.value.toLowerCase() === 'mentions') {
                                $('#notifyMentions').prop('checked', true);
                            } else if (setting.value.toLowerCase() === 'nothing') {
                                $('#notifyNothing').prop('checked', true);
                            }
                        } else if (element.is(':checkbox')) {
                            element.prop('checked', setting.value == 1);
                        } else if (element.length) {
                            element.val(setting.value);
                        }
                    });
                }
            },
            error: function(xhr) {
                showToast('error', _l('admin.common.default_retrieve_error'));
            },
            complete: function() {
              $(".card-loader").addClass("d-none");
              $('.real-card').removeClass('d-none');
            }
        });
    }

});
})();
