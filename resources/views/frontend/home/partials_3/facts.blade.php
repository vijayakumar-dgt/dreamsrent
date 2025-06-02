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
                    <p>Overall, car rental counters serve as the primary point of contact for customers to pick up their rental vehicles and complete the necessary paperwork before embarking on their journey. The rental agents are there to assist customers every step of the way and ensure a smooth and seamless rental experience.</p>
                    @php 
                    $factsContent = $section['facts_content'];
                    $locationCount = array_filter($factsContent, function ($item) {
                        return $item['key'] == 'location_count';
                    });
                    $locationCount = array_values($locationCount);

                    $vehicleCount = array_filter($factsContent, function ($item) {
                        return $item['key'] == 'vehicle_count';
                    });
                    $vehicleCount = array_values($vehicleCount);

                    $totalKm = array_filter($factsContent, function ($item) {
                        return $item['key'] == 'total_km';
                    });
                    $totalKm = array_values($totalKm);

                    $customersCount = array_filter($factsContent, function ($item) {
                        return $item['key'] == 'happy_customers'; 
                    });
                    $customersCount = array_values($customersCount);
                    @endphp
                    <div class="row">
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-01.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $locationCount[0]['value'] }}</span>+</h3>
                                    <p>Locations to Pickup</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-02.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $vehicleCount[0]['value'] }}</span>+</h3>
                                    <p>Count of Bikes</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-03.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $totalKm[0]['value'] }}</span></h3>
                                    <p>Total Kilometers</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="count-box">
                                <span class="counts-icon">
                                    <img src="{{ asset('frontend/assets/img/icons/count-04.svg') }}" class="img-fluid" alt="img">
                                </span>
                                <div class="count-info">
                                    <h3><span class="counterUp">{{ $customersCount[0]['value'] }}</span>+</h3>
                                    <p>Happy Customers</p>
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