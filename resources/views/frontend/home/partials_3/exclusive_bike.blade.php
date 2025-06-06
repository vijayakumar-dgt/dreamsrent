@php
    $sectionContent = $section['section_content'];
    $bike_icon = $sectionContent['bike_icon'] ?? '';
    $items = $sectionContent['items'] ?? [];
@endphp
<!-- Choose Us Section -->
<section class="section choose-us-section overflow-hidden">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <!-- Heading title-->
                <div class="section-heading heading-three" data-aos="fade-down">
                    <h2>{{ $section['section_title'] ?? ""}}</h2>
                    <p>{{ $section['section_label'] ?? ""}}</p>
                </div>
                <!-- /Heading title -->
                <div class="row">
                    @if(!empty($items) && count($items) > 0)
                    @foreach ($items as $k => $item)
                        @php
                            $icons = [
                                0 => 'bx bxs-bookmarks',
                                1 => 'bx bxs-bolt-circle',
                                2 => 'bx bxs-calendar-heart',
                                3 => 'bx bxs-badge-dollar'
                            ];
                        @endphp
                        <div class="col-md-6">
                            <div class="quality-wrap" data-aos="fade-down">
                                <span>
                                    <i class="{{ $icons[$k] ?? '' }}"></i>
                                </span>
                                <h6>{{ $item['bike_label'] ?? '' }}</h6>
                                <p>{{ $item['bike_dis'] ?? '' }}</p>
                            </div>
                        </div>                        
                    @endforeach
                    @else
                    <div class="col-md-12">
                        <p class="text-center">{{ __('web.common.empty_table') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="quality-img">
        <img src="{{ $bike_icon }}"   data-aos="fade-left" class="img-fluid" alt="img">
    </div>
    <div class="quality-bg">
        <img src="{{ asset('frontend/assets/img/bg/quality-bg.png') }}" class="img-fluid" alt="img">
    </div>
</section>
<!-- /Choose Us Section -->