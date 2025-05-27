<?php

namespace Modules\GeneralSetting\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\UpdateAdminProfileRequest;
use Modules\GeneralSetting\Repositories\AdminProfileRepository;
use Illuminate\Http\Request;


class AdminProfileController extends Controller
{
    protected $profileRepo;

    public function __construct(AdminProfileRepository $profileRepo)
    {
        $this->profileRepo = $profileRepo;
    }

    public function adminProfile(): View
    {
        return view('generalsetting::adminProfile.index');
    }

    public function updateProfile(UpdateAdminProfileRequest $request): JsonResponse
    {
        $updated = $this->profileRepo->updateProfile($request->validated());

        if (!$updated) {
            return response()->json([
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.general_settings.user_not_found'),
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('admin.general_settings.profile_update_success'),
        ]);
    }

    public function getProfile(): JsonResponse
    {
        $user = $this->profileRepo->getProfile();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'code'   => 404,
                'message' => __('admin.general_settings.user_not_found'),
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
            'profile_photo' => uploadedAsset($user->userDetail->profile_image ?? null, 'profile'),
        ];

        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'message' => __('admin.general_settings.profile_update_success'),
            'data'   => $profile,
        ]);
    }

    public function checkPassword(Request $request): JsonResponse
    {
        $isValid = $this->profileRepo->checkPassword($request->id, $request->current_password);
        return response()->json($isValid);
    }

    public function deleteAccount(): JsonResponse
    {
        $deleted = $this->profileRepo->deleteAccount();

        return response()->json([
            'success' => $deleted,
            'message' => $deleted
                ? __('admin.general_settings.account_deleted_successfully')
                : __('admin.general_settings.user_not_found'),
        ], $deleted ? 200 : 404);
    }
}
