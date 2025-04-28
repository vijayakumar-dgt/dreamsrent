
(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');
    $(document).ready(function () {
        getSecuritySettings();
        $("#current_password").on('blur', function () {
            checkCurrentPassword("#current_password");
        });

        $(document).on('click','.changePasswordBtn', function(){
            resetPasswordForm();
        });
        function resetPasswordForm(){
            $("#changePasswordForm")[0].reset();
            $("#changePasswordForm #id").val('');
            $("#changePasswordForm .submitbtn").text(_l('web.user.save_changes'));
            $("#changePasswordForm .submitbtn").prop('disabled', false);
            $("#current_password").removeClass("is-invalid");
            $("#current_password_error").text("");
            $("#passwordSuccess").text("");
        }
        function checkCurrentPassword(elementId){
            return new Promise((resolve, reject) => {
                let password = $(elementId).val();
                if(password.length >= 6){
                   $.ajax({
                       url: '/user/check-current-password',
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
                   $(elementId + "_error").text(_l('web.user.password_length_must_6'));
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
                    required: _l('web.user.current_password_required'),
                },
                new_password: {
                    required: _l('web.user.new_password_required'),
                    minlength: _l('web.user.password_must_be_8'),
                    notEqualTo: _l('web.user.new_pass_must_be_diff')
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
               $("#changePasswordForm .submitbtn").text(_l('web.user.plz_wait'));
               $("#changePasswordForm .submitbtn").attr("disabled", true);
               checkCurrentPassword("#current_password").then((currentPasswordIsTrue) => {
                    if(!currentPasswordIsTrue){
                        $("#changePasswordForm .submitbtn").text(_l('web.user.save_changes'));
                        $("#changePasswordForm .submitbtn").prop('disabled', false);
                        showToast('error', _l('web.user.current_password_correct'));
                        return false;
                    }
                    $.ajax({
                        type:"POST",
                        url:"/user/update-password",
                        data:_formData,
                        processData: false,
                        contentType: false,
                        success:function(resp){
                            if (resp.code === 200) {
                                showToast('success', resp.message);
                                $("#change_password").modal('hide');
                            }else{
                                showToast('error', resp.message);
                            }
                            getSecuritySettings();
                            $("#changePasswordForm .submitbtn").text(_l('web.user.save_changes'));
                            $("#changePasswordForm .submitbtn").prop('disabled', false);
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
                            $("#changePasswordForm .submitbtn").text(_l('web.user.save_changes'));
                            $("#changePasswordForm .submitbtn").prop('disabled', false);
                        }
                    });
               });
            }
        });
        function getSecuritySettings() {
            $.ajax({
                type:"GET",
                url:"/user/security-details",
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
                            $("#userDevicesTable tbody").html(`<tr><td colspan="5" class="text-center">${_l('web.user.no_data_found')}</td></tr>`);
                        }
                        let lastPasswordChanged = response.data.last_password_changed_at;
                        if (lastPasswordChanged && lastPasswordChanged !== "null") {
                            $('.change_password_time').text(`${_l('web.user.last_changed')} ${lastPasswordChanged}`);
                        } else {
                            $('.change_password_time').text(_l('web.user.last_changed') + ': ' + _l('web.user.not_available'));
                        }
                        let lastDeletedAt = response.data.deleted_at;
                        if (lastDeletedAt && lastDeletedAt !== "null") {
                            $('.delete_account_time').text(`${_l('web.user.last_changed')} ${lastDeletedAt}`);
                        } else {
                            $('.delete_account_time').text(_l('web.user.last_changed') + ': ' + _l('web.user.not_available'));
                        }
                        let devices = response.data.devices;
                        let lastDeviceManagement = devices.length > 0 ? devices[0].date : null;

                        if (lastDeviceManagement && lastDeviceManagement !== "null") {
                            $('.device_management_time').text(`${_l('web.user.last_changed')} ${lastDeviceManagement}`);
                        } else {
                            $('.device_management_time').text(_l('web.user.last_changed') + ': ' + _l('web.user.not_available'));
                        }

                        $(".last_changed").html(lastPasswordChanged);
                        if(response.data.user.google_auth_enabled){
                            $("#google_auth").prop("checked",true);
                        }else{
                            $("#google_auth").prop("checked",false);
                        }
                        $(".verified_emailtxt").text(response.data.user.email);
                        $(".verified_phonetxt").text(response.data.user.phone ?? '-');
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
                    }
                }
            });
        }

        $(document).on('click','.signoutall', function(){
            logoutDevice(0,true);
        });

    });
})();

function confirmDelete() {
    const userId = $('#deleteUserId').val();

    $.ajax({
        url: `/admin/delete-account/${userId}`,
        type: 'post',
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(data) {
            if (data.success) {
                showToast('success', _l('web.user.account_deleted'));
                setTimeout(() => {
                    window.location.href = '/user-logout';
                }, 1500);
            } else {
                showToast('error', data.message || 'An error occurred while deleting your account.');
            }
        },
        error: function(xhr) {
            console.error('Error:', xhr.responseText);
            showToast('error', 'Failed to delete your account. Please try again later.');
        }
    });
}
