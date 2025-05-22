(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common,home');
    $(document).ready(function () {
        initIntelInput();
        initValidation();
        initSelect2();
        initEvents();
    });
     
    function initEvents(){
        setTimeout(function () {
            $("#country").trigger('change');
        }, 100);
        $("#country").on('change', function () {
            let id = $(this).val();
            if (id) {
                fetchStatesByCountry(id);
            } else {
                $("#state").empty();
                $("#state").append(`<option value="">${_l('web.common.select')}</option>`);
                $("#city").empty();
                $("#city").append(`<option value="">${_l('web.common.select')}</option>`);
            }
        });

        $("#state").on('change', function () {
            let id = $(this).val();
            if (id) {
                fetchCitiesByState(id);
            } else {
                $("#city").empty();
                $("#city").append(`<option value="">${_l('web.common.select')}</option>`);
            }
        });
    }
    function initSelect2() {
        $('.custom-select2').select2();
    }
    function initValidation(){
        $("#userProfileForm").validate({
            rules: {
                profile_photo: {
                    required: false,
                    extension: "jpeg|jpg|png",
                    filesize: 2048
                },
                first_name: {
                    required: true,
                    maxlength: 30
                },
                last_name: {
                    required: true,
                    maxlength: 30
                },
                email: {
                    required: true,
                    email: true
                },
                user_phone: {
                    required: true,
                    maxlength: 15 ,
                    minlength: 10
                },
                address_line: {
                    required: true,
                    maxlength: 50
                },
                country: {
                    required: true,
                },
                state: {
                    required: true,
                },
                city: {
                    required: true,
                },
                postal_code: {
                    required: true,
                    pattern: /^[0-9a-zA-Z]+$/ // Pattern for alphanumeric postal code
                },
            },
            messages: {
                first_name: {
                    required: _l('web.user.enter_first_name'),
                    maxlength: _l('web.common.maxlength_30')
                },
                last_name: {
                    required: _l('web.user.enter_last_name'),
                    maxlength: _l('web.common.maxlength_30')
                },
                email: {
                    required: _l('web.user.enter_email'),
                    email: _l('web.home.valid_email')
                },
                user_phone: {
                    required: _l('web.user.phone_number_required'),
                    maxlength: _l('web.home.phone_number_maxlength'),
                    minlength: _l('web.home.phone_number_minlength')
                },
                address_line: {
                    required: _l('web.user.enter_address'),
                    maxlength: _l('web.user.maxlength_50')
                },
                postal_code: {
                    required: _l('web.home.enter_pincode'),
                    pattern: "Please enter a valid postal code"
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
            submitHandler: function (form) {
                let adminProfileData = new FormData(form);
                adminProfileData.set('user_phone', $('#international_phone_number').val());

                // CSRF Token
                adminProfileData.append("_token", $('meta[name="csrf-token"]').attr('content'));

                $(".btn-primary").text(_l('web.user.plz_wait')).prop('disabled', true);

                $.ajax({
                    type: "POST",
                    url: "/userprofile",
                    data: adminProfileData,
                    processData: false,
                    contentType: false,
                    success: function (resp) {
                        showToast('success', resp.message);
                        if (resp.data.profile_image) {
                            $('.header_profile_image').attr('src', resp.data.profile_image);
                        }
                        $(".btn-primary").text(_l('web.user.save_changes')).prop('disabled', false);
                    },
                    error: function (error) {
                        $(".btn-primary").text(_l('web.user.save_changes')).prop('disabled', false);
                        if (error.responseJSON && error.responseJSON.code === 422) {
                            $.each(error.responseJSON.errors, function (key, val) {
                                $("#" + key).addClass("is-invalid");
                                $("#" + key + "_error").text(val[0]);
                            });
                        } else {
                            showToast('error', error.responseJSON?.message || "An error occurred.");
                        }
                    }
                });
            }
        });

        $.validator.addMethod("filesize", function (value, element, param) {
            if (element.files.length === 0) return true;
            return element.files[0].size <= param * 1024;
        }, "File size must be less than {0} KB.");
    }
    function initIntelInput(){
        const userPhoneInput = document.querySelector(".user_phone");
        const intlPhoneInput = document.querySelector("#international_phone_number");
        const userProfileForm = document.querySelector("#userProfileForm");

        if (userPhoneInput && userProfileForm) {
            const iti = intlTelInput(userPhoneInput, {
                utilsScript: `${window.location.origin}/frontend/assets/plugins/intltelinput/js/utils.js`,
                separateDialCode: true,
            });

            userPhoneInput.classList.add("iti");
            userPhoneInput.parentElement.classList.add("intl-tel-input");

            userProfileForm.addEventListener("submit", function (event) {
                event.preventDefault();

                const intlNumber = iti.getNumber();
                if (intlNumber) {
                    intlPhoneInput.value = intlNumber;
                }
            });
        }
    }
        
    $('#profile_photo').on('change', function (event) {
        let file = this.files[0];
        let error = '';

        if (file) {
            let allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!allowedTypes.includes(file.type)) {
                error = 'Only jpg, jpeg and png formats are allowed.';
            }

            if (file.size > 2 * 1024 * 1024) {
                error = 'Image size should be less than 2MB.';
            }

            if (error) {
                $('#profile_photo_error').text(error);
                $('#profile_photo_preview').attr('src', '').addClass('d-none');
                return;
            }

            let img = new Image();
            let objectURL = URL.createObjectURL(file);
            img.onload = function () {
                if (this.width < 180 || this.height < 180) {
                    $('#profile_photo_error').text('Image should be at least 180 x 180 pixels.');
                    $('#profile_photo_preview').attr('src', '').addClass('d-none');
                } else {
                    $('#profile_photo_error').text('');
                    let reader = new FileReader();
                    reader.onload = function (e) {
                        $('#profile_photo_preview').attr('src', e.target.result).removeClass('d-none');
                    };
                    reader.readAsDataURL(file);
                }
                URL.revokeObjectURL(objectURL);
            };
            img.src = objectURL;
        }

        $(this).valid();
    });

    function fetchStatesByCountry(country_id) {
        $.ajax({
            type: "POST",
            url: "/api/states",
            data: { country_id: country_id },
            headers: {
                'accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#state").empty();
                    $("#state").append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $("#state").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    let defaultState = $("#state").data('default-id');
                    setTimeout(function () {
                        if(defaultState) {
                            $("#state").val(defaultState).trigger('change');
                        }
                    }, 100);
                    // empty cities
                    $("#city").empty();
                    $("#city").append('<option value="">Select</option>');
                }
            }
        });
    }

    function fetchCitiesByState(state_id) {
        $.ajax({
            type: "POST",
            url: "/api/cities",
            data: { state_id: state_id },
            headers: {
                'accept': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.code === 200) {
                    let data = response.data;
                    $("#city").empty();
                    $("#city").append('<option value="">Select</option>');
                    $.each(data, function (key, value) {
                        $("#city").append('<option value="' + value.id + '">' + value.name + '</option>');
                    });
                    let defaultState = $("#city").data('default-id');
                    setTimeout(function () {
                        if(defaultState) {
                            $("#city").val(defaultState).trigger('change');
                        }
                    }, 100);
                    $("#city").trigger('change');
                }
            }
        });
    }
})();