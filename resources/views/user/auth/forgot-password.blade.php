<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($seo_title) ? $seo_title : config('app.name') }} | {{ __('web.auth.forgot_password_title') }}
    </title>

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
                <form action="" class="" id="resetpasswordForm">
                    @csrf
                    <div class="login-auth">
                        <div class="login-auth-wrap">
                            <div class="sign-group">
                                <a href="{{ route('home') }}" class="btn sign-up"><span><i
                                            class="fe feather-corner-down-left" aria-hidden="true"></i></span>
                                    {{ __('web.common.back_to_home') }}</a>
                            </div>
                            <h1>{{ __('web.auth.forgot_password_title') }}</h1>
                            <p class="account-subtitle">
                                {{ __('web.auth.forgot_password_description') }}
                            </p>
                            <div class="input-block">
                                <label for="email" class="form-label">
                                    {{ __('web.auth.email_address') }} <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <button type="submit" id="forgot_otp" class="btn btn-outline-light w-100 btn-size">
                                {{ __('web.auth.reset_password_title') }}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade" id="otp-email-modal" tabindex="-1" data-bs-backdrop="static" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header d-flex align-items-center justify-content-end pb-0 border-0">
                        <button type="button" data-bs-dismiss="modal" aria-label="Close" id="close-otp-modal"
                            class="btn p-0 border-0 bg-transparent">
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
                                        <p>{{ __('web.auth.didnt_get_otp') }}
                                            <button type="button"
                                                class="resendEmailOtpForgot btn btn-link text-primary p-0">
                                                {{ __('web.auth.resend_otp') }}
                                            </button>
                                        </p>
                                    </div>
                                    <div>
                                        <button type="button" id="verify-email-forgot-otp-btn"
                                            class="verify-email-otp-btn btn btn-lg btn-primary w-100">
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
    <script src="{{ asset('frontend/assets/js/custom/lang_script.js') }}"></script>

    <!-- Bootstrap Core JS -->
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>

    <!-- Custom JS -->
    <script src="{{ asset('frontend/assets/js/custom/custom-script.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/user/forgot-password.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
</body>

</html>
