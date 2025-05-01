<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Mail\ForgotPasswordOtp;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class ForgotpasswordController extends Controller
{
    public function index(): View
    {
        return view('admin.auth.forgot-password');
    }

    public function sendOtp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Email is invalid',
            'email.exists' => 'Email does not exist',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'errors' => $validator->errors()->toArray(),
                'message' => $validator->errors()->first()
            ], 200);
        }
        try {
            $email = $request->email;
            $otp   = rand(1000, 9999);//4 digit OTP
            $token = Str::random(64);
            $user  = User::where('email', $email)->first();
            Cache::put('forgotPasswordEmail_' . $token, $email, 600);
            Cache::put('forgotPasswordOtp_' . $token, $otp, 600);
            $data  = [
                'otp' => $otp,
                'name' => $user->name ?? 'User',
                'subject' => 'Forgot Password Otp'
            ];
            Mail::to($email)->send(new ForgotPasswordOtp($data));

            return response()->json([
                'status' => true,
                'code'   => 200,
                'otp'    => $otp,
                'token'  => $token,
                'message' => 'OTP sent successfully',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'message' => 'Please contact administrator',
            ], 200);
        }
    }

    public function verifyOtp(Request $request): View
    {
        $token = $request->token;
        $email = Cache::get('forgotPasswordEmail_' . $token);
        $otp   = Cache::get('forgotPasswordOtp_' . $token);
        if ($token && $email && $otp) {
            $data = [
                'token' => $token,
                'email' => $email
            ];
            return view('admin.auth.verify-otp', $data);
        } else {
            return redirect()->route('forgot-password');
        }
    }

    public function resendOtp(Request $request): JsonResponse
    {
        $token = $request->token;
        $email = Cache::get('forgotPasswordEmail_' . $token);

        if ($email && User::where('email', $email)->exists()) {
            $otp   = rand(1000, 9999);//4 digit OTP
            $user  = User::where('email', $email)->first();
            Cache::put('forgotPasswordEmail_' . $token, $email, 600);
            Cache::put('forgotPasswordOtp_' . $token, $otp, 600);
            $data  = [
                'otp' => $otp,
                'name' => $user->name ?? 'User',
                'subject' => 'Forgot Password Otp'
            ];
            Mail::to($email)->send(new ForgotPasswordOtp($data));

            return response()->json([
                'status' => true,
                'code'   => 200,
                // 'otp'    => $otp,
                'token'  => $token,
                'message' => 'OTP sent successfully',
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'message' => 'Email does not exist or token is invalid',
            ], 422);
        }
    }

    public function confirmOtp(Request $request): JsonResponse
    {
        $token = $request->token;
        $email = Cache::get('forgotPasswordEmail_' . $token);
        $cache_otp   = Cache::get('forgotPasswordOtp_' . $token);
        if ($token && $email && $cache_otp) {
            if ($request->otp == $cache_otp) {
                return response()->json([
                    'status' => true,
                    'code'   => 200,
                    'redirect_url' => route('reset-password', ['token' => $token]),
                    'message' => 'OTP verified successfully',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'code'   => 422,
                    'message' => 'OTP does not match',
                    // 'valid_otp' => $cache_otp
                ], 200);
            }
        } else {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'message' => 'Email does not exist or token is invalid',
            ], 200);
        }
    }

    public function resetPassword(Request $request): View
    {
        $token = $request->token;
        $email = Cache::get('forgotPasswordEmail_' . $token);
        if ($token && $email) {
            $data = [
                'token' => $token,
                'email' => $email
            ];
            return view('admin.auth.reset-password', $data);
        } else {
            return redirect()->route('forgot-password');
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $token = $request->token;
        $email = Cache::get('forgotPasswordEmail_' . $token);
        if ($token && $email) {
            $validator = Validator::make($request->all(), [
                'password' => 'required|min:6',
                'password_confirmation' => 'required|same:password',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'code'   => 422,
                    'error' => $validator->errors()->first(),
                    'message' => $validator->errors()->first()
                ], 422);
            }

            $user = User::where('email', $email)->first();
            $user->password = Hash::make($request->password);
            $user->last_password_changed_at = now();
            $user->save();
            Cache::forget('forgotPasswordEmail_' . $token);
            Cache::forget('forgotPasswordOtp_' . $token);
            return response()->json([
                'status' => true,
                'code'   => 200,
                'message' => 'Password updated successfully',
                'redirect_url' => route('admin-login'),
            ]);
        } else {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'message' => 'Email does not exist or token is invalid',
            ], 422);
        }
    }
}
