    @extends($layout)
    @push('styles')
    <!-- Fancybox CSS -->
    <link rel="stylesheet" href="/frontend/assets/plugins/fancybox/fancybox.css">
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">
    @endpush
    @section('content')
    <!-- Skeleton Container -->
    <div class="container skeleton-container">
        <div class="vehl_dets_skeleton">
            <!-- Header -->
            <div class="vehl_dets_banner skeleton"></div>
            <!-- Vehicle Info -->
            <div class="vehl_dets_info">
                <div class="vehl_dets_badge skeleton"></div>
                <div class="vehl_dets_title skeleton"></div>
                <div class="vehl_dets_meta skeleton"></div>
            </div>
            <!-- Main Content -->
            <div class="vehl_dets_main">
                <!-- Left Side (Image, Gallery, and Additional Skeleton) -->
                <div class="vehl_dets_left">
                    <div class="vehl_dets_image skeleton"></div>
                    <div class="vehl_dets_gallery">
                        <div class="vehl_dets_gallery_item skeleton"></div>
                        <div class="vehl_dets_gallery_item skeleton"></div>
                        <div class="vehl_dets_gallery_item skeleton"></div>
                        <div class="vehl_dets_gallery_item skeleton"></div>
                    </div>
                    <div class="vehl_dets_text skeleton"></div>
                    <div class="vehl_dets_text skeleton"></div>
                    <div class="vehl_dets_text skeleton"></div>
                    <div class="vehl_dets_text skeleton"></div>
                </div>
                <!-- Right Side (Pricing & Booking) -->
                <div class="vehl_dets_sidebar">
                    <div class="vehl_dets_pricing">
                        <div class="vehl_dets_price skeleton"></div>
                        <div class="vehl_dets_price skeleton"></div>
                        <div class="vehl_dets_price skeleton"></div>
                        <div class="vehl_dets_price skeleton"></div>
                    </div>
                    <div class="vehl_dets_delivery skeleton"></div>
                    <div class="vehl_dets_location skeleton"></div>
                    <div class="vehl_dets_date skeleton"></div>
                    <div class="vehl_dets_button skeleton"></div>
                    <div class="vehl_dets_enquire skeleton"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Skeleton Container -->
    <!-- Breadscrumb Section -->
    <div class="breadcrumb-bar d-none real-data">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title vehicle_name" id="slug" data-slug="{{ $slug }}"></h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                            <li class="breadcrumb-item"><a href="/vehicles">{{__('web.common.vehicles')}}</a></li>
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
                                        <img src="/frontend/assets/img/icons/car-icon.svg" alt="img">
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
                            <h3 class="vehicle_name"></h3>
                            <div class="camaro-location">
                                <div class="camaro-location-inner">
                                    <i class='bx bx-map'></i>
                                    <span class="vehicle_location">Location : Miami St, Destin, FL 32550, USA </span>
                                </div>
                                <div class="camaro-location-inner">
                                    <i class='bx bx-show'></i>
                                    <span>{{__('web.home.views')}} : {{ $vehicleCount->views }} </span>
                                </div>
                                <div class="camaro-location-inner">
                                    <i class='bx bx-car'></i>
                                    <span>{{__('web.home.listed_on')}} : {{ $lastUpdateFormatted }} </span>
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
                            <div class="pro-badge">
                                <span class="badge-km d-none"><i class="fa-solid fa-person-walking"></i>4.2 Km Away</span>
                                <a href="javascript:void(0);" class="fav-icon"><i class="fa-regular fa-heart"></i></a>
                            </div>
                        </div>
                        <div class="slider detail-bigimg">
                            <div class="product-img">
                                <img src="/frontend/assets/img/cars/slider-01.jpg" alt="Slider">
                            </div>
                            <div class="product-img">
                                <img src="/frontend/assets/img/cars/slider-02.jpg" alt="Slider">
                            </div>
                            <div class="product-img">
                                <img src="/frontend/assets/img/cars/slider-03.jpg" alt="Slider">
                            </div>
                            <div class="product-img">
                                <img src="/frontend/assets/img/cars/slider-04.jpg" alt="Slider">
                            </div>
                            <div class="product-img">
                                <img src="/frontend/assets/img/cars/slider-05.jpg" alt="Slider">
                            </div>
                        </div>
                        <div class="slider slider-nav-thumbnails">
                            <div><img src="/frontend/assets/img/cars/slider-thum-01.jpg" alt="product image"></div>
                            <div><img src="/frontend/assets/img/cars/slider-thum-02.jpg" alt="product image"></div>
                            <div><img src="/frontend/assets/img/cars/slider-thum-03.jpg" alt="product image"></div>
                            <div><img src="/frontend/assets/img/cars/slider-thum-04.jpg" alt="product image"></div>
                            <div><img src="/frontend/assets/img/cars/slider-thum-05.jpg" alt="product image"></div>
                        </div>
                    </div>
                    <!-- Extra Services -->
                    <div class="review-sec pb-0 extra-service-div">
                        <div class="review-header">
                            <h4>{{ __('web.user.extra_services') }}</h4>
                        </div>
                        <div class="lisiting-service">
                            <div class="row">
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-01.svg" class="avatar" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>GPS Navigation Systems</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-02.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Wi-Fi Hotspot</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-03.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Child Safety Seats</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-04.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Fuel Options</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-05.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Roadside Assistance</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-06.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Satellite Radio</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-07.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Additional Accessories</p>
                                    </div>
                                </div>
                                <div class="servicelist d-flex align-items-center col-xxl-3 col-xl-4 col-sm-6">
                                    <div class="service-img">
                                        <img src="/frontend/assets/img/icons/service-08.svg" alt="Icon">
                                    </div>
                                    <div class="service-info">
                                        <p>Express Check-in/out</p>
                                    </div>
                                </div>
                            </div>
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
                                            <img src="/frontend/assets/img/specification/specification-icon-1.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.body')}} </span>
                                            <h6 class="vehicle_type"> Sedan</h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-2.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.make')}} </span>
                                            <h6 class="vehicle_brand"> Nisssan</h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-3.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.transmission')}} </span>
                                            <h6 class="vehicle_transmission"></h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-4.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.fuel_type') }}</span>
                                            <h6 class="vehicle_fuel"> Diesel</h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-5.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{__('web.home.mileage')}} </span>
                                            <h6 class="vehicle_mileage">16 Km</h6>
                                        </div>
                                    </div>

                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-7.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.year') }}</span>
                                            <h6 class="vehicle_year"> 2018</h6>
                                        </div>
                                    </div>

                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-9.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>{{ __('web.home.vin') }} </span>
                                            <h6 id="vin"> 45456444</h6>
                                        </div>
                                    </div>
                                    <div class="featureslist d-flex align-items-center col-xl-3 col-md-4 col-sm-6">
                                        <div class="feature-img">
                                            <img src="/frontend/assets/img/specification/specification-icon-10.svg" alt="Icon">
                                        </div>
                                        <div class="featues-info">
                                            <span>Door </span>
                                            <h6 class="vehicle_doors"></h6>
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
                        <div class="gallery-list">
                            <ul>
                                <li>
                                    <div class="gallery-widget">
                                        <a href="/frontend/assets/img/gallery/gallery-big-01.jpg" data-fancybox="gallery1">
                                            <img class="img-fluid" alt="Image" src="/frontend/assets/img/gallery/gallery-thumb-01.jpg">
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="gallery-widget">
                                        <a href="/frontend/assets/img/gallery/gallery-big-02.jpg" data-fancybox="gallery1">
                                            <img class="img-fluid" alt="Image" src="/frontend/assets/img/gallery/gallery-thumb-02.jpg">
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="gallery-widget">
                                        <a href="/frontend/assets/img/gallery/gallery-big-03.jpg" data-fancybox="gallery1">
                                            <img class="img-fluid" alt="Image" src="/frontend/assets/img/gallery/gallery-thumb-03.jpg">
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <div class="gallery-widget">
                                        <a href="/frontend/assets/img/gallery/gallery-big-04.jpg" data-fancybox="gallery1">
                                            <img class="img-fluid" alt="Image" src="/frontend/assets/img/gallery/gallery-thumb-04.jpg">
                                        </a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <!-- /Gallery -->
                    <!-- Video -->
                    <div class="review-sec mb-0 video_section d-none">
                        <div class="review-header">
                            <h4>{{ __('web.home.video') }}</h4>
                        </div>
                        <div class="short-video">
                            <img class="img-fluid" alt="Image" src="/frontend/assets/img/video-img.jpg" id="video_thumb">
                            <a href="https://www.youtube.com/embed/ExJZAegsOis" data-fancybox="video" class="video-icon" id="video">
                                <i class="bx bx-play"></i>
                            </a>
                        </div>
                    </div>
                    <!-- /Video -->
                    <!-- FAQ -->
                    <div class="review-sec faq-feature faq_section">
                        <div class="review-header">
                            <h4>{{ __('web.home.faqs') }}</h4>
                        </div>
                        <div class="faq-info">
                            <div class="faq-card">
                                <h4 class="faq-title">
                                    <a class="collapsed" data-bs-toggle="collapse" href="#faqOne" aria-expanded="false">How old do I need to be to rent a car?</a>
                                </h4>
                                <div id="faqOne" class="card-collapse collapse">
                                    <p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
                                </div>
                            </div>
                            <div class="faq-card">
                                <h4 class="faq-title">
                                    <a class="collapsed" data-bs-toggle="collapse" href="#faqTwo" aria-expanded="false">What documents do I need to rent a car?</a>
                                </h4>
                                <div id="faqTwo" class="card-collapse collapse">
                                    <p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
                                </div>
                            </div>
                            <div class="faq-card">
                                <h4 class="faq-title">
                                    <a class="collapsed" data-bs-toggle="collapse" href="#faqThree" aria-expanded="false">What types of vehicles are available for rent?</a>
                                </h4>
                                <div id="faqThree" class="card-collapse collapse">
                                    <p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
                                </div>
                            </div>
                            <div class="faq-card">
                                <h4 class="faq-title">
                                    <a class="collapsed" data-bs-toggle="collapse" href="#faqFour" aria-expanded="false">Can I rent a car with a debit card?</a>
                                </h4>
                                <div id="faqFour" class="card-collapse collapse">
                                    <p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
                                </div>
                            </div>
                        </div>
                    </div>
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
                                <a href="/pages/privacy-policy">{{ __('web.home.know_more') }}</a>
                            </div>
                            <div class="policy-item">
                                <div class="policy-info">
                                    <h6>{{__('web.home.policy')}}</h6>
                                    <p>{{__('web.home.consent')}}</p>
                                </div>
                                <a href="/pages/privacy-policy">{{ __('web.home.view_details') }}</a>
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
                                <h6 id="total_reviews">Based on 0 Reviews</h6>
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
                                <h6 id="total_reviews_count">Showing 0 reviews</h6>
                            </div>
                            <ul id="review_list_container">

                            </ul>
                        </div>
                    </div>
                    <!-- /Reviews -->
                    <!-- Leave a Reply -->
                    @if (current_user() != null)
                    <div class="review-sec leave-reply-form mb-0">
                        <div class="review-header">
                            <h4>{{ __('web.home.leave_reply') }}</h4>
                        </div>
                        <div class="review-list-rating">
                            <div class="row">
                                <div class="col-xl-4 col-md-6">
                                    <div class="set-rating">
                                        <p>{{__('web.home.service')}}</p>
                                        <div class="rating-selection" id="service_ratings">
                                            <input type="checkbox" id="service1" class="service_ratings" value="1">
                                            <label for="service1"></label>
                                            <input type="checkbox" id="service2" class="service_ratings" value="2">
                                            <label for="service2"></label>
                                            <input type="checkbox" id="service3" class="service_ratings" value="3">
                                            <label for="service3"></label>
                                            <input type="checkbox" id="service4" class="service_ratings" value="4">
                                            <label for="service4"></label>
                                            <input type="checkbox" id="service5" class="service_ratings" value="5">
                                            <label for="service5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="set-rating">
                                        <p>{{__('web.user.location')}}</p>
                                        <div class="rating-selection" id="location_ratings">
                                            <input type="checkbox" id="loc1" class="location_ratings" value="1">
                                            <label for="loc1"></label>
                                            <input type="checkbox" id="loc2" class="location_ratings" value="2">
                                            <label for="loc2"></label>
                                            <input type="checkbox" id="loc3" class="location_ratings" value="3">
                                            <label for="loc3"></label>
                                            <input type="checkbox" id="loc4" class="location_ratings" value="4">
                                            <label for="loc4"></label>
                                            <input type="checkbox" id="loc5" class="location_ratings" value="5">
                                            <label for="loc5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.facilities') }}</p>
                                        <div class="rating-selection" id="facility_ratings">
                                            <input type="checkbox" id="fac1" class="facility_ratings" value="1">
                                            <label for="fac1"></label>
                                            <input type="checkbox" id="fac2" class="facility_ratings" value="2">
                                            <label for="fac2"></label>
                                            <input type="checkbox" id="fac3" class="facility_ratings" value="3">
                                            <label for="fac3"></label>
                                            <input type="checkbox" id="fac4" class="facility_ratings" value="4">
                                            <label for="fac4"></label>
                                            <input type="checkbox" id="fac5" class="facility_ratings" value="5">
                                            <label for="fac5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.value_for_money') }}</p>
                                        <div class="rating-selection" id="value_for_money_ratings">
                                            <input type="checkbox" id="val1" class="value_for_money_ratings" value="1">
                                            <label for="val1"></label>
                                            <input type="checkbox" id="val2" class="value_for_money_ratings" value="2">
                                            <label for="val2"></label>
                                            <input type="checkbox" id="val3" class="value_for_money_ratings" value="3">
                                            <label for="val3"></label>
                                            <input type="checkbox" id="val4" class="value_for_money_ratings" value="4">
                                            <label for="val4"></label>
                                            <input type="checkbox" id="val5" class="value_for_money_ratings" value="5">
                                            <label for="val5"></label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-4 col-md-6">
                                    <div class="set-rating">
                                        <p>{{ __('web.home.cleanliness') }}</p>
                                        <div class="rating-selection" id="cleanliness_ratings">
                                            <input type="checkbox" id="clean1" class="cleanliness_ratings" value="1">
                                            <label for="clean1"></label>
                                            <input type="checkbox" id="clean2" class="cleanliness_ratings" value="2">
                                            <label for="clean2"></label>
                                            <input type="checkbox" id="clean3" class="cleanliness_ratings" value="3">
                                            <label for="clean3"></label>
                                            <input type="checkbox" id="clean4" class="cleanliness_ratings" value="4">
                                            <label for="clean4"></label>
                                            <input type="checkbox" id="clean5" class="cleanliness_ratings" value="5">
                                            <label for="clean5"></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="review-list">
                                <ul>
                                    <li class="review-box feedbackbox mb-0">
                                        <div class="review-details">
                                            <form id="reviewForm" autocomplete="off">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="input-block">
                                                            <label>{{ __('web.home.comments') }}<span class="text-danger"> *</span></label>
                                                            <textarea rows="4" class="form-control" name="comments" id="comments" placeholder="{{ __('web.home.comments') }}"></textarea>
                                                            <span class="text-danger error-text" id="comments_error"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="submit-btn text-end">
                                                    <button class="btn btn-primary submit-review" type="submit">{{__('web.home.submit_review')}}</button>
                                                </div>
                                            </form>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif
                    <!-- /Leave a Reply -->
                </div>
                <div class="col-lg-4 theiaStickySidebar">
                    <div class="review-sec mt-0">
                        <form id="validateVehicleBook" autocomplete="off" method="POST" action="{{ route('booking.checkout', ['slug' => $slug]) }}">
                            @csrf

                            <div class="review-header">
                                <h4>{{ __('web.home.pricing') }}</h4>
                            </div>
                            <input type="hidden" name="vehicle_slug" id="vehicle_slug" value="{{ $slug }}">
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
                                                <input type="radio" name="rent_type" id="location_delivery" >
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
                                                        <label>{{ __('web.home.delivery_location') }}</label>
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
                                                        <label class="custom_check d-inline-flex location-check m-0"><span>{{__('web.home.return_to_same_location')}}</span>
                                                            <input type="checkbox" name="delivery_remeber" id="delivery_remeber">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label>{{ __('web.home.return_location') }}</label>
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
                                                        <label>{{ __('web.home.pickup_location') }}</label>
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="hidden" name="pickup_location_id" value="{{ $mainLocation->id }}">
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
                                                        <label class="custom_check d-inline-flex location-check m-0"><span>{{__('web.home.return_to_same_location')}}</span>
                                                            <input type="checkbox" name="pickup_remeber" id="pickup_remeber">
                                                            <span class="checkmark"></span>
                                                        </label>
                                                    </div>
                                                </li>
                                                <li class="column-group-main">
                                                    <div class="input-block">
                                                        <label>{{ __('web.home.return_location') }}</label>
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
                                                    <label>{{__('web.home.pickup_date')}}</label>
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
                                                <div class="input-block m-0"> <label>{{__('web.home.return_date')}}</label>
                                                </div>
                                                <div class="input-block-wrapp sidebar-form">
                                                    <div class="input-block me-lg-2">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" id="return_date" name="return_date" class="form-control bookingreturndate" placeholder="00-00-0000" readonly value="{{ request()->has('rd') ? request()->rd : '' }}">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-calendar-days"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-block">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" name="return_time" id="return_time" class="form-control booking_return_timepicker" placeholder="00:00" readonly value="{{ request()->has('rt') ? request()->rt : '' }}">
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
                                                        <a href="javascript:void(0);" id="enquire_us" data-bs-toggle="modal" data-bs-target="#enquiry" class="btn btn-theme {{ $allowEnquiries != 1 ? 'btn-disabled' : '' }}">{{__('web.home.enquire_us')}}</a>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="tab-pane fade" id="pickup">
                                        <ul>
                                            <li class="column-group-main">
                                                <div class="input-block">
                                                    <label>Delivery Location</label>
                                                    <div class="group-img">
                                                        <select class="select">
                                                            <option>Newyork Office - 78, 10th street Laplace USA</option>
                                                            <option>Newyork Office - 12, 5th street USA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block">
                                                    <label class="custom_check d-inline-flex location-check m-0"><span>Return to same location</span>
                                                        <input type="checkbox" name="remeber">
                                                        <span class="checkmark"></span>
                                                    </label>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block">
                                                    <label>Delivery Location</label>
                                                    <div class="group-img">
                                                        <select class="select">
                                                            <option>Newyork Office - 78, 10th street Laplace USA</option>
                                                            <option>Newyork Office - 12, 5th street USA</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block">
                                                    <label>Return Location</label>
                                                    <div class="group-img">
                                                        <div class="form-wrap">
                                                            <input type="text" class="form-control" placeholder="78, 10th street Laplace USA">
                                                            <span class="form-icon">
                                                                <i class="fa-solid fa-location-crosshairs"></i>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block m-0">
                                                    <label>Pickup Date</label>
                                                </div>
                                                <div class="input-block-wrapp sidebar-form">
                                                    <div class="input-block  me-lg-2">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" class="form-control datetimepicker" placeholder="04/11/2023">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-calendar-days"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-block">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" class="form-control timepicker" placeholder="11:00 AM">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-clock"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="column-group-main">
                                                <div class="input-block m-0"> <label>Return Date</label>
                                                </div>
                                                <div class="input-block-wrapp sidebar-form">
                                                    <div class="input-block me-2">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" class="form-control datetimepicker" placeholder="04/11/2023">
                                                                <span class="form-icon">
                                                                    <i class="fa-regular fa-calendar-days"></i>
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="input-block">
                                                        <div class="group-img">
                                                            <div class="form-wrap">
                                                                <input type="text" class="form-control timepicker" placeholder="11:00 AM">
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
                                                        <a href="#" class="btn btn-primary check-available w-100">Book</a>
                                                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#enquiry" class="btn btn-theme">Enquire Us</a>
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
                                <a href="#"><img src="{{ $data['author_profile'] }}" alt="User"></a>
                            </div>
                            <div class="reviewbox-list-rating">
                                <h5><a>{{ $data['author_name'] ?? "" }}</a></h5>
                            </div>
                        </div>
                        <ul class="booking-list">
                            <li>
                                {{__('web.user.email')}}
                                <span>{{ $data['author_email'] ?? "" }}</span>
                            </li>
                            <li>
                                {{ __('web.home.phone_number') }}
                                <span>{{ $data['author_phone'] ?? ""}}</span>
                            </li>
                            <li>
                                {{__('web.home.location')}}
                                <span>{{ $data['author_location'] ?? "" }}</span>
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
                            <label>{{ __('web.home.name') }} <em class="text-danger">*</em></label>
                            <input type="text" class="form-control" name="enquiry_name" id="enquiry_name" placeholder="{{__('web.blog.full_name')}}" value="{{ Auth::guard('web')->user()->name ?? '' }}">
                            <span class="error-text text-danger" id="enquiry_name_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label>{{ __('web.home.email') }} <em class="text-danger">*</em></label>
                            <input type="email" class="form-control" name="enquiry_email" id="enquiry_email" placeholder="{{__('web.user.enter_email')}}" value="{{ Auth::guard('web')->user()->email ?? '' }}">
                            <span class="error-text text-danger" id="enquiry_email_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label>{{ __('web.home.phone_number') }} <em class="text-danger">*</em></label>
                            <input type="text" class="form-control" name="enquiry_phone" id="enquiry_phone" placeholder="{{__('web.home.enter_phone_number')}}" value="{{ Auth::guard('web')->user()->phone ?? '' }}">
                            <input type="hidden" name="international_phone_number" id="international_phone_number" class="international_phone_number">
                            <span class="error-text text-danger" id="enquiry_phone_error"></span>
                        </div>
                        <div class="modal-form-group">
                            <label>{{ __('web.home.message') }} <em class="text-danger">*</em></label>
                            <textarea class="form-control" rows="4" required id="enquiry_message" name="enquiry_message" placeholder="{{__('web.home.message')}}"></textarea>
                            <span class="error-text text-danger" id="enquiry_message_error"></span>
                        </div>
                        <label class="custom_check w-100">
                            <input type="checkbox" name="terms" id="terms" value="1">
                            <span class="checkmark"></span> {{ __('web.home.enquire_aggree_with') }} <a href="/pages/terms-conditions">{{ __('web.home.terms_of_service') }}</a> & <a href="javascript:void(0);">{{ __('web.home.privacy_policy') }}</a>
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
    <script src="/frontend/assets/plugins/slick/slick.js"></script>

    <!-- Sticky Sidebar JS -->
    <script src="/frontend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js"></script>
    <script src="/frontend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js"></script>

    <!-- Fancybox JS -->
    <script src="/frontend/assets/plugins/fancybox/fancybox.umd.js"></script>
    <script src="{{ asset('backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>
    <script src="{{ asset('/frontend/assets/js/custom/home/vehicle-details.js') }}"></script>
    @endpush
