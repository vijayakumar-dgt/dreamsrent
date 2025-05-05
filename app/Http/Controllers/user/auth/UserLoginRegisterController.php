<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDetail;
use Modules\GeneralSetting\Models\UserDevice;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\EmailTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserLoginRegisterController extends Controller
{
    public function userLogin(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('user.auth.login');
    }
    public function userRegister(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('user.auth.register');
    }
    public function forgotPassword(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('user.auth.forgot-password');
    }
    public function resetPassword(): View|RedirectResponse
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('home');
        }
        return view('user.auth.password-reset');
    }
    public function resetPasswordUpdate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'current_password' => 'required|string|min:6',
            'confirm_password' => 'required|same:current_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 422,
                'errors' => $validator->errors(),
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'code' => 404,
                'message' => 'User not found.'
            ], 404);
        }
        $user->password = Hash::make($request->current_password);
        $user->save();
        return response()->json([
            'code' => 200,
            'message' => 'Password updated successfully.'
        ]);
    }
    public function getOtpSettings(Request $request): JsonResponse
    {
        $email = $request->input('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }
        $user = User::where('email', $email)->first();
        $type = $request->input('type');
        if (!$user || ($type === 'forgot' && ($email === 'demouser@gmail.com' || $email === 'demoprovider@gmail.com'))) {
            return response()->json(['error' => 'The given email is not registered.'], 400);
        }
        $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
            ->pluck('value', 'key');
        if (!in_array($settings['otp_type'], ['email', 'sms'])) {
            return response()->json(['error' => 'Unsupported OTP type'], 400);
        }
        if ($email === 'demouser@gmail.com') {
            $otp = '1234';
        } elseif ($email === 'demoprovider@gmail.com') {
            $otp = '1234';
        } else {
            $otp = $this->generateOtp($settings['otp_digit_limit']);
        }
        $otpExpireMinutes = (int) filter_var($settings['otp_expire_time'], FILTER_SANITIZE_NUMBER_INT);
        $expiresAt = now()
            ->addMinutes($otpExpireMinutes)
            ->setTimezone('Asia/Kolkata')
            ->format('Y-m-d H:i:s');
        $existingOtp = DB::table('otp_settings')->where('email', $email)->first();
        if ($existingOtp) {
            DB::table('otp_settings')
                ->where('email', $email)
                ->update([
                    'otp' => $otp,
                    'expires_at' => $expiresAt,
                ]);
        } else {
            DB::table('otp_settings')->insert([
                'email' => $email,
                'otp' => $otp,
                'expires_at' => $expiresAt,
            ]);
        }
        $subject = 'OTP Verification for login';
        $content = 'Your OTP Verification for login';
        if ($settings['otp_type'] === 'email') {
            $notificationType = 2;
            $subject = 'OTP Verification for login';
            $content = 'Your OTP Verification for login';
        } elseif ($settings['otp_type'] === 'sms') {
            $notificationType = 2;
            $template = EmailTemplate::select('subject', 'content')
                ->where('type', 2)
                ->where('notification_type', $notificationType)
                ->first();
            if (!$template) {
                return response()->json(['error' => 'SMS template not found'], 404);
            }
            $subject = $template->subject ?? '';
            $content = str_replace(
                ['{{user_name}}', '{{otp}}'],
                [$user->name, $otp],
                $template->content ?? ''
            );
        }
        return response()->json([
            'name' => $user->name,
            'otp_digit_limit' => $settings['otp_digit_limit'],
            'otp_expire_time' => $settings['otp_expire_time'],
            'otp_type' => $settings['otp_type'],
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'email_subject' => $subject,
            'email_content' => $content
        ]);
    }
    private function generateOtp(int $digitLimit): string
    {
        return str_pad((string) random_int(0, pow(10, $digitLimit) - 1), $digitLimit, '0', STR_PAD_LEFT);
    }
    public function verifyOtp(Request $request): JsonResponse
    {
        if ($request->login_type == "register") {
            $request->validate([
                'otp' => 'required',
            ]);
            $otpSetting = DB::table('otp_settings')->where('email', $request->email)->first();
            if (isset($otpSetting)) {
                $expire = $otpSetting->expires_at ?? "";
                if ($expire != '') {
                    $currentDateTime = now()->setTimezone('Asia/Kolkata'); // Adjust timezone if needed
                    if ($currentDateTime->greaterThanOrEqualTo($expire)) {
                        return response()->json(['error' => 'OTP is expired'], 400);
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return response()
                        ->json(['error' => 'The OTP you entered is invalid. Please check and try again.'], 400);
                    }
                }
            }
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'password' => Hash::make($request->password),
                'user_type' => 3,
            ];
            $save = User::create($data);
            $company_details = [
                'user_id' => $save->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ];
            $company = UserDetail::create($company_details);
            Auth::login($save);
            session(['user_id' => $save->id]);
            Cache::forget('user_auth_id');
            Cache::forever('user_auth_id', $save->id);
            DB::table('otp_settings')->where('email', $request->email)->delete();
            return response()->json(['message' => 'OTP verified successfully']);
        } elseif ($request->login_type == "forgot_email") {
            $request->validate([
                'forgot_email' => 'required|email',
                'otp' => 'required',
            ]);
            $user = User::where('email', $request->forgot_email)->first();
            if (!$user) {
                return response()->json([ 'code' => 200, 'error' => 'User not found'], 404);
            }
            $otpSetting = DB::table('otp_settings')->where('email', $request->forgot_email)->first();
            if (isset($otpSetting)) {
                $expire = $otpSetting->expires_at ?? "";
                if ($expire != '') {
                    $currentDateTime = now()->setTimezone('Asia/Kolkata');
                    if ($currentDateTime->greaterThanOrEqualTo($expire)) {
                        return response()->json(['error' => 'OTP is expired'], 400);
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return response()
                        ->json(['code' => 422, 'error' => 'The OTP you entered is invalid. Please check and try again.'], 400);
                    }
                }
            }
            DB::table('otp_settings')->where('email', $request->forgot_email)->delete();
            $data = "done";
            return response()
            ->json(['code' => 200, 'message' => 'OTP verified successfully', 'data' => $data, 'email' => $request->forgot_email]);
        } else {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required',
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found'], 404);
            }
            $otpSetting = DB::table('otp_settings')->where('email', $request->email)->first();
            if (isset($otpSetting)) {
                $expire = $otpSetting->expires_at ?? "";
                if ($expire != '') {
                    $currentDateTime = now()->setTimezone('Asia/Kolkata'); // Adjust timezone if needed
                    if ($currentDateTime->greaterThanOrEqualTo($expire)) {
                        return response()->json(['error' => 'OTP is expired'], 400);
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return response()
                        ->json(['error' => 'The OTP you entered is invalid. Please check and try again.'], 400);
                    }
                }
            }
            Auth::guard('web')->login($user);
            session(['user_id' => $user->id]);
            if ($user->user_type == '2') {
                Cache::forget('provider_auth_id');
                Cache::forever('provider_auth_id', $user->id);
            } else {
                Cache::forget('user_auth_id');
                Cache::forever('user_auth_id', $user->id);
            }
            DB::table('otp_settings')->where('email', $request->email)->delete();
            return response()->json(['message' => 'OTP verified successfully']);
        }
    }
    public function validateEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $exists = User::where('email', $request->email)->exists();
        return response()->json(['exists' => $exists]);
    }
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|regex:/^[A-Za-z]+$/|min:3|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ], [
            'username.required' => __('web.auth.username_required'),
            'username.regex' => __('web.auth.username_alphabets'),
            'username.min' => __('web.auth.username_minlength'),
            'username.max' => __('web.auth.username_maxlength'),
            'email.required' => __('web.auth.email_required'),
            'email.email' => __('web.auth.valid_email'),
            'email.unique' => __('web.auth.email_exists'),
            'password.required' => __('web.auth.password_required'),
            'password.min' => __('web.auth.password_minlength'),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code' => 422,
                'errors' => $validator->errors()->toArray(),
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $regStatus = DB::table('general_settings')->where('key', 'register')->value('value');
        if ($regStatus === "0") {
            $user = User::create([
                'name' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 3,
            ]);
            UserDetail::create(['user_id' => $user->id]);
            Auth::login($user);
            session(['user_id' => $user->id]);
            $notificationType = 1;
            $template = EmailTemplate::select('subject', 'description')
                ->where('notification_type', $notificationType)
                ->first();

            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $subject = $template->subject ?? '';
            $content = str_replace(
                ['{user_name}', '{company_name}'],
                [$request->username, $companyName],
                $template->description ?? ''
            );
            return response()->json([
                'status' => true,
                'code' => 200,
                'register_status' => $regStatus,
                'name' => $request->username,
                'email_subject' => $subject,
                'email_content' => $content,
                'redirect_url' => route('home'),
                'email' => $request->email,
                'message' => __('web.auth.registration_success'),
            ]);
        }
        $email = $request->email;
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'Invalid email address'], 400);
        }
        $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
            ->pluck('value', 'key');
        if (!in_array($settings['otp_type'], ['email', 'sms'])) {
            return response()->json(['error' => 'Unsupported OTP type'], 400);
        }
        $otp = $this->generateOtp($settings['otp_digit_limit']);
        $expiresAt = now()
            ->addMinutes((int) $settings['otp_expire_time'])
            ->setTimezone('Asia/Kolkata')
            ->format('Y-m-d H:i:s');
        DB::table('otp_settings')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );
        $subject = 'OTP Verification for Register';
        $content = 'Your OTP Verification for Register {{otp}} ';
        $content = str_replace(
            ['{{otp}}'],
            [$otp],
            $content
        );
        return response()->json([
            'status' => true,
            'code' => 200,
            'register_status' => $regStatus,
            'message' => __('web.auth.otp_sent_success'),
            'otp_type' => $settings['otp_type'],
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'email_subject' => $subject,
            'email_content' => $content,
            'name' => $request->username,
            'phone_number' => $request->phone_number,
            'email' => $request->email,
        ]);
    }
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:6',
        ], [
            'email.required' => __('web.auth.email_required'),
            'email.email' => __('web.auth.valid_email'),
            'email.exists' => __('web.auth.no_account_found'),
            'password.required' => __('web.auth.password_required'),
            'password.min' => __('web.auth.password_minlength'),
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'errors' => $validator->errors()->toArray(),
                'message' => $validator->errors()->first(),
            ], 422);
        }
        $user = User::where('email', $request->email)->first();
        if ($user && ($user->user_type == 1 || $user->user_type == 2)) {
            return response()->json([
                'status' => false,
                'code'   => 422,
                'message' => __('web.auth.admin_access_not_allowed'),
            ], 422);
        }
        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password], $request->has('remember'))) {
            $agent = new Agent();
            $ip = $request->ip();
            $device_type = $agent->device();
            $os = $agent->platform();
            $browser = $agent->browser();

            $locationData = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,city,regionName,lat,lon")->json();
            $location = ($locationData['status'] === 'success')
                ? $locationData['country'] . ' / ' . $locationData['city']
                : 'India / Coimbatore';

                $user = Auth::guard('web')->user();

            if ($user) {
                $user_device = new UserDevice();
                $user_device->user_id = (int) $user->id;
                $user_device->device_type = is_string($device_type) ? $device_type : null;
                $user_device->browser = is_string($browser) ? $browser : null;
                $user_device->os = is_string($os) ? $os : null;
                $user_device->ip_address = $ip;
                $user_device->location = $location;
                $user_device->save();
            }
            $redirectTo = session('intended_url', route('home'));
            session()->forget('intended_url');
            if (session()->has('intended_booking')) {
                $redirectTo = route('user.booking.redirect');
            }
            return response()->json([
                'status' => true,
                'code'   => 200,
                'redirect_url' => $redirectTo,
                'message' => __('web.auth.login_success'),
            ]);
        }

        return response()->json([
            'status' => false,
            'code'   => 401,
            'message' => __('web.auth.invalid_credentials'),
        ], 401);
    }

    public function userlogout(): RedirectResponse
    {
        Auth::guard('web')->logout();
        return redirect()->route('home');
    }
}
