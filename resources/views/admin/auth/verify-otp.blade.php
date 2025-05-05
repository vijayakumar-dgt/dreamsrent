@extends('admin.auth.layouts.app')
@push('styles')
   <style>
         .resend_otp_btn.disabled {
            pointer-events: none; 
            opacity: 0.5;        
            cursor: not-allowed; 
        }
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
        }

        /* Firefox */
        input[type=number] {
        -moz-appearance: textfield;
        }
   </style>
@endpush
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
                                <h4 class="mb-1">Verify Your Email</h4>
                                <p class="mb-0">We have Sent OTP to {{$email}} to verify your 
                                    email address and activate your account entering the OTP</p>
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
                                        <p class="d-flex align-items-center timer"><i class="ti ti-clock me-1"></i>00:55</p>
                                    </div>
                                    <div class="mb-3 d-flex justify-content-center">
                                        <a href="javascript:void(0);" class="text-secondary text-decoration-underline resend_otp_btn">Resend OTP</a>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-dark w-100 resetpasswordbtn">Reset Password</button>
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