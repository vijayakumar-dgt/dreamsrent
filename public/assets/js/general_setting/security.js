(async () => {
    await loadTranslationFile('admin', 'general_settings,common');

    $(document).ready(function () {
   
        getSecuritySettings();
      
    
        $(document).on('click','.changePasswordBtn', function(){
            resetPasswordForm();
        });
        function resetPasswordForm(){
            $("#changePasswordForm")[0].reset();
            $("#changePasswordForm #id").val('');
            $("#changePasswordForm .submitbtn").text(_l('admin.common.save_changes'));
            $("#changePasswordForm .submitbtn").prop('disabled', false);
            $("#current_password").removeClass("is-invalid");
            $("#current_password_error").text("");
            $("#passwordSuccess").text("");
        }
        $(document).on('click','.changePhoneNumberBtn', function(){
            resetPhoneNumberForm();
        })
        function resetPhoneNumberForm(){
            $("#changePhoneNumberForm")[0].reset();
            $("#changePhoneNumberForm #id").val('');
            $("#changePhoneNumberForm .submitbtn").text(_l('admin.general_settings.save_changes'));
            $("#changePhoneNumberForm .submitbtn").prop('disabled', false);
            $("#phone_current_password_error").text("");
        }
        function checkCurrentPassword(elementId){
            return new Promise((resolve, reject) => {
                let password = $(elementId).val();
                if(password.length >= 6){
                   $.ajax({
                       url: '/admin/settings/check-current-password',
                       type: 'POST',
                       data: {
                           "_token": $('meta[name="csrf-token"]').attr('content'),
                           "password": password
                       },
                       success: function (response) {
                           if (response.code === 200) {
                               $(elementId).removeClass("is-invalid");
                               $(elementId).addClass("is-valid");
                               $(elementId + "_error").text("");
                               $("#passwordSuccess").text(response.message);
                               resolve(true);
                           } else {
                               $(elementId).removeClass("is-valid");
                               $(elementId).addClass("is-invalid");
                               $(elementId + "_error").text(response.message);
                               $("#passwordSuccess").text("");
                               resolve(false);
                           }
                       },
                       error: function (error) {
                           $(elementId).removeClass("is-valid");
                           $(elementId).addClass("is-invalid");
                           $(elementId + "_error").text(error.responseJSON.message);
                           $("#passwordSuccess").text("");
                           resolve(false);
                       }
                   });
               }else{
                   $(elementId).removeClass("is-valid");
                   $(elementId).addClass("is-invalid");
                   $(elementId + "_error").text(_l('admin.general_settings.password_incorrect'));
                   $("#passwordSuccess").text("");
                   resolve(false);
               }
            });
    
        }
    
        $("#changePasswordForm").validate({
            rules: {
                current_password: {
                    required: true,
                },
                new_password: {
                    required: true,
                    minlength: 8,
                    notEqualTo: '#current_password'
                },
                confirm_password: {
                    required: true,
                    equalTo: '#new_password'
                }
            },
            messages:{
                current_password: {
                    required: _l('admin.general_settings.enter_current_password'),
                },
                new_password: {
                    required: _l('admin.general_settings.enter_current_password'),
                    minlength:_l('admin.general_settings.password_character'),
                    notEqualTo: _l('admin.general_settings.new_paasword_different'),
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
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
               let _formData = new FormData(form);
                
                $.ajax({
                    type:"POST",
                    url:"/admin/settings/update-password",
                    data:_formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function () {
                        $('#changePasswordForm .submitbtn').attr('disabled', true).html(`
                            <span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> ${_l('admin.general_settings.please_wait')}..
                        `); 
                    },
                    success:function(resp){
                        if (resp.code === 200) {
                            showToast('success', resp.message);
                            $("#change_password").modal('hide');
                        }else{
                            showToast('error', resp.message);
                        }
                        getSecuritySettings();
                    },
                    complete: function () {
                        $('#changePasswordForm .submitbtn').attr('disabled', false).html(_l('admin.common.save_changes'));

                    },
                    error:function(error){
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
                        $("#changePasswordForm .submitbtn").text(_l('admin.common.save_changes'));
                        $("#changePasswordForm .submitbtn").prop('disabled', false);
                    }
                });
            }
        });
    
    
        if ($('#passwordInput').length > 0) {
            "use strict";
    
            let $passwordInput = $('#passwordInput input[type="password"]');
            let $passwordStrength = $('#passwordStrength');
            let $poor = $('#poor');
            let $weak = $('#weak');
            let $strong = $('#strong');
            let $heavy = $('#heavy');
    
          
            let lowerCaseRegExp = /[a-zA-Z]/;  
            let numberRegExp = /[0-9]/;      
            let specialCharRegExp = /[#?!@$%^&*()_+\-=<>:{}[\]\\|~`]/;
            let whitespaceRegExp = /\s/;    
    
    
        
            $passwordInput.on('keyup', function () {
                let passwordValue = $(this).val();
                let passwordLength = passwordValue.length;
    
                let hasLetter = lowerCaseRegExp.test(passwordValue);
                let hasNumber = numberRegExp.test(passwordValue);
                let hasSpecialChar = specialCharRegExp.test(passwordValue);
                let hasWhitespace = whitespaceRegExp.test(passwordValue);
    
                let passwordStrength = 0;
    
               
                if (hasWhitespace) {
                    passwordStrength = 0;
                } else {
                    if (hasLetter) passwordStrength++;
                    if (hasNumber) passwordStrength++;
                    if (hasSpecialChar) passwordStrength++;
                    if (passwordLength >= 8) passwordStrength++;
    
                 
                    if (passwordLength < 8) {
                        passwordStrength = 1;
                    }
                }
                updateStrength(passwordStrength);
            });
    
            function updateStrength(passwordStrength){
               
                $passwordStrength.find('span').removeClass('active');
    
                $passwordStrength.removeClass('poor-active avg-active strong-active heavy-active');
                if($passwordStrength === 0) {
                    $poor.addClass('active');
                    $passwordStrength.addClass('poor-active');
                }else if(passwordStrength === 1) {
                    $poor.addClass('active');
                    $passwordStrength.addClass('poor-active');
                }else if(passwordStrength === 2) {
                    $poor.addClass('active');
                    $weak.addClass('active');
                    $passwordStrength.addClass('avg-active');
                }else if(passwordStrength === 3) {
                    $poor.addClass('active');
                    $weak.addClass('active');
                    $strong.addClass('active');
                    $passwordStrength.addClass('strong-active');
                }else if(passwordStrength === 4) {
                    $poor.addClass('active');
                    $weak.addClass('active');
                    $strong.addClass('active');
                    $heavy.addClass('active');
                    $passwordStrength.addClass('heavy-active');
                }
            }
    
        }
    
    
    
      
        function checkCurrentPhoneNumber(){
            return new Promise((resolve, reject) => {
                let currentPhoneNumber = $('#current_phonenumber').val();
                $.ajax({
                    type:"POST",
                    url:"/admin/settings/check-current-phonenumber",
                    data:{"_token": $('meta[name="csrf-token"]').attr('content'),
                        "currentPhoneNumber": currentPhoneNumber
                    },
                    success:function(resp){
                        if (resp.code === 200) {
                            resolve(resp);
                        }else{
                            resolve(resp);
                        }
                    },
                    error:function(error){
                        resolve(error.responseJSON);
                    }
                });
            });
        }
    
        $("#changePhoneNumberForm").validate({
            rules: {
                current_phonenumber: {
                    required: true,
                    minlength: 8,
                    maxlength: 12,
                },
                new_phonenumber: {
                    required: true,
                    minlength: 8,
                    maxlength: 12,
                },
                phone_current_password: {
                    required: true,
                    minlength: 6,
                }
            },
            messages:{
                current_phonenumber: {
                    required: _l('admin.general_settings.enter_phone_number'),
                    minlength: _l('admin.general_settings.phone_number_character'),
                    maxlength: _l('admin.general_settings.phone_number_max_character'),
                },
                new_phonenumber: {
                    required:  _l('admin.general_settings.phone_number_max_character'),
                    minlength:  _l('admin.general_settings.new_phone_number_min_character'),
                    maxlength:  _l('admin.general_settings.new_phone_number_max_character'),
                },
                phone_current_password: {
                    required:  _l('admin.general_settings.enter_current_password'),
                    minlength:_l('admin.general_settings.password_character'),
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
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
               let _formData = new FormData(form);
               $("#changePhoneNumberForm .submitbtn").text(_l('admin.common.please_wait'));
               $("#changePhoneNumberForm .submitbtn").attr("disabled", true);
               $("#current_phonenumber_error").text("");
                           
                $.ajax({
                    type:"POST",
                    url:"/admin/settings/update-phone-number",
                    data:_formData,
                    processData: false,
                    contentType: false,
                    
                    success:function(resp){
                        if (resp.status === 'success') {
                            showToast('success', resp.message);
                            $("#changePhoneNumberForm")[0].reset();
                            $("#changePhoneNumberForm .submitbtn").text(_l('admin.common.save_changes'));
                            $("#changePhoneNumberForm .submitbtn").prop('disabled', false);
                            $("#change_phonenumber").modal('hide');
                        }else{
                            showToast('error', resp.message);
                            $("#changePhoneNumberForm .submitbtn").text(_l('admin.common.save_changes'));
                            $("#changePhoneNumberForm .submitbtn").prop('disabled', false);
                        }
                        getSecuritySettings();
                    },
                    error:function(error){
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $("#changePhoneNumberForm .submitbtn").text(_l('admin.common.save_changes'));
                        $("#changePhoneNumberForm .submitbtn").prop('disabled', false);
                        showToast('error', error.responseJSON.message);
                    }
                });
            }
        });
      
        function checkCurrentEmail(){
            return new Promise((resolve, reject) => {
                let currentemail = $('#current_email').val();
                let useremail = $("#current_email").data('email');
                if(currentemail === useremail){
                    resolve({code: 200});
                }else{
                    resolve({code: 422, error: 'incorrect'});
                }
            });
        }
        $("#changeEmailForm").validate({
            rules: {
                current_email: {
                    required: true,
                    email: true,
                },
                new_email: {
                    required: true,
                    email: true,
                },
                email_current_password: {
                    required: true,
                    minlength: 6,
                }
            },
            messages:{
                current_email: {
                    required: _l('admin.general_settings.current_email_required'),
                    email: _l('admin.general_settings.enter_valid_email'),
                },
                new_email: {
                    required:_l('admin.general_settings.new_email_required'),
                    email: _l('admin.general_settings.enter_valid_email')
                },
                email_current_password: {
                    required: _l('admin.general_settings.enter_current_password'),
                    minlength: _l('admin.general_settings.password_character'),
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
            onkeyup: function(element) {
                $(element).valid();
            },
            onchange: function(element) {
                $(element).valid();
            },
            submitHandler: function(form) {
               let _formData = new FormData(form);
               $("#changeEmailForm .submitbtn").text(_l('admin.common.save_changes'));
               $("#changeEmailForm .submitbtn").attr("disabled", true);
               $("#current_email_error").text("");
               $("#email_current_password_error").text("");
                            
                $.ajax({
                    type:"POST",
                    url:"/admin/settings/update-email",
                    data:_formData,
                    processData: false,
                    contentType: false,
                    success:function(resp){
                        if (resp.status == 'success') {
                            showToast('success', resp.message);
                            $("#changeEmailForm")[0].reset();
                            $("#changeEmailForm .submitbtn").text('Save Changes');
                            $("#changeEmailForm .submitbtn").prop('disabled', false);
                            $("#change_email").modal('hide');
                        }else{
                            showToast('error', resp.message);
                            $("#changeEmailForm .submitbtn").text('Save Changes');
                            $("#changeEmailForm .submitbtn").prop('disabled', false);
                        }
                        getSecuritySettings();
                    },
                    error:function(error){
                        $(".error-text").text("");
                        $(".form-control").removeClass("is-invalid is-valid");
                        $("#changeEmailForm .submitbtn").text(_l('admin.common.save_changes'));
                        $("#changeEmailForm .submitbtn").prop('disabled', false);
                        showToast('error', error.responseJSON.message);
                    }
                });
            }
        });
       
    
        function getSecuritySettings() {
            $.ajax({
                type:"GET",
                url:"/admin/settings/get-security-settings",
                beforeSend: function () {
                    $(".table-loader").show();
                    $(".real-table").addClass('d-none');
                },
                success: function(response) {
                    if(response.code === 200){
                        if(response.data && response.data.devices && response.data.devices.length > 0){
                            let devices = response.data.devices;
                            let deviceList = '';
                            $.each(devices, function(index, device) {
                                deviceList += `<tr>
                                                <td>
                                                        <h6 class="fs-14">${device.browser ?? ''} - ${device.os ?? ''}</h6>
                                                    </td>
                                                    <td>
                                                        <p class="text-gray-9">${device.date ?? '-'}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-gray-9">${device.ip_address ?? "-"}</p>
                                                    </td>
                                                    <td>
                                                        <p class="text-gray-9">${device.location ?? "-"}</p>
                                                    </td>
                                                    <td>
                                                        <div class="action-btn">
                                                            <a href="javascript:void(${device.id});" data-id="${device.id}" class="p-1 logoutDevice"><i class="ti ti-logout text-dark"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>`;
                            });
                            $("#userDevicesTable tbody").html(deviceList);
                        }else{
                            $("#userDevicesTable tbody").html(`<tr><td colspan="5" class="text-center">${_l('admin.common.no_data_found')}</td></tr>`);
                        }
                        let lastPasswordChanged = '';
                        if(response.data.last_password_changed_at != "null"){
                             lastPasswordChanged = `<i class="ti ti-circle-check-filled text-success me-1"></i> ${_l('admin.general_settings.last_changed')} ${response.data.last_password_changed_at}`;
                        }else{
                             lastPasswordChanged = `<i class="ti ti-circle-check-filled text-danger me-1"></i> ${_l('admin.general_settings.not_changed')}`;
                        }
                        let google_authText = '';
                        if(response.data.user.google_auth_enabled){
                            google_authText = `<i class="ti ti-point-filled text-success me-1"></i> ${_l('admin.general_settings.connected')}`;
                        }else{
                            google_authText = `<i class="ti ti-point-filled text-danger me-1"></i>${_l('admin.general_settings.disconnected')}`;
                        }
                        $(".last_changed").html(lastPasswordChanged);
                        $(".google_auth").html(google_authText);
                        if(response.data.user.google_auth_enabled){
                            $("#google_auth").prop("checked",true);
                        }else{
                            $("#google_auth").prop("checked",false);
                        }
                        $(".verified_emailtxt").text(response.data.user.email);
                        $(".verified_phonetxt").text(response.data.user.phone_number ?? '-');
                    }
                },
                complete: function () {
                    $(".table-loader").hide();
                    $(".real-table").removeClass('d-none');
                }
            });
        }
    
        $(document).on('click', '.logoutDevice', function(e){
            e.preventDefault();
            logoutDevice($(this).data('id'));
        })
        function logoutDevice(id, isAll = false) {
            $.ajax({
                type:"POST",
                url:"/admin/settings/logout-device",
                data:{id:id, isAll:isAll, _token:$('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if(response.code === 200){
                        getSecuritySettings();
                        showToast('success', response.message);
                        setTimeout(function () {
                            if(isAll){
                                location.reload();
                            } 
                        }, 3000);
                    }
                }
            });
        }
    
        $(document).on('click','.signoutall', function(){
            logoutDevice(0,true);
        });
    
        $(document).on('click','#google_auth', function(){
            let googleAuthEnabled = false;
            if($(this).is(":checked")){
                googleAuthEnabled = true;
            }
            $.ajax({
                type:"POST",
                url:"/admin/settings/update-google-auth",
                data:{googleAuthEnabled:googleAuthEnabled, _token:$('meta[name="csrf-token"]').attr('content')},
                success: function(response) {
                    if(response.code === 200){
                        getSecuritySettings();
                        showToast('success', response.message);
                    }else{
                        getSecuritySettings();
                        showToast('error', response.message);
                    }
                },
                error:function(error){
                    getSecuritySettings();
                    showToast('error', error.responseJSON.message);
                }
            });
        });
    });
    
})();


