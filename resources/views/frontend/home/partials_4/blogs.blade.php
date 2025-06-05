<!-- News & Insights -->
<section class="news-insights-sec">
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-big"></span>
        <span class="bg-dark-blue round-big"></span>
    </div>
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/ship-part-bg-01.png') }}" alt="Img">
    </div>
    <div class="container">
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <div class="row">
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            @foreach($section['section_content'] as $blog)
            <div class="col-lg-4 col-md-6">
                <div class="article-grid-card">
                    <div class="article-img">
                        <a href="{{ route('blog.details', $blog['slug'] ?? '') }}"><img src="{{ $blog['image'] ?? "" }}" class="img-fluid" alt="Img"></a>
                        <span class="date-info"><i class="bx bx-calendar me-2"></i>{{ $blog['updated_at'] ?? "" }}</span>
                    </div>
                    <div class="user-head">
                        <a href="javascript:void(0);" class="img-avatar"><img src="{{ $blog['author']['avatar'] ?? "" }}" alt="Img">By {{ $blog['author']['name'] ?? "" }}</a>
                    </div>
                    @php
                        $desc = strip_tags($blog['description']);
                        $words = explode(' ', $desc);
                        $limitedWords = array_slice($words, 0, 15);
                        $blogDesc = implode(' ', $limitedWords);

                        if (str_word_count($desc) > 15) {
                            $blogDesc .= '...';
                        }

                    @endphp
                    <div class="article-title">
                        <h4><a href="{{ route('blog.details', $blog['slug'] ?? '') }}">{{ $blog['title'] ?? "" }}</a></h4>
                        <p>{{ $blogDesc ?? "" }}</p>
                        <a href="{{ route('blog.details', $blog['slug'] ?? '') }}" class="read-more">{{ __('web.blog.read_more') }} <i class="bx bx-right-arrow-alt ms-2"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
            <div class="col-md-12">
                <div class="view-more text-center">
                    <a href="{{ route('blogs.list') }}" class="btn btn-secondary">{{ __('web.home.view_all_blogs') }}</a>
                </div>
            </div>
            @else 
            <div class="col-md-12">
                <p>{{ __('web.common.empty_table') }}</p>
            </div>
            @endif
        </div>
        
    </div>
</section>
<div class="yacht-owner-card party-rental-yacht">
    <div class="sec-bg">
        <img src="{{ asset('frontend/assets/img/bg/sec-bg-wave.png') }}" alt="Img">
    </div>
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-8">
            <div class="yacht-owner-title">
                <h3>Special Offers on Party rental Yachts!!!</h3>
                <p>View all the yachts under the Offer</p>
                <a href="listing-details.html" class="btn btn-primary">View Yachts</a>
            </div>
        </div>
    </div>
    <div class="yacht-owner-img">
        <img src="/frontend/assets/img/bg/yacht-owner-bg-02.png" alt="Img">
    </div>
</div>
<!-- /News & Insights -->