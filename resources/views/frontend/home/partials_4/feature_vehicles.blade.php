@php
    $yachts = $section['section_content']['vehicles'] ?? [];
@endphp
<!-- Yacht Offer -->
<section class="yacht-offer-sec">
    <div class="sec-bg">
        <img src="/frontend/assets/img/bg/sec-bg-wave.png" class="wave-bottom" alt="Bg">
    </div>
    <div class="container">
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <div class="yacht-list-cards">
            <div class="row">
                @if(!empty($yachts) && count($yachts) > 0)
                @foreach($yachts as $yacht)
                <div class="col-xl-6">
                    <div class="top-rated-card">
                        <div class="rated-yacht-img slide-card-images">
                            <div class="yacht-image-slider owl-carousel">
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
                        </div>
                        <div class="rated-yacht-content">
                            <div class="yacht-content-head">
                                <div class="head-items-left">
                                    <h4><a href="{{ route('vehicleDetails', $yacht['slug']) }}">{{ $yacht['name'] ?? "" }}</a></h4>
                                    <span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>{{ $yacht['location'] ?? "" }}</span>
                                </div>
                                @php 
                                    $filledStars = floor($yacht['rating']);
                                    $emptyStars = 5 - $filledStars;
                                @endphp
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
                                        <h6>{{ __('web.home.people') }} <span> : {{ $yacht['passenger_capacity'] ?? 0 }}</span></h6>
                                    </li>
                                    <li>
                                        <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
                                        <h6>{{ __('web.home.length') }} <span> : 4.6m</span></h6>
                                    </li>
                                    <li>
                                        <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
                                        <h6>{{ __('web.home.build') }} <span> : {{ $yacht['year'] ?? "" }}</span></h6>
                                    </li>
                                    <li>
                                        <span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
                                        <h6>{{ __('web.home.cabins') }} <span> : 4</span></h6>
                                    </li>
                                </ul>
                            </div>
                            @php    
                                $prices = array_slice($yacht['price'][0], 0, 1);
                            @endphp
                            <div class="yacht-content-footer">
                                @foreach($prices as $price_type => $price_val)
                                <p>{{ __('web.home.from') }} <span>{{ $data['currency'] }}{{ $price_val }}  </span> /{{ ucfirst($price_type) }}</p>
                                @endforeach
                                <div class="yacht-book-btn">
                                    <a href="javascript:void(0);" class="yacht-user-img"><img src="{{ $yacht['avatar_image'] }}" alt="Img"></a>
                                    <a href="{{ route('vehicleDetails', $yacht['slug']) }}" class="btn btn-secondary">{{ __('web.home.book_now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-md-12">
                    <div class="view-more-btn text-center">
                        <a href="{{ route('list') }}" class="btn btn-secondary">{{ __('web.home.view_all_yachts') }}</a>
                    </div>
                </div>
                @else
                <div class="col-md-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- /Yacht Offer -->