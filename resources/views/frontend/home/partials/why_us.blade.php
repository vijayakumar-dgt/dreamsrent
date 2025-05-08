    <!-- Why Choose Us -->
    <section class="section why-choose popular-explore">
        <div class="choose-left">
            <img src="{{ asset('frontend/assets/img/bg/choose-left.png') }}" class="img-fluid" alt="{{ __('web.home.why_choose_us') }}">
        </div>
        <div class="container">
            <!-- Heading title-->
            <div class="row">
                <div class="col-lg-4 mx-auto">
                    <div class="section-heading" data-aos="fade-down">
                        <h2>{{ $section['section_title'] }}</h2>
                        <p>{{ $section['section_label'] }}</p>
                    </div>
                </div>
            </div>
            <!-- /Heading title -->
            <div class="why-choose-group">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12 d-flex" data-aos="fade-down">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="choose-img choose-black">
                                    <img src="{{ asset('frontend/assets/img/icons/bx-selection.svg') }}" alt="Icon">
                                </div>
                                <div class="choose-content">
                                    <h4>{{ __('web.home.easy_and_fast_booking') }}</h4>
                                    <p>{{ __('web.home.fast_booking_content') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 d-flex" data-aos="fade-down">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="choose-img choose-secondary">
                                    <img src="{{ asset('frontend/assets/img/icons/bx-crown.svg') }}" alt="Icon">
                                </div>
                                <div class="choose-content">
                                    <h4>{{ __('web.home.many_pickup_locations') }}</h4>
                                    <p>{{ __('web.home.many_pickup_locations_content') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-12 d-flex" data-aos="fade-down">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="choose-img choose-primary">
                                    <img src="{{ asset('frontend/assets/img/icons/bx-user-check.svg') }}" alt="Icon">
                                </div>
                                <div class="choose-content">
                                    <h4>{{ __('web.home.customer_satisfaction') }}</h4>
                                    <p>{{ __('web.home.customer_satisfaction_content')  }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Why Choose Us -->