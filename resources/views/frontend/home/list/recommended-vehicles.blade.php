<div class="row">
    <div class="col-md-12">
        <div class="details-car-grid">
            <div class="details-slider-heading">
                <h3>{{ __('web.home.you_maybe_interested_in') }}</h3>
            </div>
            <div class="owl-carousel rental-deal-slider details-car owl-theme">
                @if(!empty($data) && count($data) > 0)
                    @foreach($data as $vehicle)
                        <!-- owl carousel item -->
                        <div class="rental-car-item">
                            <div class="listing-item pb-0">
                                <div class="listing-img">
                                    <a href="/vehicle-details/{{ $vehicle['slug'] }}">
                                        <img src="{{ $vehicle['vehicle_image'] }}" class="img-fluid" alt="Audi">
                                    </a>
                                    <div class="fav-item justify-content-end">
                                        @if(Auth::guard('web')->check())
                                            <a href="javascript:void(0)" class="fav-icon wishlist-icon {{ $vehicle['wishlist'] == 1 ? 'selected' : '' }}" data-id="{{ $vehicle['id'] }}">
                                                <i class="feather-heart"></i>
                                            </a>
                                        @endif
                                    </div>
                                    <span class="featured-text">{{ $vehicle['brand'] }}</span>
                                </div>
                                <div class="listing-content">
                                    <div class="listing-features d-flex align-items-end justify-content-between">
                                        <div class="list-rating">
                                            <a href="javascript:void(0)" class="author-img">
                                                <img src="{{ $vehicle['avatar_image'] }}" alt="author">
                                            </a>
                                            <h3 class="listing-title">
                                                <a href="/vehicle-details/{{ $vehicle['slug'] }}">{{ $vehicle['name'] }}</a>
                                            </h3>
                                            @php 
                                                $rating = $vehicle['rating'] ?? 0;
                                                $rating = is_numeric($rating) ? round($rating) : 0;
                                                $emptyStars = 5 - $rating;
                                                $fullStars = $rating;
                                            @endphp
                                            <div class="list-rating">
                                                @for ($i = 0; $i < $fullStars; $i++)
                                                    <i class="fas fa-star filled"></i>
                                                @endfor
                                                @for ($i = 0; $i < $emptyStars; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                                <span>({{ $vehicle['rating'] }}) {{ $vehicle['review_count'] }} {{ __('web.common.reviews') }}</span>
                                            </div>
                                        </div>
                                        <div class="list-km d-none">
                                            <span class="km-count"><img src="/frontend/assets/img/icons/map-pin.svg" alt="author">4.5m</span>
                                        </div>
                                    </div>
                                    <div class="listing-details-group">
                                        <ul>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="Manual"></span>
                                                <p>{{ $vehicle['transmission'] ?? "-" }}</p>
                                            </li>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/car-parts-02.svg" alt="18 KM"></span>
                                                <p>{{ round($vehicle['mileage']) }} KM</p>
                                            </li>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/car-parts-03.svg" alt="Diesel"></span>
                                                <p>{{ $vehicle['fuel_type'] }}</p>
                                            </li>
                                        </ul>
                                        <ul>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/door-icon.svg" alt="Power"></span>
                                                <p>{{ $vehicle['num_airbags'] ? $vehicle['num_airbags'] : "" }}</p>
                                            </li>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/car-parts-05.svg" alt="2018"></span>
                                                <p>{{ $vehicle['year'] }}</p>
                                            </li>
                                            <li>
                                                <span><img src="/frontend/assets/img/icons/car-parts-06.svg" alt="Persons"></span>
                                                <p>{{ $vehicle['passenger_capacity'] }} {{ __('web.user.persons') }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                    @php
                                        $firstPrice = $vehicle['price'][0]; 
                                        $price_type = array_key_first($firstPrice);
                                        $price_val = $firstPrice[$price_type];
                                    @endphp
                                    <div class="listing-location-details">
                                        <div class="listing-price">
                                            <span><i class="feather-map-pin"></i></span>{{ $vehicle['location'] }}
                                        </div>
                                        <div class="listing-price">
                                            <h6>{{ $vehicle['currency'] }}{{ $price_val }} <span>/ {{ $price_type }}</span></h6>
                                        </div>
                                    </div>
                                    <div class="listing-button">
                                        <a href="/vehicle-details/{{ $vehicle['slug'] }}" class="btn btn-order">
                                            <span><i class="feather-calendar me-2"></i></span>{{ __('web.home.rent_now') }}
                                        </a>
                                    </div>
                                </div>
                                @if($vehicle['is_top_rated'] == 1)
                                    <div class="feature-text">
                                        <span class="bg-warning">{{ __('web.home.top_rated') }}</span>
                                    </div>
                                @elseif($vehicle['is_featured'] == 1)
                                    <div class="feature-text">
                                        <span class="bg-danger">{{ __('web.home.featured') }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @endif
                <!-- /owl carousel item -->
            </div>
        </div>
    </div>
</div>