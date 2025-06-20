    <section class="section popular-car-type">
        <div class="container">
            <!-- Heading title-->
            <div class="section-heading"  data-aos="fade-down">
                <h2>{{ $section['section_title'] }}</h2>
                <p>{{ $section['section_label'] }}</p>
            </div>
            <!-- /Heading title -->
            <div class="row">
                @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                <div class="popular-slider-group">
                    <div class="owl-carousel popular-cartype-slider owl-theme">
                        @foreach($section['section_content'] as $cartype)
                        <div class="listing-owl-item">
                            <div class="listing-owl-group">
                                <div class="listing-owl-img">
                                    <img src="{{ $cartype->image_url }}" class="img-fluid" alt="{{ $cartype->name ?? "" }} {{ __('web.common.vehicles') }}">
                                </div>
                                <h6>{{ ucfirst($cartype->name ?? "") }}</h6>
                                <p>{{ $cartype->car_count ?? ""}} {{ __('web.common.vehicles') }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @else
                <div class="col-12">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                </div>
                @endif
        </div>
        <!-- View More -->
        @if(!empty($section['section_content']) && count($section['section_content']) > 0)
        <div class="view-all text-center" data-aos="fade-down">
            <a href="{{ route('list') }}" class="btn btn-view d-inline-flex align-items-center">{{ __('web.home.view_all_cars') }}<i class="feather-arrow-right ms-2"></i></a>
        </div>
        @endif
        <!-- View More -->
        </div>
    
</section>
