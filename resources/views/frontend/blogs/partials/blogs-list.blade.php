@if(count($blogPosts) != 0)
@foreach($blogPosts as $blogPost)
<div class="col-lg-12 col-md-12 d-lg-flex" id="blogs-filter-item" data-category="{{ $blogPost->category }}">
    <div class="blog grid-blog">
        <div class="blog-image-list">
            <a href="/blog-details/{{$blogPost->slug}}"><img class="img-fluid" src="{{asset('storage/' . $blogPost->image)}}" alt="Post Image"></a>
        </div>
        <div class="blog-content">
            <div class="blog-list-date">
                <ul class="meta-item-list">
                    <li>
                        <div class="post-author">
                            <div class="post-author-img">
                                @php
                                $imagePath = 'storage/' . $blogPost->profile_image;
                                $defaultImage = asset('custom/img/default-profile.png');
                                @endphp

                                <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Post Image">

                            </div>
                            <a href="javascript:void(0)"> <span> {{$blogPost->customer}} </span></a>
                        </div>
                    </li>
                    <li class="date-icon ms-3"><i class="fa-solid fa-calendar-days custom-calendar"></i> <span>{{ \Carbon\Carbon::parse($blogPost->created_at)->format('d M Y') }}</span></li>
                </ul>
                <p class="blog-category mb-0">
                    <a href="javascript:void(0)"><span>{{ucfirst($blogPost->category)}}</span></a>
                </p>
            </div>
            <h3 id="blog-title"><a href="/blog-details/{{$blogPost->slug}}">{{ucfirst($blogPost->title)}}</a></h3>
            <p id="blog-description">{{ Str::limit(strip_tags($blogPost->description), 250, '...') }}</p>
            <a href="/blog-details/{{$blogPost->slug}}" class="viewlink btn btn-primary justify-content-center">{{__('web.blog.read_more')}} <i class="feather-arrow-right ms-2"></i></a>
        </div>
    </div>
</div>
@endforeach
@else
 <h4 class="no-blog">{{__('web.blog.no_blog_found')}}</h4>
@endif

<!--Pagination-->
<div class="pagination">
@if ($blogPosts->lastPage() > 1)
    <nav class="d-flex justify-content-center mt-4">
        <ul class="pagination custom-pagination mb-0">
            <li class="page-item {{ $blogPosts->onFirstPage() ? 'disabled' : '' }}">
                <a class="page-link prev-next" href="{{ $blogPosts->previousPageUrl() }}">&larr; Prev</a>
            </li>

            @for ($i = 1; $i <= $blogPosts->lastPage(); $i++)
                <li class="page-item {{ $blogPosts->currentPage() == $i ? 'active' : '' }}">
                    <a class="page-link number-btn" href="{{ $blogPosts->url($i) }}">{{ $i }}</a>
                </li>
            @endfor

            <li class="page-item {{ !$blogPosts->hasMorePages() ? 'disabled' : '' }}">
                <a class="page-link prev-next" href="{{ $blogPosts->nextPageUrl() }}">Next &rarr;</a>
            </li>
        </ul>
    </nav>
@endif

</div>
<!--/Pagination-->
