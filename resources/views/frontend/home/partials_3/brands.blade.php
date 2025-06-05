@php
    $sectionContent = $section['section_content'];    
@endphp
<!-- Brand Slider -->
<div class="row mt-3">
    <div class="col-md-12">
        <div class="brand-sec">
            <div class="section-heading heading-three" data-aos="fade-down">
                <p>{{ $section['section_title'] ?? "" }}</p>
            </div>
            @if(!empty($sectionContent) && count($sectionContent) > 0)
            <div class="brand-slider owl-carousel">
                @foreach($sectionContent as $brand)
                <div class="brand-item">
                    <img src="{{ $brand->brand_image ?? "" }}" class="img-fluid" alt="brand">
                </div>
                @endforeach
            </div>
            @else
            <div class="col-md-12 mb-3">
                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
<!-- /Brand Slider -->