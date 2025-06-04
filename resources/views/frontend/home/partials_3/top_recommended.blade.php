@php 
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(1, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- Recommended Section -->
<section class="section recommend-section">
    <div class="container-fluid">	
        <!-- Heading title-->
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="section-heading heading-three mx-auto" data-aos="fade-down">
                    <h2>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "s" }}</span></h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
            </div>
        </div>
        <!-- /Heading title -->

        <div class="row">
            <div class="col-md-12">
                @if(!empty($sectionContent) && count($sectionContent) > 0)
                <div class="recommend-slider owl-carousel">
                   @foreach($sectionContent as $vehicle)
                    <div class="listing-item bike-list">											
                        <div class="listing-img">
                            <a href="{{ route('vehicleDetails', $vehicle['slug']) }}">
                                <img src="{{ $vehicle['vehicle_image'] }}" class="img-fluid" alt="img">
                            </a>
                        </div>			
                        @php
                            $filledStar = $vehicle['rating'] ?? 0;
                            $emptyStar = 5 - $filledStar;
                        @endphp							
                        <div class="listing-content">
                            <div class="listing-features d-flex justify-content-between">
                                <div class="list-rating">												  
                                    <div class="list-ratings">	
                                        @for ($i = 0; $i < $filledStar; $i++)						
                                        <i class="fas fa-star filled"></i>
                                        @endfor
                                        @for ($i = 0; $i < $emptyStar; $i++)						
                                        <i class="fas fa-star"></i>
                                        @endfor
                                        <span>{{ $vehicle['total_review'] ?? 0}} {{ __('web.home.reviews') }}</span>
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
                                        <p>{{ round($vehicle['mileage']) ?? "" }} Km/L</p>
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
                                <div class="listing-price">
                                    @foreach($prices as $price_type => $price_val)
                                    <h6>{{ $data['currency'] }}{{ $price_val }} <span>/ {{ ucfirst($price_type) }}</span></h6>
                                    @endforeach
                                </div>
                                <div class="d-flex align-items-center">
                                    @auth
                                    <a href="javascript:void(0)" class="fav-icon wishlist-icon @if($vehicle['wishlist'] == 1) selected @endif" data-id="{{ $vehicle['id'] }}">
                                        <i class="feather-heart"></i>
                                    </a>		
                                    @endauth
                                    <a href="{{ route('vehicleDetails', $vehicle['slug']) }}" class="btn btn-order">{{ __('web.home.book_now') }}</a>
                                </div>
                            </div>	
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="col-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
            </div>
        </div>

    </div>
</section>
<!-- /Recommended Section -->