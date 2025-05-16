<!-- Why Choose Us -->
    <section class="section why-choose popular-explore">
        <div class="choose-left">
            <img src="{{ asset('frontend/assets/img/bg/choose-left.png') }}" class="img-fluid" alt="{{ __('web.home.why_choose_us') }}">
        </div>
        <div class="container">
            <!-- Heading title-->
            <div class="row">
                <div class="col-lg-4 mx-auto">
                    <div class="section-heading" data-aos="fade-down">
                        <h2>{{ $section['section_title'] }}</h2>
                        <p>{{ $section['section_label'] }}</p>
                    </div>
                </div>
            </div>
            <!-- /Heading title -->
            <div class="why-choose-group">
                <div class="row">
                    @if(!empty($section['section_content']['items']) && count($section['section_content']['items']) > 0)
                        @foreach($section['section_content']['items'] as $item)
                    <div class="col-lg-4 col-md-6 col-12 d-flex" data-aos="fade-down">
                        <div class="card flex-fill">
                            <div class="card-body">
                                <div class="choose-img choose-black">
                                    <img src="{{ $item['why_icon'] }}" alt="Icon">
                                </div>
                                <div class="choose-content">
                                    <h4>{{ $item['why_label'] }}</h4>
                                    <p>{{ $item['why_dis'] }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>
    <!-- /Why Choose Us -->