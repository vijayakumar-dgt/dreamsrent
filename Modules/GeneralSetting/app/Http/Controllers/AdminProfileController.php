<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function adminProfile(Request $request): View
    {
        return view('generalsetting::adminProfile.index');
    }
    public function updateProfile(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id'            => 'required|exists:users,id',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'first_name'    => 'required|string|max:255',
            'last_name'     => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $request->id,
            'phone'         => 'required',
            'address_line'  => 'nullable|string|max:255',
            'country'       => 'required|integer|exists:countries,id',
            'state'         => 'required|integer|exists:states,id',
            'city'          => 'required|integer|exists:cities,id',
            'postal_code'   => 'nullable|string|max:10',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'code'    => 422,
                'message' =>  __('admin.general_settings.validation_failed'),
                'errors'  => $validator->errors()
            ], 422);
        }
        try {
            $user = User::find(Auth::guard('admin')->id());
            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => __('admin.general_settings.user_not_found')
                ], 404);
            }
            $user->update([
                'email'        => $request->email,
                'phone_number' => $request->phone,
            ]);
            $profilePhoto = null;
            if ($request->hasFile('profile_photo')) {
                $profilePhoto = $request->file('profile_photo') ? uploadFile($request->file('profile_photo'), 'profile') : null;
                if ($user->userDetail && $user->userDetail->profile_image) {
                    Storage::disk('public')->delete($user->userDetail->profile_image);
                }
            }
            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name'    => $request->first_name,
                    'last_name'     => $request->last_name,
                    'address'       => $request->address_line,
                    'country_id'    => $request->country,
                    'state_id'      => $request->state,
                    'city_id'       => $request->city,
                    'postal_code'   => $request->postal_code,
                    'profile_image' => $profilePhoto ?? $user->userDetail->profile_image ?? null,
                ]
            );
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' => __('admin.general_settings.profile_update_success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.profile_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function getProfile(int $id): JsonResponse
    {
        try {
            $user = Auth::guard('admin')->user();
            if (!$user) {
                return response()->json([
                    'status'  => 'error',
                    'code'    => 404,
                    'message' => 'User not found'
                ], 404);
            }
            $profile = [
                'id'            => $user->id,
                'email'         => $user->email,
                'phone'         => $user->phone_number,
                'first_name'    => $user->userDetail->first_name ?? null,
                'last_name'     => $user->userDetail->last_name ?? null,
                'address_line'  => $user->userDetail->address ?? null,
                'country'       => $user->userDetail->country_id ?? null,
                'state'         => $user->userDetail->state_id ?? null,
                'city'          => $user->userDetail->city_id ?? null,
                'postal_code'   => $user->userDetail->postal_code ?? null,
                'profile_photo' => uploadedAsset($user->userDetail->profile_image ?? null, 'profile')
            ];
            return response()->json([
                'status'  => 'success',
                'code'    => 200,
                'message' =>  __('admin.general_settings.profile_update_success'),
                'data'    => $profile
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'code'    => 500,
                'message' =>  __('admin.general_settings.profile_update_error'),
                'error'   => $e->getMessage()
            ], 500);
        }
    }
    public function checkPassword(Request $request): JsonResponse
    {
        $id = $request->id;
        $user = User::find($id);
        if (!$user) {
            return response()->json(false);
        }
        /** @var \App\Models\User|null $user */
        $isValid = $user && $user->password ? Hash::check($request->current_password, $user->password) : false;
        return response()->json($isValid);
    }
    public function deleteAccount(int $id, Request $request): JsonResponse
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::guard('admin')->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' =>   __('admin.general_settings.user_not_found')], 404);
        }
        $user->delete();
        return response()->json(['success' => true, 'message' =>  __('admin.general_settings.account_deleted_successfully')]);
    }
}
