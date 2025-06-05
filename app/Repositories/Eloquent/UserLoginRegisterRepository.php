<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\UserLoginRegisterInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Modules\GeneralSetting\Models\EmailTemplate;
use Modules\GeneralSetting\Models\GeneralSetting;
use Jenssegers\Agent\Agent;
use Illuminate\Support\Facades\Http;
use Modules\GeneralSetting\Models\UserDevice;

class UserLoginRegisterRepository implements UserLoginRegisterInterface
{
    public function resetPasswordUpdate(Request $request): array
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            $response = [
                'code' => 404,
                'message' => 'User not found.'
            ];
            return $response;
        }
        $user->password = Hash::make($request->current_password);
        $user->save();
        $response = [
            'code' => 200,
            'message' => 'Password updated successfully.'
        ];
        return $response;
    }

    public function getOtpSettings(Request $request): array
    {
         $email = $request->input('email');
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'code' => 400,
                'error' => __('web.auth.invalid_email')
            ];
        }
        $user = User::where('email', $email)->first();
        $type = $request->input('type');
        if (!$user || ($type === 'forgot' && ($email === 'demouser@gmail.com' || $email === 'demoprovider@gmail.com'))) {
            return [
                'code' => 400,
                'error' => __('web.auth.email_not_registered')
            ];
        }
        $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
            ->pluck('value', 'key');
        if (!in_array($settings['otp_type'], ['email', 'sms'])) {
            return [
                'code' => 400,
                'error' => __('web.auth.unsupported_otp_type')
            ];
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
        $subject = __('web.auth.otp_verification_for_login');
        $content = __('web.auth.your_otp_verification_code_for_login');
        if ($settings['otp_type'] === 'email') {
            $notificationType = ($type === 'forgot') ? 9 : 8;

            $template = EmailTemplate::select('subject', 'description')
                ->where('notification_type', $notificationType)
                ->first();

            $subject = $template?->subject ?: __('web.auth.otp_verification');
            $content = $template?->description ?: __('web.auth.your_otp_verification');
        } elseif ($settings['otp_type'] === 'sms') {
            $notificationType = 2;
            $template = EmailTemplate::select('subject', 'content')
                ->where('type', 2)
                ->where('notification_type', $notificationType)
                ->first();
            if (!$template) {
                return [
                    'code' => 404,
                    'error' => __('web.auth.sms_template_not_found')
                ];
            }
            $subject = $template->subject ?? '';
            $content = str_replace(
                ['{{user_name}}', '{{otp}}'],
                [$user->name, $otp],
                $template->content ?? ''
            );
        }
        return [
            'code' => 200,
            'name' => $user->name,
            'otp_digit_limit' => $settings['otp_digit_limit'],
            'otp_expire_time' => $settings['otp_expire_time'],
            'otp_type' => $settings['otp_type'],
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'email_subject' => $subject,
            'email_content' => $content
        ];
    }
    public function generateOtp(int $digitLimit): string
    {
        return str_pad((string) random_int(0, pow(10, $digitLimit) - 1), $digitLimit, '0', STR_PAD_LEFT);
    }

    public function verifyOtp(Request $request): array
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
                        return [
                            'code' => 400,
                            'error' => __('web.auth.otp_is_expired')
                        ];
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return [
                            'code' => 400,
                            'error' => __('web.auth.invalid_otp')
                        ];
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
            return [
                'code' => 200,
                'message' => __('web.auth.otp_verified_successfully')
            ];
        } elseif ($request->login_type == "forgot_email") {
            $request->validate([
                'forgot_email' => 'required|email',
                'otp' => 'required',
            ]);
            $user = User::where('email', $request->forgot_email)->first();
            if (!$user) {
                return [
                    'code' => 404,
                    'error' => __('web.auth.user_not_found')
                ];
            }
            $otpSetting = DB::table('otp_settings')->where('email', $request->forgot_email)->first();
            if (isset($otpSetting)) {
                $expire = $otpSetting->expires_at ?? "";
                if ($expire != '') {
                    $currentDateTime = now()->setTimezone('Asia/Kolkata');
                    if ($currentDateTime->greaterThanOrEqualTo($expire)) {
                        return [
                            'code' => 400,
                            'error' => __('web.auth.otp_is_expired')
                        ];
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return [
                            'code' => 400,
                            'error' => __('web.auth.invalid_otp')
                        ];
                    }
                }
            }
            DB::table('otp_settings')->where('email', $request->forgot_email)->delete();
            $data = "done";
            return [
                'code' => 200,
                'message' => __('web.auth.otp_verified_successfully'),
                'email' => $request->forgot_email,
                'data' => $data
            ];
        } else {
            $request->validate([
                'email' => 'required|email',
                'otp' => 'required',
            ]);
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return [
                    'code' => 404,
                    'error' => __('web.auth.user_not_found')
                ];
            }
            $otpSetting = DB::table('otp_settings')->where('email', $request->email)->first();
            if (isset($otpSetting)) {
                $expire = $otpSetting->expires_at ?? "";
                if ($expire != '') {
                    $currentDateTime = now()->setTimezone('Asia/Kolkata'); // Adjust timezone if needed
                    if ($currentDateTime->greaterThanOrEqualTo($expire)) {
                        return [
                            'code' => 400,
                            'error' => __('web.auth.otp_is_expired')
                        ];
                    }
                }
                $otp = $otpSetting->otp ?? "";
                if ($otp != '') {
                    if ($otp !== $request->otp) {
                        return [
                            'code' => 400,
                            'error' => __('web.auth.invalid_otp')
                        ];
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
            return [
                'code' => 200,
                'message' => __('web.auth.otp_verified_successfully')
            ];
        }
    }

    public function validateEmail(string $email): array
    {
        $exists = User::where('email', $email)->exists();
        return [
            'code' => 200,
            'exists' => $exists
        ];
    }

    public function register(Request $request): array
    {
        $regStatus = DB::table('general_settings')->where('key', 'register')->value('value');
        if ($regStatus === "0") {
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'user_type' => 3,
            ]);

            UserDetail::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            Auth::login($user);
            session(['user_id' => $user->id]);

            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name' => $request->first_name,
                'company_name' => $companyName,
            ];

            try {
                sendNotification($request->email, 'welcome-email', $notifyData);
            } catch (\Throwable $e) {
                \Log::error("Failed to send welcome email: " . $e->getMessage());
            }

            // Handle redirect
            $redirectTo = session('intended_url', route('home'));
            session()->forget('intended_url');
            if (session()->has('intended_booking')) {
                $redirectTo = route('user.booking.redirect');
            }
            return [
                'status' => true,
                'code' => 200,
                'register_status' => $regStatus,
                'name' => $request->username,
                'redirect_url' => $redirectTo,
                'email' => $request->email,
                'message' => __('web.auth.registration_success'),
            ];
        }
        $email = $request->email;
            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'status' => false,
                'code' => 400,
                'message' => __('web.auth.valid_email')
            ];
        }

        $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
            ->pluck('value', 'key');

        if (!in_array($settings['otp_type'], ['email', 'sms'])) {
            return [
                'status' => false,
                'code' => 400,
                'message' => __('web.auth.unsupported_otp_type')
            ];
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
        $notifyData =[
            // 'otp' => $otp,
            'user_name' => $request->first_name ?? '',
            'company_name' => GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Our Company',
        ];
        try {
            sendNotification($request->email, 'register-otp', $notifyData);
        } catch (\Throwable $e) {
            \Log::error("Failed to send welcome email: " . $e->getMessage());
        }

        return [
            'status' => true,
            'code' => 200,
            'register_status' => $regStatus,
            'message' => __('web.auth.otp_sent_success'),
            'otp_type' => $settings['otp_type'],
            'otp' => $otp,
            'expires_at' => $expiresAt,
            'name' => $request->username,
            'phone_number' => $request->phone_number,
            'email' => $email,
            'otp_digit_limit' => $settings['otp_digit_limit'],
        ];
    }

    public function login(Request $request): array
    {
        $user = User::where('email', $request->email)->first();
        if ($user && ($user->user_type == 1 || $user->user_type == 2)) {
            return [
                'status' => false,
                'code'   => 422,
                'message' => __('web.auth.admin_access_not_allowed'),
            ];
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
            return  [
                'status' => true,
                'code'   => 200,
                'redirect_url' => $redirectTo,
                'message' => __('web.auth.login_success'),
            ];
        }

        return [
            'status' => false,
            'code'   => 401,
            'message' => __('web.auth.invalid_credentials'),
        ];
    }
}
