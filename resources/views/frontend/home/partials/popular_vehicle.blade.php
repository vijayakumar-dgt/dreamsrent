
<!-- Popular Services -->
<section class="section popular-services popular-explore">
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading" data-aos="fade-down">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <!-- /Heading title -->
        <div class="row justify-content-center">
            <div class="col-lg-12" data-aos="fade-down">
                <div class="listing-tabs-group">
                    <ul class="nav listing-buttons gap-3" data-bs-tabs="tabs">
                        @foreach($vehicleBrand as $brand)
                        <li>
                            <a class="@if($loop->first) active @endif" @if($loop->first) aria-current="true" @endif data-bs-toggle="tab" href="#tab_{{ $brand->brand_name ?? "" }}">
                                <span>
                                    <img src="{{ uploadedAsset($brand->brand_icon) }}" alt="{{ $brand->brand_name ?? '' }}">
                                </span>
                                {{ $brand->brand_name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content">
            @foreach($vehicleBrand as $brand)
            <div class="tab-pane @if($loop->first) active show @endif" id="tab_{{ $brand->brand_name ?? '' }}">
                <div class="row">
                    @php
                       $vehicles = collect($section['section_content'])->where('brand_id', $brand->id)->take(6);
                    @endphp
                    @forelse($vehicles as $vehicle)
                    <!-- col -->
                    <div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
                        <div class="listing-item">
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    @foreach($vehicle['multiple_vehicle_images'] as $image)
                                    <div class="slide-images">
                                        <a href="/vehicle-details/{{ $vehicle['slug'] }}">
                                            <img src="{{ $image }}" class="img-fluid" alt="{{ $vehicle['name'] ?? '' }}">
                                        </a>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>{{ count($vehicle['multiple_vehicle_images']) }}</span>
                                    @if(Auth::guard('web')->check())
                                    <a href="javascript:void(0)" class="fav-icon wishlist-icon {{ $vehicle['wishlist'] ? 'selected' : '' }}" data-id="{{ $vehicle['id'] }}">
                                        <i class="feather-heart"></i>
                                    </a>
                                    @endif
                                </div>
                                <span class="featured-text">{{ $vehicle['brand'] ?? "" }}</span>
                            </div>
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ $vehicle['avatar_image'] ?? '' }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="/vehicle-details/{{ $vehicle['slug'] }}">{{ $vehicle['name'] ?? "" }}</a>
                                        </h3>
                                        <div class="list-rating">
                                            @php
                                                $rating = $vehicle['rating'] ?? 0;
                                                $rating = is_numeric($rating) ? round($rating) : 0;
                                                $filledStars = min($rating, 5);
                                                $emptyStars = 5 - $filledStars;
                                            @endphp

                                            @for($i = 0; $i < $filledStars; $i++)
                                                <i class="fas fa-star filled"></i>
                                            @endfor

                                            @for($i = 0; $i < $emptyStars; $i++)
                                                <i class="fas fa-star"></i>
                                            @endfor

                                            <span>({{ number_format($rating, 1) }}) {{ $vehicle['review_count'] ?? 0 }} {{ __('web.common.reviews') }}</span>
                                        </div>
                                    </div>
                                    <div class="list-km d-none">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.2m</span>
                                    </div>
                                </div>
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="{{ $vehicle['transmission'] ?? '' }}"></span>
                                            <p>{{ $vehicle['transmission'] ?? "" }}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="{{ $vehicle['mileage'] ? round($vehicle['mileage']) : '' }} KM"></span>
                                            <p>{{ $vehicle['mileage'] ? round($vehicle['mileage']) : "" }} KM</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="{{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : '' }}"></span>
                                            <p>{{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : "" }}</p>
                                        </li>
                                    </ul>
                                    <ul>
                                        <!-- <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li> -->
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="{{ $vehicle['year'] ?? '' }}"></span>
                                            <p>{{ $vehicle['year'] ?? "" }}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="{{ __('web.home.persons') }}"></span>
                                            <p>{{ $vehicle['passenger_capacity'] ?? 0 }} {{ __('web.home.persons') }}</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>{{ $vehicle['location'] ?? "" }}
                                    </div>
                                    @php
                                    $prices = array_slice($vehicle['price'][0], 0, 1);
                                    @endphp
                                    @foreach($prices as $price_type => $price_val)
                                    <div class="listing-price">
                                        <h6>{{ $data['currency'] }}{{ $price_val }} <span>/ {{ ucfirst($price_type) }}</span></h6>
                                    </div>
                                    @endforeach
                                </div>
                                <div class="listing-button">
                                    <a href="/vehicle-details/{{ $vehicle['slug'] }}" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>{{ __('web.home.rent_now') }}</a>
                                </div>
                            </div>
                            <div class="feature-text">
                                @if($vehicle['is_featured'])
                                <span class="bg-danger">Featured</span>
                                @elseif($vehicle['is_top_rated'])
                                <span class="bg-warning">{{ __('web.common.top_rated') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <!-- /col -->
                     @empty
                        <div class="col-12">
                            <p class="text-center">{{ __('web.home.no_vehicles_found_for') }} {{ $brand->brand_name }}</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
<!-- /Popular Services -->
