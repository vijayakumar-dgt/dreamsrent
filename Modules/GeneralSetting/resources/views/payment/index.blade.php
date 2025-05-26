@extends('admin.admin')

@section('meta_title', __('admin.general_settings.payment_methods') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
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
                            <h5>{{ __('admin.general_settings.finance_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="card-body pb-0 d-none real-card">
                            <div class="payment-section">
                                <h6 class="mb-3">{{ __('admin.general_settings.payment_method') }}</h6>
                                <div class="row">
                                    <div class="col-xl-4 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div>
                                                    <div class="payment-content">
                                                        <span class=""><img src="/backend/assets/img/icons/paypal-name.svg" alt="image"></span>
                                                        @if (hasPermission($permissions, 'finance_settings', 'edit'))
                                                        <a href="javascript:void(0);" class="" data-bs-toggle="modal" data-bs-target="#add_paypal"><i class="ti ti-settings"></i></a>
                                                        @endif
                                                    </div>
                                                    <p class="fs-13">{{ __('admin.general_settings.paypal_description') }} </p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge badge-outline d-inline-flex align-items-center text-gray-9 paypalIn"><i class="ti ti-point-filled text-dark me-1"></i>{{ __('admin.general_settings.not_connected') }} </span>
                                                    @if (hasPermission($permissions, 'finance_settings', 'edit'))
                                                    <div class="form-check form-check-sm form-switch p-0 m-0">
                                                        <input class="form-check-input form-label m-0 checkStatus" name="paypal_status" id="paypal_status" type="checkbox" role="switch">
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-4 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div>
                                                    <div class="payment-content">
                                                        <span class=""><img src="/backend/assets/img/icons/stripe-icon.svg" alt="image"></span>
                                                        @if (hasPermission($permissions, 'finance_settings', 'edit'))
                                                        <a href="javascript:void(0);" class="" data-bs-toggle="modal" data-bs-target="#add_stripe"><i class="ti ti-settings"></i></a>
                                                        @endif
                                                    </div>
                                                    <p class="fs-13">{{ __('admin.general_settings.api_cards') }} </p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge badge-outline d-inline-flex align-items-center text-gray-9 stripeIn"><i class="ti ti-point-filled text-dark me-1"></i>{{ __('admin.general_settings.not_connected') }} </span>
                                                    @if (hasPermission($permissions, 'finance_settings', 'edit'))
                                                    <div class="form-check form-check-sm form-switch p-0 m-0">
                                                        <input class="form-check-input form-label m-0 checkStatus" name="stripe_status" id="stripe_status" type="checkbox" role="switch">
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div> 
                                    <div class="col-xl-4 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div>
                                                    <div class="payment-content">
                                                        <span class=""><img src="/backend/assets/img/icons/cash-delivery-icon.svg" alt="image"></span>
                                                    </div>
                                                    <p class="fs-13">{{ __('admin.general_settings.cod_cards') }}</p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge badge-outline d-inline-flex align-items-center text-gray-9 codIn">
                                                        <i class="ti ti-point-filled text-dark me-1"></i>{{ __('admin.general_settings.not_connected') }} 
                                                    </span>
                                                    <div class="form-check form-check-sm form-switch p-0 m-0">
                                                        <input class="form-check-input form-label m-0 checkStatus" name="cod_status" id="cod_status" type="checkbox" role="switch">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-xl-4 d-flex">
                                        <div class="card flex-fill">
                                            <div class="card-body">
                                                <div>
                                                    <div class="payment-content">
                                                        <span class=""><img src="/backend/assets/img/icons/payment-method-04.svg" alt="image"></span>
                                                    </div>
                                                    <p class="fs-13">{{ __('admin.general_settings.cod_cards') }}</p>
                                                </div>
                                            </div>
                                            <div class="card-footer">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="badge badge-outline d-inline-flex align-items-center text-gray-9 walletIn">
                                                        <i class="ti ti-point-filled text-dark me-1"></i>{{ __('admin.general_settings.not_connected') }} 
                                                    </span>
                                                    <div class="form-check form-check-sm form-switch p-0 m-0">
                                                        <input class="form-check-input form-label m-0 checkStatus" name="wallet_status" id="wallet_status" type="checkbox" role="switch">
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
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add paypal -->
    <div class="modal fade addmodal" id="add_paypal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">{{ __('admin.common.paypal') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PaypalSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.api_keys') }} <span class="text-danger">*</span></label>
                            <input type="text" id="paypal_key" name="paypal_key" class="form-control">
                            <span class="invalid-feedback" id="paypal_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.secret_key') }} <span class="text-danger">*</span></label>
                            <input type="text" id="paypal_secret" name="paypal_secret" class="form-control">
                            <span class="invalid-feedback" id="paypal_secret_error"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add paypal -->

    <!-- Add Stripe -->
    <div class="modal fade addmodal" id="add_stripe">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">{{ __('admin.common.stripe') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="StripeSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.api_keys') }} <span class="text-danger">*</span></label>
                            <input type="text" id="stripe_key" name="stripe_key" class="form-control">
                            <span class="invalid-feedback" id="stripe_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.secret_key') }} <span class="text-danger">*</span></label>
                            <input type="text" id="stripe_secret" name="stripe_secret" class="form-control">
                            <span class="invalid-feedback" id="stripe_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Stripe -->

   
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/payment.js') }}"></script>
@endpush