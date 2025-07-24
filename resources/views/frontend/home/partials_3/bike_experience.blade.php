@php
    $sectionContent = $section['section_content'];
    $content = $sectionContent[0] ?? [];
@endphp
<!-- Best Section -->
<section class="section rental-section">
    <div class="container">
        <div class="rental-wrap">
            <div class="rental-content">
                <h2>{{ $content['data']['label_bike_experience_1'] ?? "" }}</h2>
                <div class="btn-item">
                    <a href="{{ route('list') }}" class="btn btn-theme">{{ __('web.home.view_all_bikes') }}</a>
                </div>
            </div>
        </div>
    </div>
    <div class="rental-bg">
        <img class="img-fluid ban-bg" src="{{ $content['data']['thumbnail_image_bike_experience_1'] ?? asset('frontend/assets/img/bg/bike-bg.jpg') }}" alt="Image">
        <img class="img-fluid shape-01" src="{{ asset('frontend/assets/img/bg/ban-bg-05.png') }}" alt="Image">
        <img class="img-fluid shape-02" src="{{ asset('frontend/assets/img/bg/ban-bg-06.png') }}" alt="Image">
        <img class="img-fluid shape-03" src="{{ asset('frontend/assets/img/bg/shape-bg.png') }}" alt="Image">
        <img class="img-fluid shape-04" src="{{ asset('frontend/assets/img/bg/ban-bg-04.png') }}" alt="Image">
    </div>
</section>
<!-- /Best Section -->