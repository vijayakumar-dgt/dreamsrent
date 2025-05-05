@extends('admin.admin')

@section('meta_title', __('admin.general_settings.sms_gateway') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
                <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ __('admin.general_settings.settings') }}</h2>
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
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.system_settings') }}</h5>
                        </div>
                        <div class="card-body pb-0">
                            <div class="sms-gateway">
                                <h6 class="mb-3">{{ __('admin.general_settings.sms_gateway') }}</h6>
                                <div class="row">
                                    <!-- Nexmo -->
                                    <div class="col-xxl-4 col-md-6 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between mb-3">
                                                    <img src="/backend/assets/img/icons/nexmo-logo-icon.svg" alt="Nexmo" class="img-flui">
                                                    <span class="badge badge-outline d-flex align-items-center"><i class="ti ti-point-filled text-success me-1"></i>{{ __('admin.general_settings.connected') }}</span>
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.enable_communication_sms') }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <a href="javascript:void(0);" class="d-inline-flex align-items-center text-gray-9" data-bs-toggle="modal" data-bs-target="#add_nexmo"><i class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="nexmo-switch" name="nexmo" class="form-check-input form-label gateway-switch" type="checkbox" role="switch" checked>
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
                                                    <img src="/backend/assets/img/icons/two-factor-icon.svg" alt="2Factor" class="img-flui">
                                                    <span class="badge badge-outline d-flex align-items-center"><i class="ti ti-point-filled text-success me-1"></i>{{ __('admin.general_settings.connected') }}</span>
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.sms_integration') }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <a href="javascript:void(0);" class="d-inline-flex align-items-center text-gray-9" data-bs-toggle="modal" data-bs-target="#add_2factor"><i class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="twofactor-switch" name="twofactor" class="form-check-input form-label gateway-switch" type="checkbox" role="switch" checked>
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
                                                    <img src="/backend/assets/img/icons/twilio-icon.svg" alt="Twilio" class="img-flui">
                                                    <span class="badge badge-outline d-flex align-items-center"><i class="ti ti-point-filled text-success me-1"></i>{{ __('admin.general_settings.connected') }}</span>
                                                </div>
                                                <p class="mb-0">{{ __('admin.general_settings.twilio_api_integration') }}</p>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <a href="javascript:void(0);" class="d-inline-flex align-items-center text-gray-9" data-bs-toggle="modal" data-bs-target="#add_twilio"><i class="ti ti-settings me-1"></i>{{ __('admin.general_settings.configure') }}</a>
                                                    @endif
                                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="twilio-switch" name="twilio" class="form-check-input form-label gateway-switch" type="checkbox" role="switch" checked>
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
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add nexom -->
    <div class="modal fade addmodal" id="add_nexmo">
        <form id="addNexmoForm">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="mb-0">{{ __('admin.general_settings.nexmo_configuration') }}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- API Key -->
                        <input type="hidden" name="type" id="type" class="form-control" value="nexmo" >

                        <div class="mb-3">
                            <label for="nexmoApiKey" class="form-label">{{ __('admin.general_settings.api_key') }}<span class="text-danger">*</span></label>
                            <input type="text" id="nexmo_api_key" name="nexmo_api_key" class="form-control" placeholder="Enter Nexmo API Key">
                            <span id="nexmo_api_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- API Secret Key -->
                        <div class="mb-3">
                            <label for="nexmoApiSecret" class="form-label">{{ __('admin.general_settings.api_secret_key') }}<span class="text-danger">*</span></label>
                            <input type="text" id="nexmo_secret_key" name="nexmo_secret_key" class="form-control" placeholder="Enter Nexmo API Secret">
                            <span id="nexmo_secret_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- Sender ID -->
                        <div class="mb-3">
                            <label for="nexmoSenderId" class="form-label">{{ __('admin.general_settings.sender_id') }} <span class="text-danger">*</span></label>
                            <input type="text" id="nexmo_sender_id" name="nexmo_sender_id" class="form-control" placeholder="Enter Nexmo Sender ID">
                            <span id="nexmo_sender_id_error" class="text-danger error-text"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /Add nexom -->

    <!-- Add Twilio -->
    <div class="modal fade addmodal" id="add_twilio">
        <form id="addTwilioForm">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="mb-0">{{ __('admin.general_settings.twilio_configuration') }}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- API Key -->
                        <input type="hidden" name="type" id="type" class="form-control" value="twilio" >

                        <div class="mb-3">
                            <label for="twilioApiKey" class="form-label">{{ __('admin.general_settings.account_sid') }}<span class="text-danger">*</span></label>
                            <input type="text" id="twilio_api_key" name="twilio_api_key" class="form-control" placeholder="Enter Twilio API Key">
                            <span id="twilio_api_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- API Secret Key -->
                        <div class="mb-3">
                            <label for="twilioApiSecret" class="form-label">{{ __('admin.general_settings.api_secret_key_with_token') }}  <span class="text-danger">*</span></label>
                            <input type="text" id="twilio_secret_key" name="twilio_secret_key" class="form-control" placeholder="Enter Twilio API Secret">
                            <span id="twilio_secret_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- Sender ID -->
                        <div class="mb-3">
                            <label for="twilioSenderId" class="form-label">{{ __('admin.general_settings.sender_twilio_phone_number') }} <span class="text-danger">*</span></label>
                            <input type="text" id="twilio_sender_id" name="twilio_sender_id" class="form-control" placeholder="Enter Twilio Sender ID">
                            <span id="twilio_sender_id_error" class="text-danger error-text"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /Add Twilio -->

    <!-- Add 2factor -->
    <div class="modal fade addmodal" id="add_2factor">
        <form id="add2FactorForm">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="mb-0">{{ __('admin.general_settings.2factor_configuration') }}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- API Key -->
                        <input type="hidden" name="type" id="type" class="form-control" value="twofactor" >

                        <div class="mb-3">
                            <label for="twofactor_api_key" class="form-label">{{ __('admin.general_settings.api_key') }}<span class="text-danger">*</span></label>
                            <input type="text" id="twofactor_api_key" name="twofactor_api_key" class="form-control" placeholder="Enter 2Factor API Key">
                            <span id="twofactor_api_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- API Secret Key -->
                        <div class="mb-3">
                            <label for="twofactor_secret_key" class="form-label">{{ __('admin.general_settings.api_secret_key') }}<span class="text-danger">*</span></label>
                            <input type="text" id="twofactor_secret_key" name="twofactor_secret_key" class="form-control" placeholder="Enter 2Factor API Secret">
                            <span id="twofactor_secret_key_error" class="text-danger error-text"></span>
                        </div>

                        <!-- Sender ID -->
                        <div class="mb-3">
                            <label for="twofactor_sender_id" class="form-label">{{ __('admin.general_settings.sender_id') }}<span class="text-danger">*</span></label>
                            <input type="text" id="twofactor_sender_id" name="twofactor_sender_id" class="form-control" placeholder="Enter 2Factor Sender ID">
                            <span id="twofactor_sender_id_error" class="text-danger error-text"></span>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <!-- /Add 2factor -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/sms-gateway-setting.js') }}"></script>
@endpush










