@extends($layout)
@section('content')
<!-- Breadscrumb Section -->
<div class="blogbanner">
    <div class="blogbanner-content">
        <span class="blog-hint">{{$blogPosts->category}}</span>
        <h1>{{$blogPosts->title}}</h1>
        <ul class="entry-meta meta-item justify-content-center">
            <li>
                <div class="post-author">
                    <div class="post-author-img">
                        @php
                        $imagePath = 'storage/' . $blogPosts->profile_image;
                        $defaultImage = asset('custom/img/default-profile.png');
                        @endphp

                        <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Post Image">

                    </div>
                    <a href="javascript:void(0)"><span> {{$blogPosts->customer}} </span></a>
                </div>
            </li>
            <li class="date-icon"><i class="fa-solid fa-calendar-days"></i> {{ \Carbon\Carbon::parse($blogPosts->created_at)->format('d M Y') }}</li>
        </ul>
    </div>
</div>
<!-- /Breadscrumb Section -->

<!-- Blog Grid-->
<div class="blog-section">
    <div class="container">
        <div class="blog-description">
        {!! $blogPosts->description !!}
        </div>
        <div class="row">
            <div class="col-lg-12 col-md-12">
                <div class="bloginner-img">
                    <img src="{{asset('/storage/'.$blogPosts->image)}}" class="img-fluid" alt="Blog">
                </div>
            </div>
        </div>
        <div class="share-postsection">
            <div class="row">
                <div class="col-lg-4">


                </div>
                <div class="col-lg-8">
                    <div class="tag-list">
                        <ul class="tags">
                            @php
                            $tagIds = is_array($blogPosts->tags) ? $blogPosts->tags : json_decode($blogPosts->tags, true);
                            $tagNames = \Modules\GeneralSetting\Models\BlogTag::whereIn('id', $tagIds)->pluck('name');
                            @endphp

                            @foreach($tagNames as $tagName)
                            <li>{{ $tagName }}</li>
                            @endforeach
                        </ul>
                    </div>

                </div>
            </div>
        </div>
        <div class="blogdetails-pagination">
            <ul>
                <li>
                    <a href="/blog-details/{{$otherBlogs[0]->slug ?? ''}}" class="prev-link"><i class="fas fa-regular fa-arrow-left"></i> {{__('web.blog.previous_post')}}</a>
                    <a href="/blog-details/{{$otherBlogs[0]->slug ?? ''}}">
                        <h3>{{$otherBlogs[0]->title ?? ''}}</h3>
                    </a>
                </li>
                <li>
                    <a href="/blog-details/{{$otherBlogs[1]->slug ?? ''}}" class="next-link">{{__('web.blog.next_post')}} <i class="fas fa-regular fa-arrow-right"></i> </a>
                    <a href="/blog-details/{{$otherBlogs[1]->slug ?? ''}}">
                        <h3>{{$otherBlogs[1]->title ?? ''}}</h3>
                    </a>
                </li>
            </ul>
        </div>
        <div class="review-sec mb-0">
            <div class="review-header">
                <h4>{{__('web.blog.reviews')}}<span>({{$countReview}})</span></h4>
            </div>
            @foreach($blogReviews as $review)
            <div class="review-card">
                <div class="review-header-group">
                    <div class="review-widget-header">
                        <span class="review-widget-img">
                            <img class="img-fluid" src="{{ asset('custom/img/default-profile.png')}}" alt="Post Image">
                        </span>
                        <div class="review-design">
                            <h6>{{$review->name}}</h6>
                            <p>{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                <p>{{$review->comments}}</p>
            </div>
            @endforeach
        </div>

        <div class="review-sec mb-0">
            <div class="review-header">
                <h4>{{__('web.blog.leave_a_reply')}}</h4>
            </div>
            <div class="card-body">
                <form id="blogReviewForm" action="{{ route('blogs.review.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="blog_id" value="{{ $blogPosts->id }}">
                    <div class="review-list">
                        <ul>
                            <li class="review-box feedbackbox mb-0">
                                <div class="review-details">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <div class="input-block">
                                                <label>{{ __('web.blog.full_name') }} <span class="text-danger">*</span></label>
                                                <input type="text" name='name' class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="input-block">
                                                <label>{{ __('web.blog.email_address') }} <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" required>
                                            </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <div class="input-block">
                                                <label>{{ __('web.blog.comments') }}</label>
                                                <textarea rows="4" name="comment" class="form-control" required></textarea>
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

            </div>
        </div>
    </div>
</div>
<!-- /Blog Grid-->
@endsection
@push('scripts')
<script src="{{ asset('frontend/assets/js/blogs/blog.js') }}"></script>
@endpush