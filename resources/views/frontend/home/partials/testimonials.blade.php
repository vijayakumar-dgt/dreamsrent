   <!-- Testimonials -->
   <section class="section about-testimonial testimonials-section">
        <div class="container">
            <!-- Heading title-->
            <div class="section-heading" data-aos="fade-down">
                <h2 class="title text-white">{{ $section['section_title'] }} </h2>
                <p class="description text-white">{{ $section['section_label'] }}</p>
            </div>
            <!-- /Heading title -->
            <div class="owl-carousel about-testimonials testimonial-group mb-0 owl-theme">

                <!-- Carousel Item -->
                @foreach($section['section_content'] as $testimonial)
                <div class="testimonial-item d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="quotes-head"></div>
                            <div class="review-box">
                                <div class="review-profile">
                                    <div class="review-img">
                                        <img src="{{ $testimonial->image }}" class="img-fluid"
                                            alt="{{ $testimonial->customer_name }}">
                                    </div>
                                </div>
                                <div class="review-details">
                                    <h6>{{ $testimonial->customer_name }}</h6>
                                    <p>{{ $testimonial->location ?? "" }}</p>
                                </div>
                            </div>
                            @php
                            $limit = 225;
                            $shortReview = strlen($testimonial->review) > $limit ? substr($testimonial->review, 0, $limit) .
                            '...' : $testimonial->review;
                            @endphp
                            <p>{{ $shortReview }}</p>
                            @php $ratings = $testimonial->ratings @endphp
                            <div class="list-rating">
                                <div class="list-rating-star">
                                    @for($i = 1; $i <= $ratings; $i++) <i class="fas fa-star filled"></i>
                                        @endfor
                                </div>
                                <p><span>({{ $ratings }})</span></p>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <!-- /Carousel Item  -->
            </div>
        </div>
    </section>
    <!-- /Testimonials -->