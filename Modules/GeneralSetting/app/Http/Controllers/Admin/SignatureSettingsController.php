<?php

namespace Modules\GeneralSetting\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\View\View;
use Modules\GeneralSetting\Http\Requests\SignatureSettingRequest;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;

class SignatureSettingsController extends Controller
{
    protected $repository;

    public function __construct(SignatureSettingInterface $repository)
    {
        $this->repository = $repository;
    }

    public function signature(): View
    {
        return view('generalsetting::app_settings.signature-setting');
    }

    public function clearCache(): View
    {
        return view('generalsetting::other_settings.clear-cache');
    }

    public function clear(): JsonResponse
    {
        try {
            Artisan::call('optimize:clear');
            return $this->jsonResponse(200, __('admin.general_settings.cache_cleared_successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse(500, __('admin.general_settings.cache_clear_error'), $e->getMessage());
        }
    }

    public function store(SignatureSettingRequest $request): JsonResponse
    {
        try {
            $signature = $this->repository->createSignature(
                $request->all(),
                $request->file('signature_image')
            );

            return $this->jsonResponse(
                200,
                __('admin.general_settings.signature_success'),
                $signature,
                ['totalRecords' => $this->repository->getTotalSignaturesCount()]
            );
        } catch (\Exception $e) {
            return $this->jsonResponse(
                500,
                __('admin.general_settings.retrive_error'),
                null,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function update(SignatureSettingRequest $request): JsonResponse
    {
        try {
            $signature = $this->repository->updateSignature(
                $request->id,
                $request->all(),
                $request->file('signature_image')
            );

            return $this->jsonResponse(
                200,
                __('admin.general_settings.signature_update_success'),
                $signature
            );
        } catch (\Exception $e) {
            return $this->jsonResponse(
                500,
                __('admin.general_settings.retrive_error'),
                null,
                ['error' => $e->getMessage()]
            );
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $signatures = $this->repository->getAllSignatures($request->input('search'));

            return $this->jsonResponse(
                200,
                __('admin.general_settings.signature_list_fetch_success'),
                $signatures,
                ['totalRecords' => $signatures->count()]
            );
        } catch (\Exception $e) {
            return $this->jsonResponse(
                500,
                __('admin.general_settings.fail_signature_list'),
                [],
                ['error' => $e->getMessage()]
            );
        }
    }

    public function destroy(Request $request): JsonResponse
    {
        try {
            $totalRecords = $this->repository->deleteSignature($request->id);

            return $this->jsonResponse(
                200,
                __('admin.general_settings.signature_deleted_successfully'),
                null,
                ['totalRecords' => $totalRecords]
            );
        } catch (\Exception $e) {
            return $this->jsonResponse(
                500,
                __('admin.general_settings.fail_delete_signature'),
                null,
                ['error' => $e->getMessage()]
            );
        }
    }

    protected function jsonResponse(int $code, string $message, $data = null, array $additional = [])
    {
        $response = [
            'code'    => $code,
            'message' => $message,
            'data'    => $data
        ];

        return response()->json(array_merge($response, $additional), $code);
    }
}
