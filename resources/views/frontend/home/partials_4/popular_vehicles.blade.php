<!-- Top Feature Yacht -->
<section class="top-features-yachts">
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-01.png') }}" class="anchor-img" alt="Img">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-03.png') }}" class="design-round" alt="Img">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-01.png') }}" alt="Bg">
    </div>
    <div class="container">
        <div class="sec-title">
            <h4>Select From Professional Charter Companies</h4>
        </div>
        <div class="charter-company-slider owl-carousel">
            <div class="charter-company-logo">
                <span><img src="/frontend/assets/img/icons/charter-company-01.svg" alt="Icon"></span>
            </div>
            <div class="charter-company-logo">
                <span><img src="/frontend/assets/img/icons/charter-company-02.svg" alt="Icon"></span>
            </div>
            <div class="charter-company-logo">
                <span><img src="/frontend/assets/img/icons/charter-company-03.svg" alt="Icon"></span>
            </div>
            <div class="charter-company-logo">
                <span><img src="/frontend/assets/img/icons/charter-company-04.svg" alt="Icon"></span>
            </div>
            <div class="charter-company-logo">
                <span><img src="/frontend/assets/img/icons/charter-company-05.svg" alt="Icon"></span>
            </div>
        </div>
        <div class="top-rated-yachts">
            <div class="row align-items-center">
                <div class="col-lg-4">
                    <div class="section-header-two">
                        <h2>{{ $section['section_title'] ?? "" }}</h2>
                        <p>{{ $section['section_label'] ?? "" }}</p>
                        <div class="owl-nav slide-nav-1 nav-control"></div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="top-rated-yachts-slider owl-carousel">
                        @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                        @foreach ($section['section_content'] as $yacht)
                        <div class="top-rated-card">
                            <div class="rated-yacht-img slide-card-images">
                                <div class="image-slider owl-carousel">
                                    @if(!empty($yacht['multiple_vehicle_images']) && count($yacht['multiple_vehicle_images']) > 0)
                                    @foreach ($yacht['multiple_vehicle_images'] as $image)
                                    <div class="slide-images">
                                        <a href="{{ route('vehicleDetails', $yacht['slug']) }}">
                                            <img src="{{ $image }}" class="img-fluid" alt="img">
                                        </a>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                                @if($yacht['is_featured'] == 1)
                                <div class="img-top-ribbon">
                                    <span class="ribbon-text bg-danger">Featured</span>
                                </div>
                                @elseif($yacht['is_top_rated'] == 1)
                                <div class="img-top-ribbon">
                                    <span class="ribbon-text bg-danger">Featured</span>
                                </div>
                                @endif
                            </div>
                            @php
                                $filledStars = floor($yacht['rating']);
                                $emptyStars = 5 - $filledStars;
                            @endphp
                            <div class="rated-yacht-content">
                                <div class="yacht-content-head">
                                    <div class="head-items-left">
                                        <h4><a href="{{ route('vehicleDetails', $yacht['slug']) }}">{{ $yacht['name'] ?? "" }}</a></h4>
                                        <span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>{{ $yacht['location'] ?? "" }}</span>
                                    </div>
                                    <div class="head-items-right">
                                        <div class="rated-star">
                                            @for($i = 0; $i < $filledStars; $i++)
                                            <i class="bx bxs-star filled"></i>
                                            @endfor
                                            @for($i = 0; $i < $emptyStars; $i++)
                                            <i class="bx bxs-star"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="yacht-content-body">
                                    <ul class="yacht-features-info">
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-01.svg" alt="Img"></span>
                                            <h6>People <span> : {{ $yacht['passenger_capacity'] ?? 0 }}</span></h6>
                                        </li>
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
                                            <h6>Length <span> : 4.6m</span></h6>
                                        </li>
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-03.svg" alt="Img"></span>
                                            <h6>Fuel <span> : {{ $yacht['fuel_type'] ?? "" }}</span></h6>
                                        </li>
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
                                            <h6>Build <span> : {{ $yacht['year'] ?? "" }}</span></h6>
                                        </li>
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-05.svg" alt="Img"></span>
                                            <h6>Engine <span> : MTU</span></h6>
                                        </li>
                                        <li>
                                            <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
                                            <h6>Cabins <span> :4</span></h6>
                                        </li>
                                    </ul>
                                </div>
                                @php    
                                    $prices = array_slice($yacht['price'][0], 0, 1);
                                @endphp
                                <div class="yacht-content-footer">
                                    @foreach($prices as $price_type => $price_val)
                                    <p>From <span>{{ $data['currency'] }}{{ $price_val }} </span> /{{ ucfirst($price_type) }}</p>
                                    @endforeach
                                    <div class="yacht-book-btn">
                                        <a href="javascript:void(0);" class="yacht-user-img"><img src="{{ $yacht['avatar_image'] }}" alt="Img"></a>
                                        <a href="{{ route('vehicleDetails', $yacht['slug']) }}" class="btn btn-secondary">Book Now</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Top Feature Yacht -->