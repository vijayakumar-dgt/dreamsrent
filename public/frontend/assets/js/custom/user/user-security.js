(async () => {
    "use strict";
    await loadTranslationFile('web', 'user,common');

    $(document).ready(function () {
        initializeEventListeners();
        getSecuritySettings();
    });

    function initializeEventListeners() {
        $("#current_password").on('blur', () => checkCurrentPassword("#current_password"));
        $(document).on('click', '.changePasswordBtn', resetPasswordForm);
        $(document).on('click', '.logoutDevice', (e) => {
            e.preventDefault();
            logoutDevice($(e.currentTarget).data('id'));
        });
        $(document).on('click', '.signoutall', () => logoutDevice(0, true));

        $("#changePasswordForm").validate({
            rules: getValidationRules(),
            messages: getValidationMessages(),
            errorPlacement: (error, element) => $(`#${element.attr("id")}_error`).text(error.text()),
            highlight: (element) => $(element).addClass("is-invalid").removeClass("is-valid"),
            unhighlight: (element) => {
                $(element).removeClass("is-invalid").addClass("is-valid");
                $(`#${element.id}_error`).text("");
            },
            onkeyup: (element) => $(element).valid(),
            onchange: (element) => $(element).valid(),
            submitHandler: handlePasswordChange
        });
    }

    function resetPasswordForm() {
        const form = $("#changePasswordForm");
        form[0].reset();
        form.find("#id").val('');
        form.find(".submitbtn").text(_l('web.user.save_changes')).prop('disabled', false);
        $("#current_password").removeClass("is-invalid");
        $("#current_password_error, #passwordSuccess").text("");
    }

    function checkCurrentPassword(elementId) {
        return new Promise((resolve) => {
            const password = $(elementId).val();
            if (password.length < 6) {
                setInvalidPassword(elementId, _l('web.user.password_length_must_6'));
                return resolve(false);
            }

            $.ajax({
                url: '/user/check-current-password',
                type: 'POST',
                data: {
                    "_token": $('meta[name="csrf-token"]').attr('content'),
                    "password": password
                },
                success: (response) => handlePasswordCheckResponse(response, elementId, resolve),
                error: (error) => handlePasswordCheckError(error, elementId, resolve)
            });
        });
    }

    function handlePasswordCheckResponse(response, elementId, resolve) {
        if (response.code === 200) {
            setValidPassword(elementId, response.message);
            resolve(true);
        } else {
            setInvalidPassword(elementId, response.message);
            resolve(false);
        }
    }

    function handlePasswordCheckError(error, elementId, resolve) {
        setInvalidPassword(elementId, error.responseJSON.message);
        resolve(false);
    }

    function setValidPassword(elementId, message) {
        $(elementId).removeClass("is-invalid").addClass("is-valid");
        $(`${elementId}_error`).text("");
        $("#passwordSuccess").text(message);
    }

    function setInvalidPassword(elementId, message) {
        $(elementId).removeClass("is-valid").addClass("is-invalid");
        $(`${elementId}_error`).text(message);
        $("#passwordSuccess").text("");
    }

    function handlePasswordChange(form) {
        const formData = new FormData(form);
        const submitBtn = $("#changePasswordForm .submitbtn");
        submitBtn.text(_l('web.user.plz_wait')).attr("disabled", true);

        checkCurrentPassword("#current_password").then((isValid) => {
            if (!isValid) {
                submitBtn.text(_l('web.user.save_changes')).prop('disabled', false);
                showToast('error', _l('web.user.current_password_correct'));
                return;
            }

            $.ajax({
                type: "POST",
                url: "/user/update-password",
                data: formData,
                processData: false,
                contentType: false,
                success: (resp) => handlePasswordUpdateSuccess(resp, submitBtn),
                error: (error) => handlePasswordUpdateError(error, submitBtn)
            });
        });
    }

    function handlePasswordUpdateSuccess(resp, submitBtn) {
        if (resp.code === 200) {
            showToast('success', resp.message);
            $("#change_password").modal('hide');
        } else {
            showToast('error', resp.message);
        }
        getSecuritySettings();
        resetSubmitButton(submitBtn);
    }

    function handlePasswordUpdateError(error, submitBtn) {
        $(".error-text").text("");
        $(".form-control").removeClass("is-invalid is-valid");

        if (error.responseJSON.code === 422) {
            $.each(error.responseJSON.errors, (key, val) => {
                $(`#${key}`).addClass("is-invalid");
                $(`#${key}_error`).text(val[0]);
            });
        } else {
            showToast('error', error.responseJSON.message);
        }
        resetSubmitButton(submitBtn);
    }

    function resetSubmitButton(button) {
        button.text(_l('web.user.save_changes')).prop('disabled', false);
    }

    function getValidationRules() {
        return {
            current_password: { required: true },
            new_password: {
                required: true,
                minlength: 8,
                notEqualTo: '#current_password'
            },
            confirm_password: {
                required: true,
                equalTo: '#new_password'
            }
        };
    }

    function getValidationMessages() {
        return {
            current_password: { required: _l('web.user.current_password_required') },
            new_password: {
                required: _l('web.user.new_password_required'),
                minlength: _l('web.user.password_must_be_8'),
                notEqualTo: _l('web.user.new_pass_must_be_diff')
            }
        };
    }

    function getSecuritySettings() {
        $.ajax({
            type: "GET",
            url: "/user/security-details",
            beforeSend: () => toggleTableLoader(true),
            success: updateSecuritySettings,
            complete: () => toggleTableLoader(false)
        });
    }

    function updateSecuritySettings(response) {
        if (response.code !== 200) return;

        const data = response.data || {};
        updateDeviceList(data.devices || []);
        updateLastChangedInfo(data);
        updateUserInfo(data.user || {});
    }

    function updateDeviceList(devices) {
        const deviceList = devices.length
            ? devices.map(device => `
                <tr>
                    <td><h6 class="fs-14">${device.browser || ''} - ${device.os || ''}</h6></td>
                    <td><p class="text-gray-9">${device.date || '-'}</p></td>
                    <td><p class="text-gray-9">${device.ip_address || '-'}</p></td>
                    <td><p class="text-gray-9">${device.location || '-'}</p></td>
                </tr>`).join('')
            : `<tr><td colspan="5" class="text-center">${_l('web.user.no_data_found')}</td></tr>`;

        $("#userDevicesTable tbody").html(deviceList);
    }

    function updateLastChangedInfo(data) {
        const lastPasswordChanged = data.last_password_changed_at || _l('web.user.not_available');
        const lastDeletedAt = data.deleted_at || _l('web.user.not_available');
        const lastDeviceManagement = (data.devices?.[0]?.date) || _l('web.user.not_available');

        $('.change_password_time').text(`${_l('web.user.last_changed')} ${lastPasswordChanged}`);
        $('.delete_account_time').text(`${_l('web.user.last_changed')} ${lastDeletedAt}`);
        $('.device_management_time').text(`${_l('web.user.last_changed')} ${lastDeviceManagement}`);
    }

    function updateUserInfo(user) {
        $("#google_auth").prop("checked", !!user.google_auth_enabled);
        $(".verified_emailtxt").text(user.email || '');
        $(".verified_phonetxt").text(user.phone || '-');
    }

    function toggleTableLoader(show) {
        $(".table-loader").toggle(show);
        $(".real-table").toggleClass('d-none', show);
    }

    function logoutDevice(id, isAll = false) {
        $.ajax({
            type: "POST",
            url: "/admin/settings/logout-device",
            data: { id, isAll, _token: $('meta[name="csrf-token"]').attr('content') },
            success: (response) => {
                if (response.code === 200) {
                    getSecuritySettings();
                    showToast('success', response.message);
                }
            }
        });
    }
})();

function confirmDelete() {
    const userId = $('#deleteUserId').val();

    $.ajax({
        url: `/admin/delete-account/${userId}`,
        type: 'POST',
        contentType: 'application/json',
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: (data) => {
            if (data.success) {
                showToast('success', _l('web.user.account_deleted'));
                setTimeout(() => window.location.href = '/user-logout', 1500);
            } else {
                showToast('error', data.message || 'An error occurred while deleting your account.');
            }
        },
        error: (xhr) => {
            console.error('Error:', xhr.responseText);
            showToast('error', 'Failed to delete your account. Please try again later.');
        }
    });
}
