    @extends($layout)
    @section('content')
    <!-- Breadscrumb Section -->
    <div class="blogbanner">
        <div class="blogbanner-content">
            <span class="blog-hint">{{ ucfirst($blogPosts->category) }}</span>
            <h1>{{ ucfirst($blogPosts->title) }}</h1>
            <ul class="entry-meta meta-item justify-content-center">
                <li>
                    <div class="post-author">
                        <div class="post-author-img">
                            @php
                            $imagePath = 'storage/' . $blogPosts->profile_image;
                            $defaultImage = asset('assets/img/default-profile.png');
                            @endphp
                            <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Profile">
                        </div>
                        <a href="javascript:void(0)"><span>{{ $blogPosts->full_name ?? $blogPosts->customer }}</span></a>
                    </div>
                </li>
                <li class="date-icon"><i class="fa-solid fa-calendar-days custom-cal"></i> {{ formatDateTime($blogPosts->created_at) }}</li>
            </ul>
        </div>
    </div>
    <!-- /Breadscrumb Section -->
    <!-- Blog Grid-->
    <div class="blog-section">
        <div class="container">
            <div class="row g-4 g-lg-6">
                <div class="col-lg-8 mx-auto">
                    <div class="bloginner-img mt-0">
                        <img src="{{ asset('/storage/' . $blogPosts->image) }}" class="img-fluid" alt="Blog Post">
                    </div>
                    <div class="blog-description">
                        {!! $blogPosts->description !!}
                    </div>
                    <div class="share-postsection">
                        <div class="row">
                            <div class="col-lg-4"></div>
                            <div class="col-lg-8">
                                <div class="tag-list">
                                    <ul class="tags">
                                        @php
                                        $tagIds = is_array($blogPosts->tags) ? $blogPosts->tags : json_decode($blogPosts->tags, true);
                                        $tagNames = \Modules\GeneralSetting\Models\BlogTag::whereIn('id', $tagIds)->pluck('name');
                                        @endphp
                                        @foreach($tagNames as $tagName)
                                        <li>{{ ucfirst($tagName) }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="blogdetails-pagination">
                        <ul>
                            <li>
                                <a href="{{ route('blogs.detail', $otherBlogs[0]->slug ?? '#') }}" class="prev-link">
                                    <i class="fas fa-regular fa-arrow-left"></i> {{ __('web.blog.previous_post') }}
                                </a>
                                <a href="{{ route('blogs.detail', $otherBlogs[0]->slug ?? '#') }}">
                                    <h3>{{ ucfirst($otherBlogs[0]->title ?? '') }}</h3>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('blogs.detail', $otherBlogs[1]->slug ?? '#') }}" class="next-link">
                                    {{ __('web.blog.next_post') }} <i class="fas fa-regular fa-arrow-right"></i>
                                </a>
                                <a href="{{ route('blogs.detail', $otherBlogs[1]->slug ?? '#') }}">
                                    <h3>{{ ucfirst($otherBlogs[1]->title ?? '') }}</h3>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="review-sec mb-0">
                        <div class="review-header">
                            <h4>{{ __('web.blog.reviews') }}<span>({{ $countReview }})</span></h4>
                        </div>
                        @foreach($blogReviews as $review)
                        <div class="review-card">
                            <div class="review-header-group">
                                <div class="review-widget-header">
                                    <span class="review-widget-img">
                                        <img class="img-fluid" src="{{ uploadedAsset('default','profile') }}" alt="Blog Post">
                                    </span>
                                    <div class="review-design">
                                        <h6>{{ $review->name }}</h6>
                                        <p>{{ formatDateTime($review->created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                            <p>{{ $review->comments }}</p>
                        </div>
                        @endforeach
                    </div>
                    <div class="review-sec mb-0">
                        <div class="review-header">
                            <h4>{{ __('web.blog.leave_a_reply') }}</h4>
                        </div>
                        <div class="card-body">
                            @auth
                            <form id="blogReviewForm" action="{{ route('blogs.review.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="blog_id" value="{{ $blogPosts->id }}">
                                <div class="review-list">
                                    <ul>
                                        <li class="review-box feedbackbox mb-0">
                                            <div class="review-details">
                                                <div class="row">
                                                    <div class="col-lg-12">
                                                        <div class="input-block">
                                                            <label for="comment" class="form-label">{{ __('web.blog.comments') }}</label>
                                                            <textarea rows="4" name="comment" id="comment" class="form-control" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="submit-section">
                                                    <button class="btn btn-primary submit-review" id="blogReviewBtn" type="submit">
                                                        {{ __('web.blog.submit_review') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </form>
                            @else
                            <div class="alert alert-warning mt-3" role="alert">
                                  Please login to comment
                            </div>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Blog Grid-->
    @endsection
    @push('scripts')
       <script src="{{ asset('frontend/assets/js/blogs/blog.js') }}"></script>
    @endpush
