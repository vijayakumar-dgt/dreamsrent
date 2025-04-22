@extends($layout)
@push('styles')
<!-- Datatable CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/datatables/datatables.min.css') }}">
@endpush
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_wallet') }} </h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{ __('web.home.home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_wallet') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->
@include('frontend.user.nav_menu')
<!-- Page Content -->
<div class="content">
    <div class="container">

        <!-- Content Header -->
        <div class="content-header">
            <h4>{{ __('web.user.wallet') }}</h4>
        </div>
        <!-- /Content Header -->

        <!-- Wallet Info -->
        <div class="row">
            <div class="col-lg-12 col-md-12 d-flex">
                <div class="card wallet-card flex-fill">
                    <div class="card-body">
                        <div class="balance-info">
                            <div class="balance-grid">
                                <div class="balance-content">
                                    <h6 >{{ __('web.user.available_balance') }}</h6>
                                    <h4 class="available_balance">$4544</h4>
                                </div>
                                <div class="refersh-icon d-none">
                                    <a href="javascript:void(0);">
                                        <i class="fas fa-arrows-rotate"></i>
                                    </a>
                                </div>
                            </div>
                            <div class="balance-list">
                                <div class="row">
                                    <div class="col-lg-4 col-md-6 d-flex">
                                        <div class="balance-inner credit-info">
                                            <h6 class="total_credit">$1,23,464</h6>
                                            <p>{{ __('web.user.total_credit') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 d-flex">
                                        <div class="balance-inner debit-info">
                                            <h6 class="total_debit">$7,357</h6>
                                            <p>{{ __('web.user.total_debit') }}</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 col-md-6 d-flex">
                                        <div class="balance-inner transaction-info">
                                            <h6 class="available_balance">$6,56,415</h6>
                                            <p>{{ __('web.user.total_transaction') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group wallet-form-group">
                            <label>{{ __('web.user.add_wallet_credits') }} ($)</label>
                            <input type="number" class="form-control" id="wallet_amount" name="wallet_amount" placeholder="{{ __('web.user.enter_amount') }}" min="0" max="99999" oninput="if(this.value.length > 5) this.value = this.value.slice(0, 5);">
                        </div>
                        <div class="wallet-btn">
                            <a href="javascript:void(0);" class="btn" data-bs-toggle="modal" data-bs-target="#add_payment">{{ __('web.user.add_payment') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7 col-md-12 d-none">
                <div class="card your-card flex-fill">
                    <div class="card-body">
                        <div class="wallet-header">
                            <h4>Your Cards</h4>
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_card">
                                <i class="feather-plus-circle"></i>
                            </a>
                        </div>
                        <div class="wallet-table">
                            <div class="table-responsive">
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>
                                                <label class="custom_radio">
                                                    <input type="radio" name="payment" value="wallet" checked="">
                                                    <span class="checkmark"></span>
                                                    <img src="/frontend/assets/img/icons/wallet-01.svg" alt="Icon">
                                                </label>
                                            </td>
                                            <td>
                                                <h6>3210 **** **** **12</h6>
                                                <p>Card Number</p>
                                            </td>
                                            <td>
                                                <h6>$3000</h6>
                                                <p>Balance</p>
                                            </td>
                                            <td>
                                                <h6>Active</h6>
                                                <p>Status</p>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="feather-edit-3"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                                            <i class="feather-trash-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="custom_radio">
                                                    <input type="radio" name="payment" value="wallet">
                                                    <span class="checkmark"></span>
                                                    <img src="/frontend/assets/img/icons/wallet-02.svg" alt="Icon">
                                                </label>
                                            </td>
                                            <td>
                                                <h6>7847 **** **** **78</h6>
                                                <p>Card Number</p>
                                            </td>
                                            <td>
                                                <h6>$2300</h6>
                                                <p>Balance</p>
                                            </td>
                                            <td>
                                                <h6>Active</h6>
                                                <p>Status</p>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="feather-edit-3"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                                            <i class="feather-trash-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label class="custom_radio">
                                                    <input type="radio" name="payment" value="wallet">
                                                    <span class="checkmark"></span>
                                                    <img src="/frontend/assets/img/icons/wallet-03.svg" alt="Icon">
                                                </label>
                                            </td>
                                            <td>
                                                <h6>4710 **** **** **64</h6>
                                                <p>Card Number</p>
                                            </td>
                                            <td>
                                                <h6>$1800</h6>
                                                <p>Balance</p>
                                            </td>
                                            <td>
                                                <h6>Active</h6>
                                                <p>Status</p>
                                            </td>
                                            <td class="text-end">
                                                <div class="dropdown dropdown-action">
                                                    <a href="javascript:void(0);" class="dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="fas fa-ellipsis-vertical"></i>
                                                    </a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <a class="dropdown-item" href="javascript:void(0);">
                                                            <i class="feather-edit-3"></i> Edit
                                                        </a>
                                                        <a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#delete_modal">
                                                            <i class="feather-trash-2"></i> Delete
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Wallet Info -->

        <!-- Wallet Table -->
        <div class="row">
            <div class="col-lg-12 d-flex">
                <div class="card book-card flex-fill mb-0">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-md-5">
                                <h4>{{ __('web.user.wallet_usage_history') }}</h4>
                            </div>
                            <div class="col-md-7 text-md-end">
                                <div class="table-search">
                                    <div id="tablefilter" class="me-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive dashboard-table">
                            <table id="walletTable" class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('web.user.ref_id') }}</th>
                                        <th>{{ __('web.user.transaction_for') }}</th>
                                        <th>{{ __('web.common.date') }}</th>
                                        <th>{{ __('web.common.total') }}</th>
                                        <th>{{ __('web.common.status') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Skeleton loader rows -->
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <!-- Add more skeleton rows as necessary -->
                                </tbody>
                            </table>
                        </div>
                        <div class="table-footer">
                            <div class="row">
                                <div class="col-md-6">
                                    <div id="tablelength"></div>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <div id="tablepage"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Wallet Table -->

        <!-- Real Wallet Table (Initially Hidden) -->
        <div class="table-responsive dashboard-table dashboard-table-info d-none real-table">
            <table class="table" id="realWalletTable">

                <tbody>
                    <!-- Real data rows will go here -->
                </tbody>
            </table>
        </div>


    </div>
</div>
<!-- /Page Content -->
<!-- Add Card Modal -->
<div class="modal new-modal fade" id="add_card" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add New Card</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="modal-form-group">
                        <label>Card Number <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Card Number">
                    </div>
                    <div class="modal-form-group">
                        <label>Name on Card <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" placeholder="Enter Card Name">
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="modal-form-group">
                                <label>CVV <span class="text-danger">*</span></label>
                                <div class="form-icon">
                                    <input type="text" class="form-control" placeholder="Enter CVV Number">
                                    <span class="cus-icon">
                                        <img src="/frontend/assets/img/icons/lock-icon.svg" alt="Icon">
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="modal-form-group">
                                <label>Expiry Date <span class="text-danger">*</span></label>
                                <div class="form-icon">
                                    <input type="text" class="form-control" placeholder="DD/MM/YYYY">
                                    <span class="cus-icon">
                                        <img src="/frontend/assets/img/icons/calendar-icon.svg" alt="Icon">
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label class="custom_check">
                            <input type="checkbox" name="rememberme" class="rememberme">
                            <span class="checkmark"></span>
                            Save this account for future transaction
                        </label>
                    </div>
                    <div class="modal-btn">
                        <button type="submit" class="btn btn-secondary w-100">Pay $4700</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Card Modal -->

<!-- Add Payment Modal -->
<div class="modal new-modal multi-step fade" id="add_payment" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('web.user.add_payment') }}</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <div class="total-payment">
                    <p>{{ __('web.user.available_balance') }}</p>
                    <h6 class="available_balance"></h6>
                </div>
                <form id="add_wallet">
                    <div class="choose-payment-info">
                        <h5>{{ __('web.user.choose_your_payment_method') }}</h5>
                        <input type="hidden" name="wallet_amount" id="wallet_amount">
                        <div class="d-flex justify-content-center">
                            <div class="choose-payment d-flex gap-3">
                                <label class="custom_radio text-center">
                                    <input type="radio" name="payment_one" id="paypal" value="wallet_one" checked>
                                    <span class="checkmark d-block mx-auto"></span>
                                    <img src="/frontend/assets/img/icons/payment-1.svg" alt="Icon" class="img-fluid mt-2">
                                </label>
                                <label class="custom_radio text-center">
                                    <input type="radio" name="payment_one" id="stripe" value="wallet_one">
                                    <span class="checkmark d-block mx-auto"></span>
                                    <img src="/frontend/assets/img/icons/payment-2.svg" alt="Icon" class="img-fluid mt-2">
                                </label>
                            </div>
                        </div>

                        <div class="add-payment-table-info d-none">
                            <div class="wallet-table add-payment-table">
                                <div class="table-responsive">
                                    <table class="table">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <label class="custom_radio">
                                                        <input type="radio" name="payment_two" value="wallet_two" >
                                                        <span class="checkmark"></span>
                                                        <img src="/frontend/assets/img/icons/wallet-01.svg" alt="Icon">
                                                    </label>
                                                </td>
                                                <td>
                                                    <h6>3210 **** **** **12</h6>
                                                    <p>Card Number</p>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-btn">
                        <button type="submit" class="btn btn-secondary w-100" data-bs-target="#order_success" data-bs-toggle="modal" data-bs-dismiss="modal">Add to Wallet</button>
                        <a class="btn btn-outline-cancel" data-bs-dismiss="modal">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Add Payment Modal -->

<!-- Delete Modal -->
<div class="modal new-modal fade" id="delete_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="delete-action">
                    <div class="delete-header">
                        <h4>Delete Wallet History</h4>
                        <p>Are you sure want to delete?</p>
                    </div>
                    <div class="modal-btn">
                        <div class="row">
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-secondary w-100">
                                    Delete
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                    Cancel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

<!-- scrollToTop start -->
<div class="progress-wrap active-progress">
    <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
    </svg>
</div>
<!-- scrollToTop end -->

@endsection
@push('scripts')
<!-- Datatable JS -->
<script src="{{ asset('frontend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('frontend/assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/user/wallet.js') }}"></script>
@endpush
