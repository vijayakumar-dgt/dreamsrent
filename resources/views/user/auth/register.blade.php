<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($seo_title) ? $seo_title : config('app.name') }} | {{ __('web.auth.register') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/img/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">

    <!-- Toastr CSS -->
    <link href="{{ asset('frontend/assets/plugins/toastr/toatr.css') }}" rel="stylesheet">

    <!-- Fearther CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/feather.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
</head>
<body>
    <!-- Main Wrapper -->
    <div class="main-wrapper login-body">
        <!-- Header -->
        <header class="log-header">
            <a href="{{ route('home') }}">
                <img class="img-fluid logo-dark" src="{{ $logo ?? 'assets/img/logo.svg' }}" alt="Logo">
            </a>
        </header>
        <!-- /Header -->
        <div class="login-wrapper">
            <div class="loginbox">
                <div class="login-auth">
                    <div class="login-auth-wrap">
                        <h1>{{ __('web.auth.sign_up') }}</h1>
                        <p class="account-subtitle">{{ __('web.auth.email_confirmation_subtitle') }}</p>
                        <form id="userRegisterForm">
                            @csrf                           
                            <div class="input-block">
                                <label class="form-label" for="first_name">
                                    {{ __('web.auth.first_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="first_name" name="first_name" placeholder="">
                                <span id="first_name_error" class="text-danger error-text"></span>
                            </div>
                            <div class="input-block">
                                <label class="form-label" for="last_name">
                                    {{ __('web.auth.last_name') }} <span class="text-danger">*</span>
                                </label>
                                <input type="text" class="form-control" id="last_name" name="last_name" placeholder="">
                                <span id="last_name_error" class="text-danger error-text"></span>
                            </div>
                            <div class="input-block">
                                <label class="form-label" for="email">
                                    {{ __('web.auth.email') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="">
                                <span id="email_error" class="text-danger error-text"></span>
                            </div>
                            <div class="input-block">
                                <label class="form-label" for="password">
                                    {{ __('web.auth.password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="pass-group">
                                    <input type="password" class="form-control pass-input" id="password" name="password" placeholder="">
                                    <span class="fas fa-eye-slash toggle-password"></span>
                                </div>
                                <span id="password_error" class="text-danger error-text"></span>
                            </div>
                            <button type="submit" class="btn btn-outline-light w-100 btn-size mt-1">
                                {{ __('web.auth.sign_up') }}
                            </button>                           
                           
                            <div class="text-center dont-have">
                                {{ __('web.auth.already_have_account') }}
                                <a href="{{ route('user-login') }}">{{ __('web.auth.sign_in') }}</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="otp-email-reg-modal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-end pb-0 border-0">
                        <a href="javascript:void(0);" data-bs-dismiss="modal" aria-label="Close" id="close-otp-modal">
                            <i class="ti ti-circle-x-filled fs-20"></i>
                        </a>
                    </div>
                    <div class="modal-body p-4">
                        <form action="#" class="digit-group">
                            <div class="text-center mb-3">
                                <h3 class="mb-2">{{ __('Email OTP Verification') }}</h3>
                                <p id="otp-email-message" class="fs-14">{{ __('web.auth.otp_sent_to_email') }}</p>
                            </div>
                            <div class="text-center otp-input">
                                <div class="inputcontainerreg"></div>
                                <span id="error_email_reg_message" class="text-danger"></span>
                                <div>
                                    <div class="badge bg-danger-transparent mb-3">
                                        <p class="d-flex align-items-center"><i class="ti ti-clock me-1"></i><span id="otp-reg-timer">00:00</span></p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <p>{{ __('web.auth.didnt_get_otp') }} <a href="javascript:void(0);" class="resendRegEmailOtp text-primary">{{ __('web.auth.resend_otp') }}</a></p>
                                    </div>
                                    <div>
                                        <button type="button" id="verify-email-red-otp-btn" class="verify-email-reg-otp-btn btn btn-lg btn-linear-primary w-100">
                                            {{ __('web.auth.verify_proceed') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('frontend.toast')
       
    </div>
    <!-- /Main Wrapper -->

    <!-- jQuery -->
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- jQuery validation -->
    <script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>

    <!-- Toastr JS -->
    <script src="{{ asset('backend/assets/plugins/toastr/toastr.min.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
    
    <!-- Custom JS -->
    <script src="{{ asset('frontend/assets/js/custom/lang_script.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/custom-script.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/user/register.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
</body>
</html>
