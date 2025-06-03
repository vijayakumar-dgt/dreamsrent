@php 
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(1, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- Popular Section -->
<section class="section popular-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <!-- Heading title-->
                <div class="section-heading heading-three mx-auto" data-aos="fade-down">
                    <h2>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "" }}</span></h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
                <!-- /Heading title -->
                <div class="row">
                    @if(!empty($sectionContent) && count($sectionContent) > 0)
                    @foreach ($sectionContent as $vehicle)
                    <div class="col-lg-6">
                        <div class="listing-item bike-list">											
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    @if(!empty($vehicle['multiple_vehicle_images']) && count($vehicle['multiple_vehicle_images']) > 0)
                                    @foreach($vehicle['multiple_vehicle_images'] as $image)
                                    <div class="slide-images">
                                        <a href="{{ route('vehicleDetails', $vehicle['slug']) }}">
                                            <img src="{{ $image }}" class="img-fluid" alt="img">
                                        </a>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                                <div class="fav-item justify-content-start">
                                    <a href="javascript:void(0)" class="author-img">
                                        <img src="{{ $vehicle['avatar_image'] }}" alt="author">
                                    </a>								
                                </div>	
                            </div>										
                            @php
                              $filledStars = floor($vehicle['rating']);
                              $emptyStars = 5 - $filledStars;                                
                            @endphp
                            <div class="listing-content">
                                <div class="listing-features d-flex justify-content-between">
                                    <div class="list-rating">												  
                                        <div class="list-ratings">							
                                            @for ($i = 0; $i < $filledStars; $i++)						
                                            <i class="fas fa-star filled"></i>
                                            @endfor
                                            @for ($i = 0; $i < $emptyStars; $i++)						
                                            <i class="fas fa-star"></i>
                                            @endfor
                                            <span>{{ $vehicle['total_review'] }} Reviews</span>
                                        </div>
                                        <h3 class="listing-title">
                                            <a href="{{ route('vehicleDetails', $vehicle['slug']) }}">{{ $vehicle['name'] ?? "" }}</a>
                                        </h3>					
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-01.svg') }}" alt="img"></span>
                                            <p>{{ $vehicle['break_type'] ?? "" }}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-02.svg') }}" alt="img"></span>
                                            <p>{{ round($vehicle['mileage']) }} Km/L</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-03.svg') }}" alt="img"></span>
                                            <p>{{ $vehicle['fuel_type'] ?? "" }}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-04.svg') }}" alt="img"></span>
                                            <p>{{ $vehicle['tube_type'] ?? "" }}</p>
                                        </li>
                                    </ul>
                                </div>	
                                @php    
                                    $prices = array_slice($vehicle['price'][0], 0, 1);
                                @endphp
                                <div class="listing-button">
                                    @foreach($prices as $price_type => $price_val)
                                    <div class="listing-price">
                                        <h6>{{ $data['currency'] }}{{ $price_val }} <span>/ {{ ucfirst($price_type) }}</span></h6>
                                    </div>
                                    @endforeach
                                    <div class="d-flex align-items-center">
                                        @auth
                                        <a href="javascript:void(0)" class="fav-icon wishlist-icon @if($vehicle['wishlist'] == 1) selected @endif" data-id="{{ $vehicle['id'] }}">
                                            <i class="feather-heart"></i>
                                        </a>
                                        @endauth		
                                        <a href="{{ route('vehicleDetails', $vehicle['slug']) }}" class="btn btn-order">Book Now</a>
                                    </div>
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
    <div class="bike-bg">
        <img src="{{ asset('frontend/assets/img/bg/bike-bg.png') }}" class="img-fluid" alt="img">
    </div>
</section>
<!-- /Popular Section -->