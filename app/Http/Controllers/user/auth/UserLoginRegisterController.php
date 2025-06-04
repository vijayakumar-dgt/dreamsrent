<?php

namespace App\Http\Controllers\user\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\ValidateEmailRequest;
use App\Models\User;
use App\Models\UserDetail;
use App\Repositories\Contracts\UserLoginRegisterInterface;
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
    protected UserLoginRegisterInterface $userLoginRegisterRepository;

    public function __construct(UserLoginRegisterInterface $userLoginRegisterRepository)
    {
        $this->userLoginRegisterRepository = $userLoginRegisterRepository;
    }
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
    public function resetPasswordUpdate(ResetPasswordRequest $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->resetPasswordUpdate($request);
        return response()->json($response, $response['code'] ?? 200);
    }
    public function getOtpSettings(Request $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->getOtpSettings($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->verifyOtp($request);
        return response()->json($response, $response['code'] ?? 200);
    }
    public function validateEmail(ValidateEmailRequest $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->validateEmail($request);
        $request->validate([
            'email' => 'required|email',
        ]);
        return response()->json($response, $response['code'] ?? 200);
    }
    public function register(Request $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->register($request);
        return response()->json($response, $response['code'] ?? 200);
    }
    public function login(Request $request): JsonResponse
    {
        $response = $this->userLoginRegisterRepository->login($request);
        return response()->json($response, $response['code'] ?? 200);
    }

    public function userlogout(): RedirectResponse
    {
        Auth::guard('web')->logout();
        return redirect()->route('home');
    }
}
