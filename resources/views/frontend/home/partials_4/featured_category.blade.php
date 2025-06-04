<!-- Yacht Categories -->
<section class="yacht-category-sec">
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-01.png') }}" class="anchor-img" alt="Img">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-02.png') }}" class="vector-round" alt="Img">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-03.png') }}" class="design-round" alt="Img">
    </div>
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-small"></span>
    </div>
    <div class="container">
        <div class="banner-yacht-type-slider owl-carousel">
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-01.svg" alt="Img"></span>
                </div>
                <h6>Standard</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-02.svg" alt="Img"></span>
                </div>
                <h6>Luxury</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-03.svg" alt="Img"></span>
                </div>
                <h6>50+ Guests</h6>
            </div>
            <div class="slider-card slider-card-active">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-04.svg" alt="Img"></span>
                </div>
                <h6>Wedding</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-05.svg" alt="Img"></span>
                </div>
                <h6>Birthday</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-06.svg" alt="Img"></span>
                </div>
                <h6>100+ Guests</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-04.svg" alt="Img"></span>
                </div>
                <h6>Fishing</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-08.svg" alt="Img"></span>
                </div>
                <h6>Party</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-09.svg" alt="Img"></span>
                </div>
                <h6>Corporate</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-10.svg" alt="Img"></span>
                </div>
                <h6>Community</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-02.svg" alt="Img"></span>
                </div>
                <h6>Luxury</h6>
            </div>
            <div class="slider-card">
                <div class="banner-slider-icon">
                    <span><img src="/frontend/assets/img/icons/banner-slider-03.svg" alt="Img"></span>
                </div>
                <h6>50+ Guests</h6>
            </div>
            
        </div>
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <div class="row yacht-category-lists">
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            @foreach ($section['section_content'] as $yacht)
            <div class="custom-col">
                <div class="yacht-cat-grid">
                    <div class="yatch-card-img">
                        <a href="{{ route('list', ['category' => $yacht->id]) }}"><img src="{{ $yacht->image_url }}" class="img-fluid" alt="yacht"></a>
                    </div>
                    <div class="card-content d-flex align-items-center justify-content-between">
                        <div>
                            <h4><a href="{{ route('list') }}">{{ $yacht->name ?? "" }}</a></h4>
                            <span>{{ $yacht->boat_count ?? 0 }} Yachts</span>
                        </div>
                        <a href="{{ route('list', ['category' => $yacht->id]) }}" class="arrow-right"><i class="bx bx-right-arrow-alt"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                <div class="view-more-btn text-center">
                    <a href="{{ route('list') }}" class="btn btn-secondary">View  More Categories</a>
                </div>
            </div>
            @else 
            <div class="col-md-12">
                <p class="text-center">{{ __('web.common.empty_table') }}</p>
            </div>
            @endif
        </div>
    </div>
</section>
<!-- /Yacht Categories -->