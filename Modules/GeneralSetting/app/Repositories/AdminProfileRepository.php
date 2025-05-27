<?php

namespace Modules\GeneralSetting\Repositories;

use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;


class AdminProfileRepository
{
    public function getProfile(): ?User
    {
        return Auth::guard('admin')->user();
    }

    public function updateProfile(array $data): bool
    {
        $user = User::find(Auth::guard('admin')->id());
        if (!$user) return false;

        $user->update([
            'email'        => $data['email'],
            'phone_number' => $data['phone'],
        ]);

        $profilePhoto = $user->userDetail->profile_image ?? null;
        if (isset($data['profile_photo'])) {
            $profilePhoto = uploadFile($data['profile_photo'], 'profile');
            if ($user->userDetail && $user->userDetail->profile_image) {
                Storage::disk('public')->delete($user->userDetail->profile_image);
            }
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
                'profile_image' => $profilePhoto,
            ]
        );

        return true;
    }

    public function checkPassword(int $id, string $password): bool
    {
        $user = User::find($id);
        return $user && Hash::check($password, $user->password);
    }

    public function deleteAccount(): bool
    {
        $user = Auth::guard('admin')->user();
    
        if ($user instanceof \App\Models\User) {
            return $user->delete();
        }
    
        return false;
    }
    
}
