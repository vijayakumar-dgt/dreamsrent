<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\GeneralSetting\Exceptions\OtpSettingsSaveException;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\UserDevice;
use Modules\GeneralSetting\Repositories\Contracts\SecuritySettingRepositoryInterface;

class SecuritySettingRepository implements SecuritySettingRepositoryInterface
{
    public function storeOtpSettings(array $data): void
    {
        $settings = [
            'otp_type'        => $data['otp_type'],
            'otp_digit_limit' => $data['otp_digit_limit'],
            'otp_expire_time' => $data['otp_expire_time'],
            'login'           => $data['login'] ?? false,
            'register'        => $data['register'] ?? false,
        ];

        foreach ($settings as $key => $value) {
            $saved = GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value]
            );

            if (!$saved) {
                \Log::error("Failed to save OTP setting: $key");
                throw new OtpSettingsSaveException("Failed to save $key");
            }
        }
    }

    public function updatePassword(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        $user->password = Hash::make($data['new_password']);
        $user->last_password_changed_at = now();
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.password_updated_successfully')];
    }

    public function updatePhoneNumber(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['phone_current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        if ($user->phone_number !== $data['current_phonenumber']) {
            return ['success' => false, 'message' => __('admin.general_settings.phone_number_incorrect')];
        }

        $user->phone_number = $data['new_phonenumber'];
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.phone_number_updated_successfully')];
    }

    public function updateEmail(array $data)
    {
        $user = Auth::guard('admin')->user();

        if (!Hash::check($data['email_current_password'], $user->password)) {
            return ['success' => false, 'message' => __('admin.general_settings.current_password_incorrect')];
        }

        if ($user->email !== $data['current_email']) {
            return ['success' => false, 'message' => __('admin.general_settings.current_email_incorrect')];
        }

        $user->email = $data['new_email'];
        $user->save();

        return ['success' => true, 'message' => __('admin.general_settings.email_updated_successfully')];
    }

    public function getSecuritySettings()
    {
        $user = Auth::guard('admin')->user();

        $devices = UserDevice::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->take(5)
            ->get()
            ->map(fn ($device) => [
                'id'          => $device->id,
                'device_type' => $device->device_type,
                'browser'     => $device->browser,
                'os'          => $device->os,
                'ip_address'  => $device->ip_address,
                'location'    => $device->location,
                'date'        => formatDateTime($device->created_at)
            ]);

        return [
            'user'                     => $user,
            'last_password_changed_at' => $user->last_password_changed_at ? formatDateTime($user->last_password_changed_at) : 'null',
            'devices'                  => $devices
        ];
    }

    public function logoutDevice(array $data)
    {
        $user = Auth::guard('admin')->user();

        if ($data['isAll'] === "true") {
            UserDevice::where('user_id', $user->id)->delete();
            Auth::guard('admin')->logout();

            return ['success' => true, 'message' => __('admin.general_settings.all_device_removed_successfully')];
        }

        $device = UserDevice::find($data['id']);
        if ($device) {
            $device->delete();
        }

        return ['success' => true, 'message' => __('admin.general_settings.device_removed_successfully')];
    }
}
