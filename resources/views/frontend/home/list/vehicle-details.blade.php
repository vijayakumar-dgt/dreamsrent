    @extends($layout)
    @push('styles')
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fancybox/fancybox.css') }}">
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">
    @endpush
    @section('content')

    <div class="container skeleton-container position-relative vh-50">

    </div>
    <!-- Breadscrumb Section -->
    <div class="breadcrumb-bar d-none real-data">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title vehicle_name" id="slug" data-slug="{{ $slug }}">{{ $slug }}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('list') }}">{{__('web.common.vehicles')}}</a></li>
                            <li class="breadcrumb-item active vehicle_name" aria-current="page"></li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadscrumb Section -->
    <!-- Detail Page Head-->
    <section class="product-detail-head d-none real-data">
        <div class="container">
            <div class="detail-page-head">
                <div class="detail-headings">
                    <div class="star-rated">
                        <ul class="list-rating">
                            <li>
                                <div class="car-brand">
                                    <span>
                                        <img src="{{asset('frontend/assets/img/icons/car-icon.svg')}}" alt="img">
                                    </span>
                                    <div class="vehicle_type"></div>
                                </div>
                            </li>
                            <li>
                                <span class="year vehicle_year"></span>
                            </li>
                            <li class="headratings">
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <i class="fas fa-star filled"></i>
                                <span class="d-inline-block average-list-rating">(5.0)</span>
                            </li>
                        </ul>
                        <div class="camaro-info">
                            <h3 class="vehicle_name">
                                <span class="visually-hidden"></span>
                            </h3>
                            <div class="camaro-location">
                                <div class="camaro-location-inner">
                                    <i class='bx bx-map'></i>
                                    <span class="vehicle_location"></span>
                                </div>
                                <div class="camaro-location-inner">
                                    <i class='bx bx-show'></i>
                                    <span>{{__('web.home.views')}} : {{ $vehicleCount->views }} </span>
                                </div>
                                <div class="camaro-location-inner">
                                    <i class='bx bx-car'></i>
                                    <span>{{__('web.home.listed_on')}} : {{ $lastUpdate }} </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="details-btn">
                    <span class="total-badge"><i class='bx bx-calendar-edit'></i>{{__('web.home.total_bookings')}} : {{ $bookingCount }}</span>
                </div>
            </div>
        </div>
    </section>
    <!-- /Detail Page Head-->
    <section class="section product-details vechicle-card d-none real-data">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="detail-product">
                        <div class="pro-info">
                            <div class="">
                                <button type="button" class="fav-icon"><i class="fa-regular fa-heart"></i></button>
                            </div>
                        </div>
                        <div class="slider detail-bigimg">

                        </div>
                        <div class="slider slider-nav-thumbnails">

                        </div>
                    </div>
                    <!-- Extra Services -->
                    <div class="review-sec pb-0 extra-service-div">
                        <div class="review-header">
                            <h4>{{ __('web.user.extra_services') }}</h4>
                        </div>
                    </div>
                    <!-- /Extra Services -->
                    <!-- Listing Section -->
                    <div class="review-sec mb-0 description_section">
                        <div class="review-header">
                            <h4>{{ __('web.home.desc_of_listing') }}</h4>
                        </div>
                        <div class="description-list">
                        </div>
                    </div>
                    <!-- /Listing Section -->
                    <!-- Specifications -->
                    <div class="review-sec specification-card ">
                        <div class="review-header">
                            <h4>{{__('web.home.specifications')}}</h4>
                        </div>
                        <div class="card-body">
                            <div class="lisiting-featues">
                                <div class="row">
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            @if(in_array($vehicle->category_id, [3, 6]))
                                            <img src="{{ asset('frontend/assets/img/specification/specification-icon-13.svg') }}" alt="Icon">
                                            @else
                                            <img src="{{ asset('frontend/assets/img/specification/specification-icon-1.svg') }}" alt="Icon">
                                            @endif
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.body')}} </span>
                                            <h6 class="vehicle_type">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-2.svg')}}" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.make')}} </span>
                                            <h6 class="vehicle_brand">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-3.svg')}}" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.transmission')}} </span>
                                            <h6 class="vehicle_transmission">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-4.svg')}}" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.fuel_type') }}</span>
                                            <h6 class="vehicle_fuel">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-5.svg')}}" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.mileage')}} </span>
                                            <h6 class="vehicle_mileage">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-7.svg')}}" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.year') }}</span>
                                            <h6 class="vehicle_year">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            @if(in_array($vehicle->category_id, [3, 6]))
                                            <img src="{{ asset('frontend/assets/img/specification/specification-icon-14.svg') }}" alt="Icon">
                                            @else
                                            <img src="{{ asset('frontend/assets/img/specification/specification-icon-1.svg') }}" alt="Icon">
                                            @endif
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.vin') }} </span>
                                            <h6 id="vin">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            @if(in_array($vehicle->category_id, [3, 6]))
                                            <img src="{{ asset('frontend/assets/img/specification/specification-icon-15.svg') }}" alt="Icon">
                                            @else
                                            <img src="{{asset('frontend/assets/img/specification/specification-icon-10.svg')}}" alt="Icon">
                                            @endif
                                        </div>
                                        <div class="featues-info">
                                            @if(in_array($vehicle->category_id, [3, 6]))
                                            <span>{{ __('web.home.hatch') }} </span>
                                            <h6 class="vehicle_hatch">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                            @else
                                            <span>{{ __('web.home.door') }} </span>
                                            <h6 class="vehicle_doors">
                                                <span class="visually-hidden"></span>
                                            </h6>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Specifications -->
                    <!-- Car Features -->
                    <div class="review-sec listing-feature feature_section">
                        <div class="review-header">
                            <h4>{{ __('web.home.features') }}</h4>
                        </div>
                        <div class="listing-description">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                    <!-- /Car Features -->
                    <!-- Tariff -->
                    <div class="review-sec listing-feature tariff_section">
                        <div class="review-header">
                            <h4>{{ __('web.home.tariff') }}</h4>
                        </div>
                        <div class="table-responsive">
                            <table class="table border mb-3" id="tarrifTable">
                                <thead class="thead-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Daily Price</th>
                                        <th>Base Kilometers</th>
                                        <th>Kilometers Extra Price</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /Tariff -->
                    <!-- Gallery -->
                    <div class="review-sec mb-0 pb-0 gallery_section">
                        <div class="review-header">
                            <h4>{{ __('web.home.gallery') }}</h4>
                        </div>
                    </div>
                    <!-- /Gallery -->
                    <!-- Video -->
                    <div class="review-sec mb-0 video_section d-none">
                        <div class="review-header">
                            <h4>{{ __('web.home.video') }}</h4>
                        </div>
                        <div class="short-video">
                            <img class="img-fluid" alt="Short Video" src="{{asset('frontend/assets/img/video-img.jpg')}}" id="video_thumb">
                            <a href="#" data-fancybox="video" class="video-icon" id="video">
                                <i class="bx bx-play"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /Video -->
                    <!-- FAQ -->
                    <div class="review-sec faq-feature faq_section"></div>
                    <!-- /FAQ -->
                    <!-- Policies -->
                    <div class="review-sec">
                        <div class="review-header">
                            <h4>{{__('web.home.polices')}}</h4>
                        </div>
                        <div class="policy-list">
                            <div class="policy-item">
                                <div class="policy-info">
                                    <h6>{{ __('web.home.cancel_charges') }}</h6>
                                    <p>{{ __('web.home.cancel_policy_content') }}</p>
                                </div>
                                <a href="{{url('pages/privacy-policy')}}">{{ __('web.home.know_more') }}</a>
                            </div>
                            <div class="policy-item d-none" id="policy-section">
                                <div class="policy-info">
                                    <h6>{{__('web.home.policy')}}</h6>
                                    <p>{{__('web.home.consent')}}</p>
                                </div>
                                <button type="button" class="btn btn-light border-0 view-policies">{{ __('web.home.view_details') }}</a>
                            </div>
                        </div>
                    </div>
                    <!-- /Policies -->
                    <!-- Reviews -->
                    <div class="review-sec listing-review">
                        <div class="review-header">
                            <h4>{{__('web.common.reviews')}}</h4>
                        </div>
                        <div class="rating-wrapper">
                            <div class="rating-wraps">
                                <h2 id="overall_ratings"><span>/5</span></h2>
                                <p id="rating_description"></p>
                                <h6 id="total_reviews">
                                    <span class="visually-hidden">0</span>
                                </h6>
                            </div>
                            <div class="rating-progress">
                                <div class="progress-info">
                                    <h6>{{__('web.home.service')}}</h6>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar bg-primary" id="service_progress"></div>
                                    </div>
                                    <div class="progress-percent" id="avg_service_ratings">0.0</div>
                                </div>
                                <div class="progress-info">
                                    <h6>{{ __('web.user.location') }}</h6>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar bg-primary" id="location_progress"></div>
                                    </div>
                                    <div class="progress-percent" id="avg_location_ratings">0.0</div>
                                </div>
                                <div class="progress-info">
                                    <h6>{{__('web.home.value_for_money')}}</h6>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar bg-primary" id="value_for_money_progress"></div>
                                    </div>
                                    <div class="progress-percent" id="avg_value_for_money_ratings">0.0</div>
                                </div>
                                <div class="progress-info">
                                    <h6>{{ __('web.home.facilities') }}</h6>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar bg-primary" id="facility_progress"></div>
                                    </div>
                                    <div class="progress-percent" id="avg_facility_ratings">0.0</div>
                                </div>
                                <div class="progress-info">
                                    <h6>{{ __('web.home.cleanliness') }}</h6>
                                    <div class="progress" role="progressbar">
                                        <div class="progress-bar bg-primary" id="cleanliness_progress"></div>
                                    </div>
                                    <div class="progress-percent" id="avg_cleanliness_ratings">0.0</div>
                                </div>
                            </div>
                        </div>
                        <div class="review-card" id="review_list_main_card">
                            <div class="review-head">
                                <h6 id="total_reviews_count">
                                    <span class="visually-hidden">0</span>
                                </h6>
                            </div>
                            <ul id="review_list_container">
                            </ul>
                        </div>
                    </div>
                    <!-- /Reviews -->
                </div>
                <div class="col-lg-4 theiaStickySidebar">
                    <div class="review-sec mt-0">
                        <form id="validateVehicleBook" autocomplete="off" method="POST" action="{{ route('booking.checkout', ['slug' => $slug]) }}">
                            @csrf
                            <div class="review-header">
                                <h4>{{ __('web.home.pricing') }}</h4>
                            </div>
                            <input type="hidden" name="vehicle_slug" id="vehicle_slug" value="{{ $slug }}">
                            <input type="hidden" name="category_id" id="category_id" value="{{ $vehicle->category_id }}">
                            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicle->id }}">
                            <input type="hidden" name="price_type" id="price_type" value="">
                            <input type="hidden" name="rent_value" id="rent_value" value="">
                            <input type="hidden" name="final_price_rate" id="final_price_rate" value="">
                            <input type="hidden" name="auth_user_id" id="auth_user_id" value="{{ Auth::guard('web')->user()->id ?? '' }}">
                            <div class="mb-3 price_options">
                            </div>
                            <div class="location-content">
                                <div class="delivery-tab">
                                    <ul class="nav">
                                        <li class="d-none">
                                            <label class="booking_custom_check">
                                                <input type="radio" name="rent_type" id="location_delivery">
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{__('web.home.delivery')}}</span>
                                                </span>
                                            </label>
                                        </li>
                                        <li>
                                            <label class="booking_custom_check">
                                                <input type="radio" name="rent_type" id="location_pickup" checked>
                                                <span class="booking_checkmark">
                                                    <span class="checked-title">{{ __('web.home.self_pickup') }}</span>
                                                </span>
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <div class="tab-content">
                                    <div class="tab-pane fade active show" id="delivery">
                                        <ul>
                                            <div id="devliveryCOntainer">
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label for="delivery_location">{{ __('web.home.delivery_location') }}</label>
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <select name="delivery_location" id="delivery_location" class="form-control select2">
                                                                    <option value="">{{ __('web.home.select_delivery_location') }}</option>
                                                                    @foreach($allLocation as $location)
                                                                    <option value="{{ $location->id }}" @if(request()->has('pl') && request()->pl == $location->id) selected @endif>{{ $location->name }} - {{ $location->address }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="form-icon">
                                                                    <i class="fa-solid fa-location-crosshairs"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label class="custom_check d-inline-flex location-check m-0" for="delivery_remeber"><span>{{__('web.home.return_to_same_location')}}</span>
                                                            <input type="checkbox" name="delivery_remeber" id="delivery_remeber">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label for="delivery_return_location">{{ __('web.home.return_location') }}</label>
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <select name="delivery_return_location" id="delivery_return_location" class="form-control select2">
                                                                    <option value="">{{ __('web.home.select_return_location') }}</option>
                                                                    @foreach($allLocation as $location)
                                                                    <option value="{{ $location->id }}">{{ $location->name }} - {{ $location->address }}</option>
                                                                    @endforeach
                                                                </select>
                                                                <span class="form-icon">
                                                                    <i class="fa-solid fa-location-crosshairs"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </div>
                                            <div id="selfCOntainer">
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label for="pickup_location_id">{{ __('web.home.pickup_location') }}</label>
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="hidden" name="pickup_location_id" id="pickup_location_id" value="{{ $mainLocation->id }}">
                                                                <select name="pickup_location" id="pickup_location" class="form-control select2">
                                                                    <option value="">{{ __('web.home.select_delivery_location') }}</option>
                                                                    @foreach($allLocation as $location)
                                                                    <option value="{{ $location->id }}" @if(request()->has('pl') && request()->pl == $location->id) selected @endif>{{ $location->name }} - {{ $location->address }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label class="custom_check d-inline-flex location-check m-0" for="pickup_remeber"><span>{{__('web.home.return_to_same_location')}}</span>
                                                            <input type="checkbox" name="pickup_remeber" id="pickup_remeber">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label for="pickup_return_location">{{ __('web.home.return_location') }}</label>
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="hidden" name="pickup_return_location_id" value="{{ $mainLocation->id }}">
                                                                <select name="pickup_return_location" id="pickup_return_location" class="form-control select2">
                                                                    <option value="">{{ __('web.home.select_return_location') }}</option>
                                                                    @foreach($allLocation as $location)
                                                                    <option value="{{ $location->id }}">{{ $location->name }} - {{ $location->address }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            </div>
                                            <input type="hidden" id="has_pickup_date" value="{{request()->has('pd') ? request()->pd : ''}}">
                                            <li class="column-group-main">
                                                <div class="input-block m-0">
                                                    <label for="pickup_date">{{__('web.home.pickup_date')}}</label>
                                                </div>
                                                <div class="input-block-wrapp sidebar-form">
                                                    <div class="input-block  me-lg-2">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" name="pickup_date" id="pickup_date" class="form-control bookingpickupdate" placeholder="dd-mm-yyyy" value="{{ request()->has('pd') ? request()->pd : '' }}">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-calendar-days"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-block">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" name="pickup_time" id="pickup_time" class="form-control booking_timepicker" placeholder="hh:mm" value="{{ request()->has('pt') ? request()->pt : '' }}">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-clock"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block m-0"> <label for="return_date">{{__('web.home.return_date')}}</label>
                                                </div>
                                                <div class="input-block-wrapp sidebar-form">
                                                    <div class="input-block me-lg-2">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" id="return_date" name="return_date" class="form-control bookingreturndate" placeholder="00-00-0000" value="{{ request()->has('rd') ? request()->rd : '' }}">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-calendar-days"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-block">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" name="return_time" id="return_time" class="form-control booking_return_timepicker" placeholder="00:00" value="{{ request()->has('rt') ? request()->rt : '' }}">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-clock"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-last">
                                                <div class="input-block mb-0">
                                                    <div class="search-btn">
                                                        @auth
                                                        <a type="submit"
                                                            class="btn btn-primary check-available w-100 {{ $allowBooking != 1 ? 'btn-disabled' : '' }}"
                                                            id="validate_btn">
                                                            {{ __('web.home.book') }}
                                                        </a>
                                                        @else
                                                        <a type="submit"
                                                            class="btn btn-primary check-available w-100 {{ $allowBooking != 1 ? 'btn-disabled' : '' }}"
                                                            id="validate_btn">
                                                            {{ __('web.home.book') }}
                                                        </a>
                                                        @endauth
                                                        <button type="button" id="enquire_us" data-bs-toggle="modal" data-bs-target="#enquiry" class="btn btn-theme {{ $allowEnquiries != 1 ? 'btn-disabled' : '' }}">{{__('web.home.enquire_us')}}</button>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="review-sec extra-service mt-0">
                        <div class="review-header">
                            <h4>{{ __('web.home.listing_owner_details') }}</h4>
                        </div>
                        <div class="owner-detail">
                            <div class="owner-img">
                                <a href="#"><img src="{{ $author_profile }}" alt="User"></a>
                            </div>
                            <div class="reviewbox-list-rating">
                                <h5><a>{{ $author_name ?? "" }}</a></h5>
                            </div>
                        </div>
                        <ul class="booking-list mb-0">
                            <li>
                                {{__('web.user.email')}}
                                <span>{{ $author_email ?? "" }}</span>
                            </li>
                            <li>
                                {{ __('web.home.phone_number') }}
                                <span>{{ $author_phone ?? ""}}</span>
                            </li>
                            <li>
                                {{__('web.home.location')}}
                                <span>{{ $author_location ?? "" }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div id="recommended-vehicle">
            </div>
        </div>
    </section>
    <div class="modal new-modal fade enquire-mdl" id="enquiry" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('web.home.enquiry') }}</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form class="enquire-modal" id="enquiryForm">
                        @csrf
                        <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $vehicleDetail->id ?? "" }}">
                        <div class="booking-header">
                            <div class="booking-img-wrap">
                                <div class="book-img">
                                    <img src="{{ $vehicleDetail->image_url ?? "" }}" alt="img">
                                </div>
                                <div class="book-info">
                                    <h6>{{ $vehicleDetail->name ?? "" }}</h6>
                                    <p><i class="feather-map-pin"></i> {{ __('web.home.location') }} : {{ $vehicleDetail->location_name ?? "" }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-form-group">
                            <label for="enquiry_name">{{ __('web.home.name') }} <em class="text-danger">*</em></label>
                            <input type="text" class="form-control" name="enquiry_name" id="enquiry_name" placeholder="{{__('web.blog.full_name')}}" @auth value="{{ getCurrentUserFullname(Auth::guard('web')->user()->id) ?? '' }}" @endauth>
                            <span class="error-text text-danger" id="enquiry_name_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label for="enquiry_email">{{ __('web.home.email') }} <em class="text-danger">*</em></label>
                            <input type="email" class="form-control" name="enquiry_email" id="enquiry_email" placeholder="{{__('web.user.enter_email')}}" value="{{ Auth::guard('web')->user()->email ?? '' }}">
                            <span class="error-text text-danger" id="enquiry_email_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label for="enquiry_phone">{{ __('web.home.phone_number') }} <em class="text-danger">*</em></label>
                            <input type="text" class="form-control" name="enquiry_phone" id="enquiry_phone" placeholder="{{__('web.home.enter_phone_number')}}" value="{{ Auth::guard('web')->user()->phone_number ?? '' }}">
                            <input type="hidden" name="international_phone_number" id="international_phone_number" class="international_phone_number">
                            <span class="error-text text-danger" id="enquiry_phone_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label for="enquiry_message">{{ __('web.home.message') }} <em class="text-danger">*</em></label>
                            <textarea class="form-control" rows="4" required id="enquiry_message" name="enquiry_message" placeholder="{{__('web.home.message')}}"></textarea>
                            <span class="error-text text-danger" id="enquiry_message_error"></span>
                        </div>
                        <label class="custom_check w-100" for="terms">
                            <input type="checkbox" name="terms" id="terms" value="1">
                            <span class="checkmark"></span> {{ __('web.home.enquire_aggree_with') }} <a href="{{ route('pages', 'terms-conditions') }}">{{ __('web.home.terms_of_service') }}</a> & <button type="button" class="border-0 bg-white">{{ __('web.home.privacy_policy') }}</button>
                        </label>
                        <span class="error-text text-danger" id="terms_error"></span>
                        <div class="modal-btn modal-btn-sm">
                            <button type="submit" class="btn btn-primary w-100 submitbtn">
                                {{ __('web.common.submit') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Enquiry Modal -->
    @endsection
    @push('scripts')

    <!-- Slick JS -->
    <script src="{{asset('frontend/assets/plugins/slick/slick.js')}}"></script>

    <!-- Sticky Sidebar JS -->
    <script src="{{asset('frontend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js')}}"></script>
    <script src="{{asset('frontend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js')}}"></script>

    <!-- Fancybox JS -->
    <script src="{{asset('frontend/assets/plugins/fancybox/fancybox.umd.js')}}"></script>
    <script src="{{ asset('backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>
    <script src="{{ asset('/frontend/assets/js/purify.min.js') }}"></script>
    <script src="{{ asset('/frontend/assets/js/custom/home/vehicle-details.js') }}"></script>
    @endpush
