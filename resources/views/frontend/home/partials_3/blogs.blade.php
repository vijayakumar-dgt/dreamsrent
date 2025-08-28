@php
    $sectionContent = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(2, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
@endphp
<!-- Blog Section -->
<section class="blog-section bike-news">
    <div class="container">
        <!-- Heading title-->
        <div class="section-heading heading-three mx-auto" data-aos="fade-down">
            <h2>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "" }}</span></h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <!-- /Heading title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="blog-slider nav-center owl-carousel">
                    @if(!empty($sectionContent) && count($sectionContent) > 0)
                    @foreach($sectionContent as $blog)
                    <div class="blog grid-blog">
                        <div class="blog-image">
                            <a href="{{ route('blogs.detail', $blog['slug']) }}"><img class="img-fluid" src="{{ $blog['image'] ?? "" }}" alt="Blog"></a>
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
                        <div class="blog-content">
                            <h3 class="blog-title"><a href="{{ route('blogs.detail', $blog['slug']) }}">{{ $blog['title'] ?? "" }}</a></h3>
                            <p class="blog-description">{!! $blogDesc ?? "" !!}</p>
                            <div class="blog-footer">
                                <p><i class="bx bx-calendar"></i>{{ $blog['updated_at'] }}</p>
                                <a href="{{ route('blogs.detail', $blog['slug']) }}" class="read-more">{{ __('web.blog.read_more') }}<i class="bx bx-right-arrow-alt"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                    <!-- /Blog List -->
                </div>
            </div>
        </div>
        <div class="view-all-btn text-center aos-init aos-animate" data-aos="fade-down">
            <a href="{{ route('blogs.list') }}" class="btn btn-secondary">{{ __('web.home.view_all_blogs') }}</a>
        </div>

    </div>
</section>
<!-- /Blog Section -->
