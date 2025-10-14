<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserSecurityRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\GeneralSetting\Models\UserDevice;

class UserSecurityRepository implements UserSecurityRepositoryInterface
{
    public function checkCurrentPassword(Request $request): array
    {
        $password = $request->password;
        $user = Auth::guard('web')->user();

        if ($user && $user->password && Hash::check($password, $user->password)) {
            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.current_password_correct'),
            ];
        }

        return [
            'status'  => 'error',
            'code'    => 422,
            'message' => __('web.user.current_password_incorrect'),
        ];
    }

    public function updatePassword(Request $request): array
    {
        $user = Auth::guard('web')->user();

        if (!$user || !$user->password || !Hash::check($request->current_password, $user->password)) {
            return [
                'status'  => 'error',
                'code'    => 500,
                'message' => __('web.user.current_password_incorrect'),
            ];
        }

        if ($user instanceof User && $user->password) {
            $user->password = Hash::make($request->new_password);
            $user->last_password_changed_at = now();
            $user->save();
        }

        return [
            'status'  => 'success',
            'code'    => 200,
            'message' => __('web.user.password_updated_successfully'),
        ];
    }

    public function getSecuritySettings(): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;
        $userDevices = UserDevice::where('user_id', $authUserId)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($device) {
                return [
                    'id'          => $device->id,
                    'device_type' => $device->device_type,
                    'browser'     => $device->browser,
                    'os'          => $device->os,
                    'ip_address'  => $device->ip_address,
                    'location'    => $device->location,
                    'date'        => formatDateTime($device->created_at),
                ];
            });

        $user = Auth::guard('web')->user();

        return [
            'user'                     => $user,
            'last_password_changed_at' => Auth::guard('web')->check() && $user && $user->last_password_changed_at
                ? formatDateTime($user->last_password_changed_at)
                : "",
            'devices' => $userDevices,
        ];
    }

    public function logoutDevice(Request $request): array
    {
        $authUserId = Auth::guard('web')->user()->id ?? 0;

        if ($request->isAll === "true") {
            UserDevice::where('user_id', $authUserId)->delete();

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.all_devices_removed'),
            ];
        }

        $device = UserDevice::find($request->id);

        if ($device) {
            if ($device instanceof UserDevice) {
                $device->delete();
            }

            return [
                'status'  => 'success',
                'code'    => 200,
                'message' => __('web.user.device_removed_successfully'),
            ];
        }

        return [
            'status'  => 'error',
            'code'    => 404,
            'message' => __('web.user.device_not_found'),
        ];
    }

    public function deleteAccount(): array
    {
        $user = Auth::guard('web')->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => __('admin.general_settings.user_not_found'),
            ];
        }

        $user->delete();

        return [
            'success' => true,
            'message' => __('web.user.account_deleted_successfully'),
        ];
    }
}

