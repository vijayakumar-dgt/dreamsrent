@extends('admin.admin')

@section('meta_title', __('admin.general_settings.payment_methods') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
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
                                                    <p class="fs-13">Indicating that goods must be paid for at the time of delivery.</p>
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
                                                    <p class="fs-13">Indicating that goods must be paid for at the time of delivery.</p>
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
                    <h4 class="mb-0">Paypal</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PaypalSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="paypal_email" name="paypal_email" class="form-control">
                            <span class="invalid-feedback" id="paypal_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="paypal_key" name="paypal_key" class="form-control">
                            <span class="invalid-feedback" id="paypal_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="paypal_secret" name="paypal_secret" class="form-control">
                            <span class="invalid-feedback" id="paypal_secret_error"></span>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
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
                    <h4 class="mb-0">Stripe</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="StripeSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="stripe_email" name="stripe_email" class="form-control">
                            <span class="invalid-feedback" id="stripe_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="stripe_key" name="stripe_key" class="form-control">
                            <span class="invalid-feedback" id="stripe_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="stripe_secret" name="stripe_secret" class="form-control">
                            <span class="invalid-feedback" id="stripe_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Stripe -->

    <!-- Add Braintree -->
    <div class="modal fade addmodal" id="add_braintree">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Braintree</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="BraintreeSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="braintree_email" name="braintree_email" class="form-control">
                            <span class="invalid-feedback" id="braintree_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="braintree_key" name="braintree_key" class="form-control">
                            <span class="invalid-feedback" id="braintree_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="braintree_secret" name="braintree_secret" class="form-control">
                            <span class="invalid-feedback" id="braintree_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Braintree -->

    <!-- Add Razorpay -->
    <div class="modal fade addmodal" id="add_razorpay">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Razorpay</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="RazorpaySettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="razorpay_email" name="razorpay_email" class="form-control">
                            <span class="invalid-feedback" id="razorpay_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="razorpay_key" name="razorpay_key" class="form-control">
                            <span class="invalid-feedback" id="razorpay_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="razorpay_secret" name="razorpay_secret" class="form-control">
                            <span class="invalid-feedback" id="razorpay_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Razorpay -->

    <!-- Add 2checkout -->
    <div class="modal fade addmodal" id="add_2checkout">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">2checkout</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="TwoCheckoutSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="twocheckout_email" name="twocheckout_email" class="form-control">
                            <span class="invalid-feedback" id="twocheckout_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="twocheckout_key" name="twocheckout_key" class="form-control">
                            <span class="invalid-feedback" id="twocheckout_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="twocheckout_secret" name="twocheckout_secret" class="form-control">
                            <span class="invalid-feedback" id="twocheckout_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add 2checkout -->

    <!-- Add Skrill -->
    <div class="modal fade addmodal" id="add_skrill">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Skrill</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="SkrillSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="skrill_email" name="skrill_email" class="form-control">
                            <span class="invalid-feedback" id="skrill_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="skrill_key" name="skrill_key" class="form-control">
                            <span class="invalid-feedback" id="skrill_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="skrill_secret" name="skrill_secret" class="form-control">
                            <span class="invalid-feedback" id="skrill_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Skrill -->

    <!-- Add PayU -->
    <div class="modal fade addmodal" id="add_payu">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">PayU</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PayUSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="payu_email" name="payu_email" class="form-control">
                            <span class="invalid-feedback" id="payu_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="payu_key" name="payu_key" class="form-control">
                            <span class="invalid-feedback" id="payu_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="payu_secret" name="payu_secret" class="form-control">
                            <span class="invalid-feedback" id="payu_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add PayU -->

    <!-- Add Apple Pay -->
    <div class="modal fade addmodal" id="add_applepay">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Apple Pay</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="ApplePaySettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="applepay_email" name="applepay_email" class="form-control">
                            <span class="invalid-feedback" id="applepay_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="applepay_key" name="applepay_key" class="form-control">
                            <span class="invalid-feedback" id="applepay_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="applepay_secret" name="applepay_secret" class="form-control">
                            <span class="invalid-feedback" id="applepay_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Apple Pay -->

    <!-- Add Payoneer -->
    <div class="modal fade addmodal" id="add_payoneer">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Payoneer</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PayoneerSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="payoneer_email" name="payoneer_email" class="form-control">
                            <span class="invalid-feedback" id="payoneer_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="payoneer_key" name="payoneer_key" class="form-control">
                            <span class="invalid-feedback" id="payoneer_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="payoneer_secret" name="payoneer_secret" class="form-control">
                            <span class="invalid-feedback" id="payoneer_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Payoneer -->

    <!-- Add Mercado Pago -->
    <div class="modal fade addmodal" id="add_mercadopago">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Mercado Pago</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="MercadoPagoSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="mercadopago_email" name="mercadopago_email" class="form-control">
                            <span class="invalid-feedback" id="mercadopago_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="mercadopago_key" name="mercadopago_key" class="form-control">
                            <span class="invalid-feedback" id="mercadopago_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="mercadopago_secret" name="mercadopago_secret" class="form-control">
                            <span class="invalid-feedback" id="mercadopago_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Mercado Pago -->

    <!-- Add Payment -->
    <div class="modal fade addmodal" id="add_payment">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Payment Settings</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PaymentSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="payment_email" name="payment_email" class="form-control">
                            <span class="invalid-feedback" id="payment_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Keys <span class="text-danger">*</span></label>
                            <input type="text" id="payment_key" name="payment_key" class="form-control">
                            <span class="invalid-feedback" id="payment_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="payment_secret" name="payment_secret" class="form-control">
                            <span class="invalid-feedback" id="payment_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Payment -->

    <!-- Add Midtrans -->
    <div class="modal fade addmodal" id="add_midtrans">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Midtrans Settings</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="MidtransSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="midtrans_email" name="midtrans_email" class="form-control">
                            <span class="invalid-feedback" id="midtrans_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                            <input type="text" id="midtrans_key" name="midtrans_key" class="form-control">
                            <span class="invalid-feedback" id="midtrans_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="midtrans_secret" name="midtrans_secret" class="form-control">
                            <span class="invalid-feedback" id="midtrans_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Midtrans -->

    <!-- Add PyTorch -->
    <div class="modal fade addmodal" id="add_pytorch">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">PyTorch Settings</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="PyTorchSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="pytorch_email" name="pytorch_email" class="form-control">
                            <span class="invalid-feedback" id="pytorch_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                            <input type="text" id="pytorch_key" name="pytorch_key" class="form-control">
                            <span class="invalid-feedback" id="pytorch_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="pytorch_secret" name="pytorch_secret" class="form-control">
                            <span class="invalid-feedback" id="pytorch_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add PyTorch -->

    <!-- Add Bank -->
    <div class="modal fade addmodal" id="add_bank">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Bank Settings</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="BankSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="bank_email" name="bank_email" class="form-control">
                            <span class="invalid-feedback" id="bank_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key <span class="text-danger">*</span></label>
                            <input type="text" id="bank_key" name="bank_key" class="form-control">
                            <span class="invalid-feedback" id="bank_key_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Secret Key <span class="text-danger">*</span></label>
                            <input type="text" id="bank_secret" name="bank_secret" class="form-control">
                            <span class="invalid-feedback" id="bank_secret_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Bank -->

    <!-- Add Cash on Delivery -->
    <div class="modal fade addmodal" id="add_cod">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">Cash on Delivery Settings</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="CODSettingForm">
                    @csrf
                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="13">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">From Email Address <span class="text-danger">*</span></label>
                            <input type="text" id="cod_email" name="cod_email" class="form-control">
                            <span class="invalid-feedback" id="cod_email_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">COD Fee <span class="text-danger">*</span></label>
                            <input type="text" id="cod_fee" name="cod_fee" class="form-control">
                            <span class="invalid-feedback" id="cod_fee_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Additional Notes</label>
                            <textarea id="cod_notes" name="cod_notes" class="form-control"></textarea>
                            <span class="invalid-feedback" id="cod_notes_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Cash on Delivery -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/payment.js') }}"></script>
@endpush