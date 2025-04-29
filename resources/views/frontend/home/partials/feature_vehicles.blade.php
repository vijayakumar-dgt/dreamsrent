<section class="section popular-services">
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading"  data-aos="fade-down">
            <h2>{{ $section['section_title'] }}</h2>
            <p>{{ $section['section_label'] }} </p>
        </div>
        <!-- /Heading title -->
        <div class="row">
            <div class="popular-slider-group">
                <div class="owl-carousel rental-deal-slider owl-theme">
                    <!-- owl carousel item -->
                    @foreach($section['section_content'] as $vehicle)
                    <div class="rental-car-item">
                        <div class="listing-item mb-0">
                            <div class="listing-img">
                                <a href="/vehicle-details/{{ $vehicle['slug'] }}">
                                    <img src="{{ $vehicle['vehicle_image'] }}" class="img-fluid" alt="{{ ucfirst($vehicle['name']) }}">
                                </a>
                                <div class="fav-item justify-content-end">
                                    @if(Auth::guard('web')->check())
                                    <a href="javascript:void(0)" class="fav-icon wishlist-icon {{ $vehicle['wishlist'] ? 'selected' : '' }}" data-id="{{ $vehicle['id'] }}">
                                        <i class="feather-heart"></i>
                                    </a>
                                    @endif
                                </div>
                            </div>
                            @php
                            $prices = array_slice($vehicle['price'][0], 0, 1);
                            @endphp
                            <div class="listing-content">
                                <div class="listing-features">
                                    <div class="fav-item-rental">
                                        @foreach($prices as $price_type => $price_val)
                                        <div class="featured-text">{{ $data['currency'] }}{{ $price_val }}<span>/ {{ ucfirst($price_type) }}</span></div>
                                        @endforeach
                                    </div>
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
                                    <h3 class="listing-title">
                                        <a href="/vehicle-details/{{ $vehicle['slug'] }}">{{ ucfirst($vehicle['name']) }}</a>
                                    </h3>
                                </div>
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="{{ ucfirst($vehicle['transmission'] ?? '') }}"></span>
                                            <p>{{ ucfirst($vehicle['transmission'] ?? "")}}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="{{ $vehicle['mileage'] ? round($vehicle['mileage']) : '' }} KM"></span>
                                            <p>{{ $vehicle['mileage'] ? round($vehicle['mileage']) : "" }} KM</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="{{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : '' }}"></span>
                                            <p>{{ $vehicle['fuel_type'] ? ucfirst($vehicle['fuel_type']) : ""}}</p>
                                        </li>
                                    </ul>
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/door-icon.svg') }}" alt="Power"></span>
                                            <p>{{ $vehicle['num_airbags'] ?? ""}}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="{{ $vehicle['year'] ?? '' }}"></span>
                                            <p>{{ $vehicle['year']}}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="{{ __('web.home.persons') }}"></span>
                                            <p>{{ $vehicle['passenger_capacity'] ?? 0 }} {{ __('web.home.persons') }}</p>
                                        </li>
                                    </ul>
                                </div>
                                <div class="listing-button">
                                    <a href="/vehicle-details/{{ $vehicle['slug'] }}" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>{{ __('web.home.rent_now')  }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <!-- /owl carousel item -->
                </div>
            </div>
        </div>
        <!-- View More -->
        <div class="view-all text-center" data-aos="fade-down">
            <a href="/vehicles" class="btn btn-view d-inline-flex align-items-center">{{ __('web.home.view_all_cars') }} <span><i class="feather-arrow-right ms-2"></i></span></a>
        </div>
        <!-- View More -->
    </div>
</section>
