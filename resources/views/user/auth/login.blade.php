<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($seo_title) ? $seo_title : config('app.name') }} | {{ __('web.auth.sign_in') }}</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('frontend/assets/img/favicon.png') }}">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    
    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">

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
                        <h1>{{ __('web.auth.sign_in') }}</h1>
                        <p class="account-subtitle">{{ __('web.auth.email_confirmation_subtitle') }}</p>
                        <form id="userLoginForm">
                            <div class="input-block">
                                <label for="email" class="form-label">
                                    {{ __('web.auth.email') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" class="form-control" id="email" name="email" >
                                <span class="error-text text-danger" id="email_error"></span>
                            </div>
                            <div class="input-block">
                                <label for="password" class="form-label">
                                    {{ __('web.auth.auth_password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="pass-group">
                                    <input type="password" class="form-control pass-input" id="password" name="password">
                                    <span class="fas fa-eye-slash toggle-password"></span>
                                </div>
                                <span class="error-text text-danger" id="password_error"></span>
                            </div>
                            <div class="input-block d-flex justify-content-between">
                                <a class="forgot-link" href="{{ route('user-forgot-password') }}">{{ __('web.auth.forgot_password') }}</a>
                               <button type="button" class="form-check-label text-decoration-underline btn btn-link p-0" id="login_otp">
                                    {{ __('web.auth.sign_in_with_otp') }}
                               </button>
                            </div>
                            <div class="input-block m-0">
                                <label for="remember" class="custom_check d-inline-flex">
                                    <span>{{ __('web.auth.remember_me') }}</span>
                                    <input type="checkbox" name="remember" id="remember">
                                    <span class="checkmark"></span>
                                </label>
                            </div>
                            <button type="submit" class="btn btn-outline-light w-100 btn-size mt-1 submitbtn" disabled>
                                {{ __('web.auth.sign_in') }}
                            </button>
                            
                            <div class="text-center dont-have">
                                {{ __('web.auth.dont_have_account') }}
                                <a href="{{ route('user-register') }}">{{ __('web.auth.register') }}</a>
                            </div>
                            <div class="mt-3">
                                <h6 class="fs-16 mb-1">{{ __('web.auth.demo_user_info') }}:</h6>
                                <div class="p-3 border d-flex flex-wrap align-items-center justify-content-between">
                                    <div>
                                        <span class="d-block"><strong>{{ __('web.auth.email') }}:</strong> demouser@example.com</span>
                                        <span class="d-block"><strong>{{ __('web.auth.auth_password') }}:</strong> 12345678</span>
                                    </div>
                                    <div>
                                        <a class="btn btn-primary copy-login-details" data-email="demouser@example.com" data-password="12345678">
                                            <i class="far fa-copy"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="otp-email-modal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-end pb-0 border-0">
                       <button type="button" data-bs-dismiss="modal" aria-label="Close" id="close-otp-modal" class="btn p-0 border-0 bg-transparent">
                            <i class="ti ti-circle-x-filled fs-20"></i>
                       </button>

                    </div>
                    <div class="modal-body p-4">
                        <form action="#" class="digit-group">
                            <div class="text-center mb-3">
                                 <h3 class="mb-2">{{ __('web.auth.email_otp_verification') }}</h3>
                                <p id="otp-email-message" class="fs-14">{{ __('web.auth.otp_sent_to_email') }}</p>
                            </div>
                            <div class="text-center otp-input">
                                <div class="inputcontainer"></div>
                                <span id="error_message" class="text-danger"></span>
                                <div>
                                    <div class="badge bg-danger-transparent mb-3">
                                        <p class="d-flex align-items-center">
                                            <i class="ti ti-clock me-1"></i>
                                            <span id="otp-timer">00:00</span>
                                        </p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <p>
                                            {{ __('web.auth.didnt_get_otp') }}
                                            <button type="button" class="resendEmailOtp text-primary btn btn-link p-0 align-baseline">
                                                {{ __('web.auth.resend_otp') }}
                                            </button>
                                        </p>
                                    </div>
                                    <div>
                                        <button type="button" id="verify-email-otp-btn" class="verify-email-otp-btn btn btn-lg btn-primary w-100">
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
    <script src="{{ asset('frontend/assets/js/user/login.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
</body>
</html>
