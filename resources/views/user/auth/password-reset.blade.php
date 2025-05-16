<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($seo_title) ? $seo_title : config('app.name') }} | {{ __('web.auth.forgot_password_title') }}</title>

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
                        <h1>{{ __('web.auth.reset_password_title') }}</h1>
                        <p class="account-subtitle">{{ __('web.auth.reset_password_description') }}</p>
                        <form action="" id="changePasswordForm">
                            @csrf
                            <div class="input-block">
                                <input type="hidden" name="email" id="email">
                                <label class="form-label">
                                    {{ __('web.auth.new_password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="pass-group">
                                    <input type="password" class="pass-inputs form-control" name="current_password" id="current_password">
                                    <span class="ti toggle-passwords ti-eye-off"></span>
                                    <span class="current_password_error text-danger error-text" id="current_password_error"></span>
                                    <span class="password-success text-success" id="passwordSuccess"></span>
                                </div>
                            </div>
                            <div class="input-block">
                                <label class="form-label">
                                    {{ __('web.auth.confirm_password') }} <span class="text-danger">*</span>
                                </label>
                                <div class="pass-group">
                                    <input type="password" class="pass-inputa form-control" name="confirm_password" id="confirm_password">
                                    <span class="ti toggle-passworda ti-eye-off"></span>
                                    <span class="confirm_password_error text-danger error-text" id="confirm_password_error"></span>
                                </div>
                            </div>
                            <button class="btn btn-outline-light w-100 btn-size">{{ __('web.auth.save_changes') }}</button>
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
    <script src="{{ asset('frontend/assets/js/user/reset-password.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
</body>
</html>
