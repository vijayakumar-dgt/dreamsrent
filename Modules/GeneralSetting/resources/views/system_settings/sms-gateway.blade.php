@extends('admin.admin')

@section('meta_title', __('admin.general_settings.sms_gateway') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <x-admin.breadcrumb
                :title="__('admin.general_settings.settings')"
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]"
            />
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.system_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="card-body pb-0 d-none real-card">
                            <div class="sms-gateway">
                                <h6 class="mb-3">{{ __('admin.general_settings.sms_gateway') }}</h6>
                                <div class="row">
                                    <!-- Nexmo -->
                                    <div class="col-xxl-4 col-md-6 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/nexmo-logo-icon.svg') }}"
                                                        alt="Nexmo" class="img-flui">
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.enable_communication_sms') }}
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <a href="javascript:void(0);"
                                                            class="d-inline-flex align-items-center text-gray-9"
                                                            data-bs-toggle="modal" data-bs-target="#add_nexmo"><i
                                                                class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <div class="form-check form-check-md form-switch">
                                                            <input id="nexmo-switch" name="nexmo"
                                                                class="form-check-input form-label gateway-switch"
                                                                type="checkbox" role="switch" checked aria-checked="true">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- 2Factor -->
                                    <div class="col-xxl-4 col-md-6 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/two-factor-icon.svg') }}"
                                                        alt="2Factor" class="img-flui">
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.sms_integration') }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <a href="javascript:void(0);"
                                                            class="d-inline-flex align-items-center text-gray-9"
                                                            data-bs-toggle="modal" data-bs-target="#add_2factor"><i
                                                                class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <div class="form-check form-check-md form-switch">
                                                            <input id="twofactor-switch" name="twofactor"
                                                                class="form-check-input form-label gateway-switch"
                                                                type="checkbox" role="switch" checked aria-checked="true">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Twilio -->
                                    <div class="col-xxl-4 col-md-6 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/twilio-icon.svg') }}"
                                                        alt="Twilio" class="img-flui">
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.twilio_api_integration') }}
                                                </p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <a href="javascript:void(0);"
                                                            class="d-inline-flex align-items-center text-gray-9"
                                                            data-bs-toggle="modal" data-bs-target="#add_twilio"><i
                                                                class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                        <div class="form-check form-check-md form-switch">
                                                            <input id="twilio-switch" name="twilio"
                                                                class="form-check-input form-label gateway-switch"
                                                                type="checkbox" role="switch" checked aria-checked="true">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
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
    <!-- /Page Wrapper -->

    <!-- Add nexom -->
    <x-admin.modal className="addmodal" id="add_nexmo" :title="__('admin.general_settings.nexmo_configuration')"
        formId="addNexmoForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" name="type" value="nexmo">
            <div class="mb-3">
                <label for="nexmo_api_key" class="form-label">{{ __('admin.general_settings.api_key') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="nexmo_api_key" name="nexmo_api_key" class="form-control">
                <span id="nexmo_api_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="nexmo_secret_key" class="form-label">{{ __('admin.general_settings.api_secret_key') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="nexmo_secret_key" name="nexmo_secret_key" class="form-control">
                <span id="nexmo_secret_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="nexmo_sender_id" class="form-label">{{ __('admin.general_settings.sender_id') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="nexmo_sender_id" name="nexmo_sender_id" class="form-control">
                <span id="nexmo_sender_id_error" class="text-danger error-text"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-light me-2"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
        </x-slot>
    </x-admin.modal>
    <!-- /Add nexom -->

    <!-- Add Twilio -->
    <x-admin.modal className="addmodal" id="add_twilio" :title="__('admin.general_settings.twilio_configuration')"
        formId="addTwilioForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" name="type" value="twilio">
            <div class="mb-3">
                <label for="twilio_api_key" class="form-label">{{ __('admin.general_settings.account_sid') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twilio_api_key" name="twilio_api_key" class="form-control">
                <span id="twilio_api_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="twilio_secret_key"
                    class="form-label">{{ __('admin.general_settings.api_secret_key_with_token') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twilio_secret_key" name="twilio_secret_key" class="form-control">
                <span id="twilio_secret_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="twilio_sender_id"
                    class="form-label">{{ __('admin.general_settings.sender_twilio_phone_number') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twilio_sender_id" name="twilio_sender_id" class="form-control">
                <span id="twilio_sender_id_error" class="text-danger error-text"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-light me-2"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
        </x-slot>
    </x-admin.modal>
    <!-- /Add Twilio -->

    <!-- Add 2factor -->
    <x-admin.modal className="addmodal" id="add_2factor" :title="__('admin.general_settings.2factor_configuration')"
        formId="add2FactorForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" name="type" value="twofactor">
            <div class="mb-3">
                <label for="twofactor_api_key" class="form-label">{{ __('admin.general_settings.api_key') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twofactor_api_key" name="twofactor_api_key" class="form-control">
                <span id="twofactor_api_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="twofactor_secret_key" class="form-label">{{ __('admin.general_settings.api_secret_key') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twofactor_secret_key" name="twofactor_secret_key" class="form-control">
                <span id="twofactor_secret_key_error" class="text-danger error-text"></span>
            </div>
            <div class="mb-3">
                <label for="twofactor_sender_id" class="form-label">{{ __('admin.general_settings.sender_id') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="twofactor_sender_id" name="twofactor_sender_id" class="form-control">
                <span id="twofactor_sender_id_error" class="text-danger error-text"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-light me-2"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
        </x-slot>
    </x-admin.modal>
    <!-- /Add 2factor -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/sms-gateway-setting.js') }}"></script>
@endpush
