<section class="blog-section-four">
    <div class="container">
        <div class="section-heading heading-four" data-aos="fade-down">
            <h2>{{ ucfirst($section['section_title'] ?? "") }}</h2>
            <p>{{ ucfirst($section['section_label'] ?? "") }}</p>
        </div>
        <div class="row row-gap-3 justify-content-center">
            @if(!empty($section['section_content'] && count($section['section_content']) > 0))
                @foreach($section['section_content'] as $k => $content)
                    <!-- Blog Item -->
                    <div class="col-lg-4 col-md-6 d-flex">
                        <div class="blog-item flex-fill theme2-blog">
                            <div class="blog-img theme2-blog-img">
                                <img src="{{ $content['image'] ?? '' }}" class="img-fluid home-blogimg" alt="img">
                            </div>
                            <div class="blog-content">
                                <div class="d-flex align-center justify-content-between blog-category">
                                    <a href="javascript:void(0);" class="category">{{ ucfirst($content['category'] ?? "") }}</a>
                                    <p class="date d-inline-flex align-center"><i class="bx bx-calendar me-1"></i>{{ $content['updated_at'] ?? "" }}</p>
                                </div>
                                @php
                                    $blogTitle = $content['title'] ?? "";
                                    if(strlen($blogTitle) > 30){
                                        $blogTitle = substr($blogTitle, 0, 30)."...";
                                    }
                                @endphp
                                <h5 class="title"><a href="/blog-details/{{ $content['slug'] ?? "" }}">{{ ucfirst($blogTitle) }}</a></h5>
                            </div>
                        </div>
                    </div>
                    <!-- /Blog Item -->
                @endforeach
            @endif
        </div>
        <div class="view-all-btn text-center aos" data-aos="fade-down">
            <a href="/blogs" class="btn btn-secondary d-inline-flex align-center">{{ __('web.home.view_more') }}<i class="bx bx-right-arrow-alt ms-1"></i></a>
        </div>
        @php
            $subscriptionSection = $content_sections->where('section_type', 'ad_card_section')->first();
        @endphp
        @if(!empty($subscriptionSection))
            <div class="subscribe-sec">
                <div class="row align-items-end">
                    <div class="col-md-6">
                        <div class="subscribe-content">
                            <h2>{{ $subscriptionSection['section_title'] ?? "" }}</h2>
                            <p>{{ $subscriptionSection['section_label'] ?? "" }}</p>
                            <div class="subscribe-form">
                                <form id="newsletterForm" autocomplete="off">
                                    <span><i class="bx bx-mail-send"></i></span>
                                    <input type="email" name="subscriber_email" id="subscriber_email" class="form-control" placeholder="{{ __('web.home.enter_your_email') }}">
                                    <button type="submit" class="btn btn-subscribe submitbtn"><i class="bx bx-send"></i></button>
                                </form>
                            </div>
                            <span class="text-danger error-text" id="subscriber_email_error"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="subscribe-img">
                            <img src="/frontend/assets/img/about/web-app.png" alt="img" class="img-fluid">
                        </div>
                    </div>
                </div>
                <img src="/frontend/assets/img/bg/app-bg.svg" alt="icon" class="app-bg-01">
            </div>
        @endif
    </div>
</section>
