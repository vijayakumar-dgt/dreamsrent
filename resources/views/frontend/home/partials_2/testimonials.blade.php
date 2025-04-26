<section class="testimonial-section">
    <div class="container">
        <div class="section-heading heading-four" data-aos="fade-down">
            <h2>{{ $section['section_title'] ?? ""}}</h2>
            <p>{{ $section['section_label'] ?? "" }} </p>
        </div>

        <div class="row row-gap-4 justify-content-center">
           @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            @foreach($section['section_content'] as $content)
            <!-- Testimonial Item -->
            <div class="col-lg-4 col-md-6 d-flex">
                <div class="testimonial-item testimonial-item-two flex-fill">
                    <div class="user-img">
                        <img src="{{ $content->image }}" class="img-fluid">
                    </div>
                    @php
                       $review = $content->review ?? "";
                       if (strlen($review) > 85) {
                           $review = substr($review, 0, 85) . '...';
                       }

                    @endphp
                    <p>{{ $review }}</p>
                    @php
                        $ratings = $content->ratings ?? 0;
                        $filled_stars = floor($ratings);
                        $empty_stars = 5 - $filled_stars;
                    @endphp

                    <div class="rating">
                        @for ($i = 0; $i < $filled_stars; $i++)
                            <i class="fas fa-star filled"></i>
                        @endfor

                        @for ($i = 0; $i < $empty_stars; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </div>

                    <div class="user-info">
                        <h6>{{ ucfirst($content->customer_name ?? "") }}</h6>
                        <p>{{ ucfirst($content->location ?? "") }}</p>
                    </div>
                </div>
            </div>
            <!-- /Testimonial Item -->
            @endforeach
            @endif
        </div>

      

        <div class="client-slider owl-carousel">
            <div>
                <img src="/frontend/assets/img/clients/client-01.svg" alt="img">
            </div>
            <div>
                <img src="/frontend/assets/img/clients/client-02.svg" alt="img">
            </div>
            <div>
                <img src="/frontend/assets/img/clients/client-03.svg" alt="img">
            </div>
            <div>
                <img src="/frontend/assets/img/clients/client-04.svg" alt="img">
            </div>
            <div>
                <img src="/frontend/assets/img/clients/client-05.svg" alt="img">
            </div>
            <div>
                <img src="/frontend/assets/img/clients/client-06.svg" alt="img">
            </div>
        </div>
    </div>
</section>
