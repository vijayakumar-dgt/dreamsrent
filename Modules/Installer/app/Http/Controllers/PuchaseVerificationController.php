<?php

namespace Modules\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Http\Requests\PurchaseVerificationRequest;
use Modules\Installer\Models\Configuration;
use Modules\Installer\Repositories\Contracts\PurchaseVerificationInterface;

class PuchaseVerificationController extends Controller
{
    protected PurchaseVerificationInterface $verificationRepository;

    public function __construct(PurchaseVerificationInterface $verificationRepository)
    {
        $this->verificationRepository = $verificationRepository;
        set_time_limit(8000000);
    }

    public function index(): RedirectResponse
    {
        return redirect()->route('setup.requirements');
    }

    /**
     * Validate the purchase code.
     *
     * @param PurchaseVerificationRequest $request
     * @return JsonResponse
     */
    public function validatePurchase(PurchaseVerificationRequest $request): JsonResponse
    {
        session()->flush();

        try {
            $data = $this->verificationRepository->verifyPurchaseCode($request->purchase_code);

            // Check status with proper type safety
            if (isset($data['status']) && $data['status'] === true) {
                session()->put('step-1-complete', true);
                Configuration::updateStep(2);

                return response()->json([
                    'success' => true,
                    'message' => "Purchase Code Verified Successfully"
                ], 200);
            }

            // Handle error response
            $errorMessage = isset($data['message']) && is_string($data['message'])
                ? $data['message']
                : 'Purchase Code is Invalid';

            return response()->json([
                'success' => false,
                'message' => $errorMessage
            ], 200);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server Error'
            ], 200);
        }
    }
}
