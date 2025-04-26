<section class="feature-section pt-0">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">

                <div class="feature-img">
                    <div class="section-heading heading-four text-start" data-aos="fade-down">
                        <h2>{{ ucfirst($section['section_title'] ?? "" )}}</h2>
                        <p>{{ ucfirst($section['section_label'] ?? "") }}</p>
                    </div>
                    <img src="{{ $section['section_content'][0]->vehicle_image_url ?? asset('frontend/assets/img/cars/car.png') }}" alt="img" class="img-fluid">
                </div>

            </div>

            <div class="col-lg-6">
                <div class="row row-gap-4">

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">
                            <span class="feature-icon">
                                <i class="bx bxs-info-circle"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_1 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_1 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">
                            <span class="feature-icon">
                                <i class="bx bx-exclude"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_2 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_2 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">
                            <span class="feature-icon">
                                <i class="bx bx-money"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_3 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_3 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">

                            <span class="feature-icon">
                                <i class="bx bxs-car-mechanic"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_4 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_4 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">
                            <span class="feature-icon">
                                <i class="bx bx-support"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_5 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_5 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                    <!-- Feature Item -->
                    <div class="col-md-6 d-flex">
                        <div class="feature-item flex-fill">
                            <span class="feature-icon">
                                <i class="bx bxs-coin"></i>
                            </span>
                            <div>
                                <h6 class="mb-1">{{ ucfirst($section['section_content'][0]->label_6 ?? "") }}</h6>
                                <p>{{ ucfirst($section['section_content'][0]->dis_6 ?? "") }}</p>
                            </div>
                        </div>
                    </div>
                    <!-- /Feature Item -->

                </div>
            </div>
        </div>
    </div>
</section>