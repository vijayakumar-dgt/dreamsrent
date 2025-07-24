<section class="blog-section news-section">
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading" data-aos="fade-down">
            <h2>{{ $section['section_title'] }}</h2>
            <p>{{ $section['section_label'] }}</p>
        </div>
        <!-- /Heading title -->
         @php
             $blogs = $section['section_content'] ?? [];
             $section['section_content'] = array_slice($blogs, 0, 3);
         @endphp
        <div class="row">
            @if(!empty($section['section_content']) && count($section['section_content']) > 0)
            @foreach($section['section_content'] as $blog)
            <div class="col-lg-4 col-md-6 d-lg-flex">
                <div class="blog grid-blog">
                    <div class="blog-image">
                        <a href="{{ route('blogs.detail', $blog['slug']) }}"><img class="img-fluid" src="{{ $blog['image'] }}" alt="{{ $blog['title'] ?? "" }}"></a>
                    </div>
                    <div class="blog-content">
                         <p class="blog-category mb-2">
                           <a href="javascript:void(0)" class="mb-0"><span>{{ ucfirst($blog['category'] ?? "") }}</span></a>
                        </p>
                        @php
                        $cleanDesc = strip_tags($blog['description']);
                        $blogDesc = strlen($cleanDesc) > 120 ? substr($cleanDesc, 0, 120) . '...' : $cleanDesc;
                        @endphp
                        <h3 class="blog-title"><a href="{{ route('blogs.detail', $blog['slug']) }}">{{ ucfirst($blog['title'] ?? "") }}</a></h3>
                        <p class="blog-description mb-0">{!! $blogDesc !!}</p>
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
        @if(!empty($section['section_content']) && count($section['section_content']) > 0)
        <div class="view-all text-center aos-init aos-animate" data-aos="fade-down">
            <a href="{{ route('blogs.list') }}" class="btn btn-view d-inline-flex align-items-center">{{  __('web.home.view_all_blogs') }} <span><i class="feather-arrow-right ms-2"></i></span></a>
        </div>
        @endif
    </div>
</section>
