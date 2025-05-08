    <section class="section popular-car-type">
        <div class="container">
            <!-- Heading title-->
            <div class="section-heading"  data-aos="fade-down">
                <h2>{{ $section['section_title'] }}</h2>
                <p>{{ $section['section_label'] }}</p>
            </div>
            <!-- /Heading title -->
            <div class="row">
                <div class="popular-slider-group">
                    <div class="owl-carousel popular-cartype-slider owl-theme">
                        @foreach($section['section_content'] as $cartype)
                        <div class="listing-owl-item">
                            <div class="listing-owl-group">
                                <div class="listing-owl-img">
                                    <img src="{{ $cartype->image_url }}" class="img-fluid" alt="Popular Cartypes">
                                </div>
                                <h6>{{ ucfirst($cartype->name ?? "") }}</h6>
                                <p>{{ $cartype->car_count ?? ""}} {{ __('web.common.vehicles') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
        </div>
        <!-- View More -->
        <div class="view-all text-center" data-aos="fade-down">
            <a href="/vehicles" class="btn btn-view d-inline-flex align-items-center">{{ __('web.home.view_all_cars') }}<i class="feather-arrow-right ms-2"></i></a>
        </div>
        <!-- View More -->
        </div>
    
</section>
