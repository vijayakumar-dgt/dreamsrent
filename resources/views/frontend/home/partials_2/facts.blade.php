    <section class="rental-section-four">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <div class="rental-img">
                        <img src="{{ asset('/frontend/assets/img/about/rent-car.png') }}" alt="img" class="img-fluid">
                        <div class="grid-img">
                            <img src="{{ asset('/frontend/assets/img/about/car-grid.png') }}" alt="img" class="img-fluid">
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="rental-content">
                        <div class="section-heading heading-four text-start" data-aos="fade-down">
                            <h2>{{__('web.home.rent_3_steps')  }}</h2>
                            <p>{{__('web.home.rent_3_steps_content')  }}</p>
                        </div>
                        <div class="step-item d-flex align-items-center">
                            <span class="step-icon bg-primary me-3">
                                <i class="bx bx-calendar-heart"></i>
                            </span>
                            <div>
                                <h5>{{ __('web.home.choose_date_locations') }}</h5>
                                <p>{{ __('web.home.choose_date_locations_content') }}</p>
                            </div>
                        </div>
                        <div class="step-item d-flex align-items-center">
                            <span class="step-icon bg-secondary-100 me-3">
                                <i class="bx bxs-edit-location"></i>
                            </span>
                            <div>
                                <h5>{{ __('web.home.select_pickup_drop_locations') }}</h5>
                                <p>{{ __('web.home.select_pickup_drop_locations_content') }}</p>
                            </div>
                        </div>
                        <div class="step-item d-flex align-items-center">
                            <span class="step-icon bg-dark me-3">
                                <i class="bx bx-coffee-togo"></i>
                            </span>
                            <div>
                                <h5>{{ __('web.home.book_your_vehicle') }}</h5>
                                <p>{{ __('web.home.book_your_vehicle_content') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="count-sec">
                <div class="row row-gap-4" >
                    @if(!empty($section['facts_content']) && count($section['facts_content']) > 0)
                    @foreach($section['facts_content'] as $fact)
                        @switch($fact['key'])
                            @case('happy_customers')
                            <div class="col-lg-3 col-md-6 d-flex">
                                <div class="count-item flex-fill">
                                    <h3><span class="counterUp">{{ $fact['value'] }} </span> +</h3>
                                    <p>{{__('web.home.happy_customers')  }}</p>
                                </div>
                            </div>
                            @break
                            @case('vehicle_count')
                            <div class="col-lg-3 col-md-6 d-flex">
                                <div class="count-item flex-fill">
                                    <h3><span class="counterUp">{{ $fact['value'] }} </span> +</h3>
                                    <p>{{ __('web.home.vehicle_count') }}</p>
                                </div>
                            </div>
                            @break
                            @case('location_count')
                            <div class="col-lg-3 col-md-6 d-flex">
                                <div class="count-item flex-fill">
                                    <h3><span class="counterUp">{{ $fact['value'] ? intval($fact['value']) : 0 }} </span>+</h3>
                                    <p>{{ __('web.home.locations_to_pickup') }}</p>
                                </div>
                            </div>
                            @break
                            @case('total_km')
                            <div class="col-lg-3 col-md-6 d-flex">
                                <div class="count-item flex-fill">
                                    <h3><span class="counterUp">{{ $fact['value'] ? intval($fact['value']) : 0 }}</span>+</h3>
                                    <p>{{ __('web.home.total_kilometers') }}</p>
                                </div>
                            </div>
                            @break
                        @endswitch
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
