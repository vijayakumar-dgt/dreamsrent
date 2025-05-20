@extends('admin.auth.layouts.app')
@section('meta_title', __('Forgot Password') . ' || ' . $companyName)
@section('content')
<div class="container-fuild">
    <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
            <div class="col-lg-5 mx-auto">
                <form action="" class="p-4" id="resetpasswordForm">
                     @csrf
                    <div class="mx-auto mb-5 text-center">
                        <img src="{{ $logo ?? asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </div>
                    <div class="card authentication-card mb-0">
                        <div class="card-body">
                            <div class="login-icon bg-dark d-flex align-items-center justify-content-center mx-auto mb-4">
                                <i class="ti ti-lock-bolt fs-24"></i>
                            </div>
                            <div class="text-center mb-3">
                                <h4 class="mb-1">{{ __('admin.auth.forgot_password') }}</h4>
                                <p class="mb-0">{{ __('admin.auth.forgot_password_info') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.common.email') }} <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" class="form-control" name="email" id="email">
                                    <span class="input-group-text border-start-0">
                                        <i class="ti ti-mail"></i>
                                    </span>
                                </div>
                                <span class="text-danger error-text" id="email_error"></span>
                            </div>
                            <div class="mt-3">
                                <button type="submit" class="btn btn-dark w-100 submitbtn" disabled>{{ __('admin.auth.reset_password') }}</button>
                            </div>
                            <div class="mt-3 text-center">
                                <p>{{ __('admin.auth.return_to') }} <a href="{{ route('admin-login') }}" class="link-secondary text-decoration-underline">{{ __('admin.auth.sign_in') }}</a></p>
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
<script src="{{ asset('backend/assets/js/admin/auth/forgot-password.js') }}"></script>
@endpush