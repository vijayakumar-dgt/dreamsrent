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
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Jenssegers\Agent\Agent;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\UserDevice;

class UserLoginRegisterRepository implements UserLoginRegisterInterface
{
    public const ASIA_KOLKATA = 'Asia/Kolkata';

    public function resetPasswordUpdate(Request $request): array
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return [
                'code'    => 404,
                'message' => 'User not found.'
            ];
        }
        $user->password = Hash::make($request->current_password);
        $user->save();
        return [
            'code'    => 200,
            'message' => 'Password updated successfully.'
        ];
    }

    public function getOtpSettings(Request $request): array
    {
        $email = $request->input('email');
        $type  = $request->input('type');
        $demoEmails = ['demouser@gmail.com', 'demoprovider@gmail.com'];
        $response = [];

        // Validate email and user
        if (!$this->isValidUser($email, $type, $demoEmails, $user)) {
            return $this->errorResponse(400, __('web.auth.invalid_email_or_not_registered'));
        }

        // Get OTP settings
        if (!$settings = $this->getOtpSettingsValues()) {
            return $this->errorResponse(400, __('web.auth.unsupported_otp_type'));
        }

        // Generate & store OTP
        $expiresAt = $this->generateAndStoreOtp($email, $settings['otp_digit_limit'], $settings['otp_expire_time'], in_array($email, $demoEmails, true));

        // Send OTP notification
        try {
            $this->sendOtpNotification($email, $type, $email === '1234' ? '1234' : $this->getStoredOtp($email), $expiresAt, $settings['otp_digit_limit'], $user->name);

            $response = [
                'code'            => 200,
                'name'            => $user->name,
                'otp_digit_limit' => $settings['otp_digit_limit'],
                'otp_expire_time' => $settings['otp_expire_time'],
                'otp_type'        => $settings['otp_type'],
                'expires_at'      => $expiresAt,
            ];
        } catch (\Throwable $e) {
            Log::error("Failed to send OTP notification: " . $e->getMessage());
            $response = $this->errorResponse(500, __('web.auth.failed_to_send_otp') . ' ' . $e->getMessage());
        }

        return $response;
    }

    /* Helper Methods */

    // 1. Validate email & get user
    private function isValidUser(?string $email, ?string $type, array $demoEmails, ?User &$user = null): bool
    {
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $user = User::where('email', $email)->first();
        if (!$user || ($type === 'forgot' && in_array($email, $demoEmails, true))) {
            return false;
        }
        return true;
    }

    // 2. Get OTP settings
    private function getOtpSettingsValues(): ?array
    {
        $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
            ->pluck('value', 'key')
            ->toArray();

        if (!isset($settings['otp_type']) || !in_array($settings['otp_type'], ['email', 'sms'], true)) {
            return null;
        }
        return $settings;
    }

    // 3. Generate & store OTP
    private function generateAndStoreOtp(string $email, int $digitLimit, $expireMinutes, bool $isDemo = false): string
    {
        $otp = $isDemo ? '1234' : $this->generateOtp($digitLimit);

        $minutes = (int) filter_var($expireMinutes ?? 10, FILTER_SANITIZE_NUMBER_INT);
        $expiresAt = now()->addMinutes($minutes)->setTimezone(self::ASIA_KOLKATA)->format('Y-m-d H:i:s');

        DB::table('otp_settings')->updateOrInsert(
            ['email' => $email],
            ['otp' => $otp, 'expires_at' => $expiresAt]
        );

        return $expiresAt;
    }

    // Retrieve stored OTP for notification
    private function getStoredOtp(string $email): string
    {
        return DB::table('otp_settings')->where('email', $email)->value('otp');
    }

    // 4. Send OTP notification
    private function sendOtpNotification(string $email, ?string $type, string $otp, string $expiresAt, int $digitLimit, string $userName): void
    {
        $notifyData = [
            'otp'             => $otp,
            'expires_at'      => $expiresAt,
            'otp_digit_limit' => $digitLimit,
            'user_name'       => $userName,
        ];

        $notificationSlug = $type === 'forgot' ? 'forgot-otp' : 'login-otp';
        sendNotification($email, $notificationSlug, $notifyData);
    }

    // Error response helper
    private function errorResponse(int $code, string $message): array
    {
        return ['code' => $code, 'error' => $message];
    }

    public function generateOtp(int $digitLimit): string
    {
        return str_pad((string) random_int(0, pow(10, $digitLimit) - 1), $digitLimit, '0', STR_PAD_LEFT);
    }

    public function verifyOtp(Request $request): array
    {
        $loginType = $request->login_type;

        switch ($loginType) {
            case 'register':
                return $this->handleRegisterOtp($request);

            case 'forgot_email':
                return $this->handleForgotOtp($request);

            default:
                return $this->handleLoginOtp($request);
        }
    }

    private function handleRegisterOtp(Request $request): array
    {
        $request->validate([
            'otp' => 'required',
        ]);

        $otpCheck = $this->validateOtp($request->email, $request->otp);
        if ($otpCheck !== true) {
            return $otpCheck;
        }

        $data = [
            'name'         => $request->name,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'password'     => Hash::make($request->password),
            'user_type'    => 3,
        ];

        $user = User::create($data);
        Auth::login($user);
        session(['user_id' => $user->id]);
        Cache::forget('user_auth_id');
        Cache::forever('user_auth_id', $user->id);
        DB::table('otp_settings')->where('email', $request->email)->delete();

        return [
            'code'    => 200,
            'message' => __('web.auth.otp_verified_successfully'),
        ];
    }

    private function handleForgotOtp(Request $request): array
    {
        $request->validate([
            'forgot_email' => 'required|email',
            'otp'          => 'required',
        ]);

        $user = User::where('email', $request->forgot_email)->first();
        if (!$user) {
            return [
                'code'  => 404,
                'error' => __('web.auth.user_not_found'),
            ];
        }

        $otpCheck = $this->validateOtp($request->forgot_email, $request->otp);
        if ($otpCheck !== true) {
            return $otpCheck;
        }

        DB::table('otp_settings')->where('email', $request->forgot_email)->delete();

        return [
            'code'    => 200,
            'message' => __('web.auth.otp_verified_successfully'),
            'email'   => $request->forgot_email,
            'data'    => 'done',
        ];
    }

    private function handleLoginOtp(Request $request): array
    {
        $request->validate([
            'email' => 'required|email',
            'otp'   => 'required',
        ]);

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return [
                'code'  => 404,
                'error' => __('web.auth.user_not_found'),
            ];
        }

        $otpCheck = $this->validateOtp($request->email, $request->otp);
        if ($otpCheck !== true) {
            return $otpCheck;
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
            'code'    => 200,
            'message' => __('web.auth.otp_verified_successfully'),
        ];
    }

    private function validateOtp(string $email, string $otp)
    {
        $response = ['code' => 200];

        $otpSetting = DB::table('otp_settings')->where('email', $email)->first();

        if (!$otpSetting) {
            $response = [
                'code'  => 400,
                'error' => __('web.auth.invalid_otp'),
            ];
        } else {
            $expire = $otpSetting->expires_at ?? '';
            $currentDateTime = now()->setTimezone(self::ASIA_KOLKATA);

            if ($expire !== '' && $currentDateTime->greaterThanOrEqualTo($expire)) {
                $response = [
                    'code'  => 400,
                    'error' => __('web.auth.otp_is_expired'),
                ];
            } elseif (($otpSetting->otp ?? '') !== $otp) {
                $response = [
                    'code'  => 400,
                    'error' => __('web.auth.invalid_otp'),
                ];
            }
        }

        return $response['code'] === 200 ? true : $response;
    }

    public function validateEmail(string $email): array
    {
        $exists = User::where('email', $email)->exists();
        return [
            'code'   => 200,
            'exists' => $exists
        ];
    }

    public function register(Request $request): array
    {
        $response = [];

        $regStatus = DB::table('general_settings')->where('key', 'register')->value('value');

        if ($regStatus === "0") {
            $user = User::create([
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'user_type' => 3,
            ]);

            UserDetail::create([
                'user_id'    => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
            ]);

            Auth::login($user);
            session(['user_id' => $user->id]);

            $companyName = GeneralSetting::where('key', 'organization_name')->value('value') ?? 'Default Company Name';
            $notifyData = [
                'user_name'    => $request->first_name,
                'company_name' => $companyName,
            ];

            try {
                sendNotification($request->email, 'welcome-email', $notifyData);
            } catch (\Throwable $e) {
                Log::error("Failed to send welcome email: " . $e->getMessage());
            }

            // Handle redirect
            $redirectTo = session('intended_url', '/');
            session()->forget('intended_url');
            if (session()->has('intended_booking')) {
                $redirectTo = '/redirect-to-booking';
            }

            $response = [
                'status'          => true,
                'code'            => 200,
                'register_status' => $regStatus,
                'name'            => $request->username,
                'redirect_url'    => $redirectTo,
                'email'           => $request->email,
                'message'         => __('web.auth.registration_success'),
            ];
        } else {
            $email = $request->email;

            if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = [
                    'status'  => false,
                    'code'    => 400,
                    'message' => __('web.auth.valid_email'),
                ];
            } else {
                $settings = GeneralSetting::whereIn('key', ['otp_digit_limit', 'otp_expire_time', 'otp_type'])
                    ->pluck('value', 'key');

                if (!in_array($settings['otp_type'], ['email', 'sms'])) {
                    $response = [
                        'status'  => false,
                        'code'    => 400,
                        'message' => __('web.auth.unsupported_otp_type'),
                    ];
                } else {
                    $otp = $this->generateOtp($settings['otp_digit_limit']);
                    $expiresAt = now()
                        ->addMinutes((int) $settings['otp_expire_time'])
                        ->setTimezone(self::ASIA_KOLKATA)
                        ->format('Y-m-d H:i:s');

                    DB::table('otp_settings')->updateOrInsert(
                        ['email' => $email],
                        ['otp' => $otp, 'expires_at' => $expiresAt]
                    );

                    $subject = __('web.auth.otp_verification_for_register');
                    $content = __('web.auth.your_otp_verification_code_for_register') . ' {{otp}} ';
                    $content = str_replace(['{{otp}}'], [$otp], $content);

                    $response = [
                        'status'          => true,
                        'code'            => 200,
                        'register_status' => $regStatus,
                        'message'         => __('web.auth.otp_sent_success'),
                        'otp_type'        => $settings['otp_type'],
                        'otp'             => $otp,
                        'expires_at'      => $expiresAt,
                        'email_subject'   => $subject,
                        'email_content'   => $content,
                        'name'            => $request->username,
                        'phone_number'    => $request->phone_number,
                        'email'           => $request->email,
                    ];
                }
            }
        }

        return $response;
    }

    public function login(Request $request): array
    {
        $user = User::where('email', $request->email)->first();

        if (isAdminUser($user)) {
            return $this->response(false, 422, __('web.auth.admin_access_not_allowed'));
        }

        if (!Auth::guard('web')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->has('remember')
        )) {
            return $this->response(false, 401, __('web.auth.invalid_credentials'));
        }

        $this->recordUserDevice($request);

        return $this->response(true, 200, __('web.auth.login_success'), [
            'redirect_url' => determineRedirectUrl(),
        ]);
    }

    /**
     * Record device and location details after login
     */
    private function recordUserDevice(Request $request): void
    {
        $agent = new Agent();
        $user = Auth::guard('web')->user();

        if (!$user) {
            return;
        }

        $ip = $request->ip();
        $device = new UserDevice([
            'user_id'     => (int) $user->id,
            'device_type' => $agent->device() ?: null,
            'browser'     => $agent->browser() ?: null,
            'os'          => $agent->platform() ?: null,
            'ip_address'  => $ip,
            'location'    => $this->getLocationFromIp($ip),
        ]);

        $device->save();
    }

    /**
     * Fetch location using IP or fallback
     */
    private function getLocationFromIp(string $ip): string
    {
        $locationData = Http::get("http://ip-api.com/json/{$ip}?fields=status,country,city")->json();
        return ($locationData['status'] ?? '') === 'success'
            ? "{$locationData['country']} / {$locationData['city']}"
            : 'India / Coimbatore';
    }

    /**
     * Standard response builder
     */
    private function response(bool $status, int $code, string $message, array $extra = []): array
    {
        return array_merge([
            'status'  => $status,
            'code'    => $code,
            'message' => $message,
        ], $extra);
    }
}
