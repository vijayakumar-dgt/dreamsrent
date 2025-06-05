@php
    $sectionContent = $section['section_content'] ?? [];
    $backgroundImage = $sectionContent[0]['data']['thumbnail_image_boat_offer'] ?? asset('frontend/assets/img/bg/yacht-owner-bg-02.png');
@endphp
<div class="yacht-owner-card party-rental-yacht">
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/sec-bg-wave.png') }}" alt="Img">
    </div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-8">
            <div class="yacht-owner-title">
                <h3>{{ $section['section_title'] ?? "" }}</h3>
                <p>{{ $section['section_label'] ?? "" }}</p>
                <a href="listing-details.html" class="btn btn-primary">View Yachts</a>
            </div>
        </div>
    </div>
    <div class="yacht-owner-img">
        <img src="{{ $backgroundImage }}" alt="Img">
    </div>
</div>