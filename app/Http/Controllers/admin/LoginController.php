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
        // default response (invalid credentials)
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
            // validation failed -> set payload and skip auth attempt
            $payload = [
                'status'  => false,
                'code'    => 422,
                'errors'  => $validator->errors()->toArray(),
                'message' => $validator->errors()->first()
            ];
        } else {
            $credentials = $request->only('email', 'password');
            $remember = $request->get('remember', false);

            if (Auth::guard('admin')->attempt($credentials, $remember)) {
                $user = Auth::guard('admin')->user();

                if ($user && ($user->status == 1 || $user->user_type == 1 || $user->user_type == 2)) {
                    if ($user->status == 0 && $user->user_type == 2) {
                        // blocked user case
                        $payload = [
                            'status'  => false,
                            'code'    => 401,
                            'message' => 'Currently you are blocked! Please contact to admin.',
                        ];
                    } else {
                        // successful login -> create device record and set success payload
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
                        if ($user && (property_exists($user, 'id') && $user->id !== null)) {
                            $user_device->user_id = $user->id;
                        }
                        $user_device->device_type = is_string($device_type) ? $device_type : null;
                        $user_device->browser = is_string($browser) ? $browser : null;
                        $user_device->os = is_string($os) ? $os : null;
                        $user_device->ip_address = $ip ?? "";
                        $user_device->location = $localtion;
                        $user_device->save();

                        $payload = [
                            'status'       => true,
                            'code'         => 200,
                            'redirect_url' => route('dashboard'),
                            'message'      => 'Login successfully',
                        ];
                    }
                }
                // If user didn't meet the outer condition, payload remains "invalid credentials"
            }
            // If attempt failed, payload remains "invalid credentials"
        }

        // single return for all cases
        return response()->json($payload, $httpStatus);
    }

    public function logout(): RedirectResponse
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-login');
    }
}
