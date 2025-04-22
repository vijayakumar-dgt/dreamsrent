(async () => {
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function() {
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 150, // Set the height of the editor
                placeholder: 'Type your content here...',
                toolbar: [
                    ['style', ['bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    
        $("#cookiesSettingForm").validate({
            rules: {
                cookiesContentText: {
                    required: true,
                    maxlength: 60
                },
                cookiesPosition: {
                    required: true
                },
                agreeButtonText: {
                    required: true,
                    minlength: 2
                },
                declineButtonText: {
                    required: true,
                    minlength: 2
                },
                showDeclineButton: {
                    required: false
                },
                cookiesPageLink: {
                    required: true,
                    url: true
                }
            },
            messages: {
                cookiesContentText: {
                    required: _l('admin.general_settings.enter_cookies_content_text'),
                    maxlength: _l('admin.general_settings.cookies_content_length'),
                },
                cookiesPosition: {
                    required: _l('admin.general_settings.select_cookies_position'),
                },
                agreeButtonText: {
                    required:_l('admin.general_settings.enter_agree_button_text'),
                    minlength:_l('admin.general_settings.agree_button_characters'),
                },
                declineButtonText: {
                    required: _l('admin.general_settings.enter_decline_button_text'),
                    minlength: _l('admin.general_settings.decline_button_characters'),
                },
                cookiesPageLink: {
                    required:  _l('admin.general_settings.cookies_page_link'),
                    url:_l('admin.general_settings.enter_valid_url'),
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
                let cookiesData = new FormData(form);
    
                $.ajax({
                    type: "POST",
                    url: "/admin/settings/cookies/store",
                    data: cookiesData,
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
                            loadCookiesSettings();
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
    
        loadCookiesSettings();
    
    
    });
    
})();


    function loadCookiesSettings(languageId = null) {

        $.ajax({
            url: '/admin/settings/cookies/list',
            type: 'POST',
            data: {
                group_id: 7,
                language_id: languageId
            },
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.code === 200) {
                    const settings = response.data;

                    Object.keys(settings).forEach(function (key) {
                        const baseKey = key.split('_')[0]; // strip _1, _2, etc.
                        const value = settings[key];

                        if (baseKey === 'cookiesContentText') {
                            $('#cookiesContentText').summernote('code', value);
                        } else {
                            const element = $('#' + baseKey);
                            if (element.length) {
                                if (element.attr('type') === 'checkbox') {
                                    element.prop('checked', value === "1");
                                } else if (element.is('select')) {
                                    element.val(value).trigger('change');
                                } else {
                                    element.val(value);
                                }
                            }
                        }
                    });
                }
            },
            error: function(xhr) {
                console.log('Error loading cookies settings:', xhr.responseText);
            },
            complete: function() {
                $(".label-loader, .input-loader").hide();
                $('.real-label, .real-input').removeClass('d-none');
            }
        });
    }
