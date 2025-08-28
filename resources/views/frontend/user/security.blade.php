@extends($layout)

@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_settings') }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('web.home.home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_settings') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

@include('frontend.user.nav_menu')

<!-- Page Content -->
<div class="content settings-profile-content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header content-settings-header">
            <h4>{{ __('web.common.settings') }}</h4>
        </div>
        <!-- /Content Header -->

        <div class="row">
            @include('frontend.user.user_sidebar')

            <!-- Settings Details -->
            <div class="col-lg-9">
                <div class="settings-info">
                    <div class="settings-sub-heading">
                        <h4>{{ __('web.user.security') }}</h4>
                    </div>
                    <div class="row">
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="security-grid flex-fill">
                                <div class="security-heading">
                                    <h5>{{ __('web.user.password') }}</h5>
                                </div>
                                <div class="security-content">
                                    <p class="change_password_time">{{ __('web.user.last_changed') }}</p>
                                </div>
                                <div class="security-btn security-btn-info">
                                    <a href="javascript:void(0)" class="btn btn-secondary changePasswordBtn" data-bs-toggle="modal" data-bs-target="#change_password">
                                        {{ __('web.user.change') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="security-grid flex-fill">
                                <div class="security-heading">
                                    <h5>{{ __('web.user.device_management') }}</h5>
                                </div>
                                <div class="security-content">
                                    <p class="device_management_time">{{ __('web.user.last_login_at') }}</p>
                                </div>
                                <div class="security-btn">
                                    <a href="javascript:void(0)" class="btn btn-secondary device_management" data-bs-toggle="modal" data-bs-target="#device_management">
                                        {{ __('web.user.manage') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 d-flex">
                            <div class="security-grid flex-fill">
                                <div class="security-heading">
                                    <h5>{{ __('web.user.delete_account') }}</h5>
                                </div>
                                <div class="security-content d-none">
                                    <p class="delete_account_time">{{ __('web.user.last_changed') }}</p>
                                </div>
                                <div class="security-btn mt-5">
                                    <a href="javascript:void(0)" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#delete_account">
                                        {{ __('web.common.delete') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Details -->
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Change-password -->
<div class="modal fade" id="change_password" tabindex="-1" aria-labelledby="changePasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form action="" id="changePasswordForm">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="changePasswordLabel">{{ __('web.user.change_password') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Current Password -->
                    <div class="mb-3">
                        <label for="current_password" class="form-label">
                            {{ __('web.user.current_password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="current_password" id="current_password">
                            <span class="ti toggle-passwords ti-eye-off position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"></span>
                        </div>
                        <span class="text-danger error-text" id="current_password_error"></span>
                        <span class="text-success" id="passwordSuccess"></span>
                    </div>

                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">
                            {{ __('web.user.new_password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="new_password" id="new_password">
                            <span class="ti toggle-password ti-eye-off position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"></span>
                        </div>
                        <span class="text-danger error-text" id="new_password_error"></span>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">
                            {{ __('web.user.confirm_password') }} <span class="text-danger">*</span>
                        </label>
                        <div class="position-relative">
                            <input type="password" class="form-control" name="confirm_password" id="confirm_password">
                            <span class="ti toggle-passworda ti-eye-off position-absolute top-50 end-0 translate-middle-y me-3 cursor-pointer"></span>
                        </div>
                        <span class="text-danger error-text" id="confirm_password_error"></span>
                    </div>

                    <div class="modal-footer d-flex justify-content-end">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                            {{ __('web.common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary btn-sm submitbtn">
                            {{ __('web.user.save_changes') }}
                        </button>
                    </div>
                </div>


            </form>
        </div>
    </div>
</div>
<!-- /Change-password -->

<!-- deviceManagement -->
<div class="modal fade" id="device_management" tabindex="-1" aria-labelledby="deviceManagementLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <form action="" id="deviceManagement">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="deviceManagementLabel">{{ __('web.user.device_management') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="userDevicesTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ strtoupper(__('web.user.device')) }}</th>
                                    <th>{{ strtoupper(__('web.user.date')) }}</th>
                                    <th>{{ strtoupper(__('web.user.ip_address')) }}</th>
                                    <th>{{ strtoupper(__('web.user.location')) }}</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <div class="modal-footer d-flex justify-content-end mt-1">
                        <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">
                            {{ __('web.common.close') }}
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<!-- /deviceManagement -->

<!-- Delete Modal -->
<div class="modal new-modal fade" id="delete_account" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="delete-action">
                    <div class="delete-header">
                        <h4>{{ __('web.user.delete_account') }}</h4>
                        <p>{{ __('web.user.confirm_delete_account') }}</p>
                    </div>
                    <div class="modal-btn">
                        <div class="row">
                            <div class="col-6">
                                <button type="button" class="btn btn-secondary w-100 delete_account_btn">
                                    {{ __('web.common.delete') }}
                                </a>
                            </div>
                            <div class="col-6">
                                <button type="button" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                    {{ __('web.common.cancel') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/user/user-security.js') }}"></script>
@endpush
