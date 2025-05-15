@extends('admin.auth.layouts.app')
@section('meta_title', __('admin.general_settings.login') . ' || ' . $companyName)
@section('content')
<div class="container-fuild">
    <div class="w-100 overflow-hidden position-relative flex-wrap d-block vh-100">
        <div class="row justify-content-center align-items-center vh-100 overflow-auto flex-wrap ">
            <div class="col-lg-5 mx-auto">
                    <div class="mx-auto mb-5 text-center">
                        <img src="{{ $logo ?? asset('backend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </div>
                    <div class="card authentication-card mb-0">
                        <div class="card-body">
                            <form action="" id="loginForm">
                            @csrf
                            <div class="login-icon bg-dark d-flex align-items-center justify-content-center mx-auto mb-4">
                                <i class="ti ti-login fs-24"></i>
                            </div>
                            <div class="text-center mb-3">
                                <h4 class="mb-1">Welcome Back</h4>
                                <p class="mb-0">Please enter your details to sign in</p>
                            </div>
                            <div class="error d-flex justify-content-center">
                                <span id="error" class="text-danger"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email / Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" id="email">
                                    <span class="input-group-text border-start-0">
                                        <i class="ti ti-user-circle"></i>
                                    </span>
                                </div>
                                <span id="email_error" class="text-danger error-text"></span>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="password" class="pass-input form-control @error('password') is-invalid @enderror" id="password" name="password">
                                    <span class="input-group-text border-start-0">
                                        <i class="ti ti-eye-off togglse-password"  id="toggle-password"></i>
                                    </span>
                                </div>
                                <span id="password_error" class="text-danger error-text"></span>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-check-md mb-0">
                                        <input class="form-check-input" id="remember_me" type="checkbox" name="remember">
                                        <label for="remember_me" class="form-check-label mt-0">Remember Me</label>
                                    </div>
                                </div>
                                <div class="text-end">
                                    <a href="{{ route('forgot-password') }}" class="link-default text-decoration-underline">Forgot Password</a>
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <button type="submit" class="btn btn-dark w-100 submitbtn" disabled>Login</button>
                            </div>
                          </form> 
                                                 
                        </div>                         
                    </div>
            </div>

        </div>
    </div>
</div>
@endsection
@push('scripts')
   <script src="{{ asset('backend/assets/js/admin/auth/login.js') }}"></script>
@endpush
