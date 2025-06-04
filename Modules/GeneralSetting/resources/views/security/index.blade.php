@extends('admin.admin')

@section('meta_title', __('admin.general_settings.security') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper admin-security-settings">
        <div class="content">
            <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
                __('admin.general_settings.settings') => ''
            ]" />
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.account_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="card-body d-none real-card">
                            <div class="security-content">
                                <h6 class="mb-3">{{ __('admin.general_settings.security') }}</h6>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-5">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">{{ __('admin.common.auth_password') }}</h6>
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div>
                                                    <p class="last_changed"></p>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">
                                                <div class="d-flex justify-content-end">
                                                    <a href="javascript:void(0);" class="btn btn-dark changePasswordBtn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#change_password">{{ __('admin.common.change') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-5">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">
                                                        {{ __('admin.general_settings.phonenumber_verification') }}</h6>
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div>
                                                    <p><i
                                                            class="ti ti-circle-check-filled text-success me-1"></i>{{ __('admin.general_settings.verified_mobilenumber') }}<span
                                                            class="verified_phonetxt"></span></p>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">
                                                <div class="d-flex justify-content-end">
                                                    <a href="javascript:void(0);" class="btn btn-dark changePhoneNumberBtn"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#change_phonenumber">{{ __('admin.common.change') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-xl-5">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">
                                                        {{ __('admin.general_settings.email_verification') }}</h6>
                                                </div>
                                            </div>
                                            <div class="col-xl-4">
                                                <div>
                                                    <p><i
                                                            class="ti ti-circle-check-filled text-success me-1"></i>{{ __('admin.general_settings.verify_email') }}<span
                                                            class="verified_emailtxt"></span></p>
                                                </div>
                                            </div>
                                            <div class="col-xl-3">
                                                <div class="d-flex justify-content-end">
                                                    <a href="javascript:void(0);" class="btn btn-dark"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#change_email">{{ __('admin.common.change') }}</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card mb-0">
                                    <div class="card-body">
                                        <div class="mb-3">
                                            <div class="row">
                                                <div class="col-xl-6">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">
                                                            {{ __('admin.general_settings.browsers_devices') }}</h6>
                                                        <p class="fs-13">
                                                            {{ __('admin.general_settings.browsers_devices_account') }}</p>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="d-flex justify-content-end">
                                                        <a href="javascript:void(0);"
                                                            class="btn btn-dark signoutall">{{ __('admin.general_settings.signout_all') }}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="custom-datatable-filter table-responsive brandstable security-table">
                                            <table class="table mb-0" id="userDevicesTable">
                                                <thead class="thead-light">
                                                    <tr>
                                                        <th>{{ __('admin.general_settings.device') }}</th>
                                                        <th>{{ __('admin.general_settings.date') }}</th>
                                                        <th>{{ __('admin.general_settings.ip_address') }}</th>
                                                        <th>{{ __('admin.general_settings.location') }}</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>

    <!-- Change-password -->
    <x-admin.modal className="addmodal" id="change_password" :title="__('admin.general_settings.change_password')"
        formId="changePasswordForm">
        <x-slot name="body">
            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.current_password') }}<span
                        class="text-danger">*</span></label>
                <div class="pass-group">
                    <div class="position-relative">
                        <input type="password" class="form-control" name="current_password" id="current_password">
                        <span
                            class="ti toggle-passwords ti-eye-off position-absolute top-50 translate-middle-y cursor-pointer"></span>
                    </div>
                    <span class="current_password_error text-danger error-text" id="current_password_error"></span>
                    <span class="password-success text-success" id="passwordSuccess"></span>
                </div>
            </div>

            <div class="input-block mb-3">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.general_settings.new_password') }}<span
                            class="text-danger">*</span></label>
                    <div class="pass-group" id="passwordInput">
                        <div class="position-relative">
                            <input type="password" class="form-control pass-input" name="new_password" id="new_password">
                            <span
                                class="ti toggle-password ti-eye-off position-absolute top-35 translate-middle-y cursor-pointer"></span>
                        </div>
                        <span class="new_password_error text-danger error-text" id="new_password_error"></span>
                    </div>
                </div>
                <div class="password-strength d-flex" id="passwordStrength">
                    <span id="poor"></span>
                    <span id="weak"></span>
                    <span id="strong"></span>
                    <span id="heavy"></span>
                </div>
                <div id="passwordInfo" class="mb-2"></div>
                <p class="fs-12">{{ __('admin.general_settings.password_characters_symbols') }}</p>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.confirm_password') }}<span
                        class="text-danger">*</span></label>
                <div class="pass-group">
                    <div class="position-relative">
                        <input type="password" class="form-control pass-inputa" name="confirm_password"
                            id="confirm_password">
                        <span
                            class="ti toggle-passworda ti-eye-off position-absolute top-35 translate-middle-y cursor-pointer"></span>
                    </div>
                    <span class="confirm_password_error text-danger error-text" id="confirm_password_error"></span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                <button type="submit"
                    class="btn btn-primary submitbtn">{{ __('admin.general_settings.save_changes') }}</button>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- /Change-password -->

    <!-- Change-phone-number -->
    <x-admin.modal className="addmodal" id="change_phonenumber" :title="__('admin.general_settings.change_phone_number')"
        formId="changePhoneNumberForm">
        <x-slot name="body">
            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.current_phone_number') }}<span
                        class="text-danger">*</span></label>
                <div>
                    <input type="text" class="form-control" name="current_phonenumber" id="current_phonenumber">
                    <span id="current_phonenumber_error" class="text-danger error-text"></span>
                    <span id="current_phonenumber_success" class="text-success"></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.new_phone_number') }} <span
                        class="text-danger">*</span></label>
                <div>
                    <input type="text" class="form-control" name="new_phonenumber" id="new_phonenumber">
                </div>
                <span id="new_phonenumber_error" class="text-danger error-text"></span>
                <p class="d-flex align-items-center">
                    <i class="ti ti-info-circle me-1"></i>{{ __('admin.general_settings.phonenumber_update_verified') }}
                </p>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.current_password') }}<span
                        class="text-danger">*</span></label>
                <div class="pass-group">
                    <div class="position-relative">
                        <input type="password" class="form-control pass-inputa" name="phone_current_password"
                            id="phone_current_password">
                        <span
                            class="ti toggle-passworda ti-eye-off position-absolute top-50 translate-middle-y cursor-pointer"></span>
                    </div>
                    <span id="phone_current_password_error" class="text-danger error-text"></span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                <button type="submit"
                    class="btn btn-primary submitbtn">{{ __('admin.general_settings.save_changes') }}</button>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- /Change-phone-number -->

    <!-- Change-email -->
   <x-admin.modal className="addmodal" id="change_email" :title="__('admin.general_settings.change_email_address')" formId="changeEmailForm">
    <x-slot name="body">
        <div class="mb-3">
            <label class="form-label">{{ __('admin.general_settings.current_email_address') }} <span class="text-danger">*</span></label>
            <div>
                <input type="email" class="form-control" name="current_email" id="current_email" data-email="{{ auth()->user()->email }}">
                <span id="current_email_error" class="text-danger error-text"></span>
                <span id="current_email_success" class="text-success"></span>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('admin.general_settings.new_email_address') }} <span class="text-danger">*</span></label>
            <div>
                <input type="email" class="form-control" name="new_email" id="new_email">
                <span id="new_email_error" class="text-danger error-text"></span>
            </div>
            <p class="d-flex align-items-center">
                <i class="ti ti-info-circle me-1"></i>{{ __('admin.general_settings.email_address_update_verified') }}
            </p>
        </div>

        <div class="mb-3">
            <label class="form-label">{{ __('admin.general_settings.current_password') }} <span class="text-danger">*</span></label>
            <div class="pass-group">
                <div class="position-relative">
                    <input type="password" class="form-control pass-inputa" name="email_current_password" id="email_current_password">
                    <span class="ti toggle-passworda ti-eye-off position-absolute top-50 translate-middle-y cursor-pointer"></span>
                </div>
                <span id="email_current_password_error" class="text-danger error-text"></span>
            </div>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                {{ __('admin.general_settings.cancel') }}
            </button>
            <button type="submit" class="btn btn-primary submitbtn">
                {{ __('admin.general_settings.save_changes') }}
            </button>
        </div>
    </x-slot>
    </x-admin.modal>

    <!-- /Change-email -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/security.js') }}"></script>
@endpush