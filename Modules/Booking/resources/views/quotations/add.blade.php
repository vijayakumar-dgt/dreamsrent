@extends('admin.admin')

@section('meta_title', __('admin.bookings.add_quotation') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <div class="mb-3">
            <a href="{{ route('quotations.index') }}" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-narrow-left me-2"></i>{{ __('admin.common.quotations') }}</a>
        </div>
        <div class="wizard-form">
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
                                                            <option value="">Select</option>
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
                                                <div class="col-lg-8">
                                                    <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                                                        <div class="dropdown me-2">
                                                            <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="filtercollapse">
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
                                                        <div class="d-none">
                                                            <a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center">
                                                                <i class="ti ti-plus me-1"></i> {{ __('admin.common.add_new') }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="collapse" id="filtercollapse">
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
                                                                            <div class="custom-scroll">
                                                                            </div>
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
                                                                            <div class="custom-scroll">
                                                                            </div>
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
                                                                            <div class="custom-scroll">
                                                                            </div>
                                                                        </ul>
                                                                    </div>
                                                                    <div class="dropdown">
                                                                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                                                            <i class="ti ti-badge me-1"></i>
                                                                            {{ __('admin.bookings.select_color') }}
                                                                        </a>
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
                                                                            <div class="custom-scroll">
                                                                            </div>
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
                                                <a class="btn btn-light me-2" href="{{ route('quotations.index') }}"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.common.cancel') }}</a>
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
                                            <li class="d-flex align-items-center activated me-2">
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
                                    <div class="row border-bottom mb-3">
                                        <div class="col-lg-3 col-sm-12">
                                            <div class="mb-3">
                                                <h6 class="mb-1">{{ __('admin.bookings.select_extra_services') }}</h6>
                                                <p>{{ __('admin.bookings.add_extra_services_for_your_rental') }}</p>
                                            </div>
                                        </div>
                                        <div class="col-lg-9 col-sm-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="base_km" class="form-label">{{ __('admin.bookings.base_kilometers') }}</label>
                                                        <input type="text" name="base_km" id="base_km" class="form-control Number" maxlength="4">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="km_extra_price" class="form-label">{{ __('admin.bookings.kilometers_extra_price') }}</label>
                                                        <input type="text" name="km_extra_price" id="km_extra_price" class="form-control Number" maxlength="4">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="expenses" class="form-label">{{ __('admin.bookings.expenses') }}</label>
                                                        <input type="text" name="expenses" id="expenses" class="form-control Number" maxlength="4">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="delivery_price" class="form-label">{{ __('admin.bookings.delivery_price') }}</label>
                                                        <input type="text" name="delivery_price" id="delivery_price" class="form-control Number" maxlength="4">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="border-bottom mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div>
                                                <h6 class="mb-1">{{ __('admin.common.insurance') }}</h6>
                                                <p>{{ __('admin.bookings.add_insurance_for_your_rental') }}</p>
                                            </div>
                                        </div>
                                        <div class="row" id="insurance_list_container"></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-lg-9 col-sm-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.bookings.tax_type') }} <span class="text-danger">*</span></label>
                                                        <select class="select" name="tax_type" id="tax_type">
                                                            <option value="">{{ __('admin.bookings.select') }}</option>
                                                            <option value="percentage">{{ __('admin.bookings.percentage') }}</option>
                                                            <option value="fixed">{{ __('admin.bookings.fixed') }}</option>
                                                        </select>
                                                        <span class="error-text text-danger" id="tax_type_error"></span>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label">{{ __('admin.bookings.add_tax_value') }} <span class="text-danger">*</span></label>
                                                        <input type="text" name="tax_val" id="tax_val" class="form-control">
                                                        <span class="error-text text-danger" id="tax_val_error"></span>
                                                    </div>
                                                </div>
                                            </div>
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
<!-- /Page Wrapper -->

<!-- Completed -->
<x-admin.modal className="deletemodal" id="reservation_completed" isHeader="{{ false }}" dialogClassName="modal-sm" modalBodyClass="text-center">
    <x-slot name="body">
        <span class="avatar avatar-lg bg-transparent-success rounded-circle text-success mb-3">
            <i class="ti ti-check fs-26"></i>
        </span>
        <h4 class="mb-1">{{ __('admin.common.created_successful') }}</h4>
        <p class="mb-3">{{ __('admin.bookings.reservation_created_for_the')}} <span class="text-gray-9" id="final_vehicle_name"> </span> {{ __('admin.common.on')}} <span class="text-gray-9" id="final_reservation_date"></span></p>
        <div class="d-flex justify-content-center">
            <a href="#" class="btn btn-primary w-100" id="reservation_view_details">{{ __('admin.common.view_details') }}</a>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Completed -->

<!-- Edit Pricing -->
<x-admin.modal
    className="addmodal"
    id="edit_price_modal"
    dialogClassName="modal-md"
    :title="__('admin.bookings.edit_pricing')"
    formId="driverPriceForm">
    <x-slot name="body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.manage.drivers') }}<span class="text-danger"> *</span></label>
                    <div class="d-flex align-items-center mt-2">
                        <div class="avatar avatar-sm avatar-rounded me-2 flex-shrink-0">
                            <img src="{{ uploadedAsset('', 'profile') }}" class="edit_driver_img" alt="Profile Image">
                        </div>
                        <div>
                            <div class="d-block fw-semibold edit_driver_name text-black"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.common.pricing') }}<span class="text-danger"> *</span></label>
                    <input type="text" name="driver_price" id="driver_price" value="0" class="form-control">
                </div>
            </div>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
            <button type="submit" class="btn btn-primary driver_price_btn">{{ __('admin.common.save_changes') }}</button>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Edit Pricing -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/quotations/add-quotations.js') }}"></script>
@endpush