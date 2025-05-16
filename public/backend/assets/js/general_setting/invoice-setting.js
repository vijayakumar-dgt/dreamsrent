(async () => {
    "use strict";
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function() {
        $("#invoiceSettingForm").validate({
            rules: {
                invoice_logo: {
                    required: false,
                    accept: "image/*"
                },
                invoice_prefix: {
                    required: true,
                    minlength: 2
                },
                invoice_due: {
                    required: true
                },
                invoice_round_off: {
                    required: true
                },
                invoice_terms: {
                    required: true,
                    maxlength: 300
                }
            },
            messages: {
                invoice_logo: {
                    accept: _l('admin.general_settings.invoice_logo_accept'),
                },
                invoice_prefix: {
                    required: _l('admin.general_settings.invoice_prefix_required'),
                    minlength:_l('admin.general_settings.invoice_prefix_minlength'),
                },
                invoice_due: {
                    required: _l('admin.general_settings.invoice_due_required'),
                },
                invoice_round_off: {
                    required: _l('admin.general_settings.invoice_round_off_required'),
                },
                invoice_terms: {
                    required: _l('admin.general_settings.invoice_terms_required'),
                    maxlength: _l('admin.general_settings.invoice_terms_maxlength'),
                }
            },
            errorPlacement: function(error, element) {
                var errorId = element.attr("id") + "_error";
                $("#" + errorId).text(error.text());
            },
            highlight: function(element) {
                $(element).addClass("is-invalid").removeClass("is-valid");
            },
            unhighlight: function(element) {
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
                let invoiceData = new FormData(form);

                $.ajax({
                    type: "POST",
                    url: "/admin/settings/invoice-settings/store",
                    data: invoiceData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    beforeSend: function () {
                        $('.btn-primary').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.common.saving')}..
                        `);
                    },
                    complete: function () {
                        $('.btn-primary').attr('disabled', false).html(_l('admin.common.save_changes'));
                    },

                    success: function(resp) {
                        if (resp.code === 200) {
                            loadInvoiceSettings();
                            showToast('success', resp.message);

                        }
                    },
                    error: function(error) {
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");

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

        loadInvoiceSettings();

        function loadInvoiceSettings() {
            $.ajax({
                url: '/admin/settings/company/list',
                type: 'POST',
                data: { 'group_id': 9 },
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },

                success: function(response) {
                    if (response.code === 200) {
                        const settings = response.data;

                        settings.forEach(setting => {
                            const element = $('#' + setting.key);

                            if (setting.key === 'invoice_logo' && setting.value) {
                                const imageUrl = `/storage/${setting.value}`;
                                $('#profile_photo_preview').attr('src', imageUrl).show();
                            }

                            else if (setting.key === 'invoice_terms') {
                                $('#invoice_terms').val(setting.value);
                            }

                            else if (setting.key === 'show_company_details' || setting.key === 'round_off_enabled') {
                                element.prop('checked', setting.value == 1);
                            }

                            else if (element.length) {
                                element.val(setting.value);
                            }
                        });
                    }
                },
                error: function(xhr) {
                    showToast('error', _l('admin.common.default_retrieve_error'));
                },
                complete: function() {
                    $(".label-loader, .input-loader, .card-loader").hide();
                    $('.real-label, .real-input, .real-card').removeClass('d-none');
                }
            });
        }
    });

})();



function previewImage(event) {
    const reader = new FileReader();
    const preview = document.getElementById('profile_photo_preview');

    reader.onload = function () {
        preview.src = reader.result;
    }

    if (event.target.files.length > 0) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

function removeImage() {
    const preview = document.getElementById('profile_photo_preview');
    const fileInput = document.getElementById('profile_photo');

    preview.src = '/backend/assets/img/settings/company-logo-01.jpg';
    fileInput.value = '';
}

