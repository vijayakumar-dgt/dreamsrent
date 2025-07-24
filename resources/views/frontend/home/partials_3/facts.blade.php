<!-- FAQ  -->
<section class="section faq-section-three">
    <div class="container">	
        <div class="row align-items-center">		
            <div class="col-lg-6">		
                <!-- Heading title-->
                <div class="section-heading heading-three" data-aos="fade-down">
                    <h2>{{ $section['section_title'] ?? "" }}</h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
                <!-- /Heading title -->
                <div class="faq-info">
                    @if(!empty($section['faqs']) && count($section['faqs']) > 0)
                    @foreach($section['faqs'] as $faq)
                    <div class="faq-card" data-aos="fade-down">
                        <h4 class="faq-title">
                            <a class="collapsed" data-bs-toggle="collapse" href="#faq{{ $faq->id ?? $loop->index }}" aria-expanded="false">{{ ucfirst($faq->question ?? "") }}</a>
                        </h4>
                        <div id="faq{{ $faq->id ?? $loop->index }}" class="card-collapse collapse">
                            <p>{{ ucfirst($faq->answer ?? "") }}</p>
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
            <div class="col-lg-6">
                <div class="customer-content">
                    <p>{{ __('web.home.theme_3_fact_desc') }}</p>
                    @php 
                        $factsContent = $section['facts_content'];

                        $mappedFacts = [];
                        foreach ($factsContent as $item) {
                            if (isset($item['key'])) {
                                $mappedFacts[$item['key']] = $item;
                            }
                        }

                        $locationCount   = $mappedFacts['location_count'] ?? null;
                        $vehicleCount    = $mappedFacts['vehicle_count'] ?? null;
                        $totalKm         = $mappedFacts['total_km'] ?? null;
                        $customersCount  = $mappedFacts['happy_customers'] ?? null;

                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-01.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $locationCount['value'] ?? 0 }}</span>+</h3>
                                    <p>{{ __('web.home.locations_to_pickup') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-02.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $vehicleCount['value'] ?? 0}}</span>+</h3>
                                    <p>{{ __('web.home.count_of_bikes') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-03.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $totalKm['value'] ?? 0}}</span></h3>
                                    <p>{{ __('web.home.total_kilometers') }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-04.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $customersCount['value'] ?? 0 }}</span>+</h3>
                                    <p>{{ __('web.home.happy_customers') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>	
    </div>	
    <div class="faq-bg">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-01.png') }}" class="img-fluid shape-01" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/faq-bg-01.png') }}" class="img-fluid shape-02" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/faq-bg-02.png') }}" class="img-fluid shape-03" alt="img">
    </div>	
</section>	
<!-- /FAQ -->