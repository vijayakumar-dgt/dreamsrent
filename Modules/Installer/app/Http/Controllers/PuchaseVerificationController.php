<?php

namespace Modules\Installer\Http\Controllers;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Modules\Installer\Enums\InstallerInfo;
use Modules\Installer\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class PuchaseVerificationController extends Controller
{
    public function __construct()
    {
        set_time_limit(8000000);
    }


    public function index(): View
    {
        /** @var view-string $view */
        $view = 'installer::index';
        return view($view);
    }

    /**
     * Validate the purchase code.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function validatePurchase(Request $request): JsonResponse
    {
        session()->flush();
        $request->validate([
            'purchase_code' => 'required|string',
        ]);

        try {
            $response = Http::asForm()->post(InstallerInfo::VERIFICATION_URL->value, [
                'purchase_code' => $request->purchase_code,
            ]);
            $data = $response->json();
            if ($data['status'] == true) {
                session()->put('step-1-complete', true);
                Configuration::updateStep(2);

                return response()->json(['success' => true, 'message' => "Purchase Code Verified Successfully"], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $data['message'] ?? 'Purchase Code is Invalid'
                ], 200);
            }
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 200);
        }
    }
}
