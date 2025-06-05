@php
    $sectionContent = $section['section_content'];
    $content = isset($section['section_content'][0]) ? $section['section_content'][0] : [];
@endphp
<!-- Boat marketplace -->
<section class="boats-marketplace-sec">
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-big"></span>
    </div>
    <div class="sec-bg">
        <img src="/frontend/assets/img/bg/dotted-round-bg.png" alt="Bg">
        <img src="/frontend/assets/img/bg/anchor-img.png" alt="Bg">
    </div>
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <div class="sec-col-left-imgs">
                    <span class="sec-left-one"><img src="{{ $content['data']['thumbnail_image_boat_experience_1'] ?? asset('frontend/assets/img/bg/sec-modal-img-01.jpg') }}" class="img-fluid" alt="Img"></span>
                    <span class="sec-left-two"><img src="{{ $content['data']['thumbnail_image_boat_experience_2'] ?? asset('frontend/assets/img/bg/sec-modal-img-02.jpg') }}" class="img-fluid" alt="Img"></span>
                    <div class="experience-info">
                        <h5>{{ $content['data']['label_boat_experience_1'] ?? 0 }}+ <span>Years of <br> Experience</span></h5>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="section-header-two">
                    <h2>{{ $section['section_title'] ?? "" }}</h2>
                    <h4>{{ $content['section_label'] ?? "" }}</h4>
                    <p>{{ $content['data']['description_boat_experience_1'] ?? "" }}</p>
                    <a href="{{ route('list') }}" class="btn btn-primary d-flex align-items-center"><i class="bx bx-bar-chart me-2"></i>Learn More</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Boat marketplace -->