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

    public function getProfile(): JsonResponse
    {
        return response()->json($this->profileRepo->getProfile());
    }

    public function updateProfile(UpdateAdminProfileRequest $request): JsonResponse
    {
        return response()->json($this->profileRepo->updateProfile($request->validated()));
    }

    public function checkPassword(Request $request): JsonResponse
    {
        return response()->json($this->profileRepo->checkPassword($request->current_password));
    }

    public function deleteAccount(): JsonResponse
    {
        return response()->json($this->profileRepo->deleteAccount());
    }
}
