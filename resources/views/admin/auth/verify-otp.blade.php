@extends('admin.auth.layouts.app')
@section('meta_title', __('Verify OTP') . ' || ' . $companyName)
@section('content')
<div class="container-fuild">
    <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
            <div class="col-lg-5 mx-auto">
                <form action="" class="digit-group p-4" id="resetpasswordForm">
                    <div class="mx-auto mb-5 text-center">
                        <img src="{{ $logo ?? asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </div>
                    <div class="card authentication-card mb-0">
                        <div class="card-body">
                            <div class="login-icon bg-dark d-flex align-items-center justify-content-center mx-auto mb-4">
                                <i class="ti ti-mail fs-24"></i>
                            </div>
                            <div class="text-center mb-3">
                                <h4 class="mb-1">{{ __('admin.auth.verify_your_email') }}</h4>
                                <p class="mb-0">{{ __('admin.auth.we_sent_otp_to_email') }} {{$email}} {{ __('admin.auth.verify_your_email_address') }}</p>
                            </div>
                            <div class="text-center otp-input">
                                <div class="d-flex align-items-center justify-content-center mb-3">
                                    <input type="text" class="form-control otpinput" id="digit_1" name="digit_1" maxlength="1" data-next="digit-2" maxlength="1">
                                    <input type="text" class="form-control otpinput" id="digit_2" name="digit_2" maxlength="1" data-next="digit-3" data-previous="digit-1" maxlength="1">
                                    <input type="text" class="form-control otpinput" id="digit_3" name="digit_3" maxlength="1" data-next="digit-4" data-previous="digit-2" maxlength="1">
                                    <input type="text" class="form-control otpinput" id="digit_4" name="digit_4" maxlength="1" data-next="digit-5" data-previous="digit-3" maxlength="1">
                                </div>
                                <span class="otp-error-text text-danger"></span>
                                <div>
                                    <div class="badge bg-danger-transparent mb-3 countdowndiv">
                                        <p class="d-flex align-items-center timer"><i class="ti ti-clock me-1"></i>{{ __('admin.auth.otp_time') }}</p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <a href="javascript:void(0);" class="text-secondary text-decoration-underline resend_otp_btn">{{ __('admin.auth.resend_otp') }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-dark w-100 resetpasswordbtn">{{ __('admin.auth.reset_password') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/auth/verify-otp.js') }}"></script>
@endpush