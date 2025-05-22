<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;
use Modules\GeneralSetting\Models\UserDevice;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        $validator = Validator::make(
            $request->all(),
            [
                'email' => 'required|email|exists:users',
                'password' => 'required|min:6',
            ],
            [
                'email.required' => 'Email is required',
                'email.email' => 'Email is invalid',
                'email.exists' => 'Email does not exist',
                'password.required' => 'Password is required',
                'password.min' => 'Password must be at least 6 characters',
            ]
        );
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 422,
                'errors' => $validator->errors()->toArray(),
                'message' => $validator->errors()->first()
            ], 200);
        }
        $credentials = $request->only('email', 'password');
        $remember = $request->get('remember', false);
        if (Auth::guard('admin')->attempt($credentials, $remember)) {
            $user = Auth::guard('admin')->user();

            if ($user && ($user->status == 1 || $user->user_type == 1 || $user->user_type == 2)) {
                if ($user->status == 0 && $user->user_type == 2) {
                    return response()->json([
                        'status' => false,
                        'code' => 401,
                        'message' => 'Currently you are blocked! Please contact to admin.',
                    ], 200);
                }
                $agent = new Agent();
                $ip = $request->ip();
                $device_type = $agent->device();
                $os = $agent->platform();
                $browser = $agent->browser();
                $locationData = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,city,regionName,lat,lon")->json();
                $localtion = "";
                if ($locationData['status'] !== 'success') {
                    $localtion = "India / Coimbatore";
                } else {
                    $localtion = $locationData['country'] . ' / ' . $locationData['city'];
                }
                $user = Auth::guard('admin')->user();
                $user_device = new UserDevice();
                if ($user && isset($user->id)) {
                    $user_device->user_id = $user->id;
                }
                $user_device->device_type = is_string($device_type) ? $device_type : null;
                $user_device->browser = is_string($browser) ? $browser : null;
                $user_device->os = is_string($os) ? $os : null;
                $user_device->ip_address = $ip ?? "";
                $user_device->location = $localtion;
                $user_device->save();
                return response()->json([
                    'status' => true,
                    'code' => 200,
                    'redirect_url' => route('dashboard'),
                    'message' => 'Login successfully',
                ]);
            }
        }

        return response()->json([
            'status' => false,
            'code' => 401,
            'message' => 'Invalid admin credentials',
        ], 200);
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-login');
    }
}
