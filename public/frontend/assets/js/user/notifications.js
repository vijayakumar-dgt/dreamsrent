/* global loadTranslationFile, document, showToast, _l, jQuery */
(function($) {
    "use strict";

    (async () => {
        await loadTranslationFile('web', 'user,common');
        
        $(document).on('click','.submitbtn', function() {
            $.ajax({
                type: 'POST',
                url: '/user/update-notification-settings',
                data: {
                    'booking_confirmation': $('#booking').is(':checked') ? 1 : 0,
                    'desktop_notifications': $('#desktop_notifications').is(':checked') ? 1 : 0,
                    'email_notifications': $('#email_notifications').is(':checked') ? 1 : 0,
                    '_token': $('meta[name="csrf-token"]').attr('content')

                },
                beforeSend: function () {
                    $('.submitbtn').attr('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('web.common.saving')}..
                    `); 
                },
                success: function(response) {
                    if(response.code == 200) {
                        showToast('success', response.message);
                    }
                },
                complete: function () {
                    $('.submitbtn').attr('disabled', false).html(_l('web.user.save_changes'));
                }
            });
        });
    })();

})(jQuery);