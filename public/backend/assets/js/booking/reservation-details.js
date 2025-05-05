(function($) {
    "use strict";

(async () => {
    await loadTranslationFile('admin', 'common, bookings');

$(document).ready(function () {
    $("#cancelBookingForm").validate({
        rules: {
            cancel_reason: {
                required: true,
                minlength: 5,
                maxlength: 500,
            },
        },
        messages:{
            cancel_reason: {
                required: _l('admin.bookings.cancel_reason_required'),
                minlength: _l('admin.bookings.cancel_reason_minlength'),
                maxlength: _l('admin.bookings.cancel_reason_maxlength'),
            },
        },
        errorPlacement: function (error, element) {
            if (element.hasClass("select2-hidden-accessible")) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            } else {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            }
        },
        highlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next(".select2-container").addClass("is-invalid").removeClass('is-valid');
            }
            $(element).addClass("is-invalid").removeClass("is-valid");
        },
        unhighlight: function (element) {
            if ($(element).hasClass("select2-hidden-accessible")) {
                $(element).next(".select2-container").removeClass("is-invalid").addClass('is-valid');
            }
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
            let formData = new FormData(form);

            $.ajax({
                type:"POST",
                url:"/admin/cancel-booking",
                data: formData,
                enctype: "multipart/form-data",
                processData: false,
                contentType: false,
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                beforeSend: function () {
                    $('.submitbtn').attr('disabled', true).html(`
                        <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.cancelling')}..
                    `);
                },
                success:function(resp){
                    $(".error-text").text("");
                    $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                    $(".submitbtn").removeAttr("disabled").html(_l('admin.bookings.cancel_booking'));
                    if (resp.code === 200) {
                        showToast('success', resp.message);
                        $("#add_driver_modal").modal('hide');
                        window.location.href = resp.redirect_url;
                    }
                },
                error:function(error){
                    $(".error-text").text("");
                    $(".form-control, .select2-container").removeClass("is-invalid is-valid");
                    $(".submitbtn").removeAttr("disabled").html(_l('admin.common.cancel_booking'));
                    if (error.responseJSON.code === 422) {
                        $.each(error.responseJSON.errors, function(key, val) {
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
});

}) ();

})(jQuery);
