@php 
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(2, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- About us Testimonials -->
<section class="section testimonials-three">
    <div class="container">
        <div class="row">
            <div class="col-lg-4">
                <div class="testimonial-feedback">
                    <!-- Heading title-->
                    <div class="section-heading heading-three" data-aos="fade-down">
                        <h2 class="title">{{ $titleMain ?? "" }} <br><span>{{ $titleLastPart ?? "" }}</span> </h2>
                    </div>
                    <!-- /Heading title -->

                    <img src="{{ asset('frontend/assets/img/testimonial-img.jpg') }}" alt="img" class="img-fluid">
                    <div class="feedback-item">
                        <div class="feedback-info">
                            <h6>{{ __('web.home.greate') }}</h6>
                            <div class="rate-icon">
                                <span><i class="bx bxs-star"></i></span>
                                <span><i class="bx bxs-star"></i></span>
                                <span><i class="bx bxs-star"></i></span>
                                <span><i class="bx bxs-star"></i></span>
                                <span><i class="bx bxs-star"></i></span>
                            </div>
                            <p>{{ __('web.home.based_on') }} {{ $data['total_reviews'] ?? 0 }} {{ __('web.home.reviews') }}</p>
                        </div>
                        <div class="feedback-user">
                            <h3><i class="bx bxs-star"></i>{{ __('web.home.trustpilot') }}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="testimonial-wrapper">
                    <div class="testimonial-slider">
                        @if(!empty($sectionContent) && count($sectionContent) > 0)
                        @foreach($sectionContent as $testimonial)
                        <div class="testimonial-wrap">	
                            <div class="users-info">
                                <div class="testimonial-name">
                                    <h6>{{ $testimonial->customer_name ?? "" }}</h6>
                                    <p>{{ $testimonial->location ?? "" }}</p>
                                </div>
                                @php 
                                    $rating = $testimonial->ratings ?? 0;
                                    $rating = round($rating);
                                    $emptyStars = 5 - $rating;
                                    $fullStars = $rating;
                                @endphp
                                <div class="users-rating">
                                    <div class="rate-icon">
                                        @for($i = 1; $i <= $fullStars; $i++)
                                            <span><i class="bx bxs-star"></i></span>
                                        @endfor
                                        @for($i = 1; $i <= $emptyStars; $i++)
                                            <span><i class="bx bx-star"></i></span>
                                        @endfor
                                    </div>
                                    <p><i class="bx bxs-check-circle"></i>{{ __('web.home.verified') }}</p>
                                </div>
                            </div>	
                            <div class="testimonial-content">					
                                <p>{{ $testimonial->review ?? "" }}</p>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div class="col-12">
                            <p class="text-center">{{ __('web.common.empty_table') }}</p>
                        </div>
                        @endif
                    </div>
                    <div class="slider testimonial-thumbnails">
                        @if(!empty($sectionContent) && count($sectionContent) > 0)
                        @foreach($sectionContent as $testimonial)
                        <div><img src="{{ $testimonial->image }}" alt="image"></div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>				
    </div>
    <div class="testimonial-bg">
        <img class="img-fluid shape-01" src="{{ asset('frontend/assets/img/bg/ban-bg-04.png') }}" alt="Image">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-01.png') }}" class="img-fluid shape-02" alt="img">
    </div>
</section>
<!-- About us Testimonials -->