@php
    $sectionContent = $section['section_content'];
@endphp
<!-- Poplar Location -->
<section class="popular-location-sec">
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-big"></span>
        <span class="bg-dark-blue round-small"></span>
    </div>
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/yacht-cat-sec-bg-02.png') }}" class="vector-round" alt="Img">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-01.png') }}" alt="Bg">
    </div>
    <div class="container">
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="popular-location-slider owl-carousel home-two-slider">
                    @if(!empty($sectionContent) && count($sectionContent) > 0)
                    @foreach($sectionContent as $content)
                    <div class="popular-location-card">
                        <div class="location-img">
                            <a href="javascript:void(0);"><img src="{{ asset($content->image) }}" alt="City Img"></a>
                        </div>
                        <div class="location-contents">
                            <div class="location-city-name">
                                <h4><a href="javascript:void(0);">{{ $content->name }}</a></h4>
                                <span>{{ $content->vehicle_count ?? 0 }} Yachts</span>
                            </div>
                            <a href="{{ route('list', ['pickuplocation' => $content->name]) }}" class="arrow-right"><i class="bx bx-right-arrow-alt"></i></a>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Poplar Location -->