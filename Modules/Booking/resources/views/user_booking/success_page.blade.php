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
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('web.home.checkout')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

<!-- Booking Success -->
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
                            <li class="active activated">
                                <span><img src="{{ asset('backend/assets/img/icons/booking-head-icon-01.svg') }}" alt="Booking Icon"></span>
                                <h6>{{__('web.home.location_and_time')}}</h6>
                            </li>
                            <li class="active activated">
                                <span><img src="{{ asset('backend/assets/img/icons/booking-head-icon-02.svg') }}" alt="Booking Icon"></span>
                                <h6>{{__('web.user.extra_services')}}</h6>
                            </li>
                            <li class="active activated">
                                <span><img src="{{ asset('backend/assets/img/icons/booking-head-icon-03.svg') }}" alt="Booking Icon"></span>
                                <h6>{{__('web.user.details')}}</h6>
                            </li>
                            <li class="active activated">
                                <span><img src="{{ asset('backend/assets/img/icons/booking-head-icon-04.svg') }}" alt="Booking Icon"></span>
                                <h6>{{__('web.home.checkout')}}</h6>
                            </li>
                            <li class="active">
                                <span><img src="{{ asset('backend/assets/img/icons/booking-head-icon-05.svg') }}" alt="Booking Icon"></span>
                                <h6>{{__('web.home.booking_confirmed')}}</h6>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="booking-card">
            <div class="success-book">
                <span class="success-icon">
                    <i class="fa-solid fa-check-double"></i>
                </span>
                <h5>{{__('web.home.thank_you_order_has_been_received')}}</h5>
                <h5 class="order-no">{{__('web.home.order_number')}} : <span>#{{ $booking->reservation_id }}</span></h5>
            </div>
            <div class="booking-header">
                <div class="booking-img-wrap">
                    <div class="book-img">
                        <img src="{{ $vehicleImageUrl }}" alt="img">
                    </div>
                    <div class="book-info">
                        <h6>{{ $vehicle->name }}</h6>
                        <p><i class="feather-map-pin"></i> {{__('web.user.location')}} : {{ $mainLocation->name }} - {{ $mainLocation->address }}</p>
                    </div>
                </div>
                <div class="book-amount">
                    <p>{{ __('web.common.total') }}</p>
                    <h6>{{ $currencySymbol }}{{ $booking->final_price }}</h6>
                </div>
            </div>
            <div class="row">
                <!-- Car Pricing -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{ __('web.home.vehicle_pricing') }}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="pricing-lists">
                                <li>
                                    <div>
                                        <p>{{__('web.home.extra_services_charges_rate')}}</p>
                                    </div>
                                    <span>{{ $currencySymbol }}{{ $booking->total_extra_service_price ?? 0 }}</span>
                                </li>
                                <li>
                                    <div>
                                        <p>{{__('web.home.insurance_charges_rate')}}</p>
                                    </div>
                                    <span>{{ $currencySymbol }}{{ $booking->total_insurance_price ?? 0 }}</span>
                                </li>

                                <li>
                                    <div>
                                        <p>{{__('web.home.vehicle_total_price')}}</p>
                                    </div>
                                    <span>{{ $currencySymbol }}{{ $booking->vehicle_total_price ?? 0 }}</span>
                                </li>
                                <li>
                                    <div>
                                        <p>{{__('web.home.tax_total')}}</p>
                                    </div>
                                    <span>{{ $currencySymbol }}{{ $booking->tax_val ?? 0 }}</span>
                                </li>
                                <li class="total">
                                    <p>{{__('web.home.subtotal')}}</p>
                                    <span>{{ $currencySymbol }}{{ $booking->final_price ?? 0 }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Car Pricing -->

                <!-- Location & Time -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{__('web.home.location_and_time')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="location-lists">
                                <li>
                                    <h6>{{__('web.home.booking_type')}}</h6>
                                    <p>{{ ucwords(str_replace('_', ' ', $booking->delivery_type ?? "N/A")) }}</p>
                                </li>
                                <li>
                                    <h6>{{ __('web.common.rental_type') }}</h6>
                                    <p>{{ ucwords(str_replace('_', ' ', $booking->rental_type ?? "N/A")) }}</p>
                                </li>
                                <li>
                                    <h6>{{ __('web.home.pickup_location') }}</h6>
                                    <p>{{ $dLocation->name }}</p>
                                    <p>{{ $startDateTime }}</p>
                                </li>
                                <li>
                                    <h6>{{ __('web.home.return_location') }}</h6>
                                    <p>{{ $rLocation->name }}</p>
                                    <p>{{ $endDateTime }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Location & Time -->

                <!-- Add-ons Pricing -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{__('web.home.extra_service_pricing')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="pricing-lists">
                                @if($vehicleExtraServicesWithPrice->isNotEmpty())
                                @foreach($vehicleExtraServicesWithPrice as $service)
                                <li>
                                    <p>{{ $service->name }}</p>
                                    <span>{{ $currencySymbol }}{{ number_format($service->price, 2) }}</span>
                                </li>
                                @endforeach
                                @else
                                <li>
                                    <p>{{ __('web.home.no_extra_service_added') }}</p>
                                </li>
                                @endif
                                <li class="total">
                                    <p>{{ __('web.home.extra_services_charges_rate') }}</p>
                                    <span>{{ $currencySymbol }}{{ $booking->total_extra_service_price ?? 0 }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Add-ons Pricing -->

                <!-- Driver Details -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{__('web.home.driver_details')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="location-lists">
                                <li>
                                    <h6>{{__('web.home.driver_type')}}</h6>
                                    <p>
                                        {{ $booking->driver_id ? __('web.home.driver') . ': ' . ($driverInfo->driver_name ?? 'N/A') : __('web.home.self_pickup') }}
                                    </p>
                                </li>
                            </ul>
                            <div class="driver-info">
                                <span>
                                </span>
                                <div class="driver-name">
                                    <h6>{{ $driverInfo->driver_name ?? 'N/A' }}</h6>
                                    <p>{{ $driverInfo->phone_number ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Driver Details -->

                <!-- Billing Information -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{__('web.home.billing_information')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="billing-lists">
                                <li>{{ $bookingInfo->first_name}} {{ $bookingInfo->last_name }}</li>
                                <li>{{ $bookingInfo->company }}</li>
                                <li>{{ $bookingInfo->address }}</li>
                                <li>{{ $bookingInfo->phone_number }}</li>
                                <li>{{ $bookingInfo->email }}</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Billing Information -->

                <!-- Payment  Details -->
                <div class="col-lg-6 col-md-6 d-flex">
                    <div class="book-card flex-fill">
                        <div class="book-head">
                            <h6>{{__('web.home.payment_details')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="location-lists">
                                <li>
                                    <h6>{{__('web.home.payment_mode')}}</h6>
                                    <p>{{ ucwords(str_replace('_', ' ', str_ireplace('COD', 'Cash on Delivery', $booking->payment_type ?? "N/A"))) }}</p>
                                </li>
                                <li>
                                    <h6>{{__('web.home.transaction_id')}}</h6>
                                    <p><span>#{{ $booking->transaction_id ?? "N/A" }}</span></p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Payment  Details -->

                <!-- Additional Information -->
                <div class="col-lg-12">
                    <div class="book-card mb-0">
                        <div class="book-head">
                            <h6>{{__('web.home.additional_information')}}</h6>
                        </div>
                        <div class="book-body">
                            <ul class="location-lists">
                                <li>
                                    <p>{{ __('web.home.company_policy') }}</p>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <!-- /Additional Information -->
            </div>
        </div>
        <div class="print-btn text-center">
            <a href="{{ route('user.bookings') }}" class="btn btn-secondary">{{ __('web.user.view_all_bookings') }}</a>
        </div>
    </div>
</div>
@endsection
