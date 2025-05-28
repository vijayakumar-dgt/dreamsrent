<?php

namespace Modules\GeneralSetting\Repositories;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Str;
use App\Services\ImageResizer;

class AdminProfileRepository
{
     protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }
    public function getProfile(): array
    {
        try {
            $user = Auth::guard('admin')->user();
            if (!$user) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => 'User not found',
                ];
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

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.profile_update_success'),
                'data' => $profile
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.profile_update_error'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function updateProfile(array $data): array
    {
        try {
            $user = User::find(Auth::guard('admin')->id());

            if (!$user) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.general_settings.user_not_found')
                ];
            }

            $user->update([
                'email' => $data['email'],
                'phone_number' => $data['phone'],
            ]);

            $profilePhotoPath = $user->userDetail->profile_image ?? null;

            if (!empty($data['profile_photo']) && $data['profile_photo'] instanceof \Illuminate\Http\UploadedFile) {
                $profilePhotoPath = $this->imageResizer->uploadFile($data['profile_photo'], $profilePhotoPath, 'profile');
            }

            UserDetail::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'first_name'    => $data['first_name'],
                    'last_name'     => $data['last_name'],
                    'address'       => $data['address_line'] ?? null,
                    'country_id'    => $data['country'] ?? null,
                    'state_id'      => $data['state'] ?? null,
                    'city_id'       => $data['city'] ?? null,
                    'postal_code'   => $data['postal_code'] ?? null,
                    'profile_image' => $profilePhotoPath,
                ]
            );

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.profile_update_success')
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.profile_update_error'),
                'error' => $e->getMessage()
            ];
        }
    }

    public function checkPassword(string $currentPassword): array
    {
        try {
            $user = Auth::guard('admin')->user();

            $isValid = $user && $user->password ? Hash::check($currentPassword, $user->password) : false;

            return [
                'status' => 'success',
                'code' => 200,
                'valid' => $isValid
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'valid' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    public function deleteAccount(): array
    {
        try {
            $user = Auth::guard('admin')->user();

            if (!$user) {
                return [
                    'status' => 'error',
                    'code' => 404,
                    'message' => __('admin.general_settings.user_not_found'),
                ];
            }
            
            if ($user instanceof \App\Models\User) {
                $user->delete();
            }

            return [
                'status' => 'success',
                'code' => 200,
                'message' => __('admin.general_settings.account_deleted_successfully'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'code' => 500,
                'message' => __('admin.general_settings.profile_update_error'),
                'error' => $e->getMessage()
            ];
        }
    }
}
