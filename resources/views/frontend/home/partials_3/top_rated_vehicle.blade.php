@php 
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(3, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- Featured Services -->
<section class="section features-section">		
    <div class="container">	
        <div class="row">
            <div class="col-md-12">

                <!-- Heading title-->
                <div class="section-heading heading-three mx-auto" data-aos="fade-down">
                    <h2>{{ $titleMain ?? "" }} <span> {{ $titleLastPart ?? "" }}</span></h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
                <!-- /Heading title -->
                @if(!empty($sectionContent) && count($sectionContent) > 0)
                <div class="bike-feature-slider nav-center owl-carousel">
                    @foreach($sectionContent as $vehicle)
                    <div class="item">
                        <div class="listing-item bike-list">											
                            <div class="listing-img">
                                <div class="image-slider owl-carousel">
                                    @if(!empty($vehicle['multiple_vehicle_images']) && count($vehicle['multiple_vehicle_images']) > 0)
                                    @foreach($vehicle['multiple_vehicle_images'] as $image)
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ $image }}" class="img-fluid" alt="img">
                                        </a>
                                    </div>
                                    @endforeach
                                    @endif
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>{{ count($vehicle['multiple_vehicle_images']) }}</span>
                                    <a href="javascript:void(0)" class="author-img">
                                        <img src="{{ asset('frontend/assets/img/profiles/avatar-04.jpg') }}" alt="author">
                                    </a>								
                                </div>	
                            </div>									
                            @php 
                            $filledStars = floor($vehicle['rating']);
                            $emptyStars = 5 - $filledStars;
                            @endphp	
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-center justify-content-between">
                                    <div class="list-rating">												  
                                        <div class="list-ratings">	
                                            @for ($i = 0; $i < $filledStars; $i++)						
                                            <i class="fas fa-star filled"></i>
                                            @endfor
                                            @for ($i = 0; $i < $emptyStars; $i++)						
                                            <i class="fas fa-star"></i>
                                            @endfor
                                            <span>{{ $vehicle['rating'] }} Reviews</span>
                                        </div>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">{{ $vehicle['name'] ?? "" }}</a>
                                        </h3>					
                                    </div>
                                    {{-- <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.6m</span>
                                    </div> --}}
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-01.svg') }}" alt="img"></span>
                                            <p>Drum</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-02.svg') }}" alt="img"></span>
                                            <p>{{ round($vehicle['mileage']) }} Km</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-03.svg') }}" alt="img"></span>
                                            <p>{{ $vehicle['fuel_type'] ?? "" }}</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/bike-icon-04.svg') }}" alt="img"></span>
                                            <p>Tubeless</p>
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
                                        @if($vehicle['wishlist'] == 1)
                                        <a href="javascript:void(0)" class="fav-icon selected" data-id="{{ $vehicle['id'] }}">
                                            <i class="feather-heart"></i>
                                        </a>		
                                        @else
                                        <a href="javascript:void(0)" class="fav-icon">
                                            <i class="feather-heart"></i>
                                        </a>		
                                        @endif
                                        <a href="listing-details.html" class="btn btn-order">Book Now</a>
                                    </div>
                                </div>
                                @if($vehicle['is_featured'] == 1)
                                <div class="feature-text">
                                    <span class="bg-danger">Featured</span>
                                </div>
                                @elseif($vehicle['is_top_rated'] == 1)
                                <div class="feature-text">
                                    <span class="bg-warning">Top Rated</span>
                                </div>			
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="view-all-btn text-center aos" data-aos="fade-down">
                    <a href="listing-grid.html" class="btn btn-secondary">View all Bikes</a>
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="feature-bg">
        <img src="{{ asset('frontend/assets/img/bg/destination-bg-01.png') }}" class="img-fluid shape-01" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/feature-bg.png') }}" class="img-fluid shape-02" alt="img">
    </div>		
</section>
<!-- /Featured Services -->