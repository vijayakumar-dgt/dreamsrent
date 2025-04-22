<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

		<title>Dreams Rent | ForgotPassword</title>

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
				<a href="{{ route('home') }}"><img class="img-fluid logo-dark" src="/assets/img/logo.svg" alt="Logo"></a>
			</header>
			<!-- /Header -->

			<div class="login-wrapper">
				<div class="loginbox">
                    <form action="" class="p-4" id="resetpasswordForm">
                        @csrf
                        <div class="login-auth">
                            <div class="login-auth-wrap">
                                <div class="sign-group">
                                    <a href="{{ route('home') }}" class="btn sign-up"><span><i class="fe feather-corner-down-left" aria-hidden="true"></i></span> Back To Home</a>
                                </div>
                                <h1>Forgot Password</h1>
                                <p class="account-subtitle">Enter your email and we will send you a link to reset your password.</p>
                                <form action="index.html">
                                    <div class="input-block">
                                        <label class="form-label">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="email" class="form-control"  placeholder="">
                                    </div>
                                    <button type="submit" id="forgot_otp" class="btn btn-outline-light w-100 btn-size">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </form>
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
                                            <p> {{ __('Didn t get the OTP?') }}  <a href="javascript:void(0);" class="resendEmailOtpForgot text-primary">{{ __('Resend OTP') }}</a></p>
                                        </div>
                                        <div>
                                            <button type="button" id="verify-email-forgot-otp-btn"
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

        <script src="{{ asset('frontend/assets/js/user/forgot-password.js') }}"></script>

		<!-- Custom JS -->
		<script src="{{ asset('frontend/assets/js/script.js') }}"></script>

	</body>
</html>
