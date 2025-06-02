@php
    $sectionContent = $section['section_content'];    
@endphp
<!-- Brand Slider -->
<div class="row">
    <div class="col-md-12">
        <div class="brand-sec">
            <div class="section-heading heading-three" data-aos="fade-down">
                <p>{{ $section['section_title'] ?? "" }}</p>
            </div>
            <div class="brand-slider owl-carousel">
                @if(!empty($sectionContent) && count($sectionContent) > 0)
                @foreach($sectionContent as $brand)
                <div class="brand-item">
                    <img src="{{ $brand->brand_icon ?? "" }}" class="img-fluid" alt="brand">
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</div>
<!-- /Brand Slider -->