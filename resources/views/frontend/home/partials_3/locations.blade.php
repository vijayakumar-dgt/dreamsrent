@php 
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(1, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- Destinations Section -->
<section class="section destination-section">
    <div class="destination-bg">
        <img src="{{ asset('frontend/assets/img/bg/destination-bg-01.png') }}" class="img-fluid shape-01" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/destination-bg-02.png') }}" class="img-fluid shape-02" alt="img">
    </div>		
    <div class="container">	
        <!-- Heading title-->
        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="section-heading heading-three mx-auto" data-aos="fade-down">
                    <h2>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "" }}</span></h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
            </div>
        </div>
        <!-- /Heading title -->
        
        <div class="row">
            @if(!empty($sectionContent) && count($sectionContent) > 0)
            @foreach($sectionContent as $location)
            <div class="col-lg-3 col-sm-6 order-lg-1 order-sm-1">
                <div class="destination-wrap" data-aos="fade-down">
                    <div class="destination-img">
                        <img src="{{ $location->image ?? "" }}" alt="image" class="img-fluid">
                    </div>
                    <div class="destination-content">
                        <h5>{{ $location->name ?? "" }}</h5>
                        <p>{{ $location->vehicle_count ?? 0 }} {{ __('web.home.bikes') }}</p>
                        <a href="{{ route('list') }}" class="btn btn-primary">{{ __('web.home.explore_all_bikes') }}<i class="bx bx-right-arrow-alt"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
            @endif
        </div>

    </div>
</section>
<!-- /Destinations Section -->