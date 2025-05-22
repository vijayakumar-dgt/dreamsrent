@extends('admin.admin')

@section('meta_title', __('admin.bookings.calendar') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h4 class="mb-1">{{ __('admin.bookings.calendar') }}</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.bookings.calendar') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                @if (hasPermission($permissions, 'calendar', 'create'))
                <div class="mb-2">
                    <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_booking"><i class="ti ti-plus me-2"></i>{{ __('admin.bookings.add_new_booking') }}</button>
                </div>
                @endif
            </div>
        </div>
        <!-- /Breadcrumb -->
        <div class="row">
            <div class="col-md-10">
                <ul class="nav nav-tabs nav-tabs-solid custom-nav-tabs bg-transparent mb-3" role="tablist" id="bookingStatusFilter">
                    <li class="nav-item" role="presentation"><a class="nav-link active">{{ __('admin.bookings.all_bookings') }}</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link">{{ __('admin.common.in_progress') }}</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link">{{ __('admin.common.confirmed') }}</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link">{{ __('admin.common.completed') }}</a></li>
                    <li class="nav-item" role="presentation"><a class="nav-link">{{ __('admin.common.rejected') }}</a></li>
                </ul>
            </div>
            <div class="col-md-2">
                <div class="text-end mb-3">
                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{ __('admin.common.filters') }}</h6>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.common.vehicle') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        @foreach ($Vehicles as $Vehicle)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 selectedVehcile" value="{{ $Vehicle->id }}" type="checkbox">{{ $Vehicle->name }}
                            </label>
                        </li>
                        @endForeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.common.customer') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        @foreach ($customerss as $customer)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 selectedCustomer" value="{{ $customer->id }}"  type="checkbox">{{ $customer->name }}
                            </label>
                        </li>
                        @endForeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.common.driver') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        @foreach ($drivers as $driver)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 selectedDriver" value="{{ $driver->id }}" type="checkbox">{{ $driver->driver_name }}
                            </label>
                        </li>
                        @endForeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.vehicle_type') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        @foreach ($cartypes as $cartype)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 selectedCartype" value="{{ $cartype->id }}"  type="checkbox">{{ $cartype->name }}
                            </label>
                        </li>
                        @endForeach
                        </div>
                    </ul>
                </div>
                <button type="button" class="me-2 btn btn-light border-0 text-purple links" id="applyFilter">{{ __('admin.common.apply') }}</button>
                <button type="button" class="text-danger btn btn-light border-0 links" id="clearFilter">{{ __('admin.common.clear_all') }}</button>
            </div>
        </div>
        <div>
            <div class="card mb-0">
                <div class="card-body">
                    <div class="adminCalendar"></div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Booking Details -->
