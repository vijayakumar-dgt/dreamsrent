<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Jenssegers\Agent\Agent;
use Modules\GeneralSetting\Models\UserDevice;

class LoginController extends Controller
{
    public function index(): View|RedirectResponse
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('dashboard');
        }
        return view('admin.auth.login');
    }

    public function verifyLogin(Request $request): JsonResponse
    {
        $httpStatus = 200;

        // default payload
        $payload = [
            'status'  => false,
            'code'    => 401,
            'message' => 'Invalid admin credentials',
        ];

        $validator = Validator::make(
            $request->all(),
            [
                'email'    => 'required|email|exists:users',
                'password' => 'required|min:6',
            ],
            [
                'email.required'    => 'Email is required',
                'email.email'       => 'Email is invalid',
                'email.exists'      => 'Email does not exist',
                'password.required' => 'Password is required',
                'password.min'      => 'Password must be at least 6 characters',
            ]
        );

        if ($validator->fails()) {
            $payload = [
                'status'  => false,
                'code'    => 422,
                'errors'  => $validator->errors()->toArray(),
                'message' => $validator->errors()->first(),
            ];
            return response()->json($payload, $httpStatus);
        }

        // Attempt authentication
        $credentials = $request->only('email', 'password');
        $remember = $request->get('remember', false);

        if (!Auth::guard('admin')->attempt($credentials, $remember)) {
            return response()->json($payload, $httpStatus);
        }

        $user = Auth::guard('admin')->user();

        if ($this->isBlockedUser($user)) {
            $payload['message'] = __('admin.auth.your_account_is_blocked');
            return response()->json($payload, $httpStatus);
        }

        $this->logUserDevice($request, $user);

        $payload = [
            'status'       => true,
            'code'         => 200,
            'redirect_url' => route('dashboard'),
            'message'      => __('admin.auth.login_success'),
        ];

        return response()->json($payload, $httpStatus);
    }

    /**
     * Check if the user is blocked (type 2 inactive)
     */
    private function isBlockedUser($user): bool
    {
        return $user && $user->status == 0 && $user->user_type == 2;
    }

    /**
     * Log user's device info
     */
    private function logUserDevice(Request $request, $user): void
    {
        if (!$user || !$user->id) {
            return;
        }

        $agent = new Agent();
        $ip = $request->ip();
        $deviceType = $agent->device();
        $os = $agent->platform();
        $browser = $agent->browser();

        $locationData = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,city")->json();
        $location = ($locationData['status'] ?? '') === 'success'
            ? ($locationData['country'] . ' / ' . $locationData['city'])
            : 'India / Coimbatore';

        UserDevice::create([
            'user_id'     => $user->id,
            'device_type' => is_string($deviceType) ? $deviceType : null,
            'browser'     => is_string($browser) ? $browser : null,
            'os'          => is_string($os) ? $os : null,
            'ip_address'  => $ip ?? '',
            'location'    => $location,
        ]);
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-login');
    }
}
