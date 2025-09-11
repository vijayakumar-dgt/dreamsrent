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
                        <ul class="nav listing-buttons gap-3" aria-label="Vehicle brands">
                            @foreach($vehicleBrand as $brand)
                                <li class="nav-item">
                                    <a
                                        class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        {{ $loop->first ? 'aria-selected=true' : 'aria-selected=false' }}
                                        role="tab"
                                        data-bs-toggle="tab"
                                        href="#tab_{{ $brand->id ?? '' }}"
                                        id="tab_{{ $brand->id ?? '' }}-tab"
                                        aria-controls="tab_{{ $brand->id ?? '' }}">
                                        <span>
                                            <img src="{{ uploadedAsset($brand->brand_icon) }}" alt="{{ ucfirst($brand->brand_name ?? '') }} logo" class="home-brand-img">
                                        </span>
                                        {{ ucfirst($brand->brand_name) }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="tab-content">
                @if(!empty($vehicleBrand) && count($vehicleBrand) > 0)
                @foreach($vehicleBrand as $brand)
                <div class="tab-pane @if($loop->first) active show @endif" id="tab_{{ $brand->id ?? '' }}">
                    <div class="row">
                        @php
                        $vehicles = collect($section['section_content'])->where('brand_id', $brand->id)->take(6);
                        @endphp
                        @if(!empty($vehicles) && count($vehicles) > 0)
                        @foreach($vehicles as $vehicle)
                        <!-- col -->
                        <div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
                            <div class="listing-item">
                                <div class="listing-img">
                                    <div class="img-slider owl-carousel">
                                        @foreach($vehicle['multiple_vehicle_images'] as $image)
                                        <div class="slide-images">
                                            <a href="{{ route('vehicleDetails', $vehicle['slug']) }}">
                                                <img src="{{ $image }}" class="img-fluid" alt="{{ ucfirst($vehicle['name'] ?? '') }} - Vehicle">
                                            </a>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="fav-item justify-content-end">
                                        <span class="img-count"><i class="feather-image"></i>{{ count($vehicle['multiple_vehicle_images']) }}</span>
                                        @if(Auth::guard('web')->check())
                                        <button type="button" class="fav-icon wishlist-icon {{ $vehicle['wishlist'] ? 'selected' : '' }}" data-id="{{ $vehicle['id'] }}"><i class="feather-heart"></i></button>
                                        @endif
                                    </div>
                                    <span class="featured-text">{{ ucfirst($vehicle['brand'] ?? "") }}</span>
                                </div>
                                <div class="listing-content">
                                    <div class="listing-features d-flex align-items-end justify-content-between">
                                        <div class="list-rating">
                                            <button type="button" class="author-img btn border-0">
                                                <img src="{{ $vehicle['avatar_image'] ?? '' }}" alt="" role="presentation">
                                            </button>
                                            <h3 class="listing-title">
                                                <a href="{{ route('vehicleDetails', $vehicle['slug']) }}">{{ ucfirst($vehicle['name'] ?? "") }}</a>
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
                                    </div>
                                    <div class="listing-details-group">
                                        <ul>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="{{ $vehicle['transmission'] ?? '' }} Transmission"></span>
                                                <p>{{ $vehicle['transmission'] ?? "" }}</p>
                                            </li>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="Mileage: {{ $vehicle['mileage'] ? round($vehicle['mileage']) : '' }} KM"></span>
                                                <p>{{ $vehicle['mileage'] ? round($vehicle['mileage']) : "" }} KM</p>
                                            </li>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Fuel Type: {{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : '' }}"></span>
                                                <p>{{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : "" }}</p>
                                            </li>
                                        </ul>
                                        <ul>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/door-icon.svg') }}" alt="{{ $vehicle['num_doors'] ?? '' }} Doors"></span>
                                                <p>{{ $vehicle['num_doors'] ?? "" }}</p>
                                            </li>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="Build Year {{ $vehicle['year'] ?? '' }}"></span>
                                                <p>{{ $vehicle['year'] ?? "" }}</p>
                                            </li>
                                            <li>
                                                <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons: {{ __('web.home.persons') }}"></span>
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
                                        <a href="{{ route('vehicleDetails', $vehicle['slug']) }}" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>{{ __('web.home.rent_now') }}</a>
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
                        @endforeach
                        <!-- /col -->
                        @else
                            <div class="col-12">
                                <p class="text-center">{{ __('web.home.no_vehicles_found_for') }} {{ $brand->brand_name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
            </div>
        </div>
    </section>
    <!-- /Popular Services -->
