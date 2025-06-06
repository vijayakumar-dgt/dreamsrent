    <section class="car-section">
        <div class="container">
            <div class="section-heading heading-four" data-aos="fade-down">
                <h2>{{ $section['section_title'] }}</h2>
                <p>{{ $section['section_label']  }}</p>
            </div>
            <div class="row">
                <!-- Car List -->
                @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                @foreach($section['section_content'] as $content)
                <div class="col-xl-4 col-lg-6 col-md-6">
                    <div class="listing-item listing-item-two theme2-popularcar">
                        <div class="listing-img">
                            <div class="img-slider owl-carousel">
                                @if(!empty($content['multiple_vehicle_images'] &&
                                count($content['multiple_vehicle_images']) > 0))
                                @foreach($content['multiple_vehicle_images'] as $image)
                                <div class="slide-images">
                                    <a href="/vehicle-details/{{ $content['slug'] }}">
                                        <img src="{{ $image }}" class="img-fluid popular-vehicle-img" alt="">
                                    </a>
                                </div>
                                @endforeach
                                @endif
                            </div>
                            <div class="fav-item">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="featured-text">{{ ucfirst($content['brand'] ?? "") }}</span>
                                </div>
                                @if(current_user() != null)
                                <a href="javascript:void(0)"
                                    class="fav-icon wishlist-icon {{ $content['wishlist'] ? 'selected' : '' }}"
                                    data-id="{{ $content['id'] }}">
                                    <i class="feather-heart"></i>
                                </a>
                                @endif
                            </div>
                            <span class="location"><i
                                    class="bx bx-map me-1"></i>{{ ucfirst($content['location'] ?? "") }}</span>
                        </div>
                        <div class="listing-content">
                            <div class="listing-features d-flex align-items-center justify-content-between">
                                <div class="list-rating">
                                    <h3 class="listing-title">
                                        <a
                                            href="{{ route('vehicleDetails',$content['slug']) }}">{{ ucfirst($content['name'] ?? "") }}</a>
                                    </h3>
                                    <div class="list-rating">
                                        @php
                                        $rating = $content['rating'] ?? 0;
                                        $rating = is_numeric($rating) ? round($rating) : 0;
                                        $filledStars = min($rating, 5);
                                        $emptyStars = 5 - $filledStars;
                                        @endphp

                                        @for($i = 0; $i < $filledStars; $i++) <i class="fas fa-star filled"></i>
                                            @endfor

                                            @for($i = 0; $i < $emptyStars; $i++) <i class="fas fa-star"></i>
                                                @endfor

                                                <span>({{ number_format($rating, 1) }})
                                                    {{ $content['review_count'] ?? 0 }}
                                                    {{ __('web.common.reviews') }}</span>
                                    </div>
                                </div>
                                <div>
                                    @php
                                    $firstPrice = $content['price'][0] ?? [];
                                    $priceType = array_key_first($firstPrice);
                                    $priceValue = $firstPrice[$priceType] ?? '';
                                    @endphp
                                    <h4 class="price">{{ getDefaultCurrencySymbol() }}{{ $priceValue }} <span>/
                                            {{ ucfirst($priceType) }}</span></h4>
                                </div>
                            </div>
                            <div class="listing-details-group">
                                <ul>
                                    <li>
                                        <img src="{{ asset('/frontend/assets/img/icons/car-parts-01.svg') }}"
                                            alt="{{ $content['transmission'] ?? '' }}">
                                        <p>{{ $content['transmission'] ?? "" }}</p>
                                    </li>
                                    <li>
                                        <img src="{{ asset('/frontend/assets/img/icons/car-parts-02.svg') }}"
                                            alt="{{ $content['mileage'] ? round($content['mileage']) : '' }} KM">
                                        <p>{{ $content['mileage'] ? round($content['mileage']) : "" }} KM</p>
                                    </li>
                                    <li>
                                        <img src="{{ asset('/frontend/assets/img/icons/car-parts-03.svg') }}" alt="">
                                        <p>{{ ucfirst($content['fuel_type'] ?? "") }}</p>
                                    </li>
                                    <li>
                                        <img src="{{ asset('/frontend/assets/img/icons/car-parts-05.svg') }}"
                                            alt="{{ $content['year'] ?? '' }}">
                                        <p>{{ $content['year'] ?? "" }}</p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
                <!-- /Car List -->
            </div>
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            <div class="view-all-btn text-center aos" data-aos="fade-down">
                <a href="{{ route('list') }}"
                    class="btn btn-secondary d-inline-flex align-items-center">{{ __('web.home.view_all_cars') }}<i
                        class="bx bx-right-arrow-alt ms-1"></i></a>
            </div>
            @endif
        </div>
    </section>