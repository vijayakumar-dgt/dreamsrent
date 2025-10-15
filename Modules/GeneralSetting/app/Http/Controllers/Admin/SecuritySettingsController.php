<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\UpdateEmailRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePasswordRequest;
use Modules\GeneralSetting\Http\Requests\UpdatePhoneNumberRequest;

class SecuritySettingsController extends GeneralSettingBaseController
{
    public function security(): View
    {
        return view('generalsetting::security.index');
    }

    public function checkCurrentPassword(Request $request): JsonResponse
    {
        $password = $request->password;
        $result = Hash::check($password, auth('admin')->user()->password);

        return response()->json([
            'status'  => $result ? 'success' : 'error',
            'code'    => $result ? 200 : 422,
            'message' => __(
                $result ? 'admin.general_settings.current_password_correct' : 'admin.general_settings.current_password_incorrect'
            ),
        ]);
    }

    public function checkCurrentPhoneNumber(Request $request): JsonResponse
    {
        $user = auth('admin')->user();
        $currentPhone = $request->currentPhoneNumber;

        if (!$user->phone_number) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => 'null',
                'message' => __('admin.general_settings.phone_number_not_set'),
            ], 422);
        }

        if ($user->phone_number !== $currentPhone) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'error'   => 'incorrect',
                'message' => __('admin.general_settings.phone_number_incorrect'),
            ], 422);
        }

        return response()->json([
            'status'  => 'success',
            'code'    => 200,
            'message' => __('admin.general_settings.phone_number_correct'),
        ]);
    }

    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $result = $this->userSecuritySettings->updatePassword($request->only([
            'current_password',
            'new_password',
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message'],
        ], $result['success'] ? 200 : 422);
    }

    public function updatePhoneNumber(UpdatePhoneNumberRequest $request): JsonResponse
    {
        $result = $this->userSecuritySettings->updatePhoneNumber($request->only([
            'phone_current_password',
            'current_phonenumber',
            'new_phonenumber',
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message'],
        ], $result['success'] ? 200 : 422);
    }

    public function updateEmail(UpdateEmailRequest $request): JsonResponse
    {
        $result = $this->userSecuritySettings->updateEmail($request->only([
            'email_current_password',
            'current_email',
            'new_email',
        ]));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => $result['success'] ? 200 : 422,
            'message' => $result['message'],
        ], $result['success'] ? 200 : 422);
    }

    public function getSecuritySettings(): JsonResponse
    {
        $data = $this->userSecuritySettings->getSecuritySettings();

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data'   => $data,
        ]);
    }

    public function logoutDevice(Request $request): JsonResponse
    {
        $result = $this->userSecuritySettings->logoutDevice($request->only(['isAll', 'id']));

        return response()->json([
            'status'  => $result['success'] ? 'success' : 'error',
            'code'    => 200,
            'message' => $result['message'],
        ]);
    }

    public function updateGoogleAuth(Request $request): JsonResponse
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::guard('admin')->user();
            $user->google_auth_enabled = $request->googleAuthEnabled === 'true' ? 1 : 0;
            $user->save();
            $message = $user->google_auth_enabled === 1 ? 'Google Authentication Enabled Successfully' : 'Google Authentication Disabled Successfully';

            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => $message,
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' => 'Something went wrong',
                'error'   => $th->getMessage(),
            ]);
        }
    }
}
