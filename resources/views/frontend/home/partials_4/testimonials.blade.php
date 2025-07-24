<!-- Client Review -->
<section class="our-client-review-sec">
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-01.png') }}" alt="Bg">
    </div>
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
    </div>
    <div class="container">
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <div class="row">
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            @foreach($section['section_content'] as $content)
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="client-review-card flex-fill">
                    <div class="client-review-content">
                        <div class="client-img">
                            <a href="javascript:void(0);" class="img-avatar"><img src="{{ $content->image ?? null}}" alt="Img"></a>
                            <p>{{ $content->review ?? "" }}</p>
                            <h5><a href="javascript:void(0);">{{ $content->customer_name ?? "" }}</a></h5>
                            <span>{{ $content->location ?? "" }}</span>
                            <div class="quataion-mark">
                                <img src="/frontend/assets/img/icons/quatation-mark.svg" class="img-fluid" alt="Img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
            @else 
            <div class="col-md-12">
                <p>{{ __('web.common.empty_table') }}</p>
            </div>
            @endif
        </div>
    </div>
</section>
<!-- /Client Review -->