<div class="modal fade" id="booking_details_modal">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="d-inline-flex align-items-center">{{ __('admin.bookings.booking_details') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="border-bottom mb-3">
                    <div class="border rounded p-3 bg-light mb-3">
                        <div class="row">
                            <div class="col-6">
                                <div class="d-flex align-items-center">
                                    <span class="avatar flex-shrink-0 me-2">
                                        <img id="car_img" src="" alt="">
                                    </span>
                                    <div>
                                        <h6 id="car_title" class="fs-14 mb-1"></h6>
                                        <p id="car_type"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-3">
                                <div>
                                    <h6 class="fs-14 mb-1">{{ __('admin.general_settings.price') }}</h6>
                                    <p class="fs-14 text-gray-9" id="car_price"> <span class="text-gray-5 fw-normal"></span></p>
                                </div>
                            </div>
                            <div class="col-3">
                                <div>
                                    <h6 class="fs-14 mb-1">{{ __('admin.general_settings.status') }}</h6>
                                    <span class="badge badge-soft-success d-inline-flex align-items-center badge-sm" id="book_status">
                                        <i class="ti ti-point-filled me-1"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-bottom mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.rentals.start_date') }}</h6>
                        <p id="start_date_time"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.rentals.end_date') }}</h6>
                        <p id="end_date_time"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.rental_period') }}</h6>
                        <p id="rent_period"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.dashboard.driving_type') }}</h6>
                        <p id="drive_type"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.pickup_location') }}</h6>
                        <p id="pickLan"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.return_location') }}</h6>
                        <p id="retLan"></p>
                    </div>
                </div>
                <div class="border-bottom mb-3">
                    <div id="customer_section" class="d-flex align-items-center justify-content-between d-none">
                        <div class="mb-3">
                            <h6 class="d-inline-flex align-items-center fs-14 fw-medium">{{ __('admin.common.customer') }}</h6>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                <img id="customer_img" src="" alt="">
                            </span>
                            <div>
                                <h6 class="fs-14 fw-medium mb-1" id="customer_name"></h6>
                                <p id="customer_num"></p>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between driverInfo">
                        <div class="mb-3">
                            <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">{{ __('admin.common.driver') }}</h6>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                <img id="driver_img" src="" alt="">
                            </span>
                            <div>
                                <h6 class="fs-14 fw-medium mb-1" id="driver_name"></h6>
                                <p id="driver_num"></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="border-bottom mb-3 extraInfo">
                    <div class="d-flex align-items-center justify-content-between">
                        <p>{{ __('admin.common.extra_service') }}</p>
                        <p id="extraService"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p>{{ __('admin.common.insurance') }}</p>
                        <p id="inService"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p>{{ __('admin.finance_accounts.tax') }}</p>
                        <p id="taxValue"></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <p>{{ __('admin.bookings.vehicle_price') }}</p>
                        <p id="totalValue"></p>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <h6>{{ __('admin.finance_accounts.total_price') }}</h6>
                    <h6 id="final_price"></h6>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Booking Details -->

<!-- Add Booking -->
<div class="modal fade" id="add_booking">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="d-inline-flex align-items-center">{{ __('admin.bookings.create_booking') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x"></i>
                </button>
            </div>
            <div class="wizard-form calTop">
                <fieldset id="first-field">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="basicInfoForm" autocomplete="off">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="reservation-wizard mb-4">
                                            <ul class="d-flex align-items-center flex-wrap row-gap-2" id="progressbar">
                                                <li class="d-flex align-items-center active me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-calendar"></i></span>
                                                    <h6>{{ __('admin.common.vehicle') }} & {{ __('admin.bookings.dates_info') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-user-check"></i></span>
                                                    <h6>{{ __('admin.common.customer') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-float-center"></i></span>
                                                    <h6>{{ __('admin.common.extra_services') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-file-invoice"></i></span>
                                                    <h6>{{ __('admin.bookings.billing_details') }}</h6>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card card-bg">
                                            <div class="card-body">
                                                <h4 class="d-flex align-items-center"><i class="ti ti-info-circle me-2 text-secondary fs-24"></i>{{ __('admin.bookings.basic_info') }}</h4>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-3">
                                                <h5 class="mb-1">{{ __('admin.common.date') }} & {{ __('admin.bookings.time_of_travel') }}</h5>
                                                <p>{{ __('admin.bookings.add_information_for_date_of_travel') }}</p>
                                            </div>
                                            <div class="border-bottom mb-3 pb-3">
                                                <div class="row gx-3">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.common.tariff') }}</label>
                                                            <select class="form-control select" name="tariff" id="tariff">
                                                                <option value="">{{ __('admin.common.select') }}</option>
                                                                @if ($priceTypes)
                                                                @foreach ($priceTypes as $priceType)
                                                                <option value="{{ $priceType->id }}">{{ $priceType->pricing_type }}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="text-danger error-text" id="tariff_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.bookings.driving_type') }}</label>
                                                            <select class="form-control select" name="driving_type" id="driving_type" data-placeholder="{{ __('admin.common.select') }}">
                                                                <option value="">{{ __('admin.common.select') }}</option>
                                                                @if ($drivingTypes)
                                                                @foreach ($drivingTypes as $drivingType)
                                                                <option value="{{ $drivingType->id }}">{{ $drivingType->name }}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-text text-danger" id="driving_type_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.bookings.no_of_passengers') }}</label>
                                                            <input type="text" class="form-control" name="no_of_passengers" id="no_of_passengers">
                                                            <span class="text-danger error-text" id="no_of_passengers_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row gx-3">
                                                    <div class="col-xl-6">
                                                        <div class="row gx-3">
                                                            <div class="col-md-7">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ __('admin.common.start_date') }}<span class="text-danger"> *</span> </label>
                                                                    <div class="input-icon-end position-relative">
                                                                        <input type="text" class="form-control start_date" name="start_date" id="start_date" placeholder="dd/mm/yyyy">
                                                                        <span class="input-icon-addon">
                                                                            <i class="ti ti-calendar"></i>
                                                                        </span>
                                                                    </div>
                                                                    <span class="error-text text-danger" id="start_date_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ __('admin.common.start_time') }}<span class="text-danger"> *</span> </label>
                                                                    <div class="d-flex align-items-center">
                                                                        <div class="input-icon-end position-relative flex-fill">
                                                                            <input type="text" class="form-control start_time" name="start_time" id="start_time">
                                                                            <span class="input-icon-addon">
                                                                                <i class="ti ti-clock"></i>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                    <span class="error-text text-danger" id="start_time_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="row gx-3">
                                                            <div class="col-md-7">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ __('admin.common.end_date') }}<span class="text-danger"> *</span> </label>
                                                                    <div class="input-icon-end position-relative">
                                                                        <input type="text" class="form-control end_date" name="end_date" id="end_date" placeholder="dd/mm/yyyy">
                                                                        <span class="input-icon-addon">
                                                                            <i class="ti ti-calendar"></i>
                                                                        </span>
                                                                    </div>
                                                                    <span class="error-text text-danger" id="end_date_error"></span>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-5">
                                                                <div class="mb-3">
                                                                    <label class="form-label">{{ __('admin.common.end_time') }}<span class="text-danger"> *</span> </label>
                                                                    <div class="input-icon-end position-relative">
                                                                        <input type="text" class="form-control end_time" name="end_time" id="end_time">
                                                                        <span class="input-icon-addon">
                                                                            <i class="ti ti-clock"></i>
                                                                        </span>
                                                                    </div>
                                                                    <span class="error-text text-danger" id="end_time_error"></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row gx-3">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.bookings.pickup_location') }}<span class="text-danger"> *</span></label>
                                                            <select class="form-control select2" name="pickup_location" id="pickup_location" data-placeholder="{{ __('admin.common.select') }}">
                                                                <option value="">{{ __('admin.common.select') }}</option>
                                                                @if ($locations)
                                                                @foreach ($locations as $location)
                                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-text text-danger" id="pickup_location_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.bookings.return_location') }}<span class="text-danger"> *</span></label>
                                                            <select class="form-control select2" name="return_location" id="return_location" data-placeholder="{{ __('admin.common.select') }}">
                                                                <option value="">{{ __('admin.common.select') }}</option>
                                                                @if ($locations)
                                                                @foreach ($locations as $location)
                                                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                                                                @endforeach
                                                                @endif
                                                            </select>
                                                            <span class="error-text text-danger" id="return_location_error"></span>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <label class="form-label">{{ __('admin.bookings.security_deposit') }}</label>
                                                            <input type="text" class="form-control" name="security_deposit" id="security_deposit">
                                                        </div>
                                                    </div>
                                                </div>
                                                <input class="form-check-input me-1" type="checkbox" id="return_same_location">
                                                <label for="return_same_location">{{ __('admin.bookings.return_same_location') }}</label>
                                            </div>
                                            <div id="vehicle_list_main_container" class="d-none">
                                                <div class="row align-items-center">
                                                    <div class="col-lg-4">
                                                        <div class="mb-3">
                                                            <h5 class="mb-1">{{ __('admin.bookings.select_vehicle') }}</h5>
                                                            <p>{{ __('admin.bookings.select_vehicle_for_your_rental') }}</p>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="d-flex align-items-center justify-content-between gap-3 mb-3">
                                                            <div class="dropdown me-2">
                                                                <a href="#vehiclefiltercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="filtercollapse">
                                                                    <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
                                                                </a>
                                                            </div>
                                                            <div class="top-search me-2">
                                                                <div class="top-search-group">
                                                                    <span class="input-icon">
                                                                        <i class="ti ti-search"></i>
                                                                    </span>
                                                                    <input type="text" class="form-control" name="overall_search" id="overall_search" placeholder="{{ __('admin.common.search') }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="collapse" id="vehiclefiltercollapse">
                                                    <div class="filterbox mb-3 px-3">
                                                        <div class="row align-items-center">
                                                            <form id="filterForm">
                                                                <div class="col-lg-10">
                                                                    <div class=" d-flex align-items-center flex-wrap row-gap-3">
                                                                        <div class="dropdown me-2">
                                                                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
                                                                                {{ __('admin.bookings.select_brand') }}
                                                                            </a>
                                                                            <ul class="dropdown-menu dropdown-menu-lg p-2" id="brandList">
                                                                                <li>
                                                                                    <div class="top-search m-2">
                                                                                        <div class="top-search-group">
                                                                                            <span class="input-icon">
                                                                                                <i class="ti ti-search"></i>
                                                                                            </span>
                                                                                            <input type="text" class="form-control" id="brand_search" name="brand_search" placeholder="{{ __('admin.common.search') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                <div class="custom-scroll"></div>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown me-2">
                                                                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                                                {{ __('admin.bookings.select_type') }}
                                                                            </a>
                                                                            <ul class="dropdown-menu dropdown-menu-lg p-2" id="typeList">
                                                                                <li>
                                                                                    <div class="top-search m-2">
                                                                                        <div class="top-search-group">
                                                                                            <span class="input-icon">
                                                                                                <i class="ti ti-search"></i>
                                                                                            </span>
                                                                                            <input type="text" class="form-control" name="type_search" id="type_search" placeholder="{{ __('admin.common.search') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                <div class="custom-scroll"></div>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown me-2">
                                                                            <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                                                <i class="ti ti-badge me-1"></i>
                                                                                {{ __('admin.bookings.select_model') }}
                                                                            </a>
                                                                            <ul class="dropdown-menu dropdown-menu-lg p-2" id="modelList">
                                                                                <li>
                                                                                    <div class="top-search m-2">
                                                                                        <div class="top-search-group">
                                                                                            <span class="input-icon">
                                                                                                <i class="ti ti-search"></i>
                                                                                            </span>
                                                                                            <input type="text" class="form-control" name="model_search" id="model_search" placeholder="{{ __('admin.common.search') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                <div class="custom-scroll"></div>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="dropdown">
                                                                            <button type="button" href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                                                <i class="ti ti-badge me-1"></i>
                                                                                {{ __('admin.bookings.select_color') }}
                                                                            </button>
                                                                            <ul class="dropdown-menu dropdown-menu-lg p-2" id="colorList">
                                                                                <li>
                                                                                    <div class="top-search m-2">
                                                                                        <div class="top-search-group">
                                                                                            <span class="input-icon">
                                                                                                <i class="ti ti-search"></i>
                                                                                            </span>
                                                                                            <input type="text" class="form-control" name="color_search" id="color_search" placeholder="{{ __('admin.common.search') }}">
                                                                                        </div>
                                                                                    </div>
                                                                                </li>
                                                                                <div class="custom-scroll"></div>
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-2">
                                                                    <div class="d-flex align-items-center justify-content-end">
                                                                        <a href="javascript:void(0);" class="me-3 text-purple links" id="apply_filter">{{ __('admin.common.apply') }}</a>
                                                                        <a href="javascript:void(0);" class="text-danger links" id="reset_filter">{{ __('admin.common.clear') }}</a>
                                                                    </div>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="list-loader">
                                                    <div class="card pb-3 border-0">
                                                        <div class="card-body">
                                                            @include('admin.content-loader')
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="vehicle_list_container" class="car-select d-none">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer px-0 pb-0">
                                            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3">
                                                <div class="field-btns">
                                                    <a class="btn btn-light me-2" href="{{ route('calendar.index') }}"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.common.cancel') }}</a>
                                                </div>
                                                <div class="field-btns">
                                                    <button class="btn btn-primary" id="basic_info_btn" type="button">{{ __('admin.bookings.add_customer') }}<i class="ti ti-chevron-right ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="second-field">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="customerForm">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="reservation-wizard mb-4">
                                            <ul class="d-flex align-items-center flex-wrap row-gap-2" id="progressbar">
                                                <li class="d-flex align-items-center activated me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-calendar"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.vehicle') }} & {{ __('admin.bookings.dates_info') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center active me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-user-check"></i></span>
                                                    <h6>{{ __('admin.common.customer') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-float-center"></i></span>
                                                    <h6>{{ __('admin.common.extra_services') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-file-invoice"></i></span>
                                                    <h6>{{ __('admin.bookings.billing_details') }}</h6>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card card-bg">
                                            <div class="card-body">
                                                <h4 class="d-flex align-items-center"><i class="ti ti-user-check me-2 text-secondary fs-24"></i>{{ __('admin.common.customer') }}</h4>
                                            </div>
                                        </div>
                                        <div class="border-bottom mb-3">
                                            <div class="mb-3">
                                                <h6 class="mb-1">{{ __('admin.bookings.select_customer') }}</h6>
                                                <p>{{ __('admin.bookings.add_information_for_customer') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.customer') }}<span class="text-danger"> *</span></label>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-fill ">
                                                        <select class="select2" name="customer_id" id="customer_id" data-placeholder="{{ __('admin.common.select') }}">
                                                            <option value="">{{ __('admin.common.select') }}</option>
                                                            @if ($customers)
                                                            @foreach ($customers as $customer)
                                                            <option value="{{ $customer->id }}" data-image="{{ $customer->profile_image }}" data-phone_number="{{ $customer->phone_number }}" data-="">{{ $customer->full_name }}</option>
                                                            @endforeach
                                                            @endif
                                                        </select>
                                                        <span class="error-text text-danger" id="customer_id_error"></span>
                                                    </div>
                                                    <div class="ms-4 d-none">
                                                        <a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center">
                                                            <i class="ti ti-plus me-1"></i>{{ __('admin.common.add_new') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="customer_details_list">

                                            </div>
                                        </div>
                                        <div>
                                            <div class="mb-3">
                                                <h6 class="mb-1">{{ __('admin.bookings.select_driver') }}</h6>
                                                <p>{{ __('admin.bookings.add_information_for_driver') }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.driver') }}<span class="text-danger"></span></label>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-fill ">
                                                        <select class="select2" name="driver_id" id="driver_id" data-placeholder="{{ __('admin.common.select') }}">
                                                            <option value="">{{ __('admin.common.select') }}</option>
                                                        </select>
                                                        <span class="error-text text-danger" id="driver_id_error"></span>
                                                    </div>
                                                    <div class="ms-4 d-none">
                                                        <a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center">
                                                            <i class="ti ti-plus me-1"></i>{{ __('admin.common.add_new') }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                            <div id="driver_details_list">

                                            </div>
                                        </div>
                                        <div class="card-footer px-0 pb-0">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="field-btns">
                                                    <button class="btn btn-light me-2" id="customer_prev_btn" type="button"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.common.back') }}</button>
                                                </div>
                                                <div class="field-btns">
                                                    <button class="btn btn-primary" id="customer_next_btn" type="button">{{ __('admin.bookings.add_extra_services') }}<i class="ti ti-chevron-right ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="third-field">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="extraServiceForm">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="reservation-wizard mb-4">
                                            <ul class="d-flex align-items-center flex-wrap row-gap-2" id="progressbar">
                                                <li class="d-flex align-items-center activated me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-calendar"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.vehicle') }} & {{ __('admin.bookings.dates_info') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center activated  me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-user-check"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.customer') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center active me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-float-center"></i></span>
                                                    <h6>{{ __('admin.common.extra_services') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-file-invoice"></i></span>
                                                    <h6>{{ __('admin.bookings.billing_details') }}</h6>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card card-bg">
                                            <div class="card-body">
                                                <h4 class="d-flex align-items-center"><i class="ti ti-float-center me-2 text-secondary fs-24"></i>{{ __('admin.common.extra_service') }}</h4>
                                            </div>
                                        </div>
                                        <div class="border-bottom mb-3">
                                            <div class="mb-3">
                                                <h6 class="mb-1">{{ __('admin.bookings.select_extra_services') }}</h6>
                                                <p>{{ __('admin.bookings.add_extra_services_for_your_rental') }}</p>
                                            </div>
                                        </div>
                                        <div class="row" id="extra_service_list_container">

                                        </div>
                                        <div class="card-footer px-0 pb-0">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="field-btns">
                                                    <button class="btn btn-light me-2" id="extra_service_prev_btn" type="button"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.common.back') }}</button>
                                                </div>
                                                <div class="field-btns">
                                                    <button class="btn btn-primary" id="extra_service_next_btn" type="button">{{ __('admin.bookings.proceed_to_billing') }}<i class="ti ti-chevron-right ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
                <fieldset id="fourth-field">
                    <div class="row">
                        <div class="col-lg-12">
                            <form id="billingForm">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="reservation-wizard mb-4">
                                            <ul class="d-flex align-items-center flex-wrap row-gap-2" id="progressbar">
                                                <li class="d-flex align-items-center activated me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-calendar"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.vehicle') }} & {{ __('admin.bookings.dates_info') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center activated  me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-user-check"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.customer') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center activated me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-float-center"></i></span>
                                                    <span class="active-check me-2"><i class="ti ti-check"></i></span>
                                                    <h6>{{ __('admin.common.extra_services') }}</h6>
                                                </li>
                                                <li class="d-flex align-items-center active me-2">
                                                    <span class="me-2 wizard-icon"><i class="ti ti-file-invoice"></i></span>
                                                    <h6>{{ __('admin.bookings.billing_details') }}</h6>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="card card-bg">
                                            <div class="card-body">
                                                <h4 class="d-flex align-items-center"><i class="ti ti-file-invoice me-2 text-secondary fs-24"></i>{{ __('admin.bookings.billing_details') }}</h4>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div>
                                                    <h6 class="mb-1">{{ __('admin.common.insurance') }}</h6>
                                                    <p>{{ __('admin.bookings.add_insurance_for_your_rental') }}</p>
                                                </div>
                                            </div>
                                            <div class="row" id="insurance_list_container">

                                            </div>
                                        </div>
                                        <div class="card-footer px-0 pb-0">
                                            <div class="d-flex align-items-center justify-content-end">
                                                <div class="field-btns">
                                                    <button class="btn btn-light me-2" id="billing_prev_btn" type="button"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.common.back') }}</button>
                                                </div>
                                                <div class="field-btns">
                                                    <button class="btn btn-primary" id="reservation_complete_btn" type="button">{{ __('admin.common.finish') }} & {{ __('admin.common.save') }}<i class="ti ti-chevron-right ms-1"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
</div>
<!-- /Add Booking -->

<!-- Edit Pricing -->
<div class="modal fade addmodal" id="edit_price_modal">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mb-0">{{ __('admin.bookings.edit_pricing') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="driverPriceForm">
                <div class="modal-body pb-1">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.manage.drivers') }} <span class="text-danger">*</span></label>
                                <div class="d-flex align-items-center mt-2">
                                    <a class="avatar avatar-sm avatar-rounded me-2 flex-shrink-0"><img src="" class="edit_driver_img" alt=""></a>
                                    <div>
                                        <a href="#" class="d-block fw-semibold edit_driver_name"></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.rentals.pricing') }} <span class="text-danger">*</span></label>
                                <input type="text" name="driver_price" id="driver_price" value="0" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary driver_price_btn">{{ __('admin.common.save_changes') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Edit Pricing -->
@endsection

@push('scripts')
<!-- Fullcalendar JS -->
<script src="{{ asset('backend/assets/plugins/fullcalendar/index.global.min.js') }}"></script>
<script src="{{ asset('backend/assets/plugins/fullcalendar/calendar-data.js') }}"></script>
<script src="{{ asset('backend/assets/js/admin/calender.js') }}"></script>
@endpush