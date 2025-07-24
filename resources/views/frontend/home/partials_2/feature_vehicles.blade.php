    <section class="popular-section-four">
        <div class="container">
            <!-- Section Header -->
            <div class="section-heading heading-four" data-aos="fade-down">
                <h2>{{ $section['section_title'] }}</h2>
                <p>{{ $section['section_label'] }}</p>
            </div>
            <!-- /Section Header -->
            @if(empty($section['section_content']) || count($section['section_content']) == 0)
            <div class="col-12 aos">
                <span class="text-center text-white">{{ __('web.common.empty_table') }}</span>
            </div>
            @endif
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            <div class="car-slider owl-carousel">
                @foreach($section['section_content'] as $content)
                <!-- Car Item -->
                @php
                $firstPrice = $content['price'][0] ?? [];
                $priceType = array_key_first($firstPrice);
                $priceValue = $firstPrice[$priceType] ?? '';
                @endphp
                <div class="car-item">
                    <h6>{{ $content['brand'] ? strtoupper($content['brand']) : "" }}</h6>
                    <h2 class="display-1">{{ ucfirst($content['name']) }}</h2>
                    <div class="car-img">
                        <img src="{{ $content['vehicle_image'] }}" alt="img" class="img-fluid">
                        <div class="amount-icon">
                            <span class="day-amt">
                                <p>{{__('web.home.starts_from')}}</p>
                                <h6>{{ getDefaultCurrencySymbol() }}{{ $priceValue }} <span>
                                        /{{ ucfirst($priceType) }}</span></h6>
                            </span>
                        </div>
                    </div>
                    <div class="spec-list">
                        <span><img src="{{ asset('/frontend/assets/img/icons/spec-01.svg') }}"
                                alt="img">{{ ucfirst($content['transmission']) }}</span>
                        <span><img src="{{ asset('/frontend/assets/img/icons/spec-03.svg') }}"
                                alt="img">{{ round($content['mileage']) }}</span>
                        <span><img src="{{ asset('/frontend/assets/img/icons/spec-05.svg') }}"
                                alt="img">{{ ucfirst($content['fuel_type']) }}</span>
                        <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}"
                                alt="img">{{ $content['passenger_capacity'] }} {{ __('web.home.persons') }}</span>
                    </div>
                    <a href="/vehicle-details/{{ $content['slug'] }}"
                        class="btn btn-primary">{{ __('web.home.rent_now') }}</a>
                </div>
                <!-- /Car Item -->
                @endforeach
            </div>
            @endif
        </div>
    </section>