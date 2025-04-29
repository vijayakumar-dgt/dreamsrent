<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Dreams Rent | Login</title>

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
				<a href="{{ route('home') }}"><img class="img-fluid logo-dark" src="{{ $logo ?? 'assets/img/logo.svg' }}" alt="Logo"></a>
			</header>
			<!-- /Header -->

			<div class="login-wrapper">
                <div class="loginbox">
                    <div class="login-auth">
                        <div class="login-auth-wrap">
                            <div class="sign-group d-none">
                                <a href="{{ route('home') }}" class="btn sign-up">
                                    <span><i class="fe feather-corner-down-left" aria-hidden="true"></i></span>
                                    {{ __('web.common.back_to_home') }}
                                </a>
                            </div>
                            <h1>{{ __('web.auth.sign_in') }}</h1>
                            <p class="account-subtitle">{{ __('web.auth.email_confirmation_subtitle') }}</p>

                            <form id="userLoginForm">
                                <div class="input-block">
                                    <label class="form-label">
                                        {{ __('web.auth.email') }} <span class="text-danger">*</span>
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" />
                                    <span class="error-text text-danger" id="email_error"></span>
                                </div>

                                <div class="input-block">
                                    <label class="form-label">
                                        {{ __('web.auth.password') }} <span class="text-danger">*</span>
                                    </label>
                                    <div class="pass-group">
                                        <input type="password" class="form-control pass-input" id="password" name="password" />
                                        <span class="fas fa-eye-slash toggle-password"></span>
                                    </div>
                                    <span class="error-text text-danger" id="password_error"></span>
                                </div>

                                <div class="input-block d-flex justify-content-between">
                                    <a class="forgot-link" href="{{ route('user-forgot-password') }}">{{ __('web.auth.forgot_password') }}</a>
                                    <a class="form-check-label text-decoration-underline" id="login_otp" href="javascript:void(0);">
                                        {{ __('web.auth.sign_in_with_otp') }}
                                    </a>
                                </div>

                                <div class="input-block m-0">
                                    <label class="custom_check d-inline-flex">
                                        <span>{{ __('web.auth.remember_me') }}</span>
                                        <input type="checkbox" name="remember">
                                        <span class="checkmark"></span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-outline-light w-100 btn-size mt-1">
                                    {{ __('web.auth.sign_in') }}
                                </button>

                                <div class="login-or d-none">
                                    <span class="or-line"></span>
                                    <span class="span-or-log">{{ __('web.auth.or_login_with_email') }}</span>
                                </div>

                                <!-- Social Login -->
                                <div class="social-login d-none">
                                    <a href="#" class="d-flex align-items-center justify-content-center input-block btn google-login w-100">
                                        <span><img src="{{ asset('frontend/assets/img/icons/google.svg') }}" class="img-fluid" alt="Google"></span>
                                        {{ __('web.auth.login_with_google') }}
                                    </a>
                                </div>

                                <div class="social-login d-none">
                                    <a href="#" class="d-flex align-items-center justify-content-center input-block btn google-login w-100">
                                        <span><img src="{{ asset('frontend/assets/img/icons/facebook.svg') }}" class="img-fluid" alt="Facebook"></span>
                                        {{ __('web.auth.login_with_facebook') }}
                                    </a>
                                </div>
                                <!-- /Social Login -->

                                <div class="text-center dont-have">
                                    {{ __('web.auth.dont_have_account') }}
                                    <a href="{{ route('user-register') }}">{{ __('web.auth.register') }}</a>
                                </div>

                                <div class="mt-3">
                                    <h6 class="fs-16 mb-1">{{ __('web.auth.demo_user_info') }}:</h6>
                                    <div class="p-3 border d-flex flex-wrap align-items-center justify-content-between">
                                        <div>
                                            <span class="d-block"><strong>{{ __('web.auth.email') }}:</strong> demouser@example.com</span>
                                            <span class="d-block"><strong>{{ __('web.auth.password') }}:</strong> 12345678</span>
                                        </div>
                                        <div>
                                            <a class="btn btn-primary copy-login-details"
                                               data-email="demouser@example.com"
                                               data-password="12345678">
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
                            <a href="javascript:void(0);" data-bs-dismiss="modal" aria-label="Close" id="close-otp-modal">
                                <i class="ti ti-circle-x-filled fs-20"></i>
                            </a>
                        </div>
                        <div class="modal-body p-4">
                            <form action="#" class="digit-group">
                                <div class="text-center mb-3">
                                    <h3 class="mb-2">{{ __('Email OTP Verification') }}</h3>
                                    <p id="otp-email-message" class="fs-14">{{ __('OTP sent to your Email Address') }}</p>
                                </div>
                                <div class="text-center otp-input">
                                    <div class="inputcontainer">

                                    </div>
                                    <span id="error_message" class="text-danger"></span>
                                    <div>
                                        <div class="badge bg-danger-transparent mb-3">
                                            <p class="d-flex align-items-center">
                                                <i class="ti ti-clock me-1"></i>
                                                <span id="otp-timer">00:00</span>
                                            </p>
                                        </div>
                                        <div class="mb-3 d-flex justify-content-center">
                                            <p> {{ __('Didn t get the OTP?') }}  <a href="javascript:void(0);" class="resendEmailOtp text-primary">{{ __('Resend OTP') }}</a></p>
                                        </div>
                                        <div>
                                            <button type="button" id="verify-email-otp-btn"
                                                class="verify-email-otp-btn btn btn-lg btn-primary w-100">{{ __('Verify & Proceed') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>

            @include('frontend.toast')
			<!-- Footer -->
			<footer class="log-footer">
				<div class="container-fluid">
					<!-- Copyright -->
					<div class="copyright">
						<div class="copyright-text">
							<p>{!! $copyright ?? 'Copyright © '.date('Y').' '.config('app.name').'. All Rights Reserved.' !!}</p>
						</div>
					</div>
					<!-- /Copyright -->
				</div>
			</footer>
			<!-- /Footer -->
		</div>
		<!-- /Main Wrapper -->

		<!-- jQuery -->
		<script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>

        <!-- jQuery validation -->
        <script src="{{ asset('assets/js/jquery/jquery-validation.min.js') }}"></script>
        <script src="{{ asset('assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>

        <!-- Toastr JS -->
	    <script src="{{ asset('assets/plugins/toastr/toastr.min.js') }}"></script>

		<!-- Bootstrap Core JS -->
		<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>

        <script src="{{ asset('frontend/assets/js/user/login.js') }}"></script>

        <script src="{{ asset('frontend/assets/js/custom/custom-script.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('frontend/assets/js/script.js') }}"></script>

	</body>
</html>
