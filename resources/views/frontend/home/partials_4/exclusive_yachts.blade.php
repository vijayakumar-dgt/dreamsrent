@php
    $sectionContent = $section['section_content'] ?? [];
    $yachts = $sectionContent[0]['vehicles'] ?? [];
@endphp
<!-- More Boats Info -->
<section class="more-boats-info-sec">
    <div class="container-fluid">
        <div class="sec-bottom-info">
            <div class="row bottom-text-row">
                @if(!empty($yachts) && count($yachts) > 0)
                @foreach($yachts as $k => $yacht)
                @php
                    $filledStars = floor($yacht['rating']);
                    $emptyStars = 5 - $filledStars;
                @endphp
                <div class="col-xl-3 col-lg-6">
                    <div class="bottom-ship-info-card">
                        <div class="hover-ship-info w-100">
                            <h4>{{ $yacht['name'] ?? "" }}</h4>
                            <div class="address-info">
                                <span><i class="bx bx-map"></i>{{ $yacht['location'] ?? "" }}</span>
                                <div class="rated-star">
                                    @for($i = 0; $i < $filledStars; $i++)
                                    <i class="bx bxs-star filled"></i>
                                    @endfor
                                    @for($i = 0; $i < $emptyStars; $i++)
                                    <i class="bx bxs-star"></i>
                                    @endfor
                                    <span>{{ $yacht['total_review'] ?? 0 }} {{ __('web.common.reviews') }}</span>
                                </div>
                            </div>
                            <ul class="ship-features">
                                <li>{{ __('web.home.cabins') }} : 4</li>
                                <li>{{ __('web.home.people') }} : 8</li>
                                <li>{{ __('web.home.length') }} : 4.6</li>
                            </ul>
                            @php    
                                $prices = array_slice($yacht['price'][0], 0, 1);
                            @endphp
                            <div class="ship-pricing">
                                @foreach($prices as $price_type => $price_val)
                                <h5>{{ __('web.home.from') }} <span> {{ $data['currency'] }}{{ $price_val }}  </span> /{{ ucfirst($price_type) }}</h5>
                                @endforeach
                                <a href="{{ route('vehicleDetails', $yacht['slug']) }}" class="btn btn-primary btn-buy">{{ __('web.home.book_now') }}</a>
                            </div>
                        </div>
                        
                    </div>
                </div>
                @endforeach
                @else
                <div class="col-md-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
<!-- /More Boats Info -->