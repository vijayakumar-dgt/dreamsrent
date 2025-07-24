    <section class="brand-section">
        <div class="container">	
            <div class="section-heading heading-four" data-aos="fade-down">
                <h2 class="text-white">{{ ucfirst($section['section_title'] ?? "") }}</h2>
                <p>{{ $section['section_label'] ?? "" }}</p>
            </div>
            <div class="brands-slider owl-carousel">
                @if(isset($section['section_content']) && count($section['section_content']) > 0)
                @foreach($section['section_content'] as $content)
                <div class="brand-wrap">
                    <img src="{{ $content->brand_image }}" alt="img" height="100" width="100">
                    <p>{{ ucfirst($content->brand_name ?? "") }}</p>
                </div>
                @endforeach
                @endif
            </div>
            @if(empty($section['section_content']) || count($section['section_content']) == 0)
            <div class="col-12">
                <p class="text-center">{{ __('web.common.empty_table') }}</p>
            </div>
            @endif
            <div class="brand-img text-center">
                <img src="{{ asset('/frontend/assets/img/bg/brand.png') }}" alt="img" class="img-fluid">
            </div>
        </div>
    </section>