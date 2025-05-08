@extends($layout)
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_dashboard') }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('web.home.home') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_dashboard') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

@include('frontend.user.nav_menu')

<!-- Page Content -->
<div class="content dashboard-content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header">
            <h4>{{ __('web.user.dashboard') }}</h4>
        </div>
        <!-- /Content Header -->

        <!-- Dashboard -->
        <div class="row">
            <!-- Widget Item -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card widget-box flex-fill">
                    <div class="widget-header">
                        <div class="widget-content">
                            <p class="mb-1">{{ __('web.user.my_bookings') }}</p>
                            <h3>{{ $totalBookingCount }}</h3>
                        </div>
                        <div class="widget-icon">
                            <span>
                                <img src="/frontend/assets/img/icons/book-icon.svg" alt="icon">
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('user.bookings') }}" class="view-link">{{ __('web.user.view_all_bookings') }} <i class="feather-arrow-right"></i></a>
                </div>
            </div>
            <!-- /Widget Item -->

            <!-- Widget Item -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card widget-box flex-fill">
                    <div class="widget-header">
                        <div class="widget-content">
                            <p class="mb-1">{{ __('web.user.wallet_balance') }}</p>
                            <h3>{{ $currency }}{{ $totalBalance }}</h3>
                        </div>
                        <div class="widget-icon">
                            <span class="bg-warning">
                                <img src="/frontend/assets/img/icons/balance-icon.svg" alt="icon">
                            </span>
                        </div>
                    </div>
                    <a href="/user/wallet" class="view-link">{{ __('web.user.view_balance') }} <i class="feather-arrow-right"></i></a>
                </div>
            </div>
            <!-- /Widget Item -->

            <!-- Widget Item -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card widget-box flex-fill">
                    <div class="widget-header">
                        <div class="widget-content">
                            <p class="mb-1">{{ __('web.user.total_transactions') }}</p>
                            <h3>{{ $currency }}{{ $totalTransaction }}</h3>
                        </div>
                        <div class="widget-icon">
                            <span class="bg-success">
                                <img src="/frontend/assets/img/icons/transaction-icon.svg" alt="icon">
                            </span>
                        </div>
                    </div>
                    <a href="/user/payments" class="view-link">{{ __('web.user.view_all_transactions') }} <i class="feather-arrow-right"></i></a>
                </div>
            </div>
            <!-- /Widget Item -->

            <!-- Widget Item -->
            <div class="col-lg-3 col-md-6 d-flex">
                <div class="card widget-box flex-fill">
                    <div class="widget-header">
                        <div class="widget-content">
                            <p class="mb-1">{{ __('web.user.wishlist') }} {{ __('web.common.vehicles') }}</p>
                            <h3>{{ $totalWishlistCount }}</h3>
                        </div>
                        <div class="widget-icon">
                            <span class="bg-danger">
                                <img src="/frontend/assets/img/icons/cars-icon.svg" alt="icon">
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('user.wishlists') }}" class="view-link">{{ __('web.user.go_to') }} {{ __('web.user.wishlist') }} <i class="feather-arrow-right"></i></a>
                </div>
            </div>
            <!-- /Widget Item -->
        </div>

        <div class="row">
            <!-- Last 5 Bookings -->
            <div class="col-lg-8 d-flex">
                <div class="card user-card flex-fill">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col-sm-5">
                                <h5>{{ __('web.user.last_bookings_5') }}</h5>
                            </div>
                            <div class="col-sm-7 text-sm-end">
                                <div class="booking-select">
                                    <select class="form-control select" id="duration">
                                        <option value="last30">{{ __('web.user.last_30_days') }}</option>
                                        <option value="last7">{{ __('web.user.last_7_days') }}</option>
                                    </select>
                                    <a href="{{ route('user.bookings') }}" class="view-link">{{ __('web.user.view_all_bookings') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="custom-datatable-filter table-responsive table-loader d-none">
                            <table class="table table-bordered" id="loader-table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                        <div class="table-responsive dashboard-table dashboard-table-info d-none real-table">
                            <table class="table" id="bookingTable">
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Last 5 Bookings -->

            <!-- Recent Transaction -->
            <div class="col-lg-4 d-flex">
                <div class="card user-card flex-fill">
                    <div class="card-header">
                        <div class="row align-items-center justify-content-between">
                            <div class="col-auto">
                                <h5>{{ __('web.user.recent_transactions') }}</h5>
                            </div>
                            <div class="col-auto text-sm-end">
                                <div class="booking-select">
                                    <select class="form-control select" id="sort" name="sort">
                                        <option value="last_30_days">{{ __('web.user.last_30_days') }}</option>
                                        <option value="last_7_days">{{ __('web.user.last_7_days') }}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive dashboard-table dashboard-table-info">
                            <table class="table trans-table-loader">
                                <tbody id="transaction-skeleton-loader-body"></tbody>
                            </table>
                            <table class="table d-none trans-real-table" id="transactionTable">
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Recent Transaction -->
        </div>
        <!-- /Dashboard -->
    </div>
</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/user/dashboard.js') }}"></script>
@endpush
