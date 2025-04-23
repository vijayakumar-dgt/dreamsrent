<section class="blog-section news-section">
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading" data-aos="fade-down">
            <h2>{{ $section['section_title'] }}</h2>
            <p>{{ $section['section_label'] }}</p>
        </div>
        <!-- /Heading title -->

        <div class="row">
            @foreach($section['section_content'] as $blog)
            <div class="col-lg-4 col-md-6 d-lg-flex">
                <div class="blog grid-blog">
                    <div class="blog-image">
                        <a href="/blog-details/{{ $blog['id'] }}"><img class="img-fluid" src="{{ $blog['image'] }}" alt="Post Image"></a>
                    </div>
                    <div class="blog-content">
                         <p class="blog-category">
                           <a href="javascript:void(0)"><span>{{ $blog['category'] ?? "" }}</span></a>
                        </p>
                        @php
                        $blogDesc = strlen($blog['description']) > 120 ? substr($blog['description'], 0, 120) . '...' : $blog['description'];
                        @endphp
                        <h3 class="blog-title"><a href="/blog-details/{{ $blog['id'] }}">{{ $blog['title'] ?? "" }}</a></h3>
                        <p class="blog-description">{{ $blogDesc }}</p>
                        <ul class="meta-item mb-0">
                            <li>
                                <div class="post-author">
                                    <div class="post-author-img">
                                        <img src="{{ $blog['author']['avatar'] }}" alt="author">
                                    </div>
                                    <a href="javascript:void(0)"> <span> {{ $blog['author']['name'] ? $blog['author']['name'] : "" }} </span></a>
                                </div>
                            </li>
                            <li class="date-icon"><i class="fa-solid fa-calendar-days"></i> <span>{{ $blog['updated_at'] ?? "" }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="view-all text-center aos-init aos-animate" data-aos="fade-down">
            <a href="/blogs" class="btn btn-view d-inline-flex align-items-center">{{  __('web.home.view_all_blogs') }} <span><i class="feather-arrow-right ms-2"></i></span></a>
        </div>
    </div>
</section>
