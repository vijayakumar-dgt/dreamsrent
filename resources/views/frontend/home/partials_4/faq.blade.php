<!-- FAQ -->
<section class="faq-sec-two">
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
                            <div class="count-content">
                                <h4><span class="counterUp">2547</span>+</h4>
                                <p>Count of Yachts</p>
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
                                <h4><span class="counterUp">16</span>k</h4>
                                <p>Happy Customers</p>
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
                                <h4><span class="counterUp">15000</span></h4>
                                <p>Total Nauticles</p>
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
                                <h4><span class="counterUp">5000</span>+</h4>
                                <p>Booking Completed</p>
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
            @php 
                $section_content = $section['section_content'];
            @endphp
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="faq-main-items" id="faq-details">
                        <!-- FAQ Item -->
                        @if(!empty($section_content) && count($section_content) > 0)
                            @foreach($section_content as $k => $faq)
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