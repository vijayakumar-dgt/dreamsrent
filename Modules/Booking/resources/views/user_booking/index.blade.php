@extends($layout)
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('web.home.checkout')}}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('web.home.checkout')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

<div class="booking-new-module">
    <div class="container">
        <div class="booking-wizard-head">
            <div class="row align-items-center">
                <div class="col-xl-4 col-lg-3">
                    <div class="booking-head-title">
                        <h4>{{__('web.home.reserve_your_vehicle')}}</h4>
                        <p>{{__('web.home.complete_steps')}}</p>
                    </div>
                </div>
                <div class="col-xl-8 col-lg-9">
                    <div class="booking-wizard-lists">
                        <ul>
                            <li class="active" id="firstBar">
                                <span><img src="/assets/img/icons/booking-head-icon-01.svg" alt="Booking Icon"></span>
                                <h6>{{__('web.home.location_and_time')}}</h6>
                            </li>
                            <li id="secondBar">
                                <span><img src="/assets/img/icons/booking-head-icon-02.svg" alt="Booking Icon"></span>
                                <h6>{{__('web.user.extra_services')}}</h6>
                            </li>
                            <li id="thirdBar">
                                <span><img src="/assets/img/icons/booking-head-icon-03.svg" alt="Booking Icon"></span>
                                <h6>{{__('web.user.details')}}</h6>
                            </li>
                            <li id="fourthbar">
                                <span><img src="/assets/img/icons/booking-head-icon-04.svg" alt="Booking Icon"></span>
                                <h6>{{__('web.home.checkout')}}</h6>
                            </li>
                            <li id="fifthBar">
                                <span><img src="/assets/img/icons/booking-head-icon-05.svg" alt="Booking Icon"></span>
                                <h6>{{__('web.home.booking_confirmed')}}</h6>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="booking-detail-info">
            <div class="row">



                <div class="col-lg-8" id="first-field">
                    <div class="booking-information-main">
                        <form id="bookLocationForm">
                            <input type="hidden" name="vehicle_id" value="{{ $vehicleId }}">
                            <div class="booking-information-card">
                                <div class="booking-info-head">
                                    <span><i class="bx bxs-car-garage"></i></span>
                                    <h5>{{__('web.common.rental_type')}}</h5>
                                </div>
                                <div class="booking-info-body">
                                    <ul class="booking-radio-btns">

                                    <li class="disabled pe-none cursor-not-allowed">
                                            <label class="booking_custom_check">
                                                <input type="radio" name="rent_type" id="location_pickup"
                                                    {{ request('rent_value') == 'self_pickup' ? 'checked' : '' }} value="self_pickup">
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{__('web.home.self_pickup')}}</span>
                                                </span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="booking-information-card delivery-location">
                                <div class="booking-info-head">
                                    <span><i class="bx bxs-car-garage"></i></span>
                                    <h5>{{__('web.user.location')}}</h5>
                                </div>
                                <input type="hidden" name="delivery_type" id="delivery_type" value="{{ old('rent_value', request('rent_value')) }}">
                                <input type="hidden" name="price_type_value" id="price_type_value" value="{{ old('price_type', request('price_type')) }}">
                                <input type="hidden" name="pickup_location_id" id="pickup_location_id" value="{{ old('pickup_location_id', request('pickup_location_id')) }}">
                                <input type="hidden" name="pickup_return_location_id" id="pickup_return_location_id" value="{{ old('pickup_return_location_id', request('pickup_return_location_id')) }}">
                                <div class="booking-info-body" id="devliveryCOntainer">
                                    <div class="form-custom input-block">
                                        <label class="form-label">{{__('web.home.delivery_location')}}</label>
                                        <div class="d-flex align-items-center">
                                            <select name="delivery_location" id="delivery_location" class="form-control select2">
                                                <option value="">{{__('web.home.select_delivery_location')}}</option>
                                                @foreach($allLocation as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ isset($dlocation) && $dlocation->id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }} - {{ $location->address }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="invalid-feedback" id="delivery_location_error"></span>
                                    </div>
                                    <div class="input-block m-0">
                                        <label class="custom_check d-inline-flex location-check">
                                            <span>{{__('web.home.return_to_same_location')}}</span>
                                            <input type="checkbox" name="delivery_remeber" id="delivery_remeber"
                                                {{ request('delivery_remeber') == 'on' ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="form-custom input-block">
                                        <label class="form-label">{{__('web.home.return_location')}}</label>
                                        <div class="d-flex align-items-center">
                                            <select name="delivery_return_location" id="delivery_return_location" class="form-control select2">
                                                <option value="">{{__('web.home.select_return_location')}}</option>
                                                @foreach($allLocation as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ isset($rlocation) && $rlocation->id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }} - {{ $location->address }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="invalid-feedback text-danger" id="delivery_return_location_error"></span>
                                    </div>
                                </div>
                                <div class="booking-info-body" id="selfCOntainer">
                                    <div class="form-custom input-block">
                                        <label class="form-label">{{__('web.home.pickup_location')}}</label>
                                        <div class="d-flex align-items-center">
                                            <select name="pickup_location" id="pickup_location" class="form-control select2">
                                                <option value="">{{__('web.home.select_delivery_location')}}</option>
                                                @foreach($allLocation as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ isset($plocation) && $plocation->id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }} - {{ $location->address }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="invalid-feedback text-danger" id="pickup_location_error"></span>
                                    </div>
                                    <div class="input-block m-0">
                                        <label class="custom_check d-inline-flex location-check"><span>{{__('web.home.return_to_same_location')}}</span>
                                            <input type="checkbox" name="pickup_remeber" id="pickup_remeber"
                                                {{ request('pickup_remeber') == 'on' ? 'checked' : '' }}>
                                            <span class="checkmark"></span>
                                        </label>
                                    </div>
                                    <div class="form-custom input-block">
                                        <label class="form-label">{{__('web.home.return_location')}}</label>
                                        <div class="d-flex align-items-center">
                                            <select name="pickup_return_location" id="pickup_return_location" class="form-control select2">
                                                <option value="">{{__('web.home.select_delivery_location')}}</option>
                                                @foreach($allLocation as $location)
                                                <option value="{{ $location->id }}"
                                                    {{ isset($prlocation) && $prlocation->id == $location->id ? 'selected' : '' }}>
                                                    {{ $location->name }} - {{ $location->address }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <span class="invalid-feedback text-danger" id="pickup_return_location_error"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="booking-information-card booking-type-card">
                                <div class="booking-info-head">
                                    <span><i class="bx bxs-location-plus"></i></span>
                                    <h5>{{__('web.home.booking_type_and_time')}}</h5>
                                </div>
                                <div class="booking-info-body">
                                    <ul class="booking-radio-btns">
                                        @foreach ($filteredPrices as $type => $price)
                                        <li class="disabled pe-none cursor-not-allowed">
                                            <label class="booking_custom_check">
                                                <input type="radio" name="price_type" value="{{ $type }}"
                                                    {{ request('price_type') == $type ? 'checked' : '' }} disabled>
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{ ucfirst($type) }} ({{ $currencySymbol }}{{ number_format($price, 2) }})</span>
                                                </span>
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                    <div class="booking-timings">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{__('web.home.start_date')}}</label>
                                                    <div class="group-img">
                                                        <input type="text" class="form-control datetimepicker"
                                                            name="pickup_date" id="pickup_date"
                                                            placeholder="Choose Date"
                                                            value="{{ old('pickup_date', request('pickup_date')) }}" readonly>
                                                        <span class="input-cal-icon"><i class="bx bx-calendar"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-block time-widget">
                                                    <label class="form-label">{{__('web.home.start_time')}}</label>
                                                    <div class="group-img">
                                                        <input type="text" class="form-control userTimepicker"
                                                            name="pickup_time" id="pickup_time"
                                                            placeholder="Choose Time"
                                                            value="{{ old('pickup_time', request('pickup_time')) }}" readonly>
                                                        <span class="input-cal-icon"><i class="bx bx-time"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{ __('web.home.return_date') }}</label>
                                                    <div class="group-img">
                                                        <input type="text" class="form-control datetimepicker"
                                                            name="return_date" id="return_date"
                                                            placeholder="Choose Date"
                                                            value="{{ old('return_date', request('return_date')) }}" readonly>
                                                        <span class="input-cal-icon"><i class="bx bx-calendar"></i></span>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-block time-widget">
                                                    <label class="form-label">{{__('web.home.return_time')}}</label>
                                                    <div class="group-img">
                                                        <input type="text" class="form-control userTimepicker"
                                                            name="return_time" id="return_time"
                                                            placeholder="Choose Time"
                                                            value="{{ old('return_time', request('return_time')) }}" readonly>
                                                        <span class="input-cal-icon"><i class="bx bx-time"></i></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-info-btns d-flex justify-content-end">
                                <a href="{{ url('/vehicle-details/' . request('vehicle_slug')) }}" class="btn btn-secondary">{{__('web.home.back_to_vehicle_details')}}</a>
                                <button class="btn btn-primary continue-book-btn extraBtn scrolUp" type="button">{{__('web.home.continue_booking')}}</button>
                            </div>
                        </form>
                    </div>
                </div>



                <div class="col-lg-8 d-none" id="second-field">
                    <div class="booking-information-main">
                        <form id="bookExtraDetailsForm">
                            <div class="booking-information-card">
                                <div class="booking-info-head justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span><i class="bx bx-add-to-queue"></i></span>
                                        <h5>{{__('web.user.extra_services')}}</h5>
                                    </div>
                                    <h6>{{__('web.common.total')}}: {{ $extraServiceCount }} {{__('web.user.extra_services')}}</h6>
                                </div>
                                <div class="booking-info-body">
                                    <ul class="adons-lists" id="extra-services-list">
                                        @if($extraServices->isNotEmpty())
                                        @foreach ($extraServices as $service)
                                        <li data-service-id="{{ $service->extra_service_id }}">
                                            <div class="adons-types">
                                                <div class="d-flex align-items-center adon-name-info">
                                                    <span class="adon-icon">
                                                        <img src="{{ $service->extraService->icon }}" alt="{{ $service->extraService->name }}" class="extra-service-icon">
                                                    </span>
                                                    <div class="adon-name">
                                                        <h6>{{ $service->extraService->name }}</h6>
                                                        <a href="javascript:void(0);" class="d-inline-flex align-items-center adon-info-btn">
                                                            <i class="bx bx-info-circle me-2"></i> {{ __('web.home.more_information') }}
                                                            <i class="bx bx-chevron-down ms-2 arrow-icon"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                <span class="adon-price">{{ $currencySymbol }}{{ number_format($service->price, 2) }}</span>

                                                <!-- Add Button -->
                                                <button type="button" class="btn add-addon-btn">
                                                    <i class="bx bx-plus-circle me-2"></i>{{ __('web.home.add') }}
                                                </button>

                                                <button type="button" class="btn btn-dark remove-adon-btn d-none">
                                                    <i class="bx bx-minus-circle me-2"></i>{{ __('web.home.remove') }}
                                                </button>

                                                <input type="checkbox" name="add_extra" hidden>
                                                <input type="hidden" name="extra_id[]" value="{{ $service->extraService->id }}">
                                                <input type="hidden" name="extra_price[]" value="{{ $service->price }}">
                                                <input type="hidden" name="extra_type[]" value="{{ $service->value }}">
                                            </div>
                                            <div class="more-adon-info">
                                                <p>{{ $service->extraService->description }}</p>
                                            </div>
                                        </li>
                                        @endforeach
                                        @else
                                        <li>
                                            <p>{{__('web.home.no_extra_service_added_to_this_vehicle')}}.</p>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                            <div class="booking-information-card">
                                <div class="booking-info-head">
                                    <span><i class="bx bx-user-pin"></i></span>
                                    <h5>{{__('web.home.driver_details')}}</h5>
                                </div>
                                <div class="booking-info-body">
                                    <ul class="booking-radio-btns">
                                        <li>
                                            <label class="booking_custom_check">
                                                <input type="radio" name="driver_type" id="self_driver" checked>
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{__('web.home.self_driver')}}</span>
                                                </span>
                                            </label>
                                        </li>
                                        @if(!empty($driverInfo->driver_name))
                                        <li>
                                            <label class="booking_custom_check">
                                                <input type="radio" name="driver_type" id="acting_driver">
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{ __('web.home.acting_driver') }}</span>
                                                </span>
                                            </label>
                                        </li>
                                        @endif
                                    </ul>
                                    <div class="booking-timings self-driver-info">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="form-title-head">
                                                    <h5>{{ __('web.home.driver_details') }}</h5>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{ __('web.user.first_name') }} <span class="text-danger"> *</span></label>
                                                    <input type="text" name="driver_first_name" id="driver_first_name" value="{{ $user->userDetail ? $user->userDetail->first_name : '' }}" maxlength="30" class="form-control" placeholder="{{__('web.user.enter_first_name')}}">
                                                    <span class="invalid-feedback" id="driver_first_name_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{__('web.user.last_name')}} <span class="text-danger"> *</span></label>
                                                    <input type="text" name="driver_last_name" id="driver_last_name" value="{{ $user->userDetail ? $user->userDetail->last_name : '' }}" maxlength="30" class="form-control" placeholder="{{__('web.user.enter_last_name')}}">
                                                    <span class="invalid-feedback" id="driver_last_name_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{ __('web.home.driver_age') }} <span class="text-danger"> *</span></label>
                                                    <input type="text" name="driver_age" id="driver_age" maxlength="2" class="form-control Number" placeholder="{{__('web.home.enter_age_of_driver')}}">
                                                    <span class="invalid-feedback" id="driver_age_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{__('web.home.phone_number')}} <span class="text-danger"> *</span></label>
                                                    <input type="text" name="driver_mobile_number" id="driver_mobile_number" value="{{ $user->phone_number ? $user->phone_number : '' }}" maxlength="12" class="form-control Number" placeholder="{{__('web.home.enter_phone_number')}}">
                                                    <span class="invalid-feedback" id="driver_mobile_number_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{__('web.home.driving_licence_number')}} <span class="text-danger"> *</span></label>
                                                    <input type="text" name="driver_licence" id="driver_licence" maxlength="20" class="form-control" placeholder="{{__('web.home.enter_driving_licence_number')}}">
                                                    <span class="invalid-feedback" id="driver_licence_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-block date-widget">
                                                    <label class="form-label">{{__('web.home.upload_documents')}} <span class="text-danger"> *</span></label>
                                                    <div class="upload-div">
                                                        <input type="file" name="driver_file" id="driver_file">
                                                        <div class="upload-photo-drag">
                                                            <span><i class="fa fa-upload me-2"></i> {{__('web.home.upload_photo')}}</span>
                                                            <h6>{{__('web.home.or_drag_photos')}}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="upload-list">
                                                        <ul>
                                                            <li>{{__('web.home.file_max_valid')}}</li>
                                                        </ul>
                                                    </div>
                                                </div>
                                                <div class="imagePreview"></div>
                                                <div id="driver_file_error" class="text-danger mb-1"></div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="input-block m-0">
                                                    <label class="custom_check d-inline-flex location-check m-0"><span>{{__('web.home.driver_age_confirm')}}</span>
                                                        <input type="checkbox" name="driver_check" id="driver_check">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                                <div>
                                                    <span class="invalid-feedback" id="driver_check_error"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="booking-timings acting-driver-info">
                                        <div class="form-title-head">
                                            <h5>{{__('web.home.driver')}}</h5>
                                        </div>
                                        @if(!empty($driverInfo->driver_name))
                                        <ul class="acting-driver-list">
                                            <li>
                                                <div class="driver-profile-info">
                                                    <span class="driver-profile" id="driver_profile">
                                                        <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRJ0tCOel3GeTItNxpqvhsILtxfV8yrbD5yA&s" alt="Img">
                                                    </span>
                                                    <div class="driver-name">
                                                        <h5>{{ $driverInfo->driver_name }}</h5>
                                                        <input type="hidden" id="driver_id" name="driver_id" value="{{ $driverInfo->id }}" disabled>
                                                        <input type="hidden" id="driver_name" value="{{ $driverInfo->driver_name }}">
                                                        <ul>
                                                            <li>{{__('web.home.no_of_rides_completed')}} : {{ $driverInfo_ride }}</li>
                                                            <input type="hidden" id="driver_rider" value="{{  $driverInfo_ride }}">

                                                        </ul>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="booking-information-card pb-1">
                                <div class="booking-info-head">
                                    <span><i class="bx bx-file-blank"></i></span>
                                    <h5>{{__('web.home.insurance')}}</h5>
                                </div>
                                <div class="booking-info-body">
                                    @if($vehicleInsurance->isNotEmpty())
                                    @foreach($vehicleInsurance as $insurance)
                                    <div class="insurance-select custom-checkbox">
                                        <div>
                                            <p class="fs-14 d-inline-flex align-items-center mb-1">
                                                {{ $insurance->insurance_name }}
                                            </p>
                                            <div>
                                                <a href="#" data-bs-toggle="tooltip" data-bs-placement="top"
                                                    data-bs-original-title="{{ $insurance->first_benefit }}">
                                                    +{{ $insurance->benefits_count }} {{__('web.home.benefits')}}

                                                </a>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <span class="d-block mb-1">
                                                {{ $insurance->price_type_id == 1 ? 'Onetime Ride' : 'Recurring Payment' }}
                                            </span>
                                            <h6 class="fw-normal">{{ $currencySymbol }}{{ $insurance->price }}</h6>
                                            <input type="checkbox" name="add_insurance" hidden>
                                            <input type="hidden" name="insurance_id[]" value="{{ $insurance->insurances_id }}">
                                            <input type="hidden" name="insurance_price[]" value="{{ $insurance->price }}">
                                            <input type="hidden" name="insurance_type[]" value="{{ $insurance->value }}">
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <p>{{__('web.home.no_insurance_available')}}.</p>
                                    @endif
                                </div>
                            </div>
                            <div class="booking-info-btns d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary backLocationBtn scrolUp">{{__('web.home.back_to_location_and_time')}}</button>
                                <button type="button" class="btn btn-primary continue-book-btn userInfoBtn scrolUp">{{__('web.home.continue_booking')}}</button>
                            </div>
                        </form>
                    </div>
                </div>



                <div class="col-lg-8 d-none" id="third-field">
                    <div class="booking-information-main">
                        <form id="bookUserInfoForm">
                            <div class="booking-information-card">
                                <div class="booking-info-head justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span><i class="bx bx-add-to-queue"></i></span>
                                        <h5>{{__('web.home.billing_information')}}</h5>
                                    </div>
                                    <div class="d-flex align-items-center">

                                    </div>

                                </div>
                                <div class="booking-info-body">
                                    @php
                                    $capacity = $vehicle->passenger_capacity ?? 8;
                                    $selected = old('no_person', $vehicle->passenger_capacity ?? '');
                                    @endphp
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="input-block mb-1">
                                                <label class="form-label">{{__('web.user.first_name')}} <span class="text-danger"> *</span></label>
                                                <input type="text" name="first_name" id="first_name" class="form-control" value="{{ $user->userDetail ? $user->userDetail->first_name : '' }}" maxlength="30" placeholder="{{__('web.user.enter_first_name')}}">
                                                <span class="invalid-feedback" id="first_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.user.last_name')}} <span class="text-danger"> *</span></label>
                                                <input type="text" name="last_name" id="last_name" class="form-control" value="{{ $user->userDetail ? $user->userDetail->last_name : '' }}" maxlength="30" placeholder="{{__('web.user.enter_last_name')}}">
                                                <span class="invalid-feedback" id="last_name_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block">
                                                <label class="form-label">
                                                    {{ __('web.home.no_of_persons') }} <span class="text-danger">*</span>
                                                </label>
                                                <select name="no_person" id="no_person" class="form-control select select2">
                                                    <option value="">{{ __('web.common.select') }}</option>
                                                    @for ($i = 1; $i <= $capacity; $i++)
                                                        <option value="{{ $i }}" {{ $selected == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                        @endfor
                                                </select>
                                                <span class="invalid-feedback" id="no_person_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.company')}}</label>
                                                <input type="text" class="form-control" name="company" id="company" maxlength="50" placeholder="{{__('web.home.enter_company_name')}}">
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.street_address')}} <span class="text-danger"> *</span></label>
                                                <input type="text" class="form-control" id="address" name="address" value="{{ $user->userDetail ? $user->userDetail->address : '' }}" maxlength="100" placeholder="{{__('web.home.enter_address')}}">
                                                <span class="invalid-feedback" id="address_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.user.country')}} <span class="text-danger"> *</span></label>
                                                <select class="form-control select" name="country_id" id="country_id">
                                                    <option value="">{{__('web.home.select_country')}}</option>
                                                    @foreach ($countries as $country)
                                                    <option value="{{ $country->id }}"
                                                        @if(optional($user->userDetail)->country_id == $country->id) selected @endif>
                                                        {{ $country->name }}
                                                    </option>
                                                    @endforeach
                                                </select>
                                                <span class="invalid-feedback" id="country_id_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.state')}} <span class="text-danger">*</span></label>
                                                <input type="hidden" id="selected_state_id" value="{{ optional($user->userDetail)->state_id }}">
                                                <select class="form-control select" name="state_id" id="state_id">
                                                    <option value="">{{__('web.home.select_state')}}</option>
                                                </select>
                                                <span class="invalid-feedback" id="state_id_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.city')}} <span class="text-danger"></span></label>
                                                <input type="hidden" id="selected_city_id" value="{{ optional($user->userDetail)->city_id }}">
                                                <select class="form-control select2" name="city_id" id="city_id">
                                                    <option value="">{{__('web.home.select_city')}}</option>
                                                </select>
                                                <span class="invalid-feedback" id="city_id_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.pincode')}} <span class="text-danger"> *</span></label>
                                                <input type="text" name="pincode" id="pincode" value="{{ $user->userDetail ? $user->userDetail->postal_code : '' }}" class="form-control Number" maxlength="6" placeholder="{{__('web.home.enter_pincode')}}">
                                                <span class="invalid-feedback" id="pincode_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{ __('web.home.email_address') }}<span class="text-danger"> *</span></label>
                                                <input type="email" name="email" id="email" value="{{ $user->email ? $user->email : '' }}" class="form-control" maxlength="30" placeholder="{{__('web.home.email_placeholder')}}">
                                                <span class="invalid-feedback" id="email_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.phone_number')}} <span class="text-danger"> *</span></label>
                                                <input type="text" name="phone_number" id="phone_number" value="{{ $user->phone_number ? $user->phone_number : '' }}" class="form-control Number" maxlength="12" placeholder="{{__('web.home.phone_number_placeholder')}}">
                                                <span class="invalid-feedback" id="phone_number_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-block">
                                                <label class="form-label">{{__('web.home.additional_information')}}</label>
                                                <textarea class="form-control" name="add_info" id="add_info" maxlength="120" placeholder="{{__('web.home.enter_additional_information')}}" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="input-block m-0">
                                                <label class="custom_check d-inline-flex location-check m-0 ml-2"><span>{{__('web.home.readed_terms')}}</span> <span class="text-danger"> *</span>
                                                    <input type="checkbox" name="trems" id="trems">
                                                    <span class="checkmark"></span>
                                                </label>
                                            </div>
                                            <span class="invalid-feedback" id="trems_error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="booking-info-btns d-flex justify-content-end">
                                <button type="button" class="btn btn-secondary backToExtra scrolUp">{{__('web.home.back_to_extra_services')}}</button>
                                <button type="button" id="nextBtn" class="btn btn-primary goCheckOut continue-book-btn scrolUp">{{__('web.home.confirm_and_pay_now')}}</button>
                            </div>
                        </form>
                    </div>
                </div>





                <div class="col-lg-8 d-none" id="fourth-field">
                    <div class="booking-information-main">
                        <form id="bookPaymentForm">
                            <div class="booking-information-card payment-info-card">
                                <div class="booking-info-head">
                                    <div class="d-flex align-items-center">
                                        <span><i class="bx bx-money"></i></span>
                                        <h5>{{__('web.user.payment')}}</h5>
                                    </div>
                                </div>
                                <div class="booking-info-body">
                                    <div class="payment-method-types">
                                        <h5>{{__('web.user.choose_your_payment_method')}}</h5>
                                        <ul>
                                            <li>
                                                <label class="payment_custom_check">
                                                    <input type="radio" name="payment_type" id="paypal" value="paypal">
                                                    <span class="payment_checkmark">
                                                        <span class="checked-title"><img src="/assets/img/icons/payment-method-01.svg" alt="Img"></span>
                                                    </span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="payment_custom_check">
                                                    <input type="radio" name="payment_type" id="stripe" value="stripe">
                                                    <span class="payment_checkmark">
                                                        <span class="checked-title"><img src="/assets/img/icons/payment-method-02.svg" alt="Img"></span>
                                                    </span>
                                                </label>
                                            </li>
                                            <li>
                                                <label class="payment_custom_check">
                                                    <input type="radio" name="payment_type" id="cod" value="cod">
                                                    <span class="payment_checkmark">
                                                        <span class="checked-title"><img src="/assets/img/icons/cash-delivery-icon.svg" alt="Img"></span>
                                                    </span>
                                                </label>
                                            </li>
                                            @auth
                                            <li>
                                                <label class="payment_custom_check">
                                                    <input type="radio" name="payment_type" id="wallet" value="wallet">
                                                    <span class="payment_checkmark">
                                                        <span class="checked-title">
                                                            <img src="/assets/img/icons/payment-method-04.svg" alt="Img">
                                                        </span>
                                                    </span>
                                                </label>
                                            </li>
                                            @endauth
                                        </ul>
                                    </div>

                                </div>
                            </div>

                            <div class="booking-info-btns d-flex justify-content-end">
                                <a class="btn btn-secondary backUserInfo scrolUp">{{__('web.home.back_to_billing_info')}}</a>
                                <button class="btn btn-primary continue-book-btn" id="sumbit_btn" type="submit">Pay $4700 & Place Reservation</button>
                            </div>
                        </form>
                    </div>
                </div>


                <div class="col-lg-4 theiaStickySidebar">
                    <div class="booking-sidebar">
                        <div class="booking-sidebar-card">
                            <div class="accordion-item border-0 mb-4">
                                <div class="accordion-header">
                                    <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#accordion_collapse_one" aria-expanded="true">
                                        <div class="booking-sidebar-head">
                                            <h5>{{__('web.home.booking_details')}}<i class="fas fa-chevron-down"></i></h5>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion_collapse_one" class="accordion-collapse collapse">
                                    <div class="booking-sidebar-body">
                                        <div class="booking-car-detail">
                                            <span class="car-img">
                                                <img src="{{ $vehicleImageUrl }}" class="img-fluid" alt="Car">
                                            </span>
                                            <div class="care-more-info">
                                                <h5>{{ $vehicle->name }}</h5>
                                                <p>{{ old('delivery_return_location', request('delivery_return_location')) }}</p>
                                                <a href="">{{__('web.home.view_vehicle_details')}}</a>
                                            </div>
                                        </div>
                                        <div class="booking-vehicle-rates">
                                            <ul>
                                                <li class="total-rate">
                                                    <h6>{{__('web.home.subtotal')}}</h6>
                                                    <h5>{{ $currencySymbol }}{{ number_format($finalRate, 2) }}</h5>
                                                </li>

                                                @foreach($calculatedTaxes as $tax)
                                                <li>
                                                    <h6>{{ $tax['group_name'] }} ({{ $tax['rate_name'] }} - {{ $tax['rate_percent'] }}%)</h6>
                                                    <h5>{{ $currencySymbol }}{{ number_format($tax['amount'], 2) }}</h5>
                                                </li>
                                                @endforeach

                                                <li class="total-rate">
                                                    <h6>{{__('web.home.total_tax')}}</h6>
                                                    <input type="hidden" name="tax_val" id="tax_val" value="{{ number_format($totalTax, 2) }}">
                                                    <h5>{{ $currencySymbol }}{{ number_format($totalTax, 2) }}</h5>
                                                </li>

                                                <li class="total-rate">
                                                    <h6>{{__('web.home.estimated_total')}}</h6>
                                                    <h5>{{ $currencySymbol }}{{ number_format($grandTotal, 2) }}</h5>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-sidebar-card d-none" id="location-card">
                            <div class="accordion-item border-0 mb-4">
                                <div class="accordion-header p-3 d-flex align-center justify-content-between">
                                    <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#accordion_collapse_three" aria-expanded="true">
                                        <div class="booking-sidebar-head p-0 d-flex justify-content-between align-items-center">
                                            <h5>{{__('web.home.location_and_time')}}<i class="fas fa-chevron-down"></i></h5>
                                        </div>
                                    </div>
                                    <a class="d-flex align-items-center sidebar-edit locationBack"><i class="bx bx-edit-alt me-2"></i>{{__('web.home.edit')}}</a>
                                </div>
                                <div id="accordion_collapse_three" class="accordion-collapse collapse">
                                    <div class="booking-sidebar-body">
                                        @php
                                        use Illuminate\Support\Str;
                                        use Carbon\Carbon;
                                        @endphp

                                        <ul class="location-address-info">
                                            <li>
                                                <h6>{{__('web.common.rental_type')}}</h6>
                                                <p id="rentalTypeText"></p>
                                            </li>

                                            <li>
                                                <h6>{{__('web.home.delivery_location_and_time')}}</h6>

                                                <p>
                                                    {{ Carbon::parse(request('pickup_date'))->format('d/m/Y') }} -
                                                    {{ Carbon::parse(request('pickup_time'))->format('H:i') }}
                                                </p>
                                            </li>
                                            <li>
                                                <h6>{{__('web.home.return_location_and_time')}}</h6>
                                                <p>
                                                    {{ Carbon::parse(request('return_date'))->format('d/m/Y') }} -
                                                    {{ Carbon::parse(request('return_time'))->format('H:i') }}
                                                </p>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-sidebar-card d-none" id="extra-card">
                            <div class="accordion-item border-0 mb-4">
                                <div class="accordion-header d-flex align-center justify-content-between p-3">
                                    <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#accordion_collapse_four" aria-expanded="true">
                                        <div class="booking-sidebar-head p-0 d-flex justify-content-between align-items-center">
                                            <h5>{{__('web.home.extra_services_and_insurance')}}<i class="fas fa-chevron-down"></i></h5>
                                        </div>
                                        <a class="d-flex align-items-center sidebar-edit backExtra"><i class="bx bx-edit-alt me-2"></i>{{__('web.home.edit')}}</a>
                                    </div>
                                </div>
                                <div id="accordion_collapse_four" class="accordion-collapse collapse">
                                    <div class="booking-sidebar-body">
                                        <div class="booking-vehicle-rates">
                                            <p class="fw-bold mb-1">{{__('web.user.extra_services')}} :</p>
                                            <ul class="mt-0 extra-charges-list">
                                            </ul>
                                            <ul class="mt-0">
                                                <li class="total-rate">
                                                    <h6>{{__('web.home.extra_services_charges_rate')}}</h6>
                                                    <h5 class="extra-services-total">$0.00</h5>
                                                </li>
                                            </ul>

                                            <br>

                                            <p class="fw-bold mb-1">{{__('web.home.insurance')}} :</p>
                                            <ul class="mt-0 insurance-charges-list">
                                            </ul>
                                            <ul class="mt-0">
                                                <li class="insurance-rate">
                                                    <h6 class="fw-bold">{{__('web.home.insurance_charges_rate')}}</h6>
                                                    <h5 class="insurance-total fw-bold">$0.00</h5>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="book-our-drivers mt-2">
                                            <p class="fw-bold mb-1">{{__('web.home.driver')}} :</p>
                                            <ul class="acting-driver-list">
                                                <li class="d-block">
                                                    <div class="driver-profile-info">
                                                        @if(!empty($driverInfo->driver_name))
                                                        <div class="acting-driver-info">
                                                            <span class="driver-profile">
                                                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRRJ0tCOel3GeTItNxpqvhsILtxfV8yrbD5yA&s" alt="Img">
                                                            </span>
                                                            <div class="driver-name">
                                                                <h5 id="driver_name_display">{{ $driverInfo->driver_name }}</h5>
                                                                <ul>
                                                                    <li>{{__('web.home.no_of_rides_completed')}}: <span id="driver_rides_display">{{ $driverInfo_ride }}</span></li>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <div class="self-driver-info">
                                                            <div class="driver-name">
                                                                <h5>{{__('web.home.self_drive')}}</h5>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-sidebar-card d-none">
                            <div class="accordion-item border-0 mb-4">
                                <div class="accordion-header">
                                    <div class="accordion-button collapsed" role="button" data-bs-toggle="collapse" data-bs-target="#accordion_collapse_two" aria-expanded="true">
                                        <div class="booking-sidebar-head d-flex justify-content-between align-items-center">
                                            <h5>{{__('web.home.coupon')}}<i class="fas fa-chevron-down"></i></h5>
                                            <a href="#" class="coupon-view">{{__('web.home.view_coupons')}}</a>
                                        </div>
                                    </div>
                                </div>
                                <div id="accordion_collapse_two" class="accordion-collapse collapse">
                                    <div class="booking-sidebar-body">
                                        <form action="">
                                            <div class="d-flex align-items-center">
                                                <div class="form-custom flex-fill">
                                                    <input type="text" class="form-control mb-0" placeholder="{{__('web.home.coupon_code')}}">
                                                </div>
                                                <button type="button" class="btn btn-secondary apply-coupon-btn d-flex align-items-center ms-2">{{__('web.common.apply')}}<i class="feather-arrow-right ms-2"></i></button>
                                            </div>
                                        </form>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="total-rate-card">
                            <div class="vehicle-total-price">
                                <h5>{{__('web.home.estimated_total')}}</h5>
                                <input type="text" name="extra_price_total" id="extra_price_total" value="" hidden>
                                <input type="text" name="insurance_price_total" id="insurance_price_total" value="" hidden>
                                <input type="text" name="driver_price_total" id="driver_price_total" value="" hidden>
                                <input type="text" name="vehicle_price" id="vehicle_price" value="{{ old('price_rate', request('price_rate')) }}" hidden>
                                <input type="text" name="vehicle_price_total" id="vehicle_price_total" value="{{ number_format($grandTotal, 2) }}" hidden>
                                <input type="text" name="total_price" id="total_price" value="{{ number_format($grandTotal, 2) }}" hidden>
                                <input type="text" name="currency" id="currency" value="{{ $currencySymbol }}" hidden>
                                <span>{{ $currencySymbol }}{{ number_format($grandTotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/booking/user-booking.js') }}"></script>
@endpush
