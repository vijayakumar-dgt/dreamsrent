@php
    $faqs = $section['faqs'] ?? [];
    $facts = $section['facts'] ?? [];
@endphp
<!-- FAQ -->
<section class="faq-sec-two overflow-hidden">
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-big"></span>
    </div>
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/anchor-img.png') }}" alt="Bg">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-02.png') }}" alt="Bg">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-03.png') }}" alt="Bg">
        <img src="{{ asset('frontend/assets/img/bg/sec-bg-wave.png') }}" alt="Bg">
    </div>
    <div class="container">
        <div class="counter-group counter-group-two">
            <div class="row">
                <div class="col-lg-3 col-md-6 col-12 d-flex">
                    <div class="count-group flex-fill">
                        <div class="customer-count d-flex align-items-center">
                            <div class="count-img">
                                <img src="{{ asset('frontend/assets/img/icons/counter-icon-01.svg') }}" alt="Icon">
                            </div>
                            @php
                               $mappedFacts = [];
                                foreach ($facts as $item) {
                                    if (isset($item['key'])) {
                                        $mappedFacts[$item['key']] = $item;
                                    }
                                }

                                $vehicle_count   = $mappedFacts['vehicle_count'] ?? null;
                                $happy_customers = $mappedFacts['happy_customers'] ?? null;
                                $location_count  = $mappedFacts['location_count'] ?? null;
                                $total_km        = $mappedFacts['total_km'] ?? null;
                            @endphp
                            <div class="count-content">
                                <h4><span class="counterUp">{{ $vehicle_count['value'] ?? 0 }}</span>+</h4>
                                <p>{{ __('web.home.count_of_yachts') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 d-flex">
                    <div class="count-group flex-fill">
                        <div class="customer-count d-flex align-items-center">
                            <div class="count-img">
                                <img src="{{ asset('frontend/assets/img/icons/counter-icon-02.svg') }}" alt="Icon">
                            </div>
                            <div class="count-content">
                                <h4><span class="counterUp">{{ $happy_customers['value'] ?? 0 }}</span></h4>
                                 <p>{{ __('web.home.happy_customers') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 d-flex">
                    <div class="count-group flex-fill">
                        <div class="customer-count d-flex align-items-center">
                            <div class="count-img">
                                <img src="{{ asset('frontend/assets/img/icons/counter-icon-03.svg') }}" alt="Icon">
                            </div>
                            <div class="count-content">
                                <h4><span class="counterUp">{{ $total_km['value'] ?? 0 }}</span></h4>
                                <p>{{ __('web.home.total_kilometers') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 col-12 d-flex">
                    <div class="count-group flex-fill">
                        <div class="customer-count d-flex align-items-center">
                            <div class="count-img">
                                <img src="{{ asset('frontend/assets/img/icons/counter-icon-04.svg') }}" alt="Icon">
                            </div>
                            <div class="count-content">
                                <h4><span class="counterUp">{{ $location_count['value'] ?? 0 }}</span>+</h4>
                                <p>{{ __('web.home.locations_to_pickup') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="faq-items">
            <div class="section-header-two">
                <h2>{{ $section['section_title'] ?? "" }}</h2>
                <p>{{ $section['section_label'] ?? "" }}</p>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-main-items" id="faq-details">
                        <!-- FAQ Item -->
                        @if(!empty($faqs) && count($faqs) > 0)
                            @foreach($faqs as $k => $faq)
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="faq-{{ $k }}">
                                    <a href="javascript:void(0);"
                                    class="accordion-button {{ $k != 0 ? 'collapsed' : '' }}"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapse-{{ $k }}"
                                    aria-expanded="{{ $k == 0 ? 'true' : 'false' }}"
                                    aria-controls="collapse-{{ $k }}">
                                        {{ $faq->question ?? '' }}
                                    </a>
                                </h2>
                                <div id="collapse-{{ $k }}"
                                    class="accordion-collapse collapse {{ $k == 0 ? 'show' : '' }}"
                                    aria-labelledby="faq-{{ $k }}"
                                    data-bs-parent="#faq-details">
                                    <div class="accordion-body">
                                        <div class="accordion-content">
                                            <p>{!! $faq->answer ?? '' !!}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="col-12">
                                <p class="text-center">{{ __('web.common.empty_table') }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /FAQ -->
