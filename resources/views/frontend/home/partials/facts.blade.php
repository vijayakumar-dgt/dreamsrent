<section class="section facts-number">
    <div class="facts-left">
        <img src="{{ asset('frontend/assets/img/bg/facts-left.png') }}" class="img-fluid" alt="facts left">
    </div>
    <div class="facts-right">
        <img src="{{ asset('frontend/assets/img/bg/facts-right.png') }}" class="img-fluid" alt="facts right">
    </div>
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading" data-aos="fade-down">
            <h2 class="title text-white">{{ $section['section_title'] }}</h2>
            <p class="description">{{ $section['section_label'] }}</p>
        </div>
        <!-- /Heading title -->
        <div class="counter-group">
            <div class="row">
                @foreach($section['facts_content'] as $fact)
                     @switch($fact['key'])
                        @case('happy_customers')
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down">
                            <div class="count-group flex-fill">
                                <div class="customer-count d-flex align-items-center">
                                    <div class="count-img">
                                        <img src="{{ asset('frontend/assets/img/icons/bx-heart.svg') }}" alt="Icon">
                                    </div>
                                    <div class="count-content">
                                        <h4><span class="counterUp">{{ $fact['value'] }}</span>+</h4>
                                        <p>{{ __('web.home.happy_customers') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break
                        @case('vehicle_count')
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down">
                            <div class="count-group flex-fill">
                                <div class="customer-count d-flex align-items-center">
                                    <div class="count-img">
                                        <img src="{{ asset('frontend/assets/img/icons/bx-car.svg') }}" alt="Icon">
                                    </div>
                                    <div class="count-content">
                                        <h4><span class="counterUp">{{ $fact['value'] }}</span>+</h4>
                                        <p>{{ __('web.home.vehicle_count') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break
                        @case('location_count')
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down">
                            <div class="count-group flex-fill">
                                <div class="customer-count d-flex align-items-center">
                                    <div class="count-img">
                                        <img src="{{ asset('frontend/assets/img/icons/bx-headphone.svg') }}" alt="Icon">
                                    </div>
                                    <div class="count-content">
                                        <h4><span class="counterUp">{{ $fact['value'] }}</span>+</h4>
                                        <p>{{ __('web.home.vehicle_center') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break
                        @case('total_km')
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down">
                            <div class="count-group flex-fill">
                                <div class="customer-count d-flex align-items-center">
                                    <div class="count-img">
                                        <img src="{{ asset('frontend/assets/img/icons/bx-history.svg') }}" alt="Icon">
                                    </div>
                                    <div class="count-content">
                                        <h4><span class="counterUp">{{ $fact['value'] }}</span>+</h4>
                                        <p>{{ __('web.home.total_km') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @break
                    @endswitch
                @endforeach
            </div>
        </div>
    </div>
</section>
