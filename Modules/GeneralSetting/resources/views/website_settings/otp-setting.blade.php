@extends('admin.admin')

@section('meta_title', __('admin.general_settings.otp_settings') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.general_settings.settings') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.settings') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Settings Prefix -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-lg-9">
                    <form id="otpSettingForm">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.otp_settings') }}</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader"></div>
                                            <label class="form-label d-none real-label" for="otp_type">{{ __('admin.general_settings.otp_type') }}<span class="text-danger"> *</span></label>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <select class="form-control d-none real-input" name="otp_type" id="otp_type" >
                                                <option value="sms">{{ __('SMS') }}</option>
                                                <option value="email">{{ __('Email') }}</option>
                                            </select>
                                            <span class="text-danger error-text" id="otp_type_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader"></div>
                                            <label class="form-label d-none real-label" for="otp_digit_limit">{{ __('admin.general_settings.otp_digit_limit') }}<span class="text-danger"> *</span></label>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <select class="form-control d-none real-input" name="otp_digit_limit" id="otp_digit_limit">
                                                <option value="4">4</option>
                                                <option value="5">5</option>
                                                <option value="6">6</option>
                                            </select>
                                            <span class="text-danger error-text" id="otp_digit_limit_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader"></div>
                                            <label class="form-label d-none real-label" for="otp_expire_time">{{ __('admin.general_settings.otp_expire_time') }}<span class="text-danger"> *</span></label>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <select class="form-control d-none real-input" name="otp_expire_time" id="otp_expire_time">
                                                <option value="5 mins">5 mins</option>
                                                <option value="2 mins">2 mins</option>
                                                <option value="10 mins">10 mins</option>
                                            </select>
                                            <span class="text-danger error-text" id="otp_expire_time_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-md form-switch me-2">
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <label class="form-check-label form-label mt-0 mb-0 d-none real-label" for="login">
                                                {{ __('admin.general_settings.login') }}
                                            </label>
                                            <input id="login" name="login" class="form-check-input form-label me-2" type="checkbox" role="switch" checked>
                                            <span id="login_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check form-check-md form-switch me-2">
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <label class="form-check-label form-label mt-0 mb-0 d-none real-label">{{ __('admin.general_settings.register') }}
                                                <input id="register" name="register" class="form-check-input form-label me-2" type="checkbox" role="switch" checked>
                                            </label>
                                            <span id="register_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <div class="skeleton label-skeleton label-loader me-2"></div>
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3 d-none real-label" >{{ __('admin.common.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <button type="submit" class="btn btn-primary submitBtn d-none real-label">{{ __('admin.common.save_changes') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/otp-setting.js') }}"></script>
@endpush










