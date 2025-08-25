@extends('admin.auth.layouts.app')
@section('meta_title', __('Reset Password') . ' || ' . $companyName)
@section('content')
<div class="container-fuild">
    <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
            <div class="col-lg-5 mx-auto">
                <form action="" class="p-4" id="resetpasswordForm">
                     @csrf
                     <input type="hidden" name="token" value="{{ $token }}">
                    <div class="mx-auto mb-5 text-center">
                        <img src="{{ $logo ?? asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </div>
                    <div class="card authentication-card mb-0">
                        <div class="card-body">
                            <div class="login-icon bg-dark d-flex align-items-center justify-content-center mx-auto mb-4">
                                <i class="ti ti-lock-star fs-24"></i>
                            </div>
                            <div class="text-center mb-3">
                                <h4 class="mb-1">{{ __('admin.auth.reset_password') }}</h4>
                                <p class="mb-0">{{ __('admin.auth.enter_new_password') }}</p>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">{{ __('admin.general_settings.new_password') }} <span class="text-danger">*</span></label>
                                <div class="pass-group">
                                    <input type="password" class="pass-input form-control" name="password" id="password">
                                    <span class="ti toggle-password ti-eye-off"></span>
                                    <span class="error-text text-danger" id="password_error"></span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">{{ __('admin.general_settings.confirm_password') }} <span class="text-danger">*</span></label>
                                <div class="pass-group">
                                    <input type="password" class="pass-inputs form-control" name="password_confirmation" id="password_confirmation">
                                    <span class="ti toggle-passwords ti-eye-off"></span>
                                    <span class="error-text text-danger" id="password_confirmation_error"></span>
                                </div>
                            </div>
                            <span class="password-error-text text-danger"></span>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-dark w-100 submitbtn">{{ __('admin.auth.reset_password') }}</button>
                            </div>
                            <p class="text-center mt-4">{{ __('admin.auth.return_to') }} <a href="{{ route('admin-login') }}" class="text-secondary text-decoration-underline">{{ __('admin.auth.sign_in') }}</a></p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/admin/auth/reset-password.js') }}"></script>
@endpush
