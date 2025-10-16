@if(count($blogPosts) != 0)
    @foreach($blogPosts as $blogPost)
        <div class="col-lg-12 col-md-12 d-lg-flex" id="blogs-filter-item-{{ $blogPost->id }}" data-category="{{ $blogPost->category }}">
            <div class="blog grid-blog">
                <div class="blog-image-list custom-blog-list-img">
                    <a href="{{ route('blogs.detail', $blogPost->slug) }}">
                        <img class="img-fluid" src="{{ asset('storage/' . $blogPost->image) }}" alt="Post">
                    </a>
                </div>
                <div class="blog-content">
                    <div class="blog-list-date gap-2 flex-wrap">
                        <ul class="meta-item-list gap-2 flex-wrap">
                            <li>
                                <div class="post-author">
                                    <div class="post-author-img">
                                        @php
                                            $imagePath = 'storage/' . $blogPost->profile_image;
                                            $defaultImage = asset('assets/img/default-profile.png');
                                        @endphp
                                        <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Post">
                                    </div>
                                    <a href="javascript:void(0)">
                                        <span>{{ $blogPost->full_name ?? $blogPost->customer }}</span>
                                    </a>
                                </div>
                            </li>
                            <li class="date-icon ms-3">
                                <i class="fa-solid fa-calendar-days custom-calendar"></i>
                                <span>{{ formatDateTime($blogPost->created_at) }}</span>
                            </li>
                        </ul>
                        <p class="blog-category mb-0 mx-2">
                            <a href="javascript:void(0)">
                                <span>{{ ucfirst($blogPost->category) }}</span>
                            </a>
                        </p>
                    </div>
                    <h3 id="blog-title-{{ $blogPost->id }}">
                        <a href="{{ route('blogs.detail', $blogPost->slug) }}">{{ ucfirst($blogPost->title) }}</a>
                    </h3>
                    <p id="blog-description-{{ $blogPost->id }}" class="mt-3">{{ Str::limit(strip_tags($blogPost->description), 250, '...') }}</p>
                    <a href="{{ route('blogs.detail', $blogPost->slug) }}" class="viewlink mt-4 btn btn-primary justify-content-center">
                        {{ __('web.blog.read_more') }} <i class="feather-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
        </div>
    @endforeach
@else
    <h4 class="no-blog">{{ __('web.blog.no_blog_found') }}</h4>
@endif

<!--/Pagination-->
<div class="pagination">
    @if ($blogPosts->lastPage() > 1)
        <nav>
            <ul class="pagination mt-0">
                <!-- Previous Button -->
                <li class="previtem {{ $blogPosts->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $blogPosts->previousPageUrl() ?? '#' }}">
                        <i class="fas fa-regular fa-arrow-left me-2"></i> {{ __('web.user.prev') }}
                    </a>
                </li>

                <!-- Page Numbers -->
                <li class="justify-content-center pagination-center">
                    <div class="page-group">
                        <ul>
                            @for ($i = 1; $i <= $blogPosts->lastPage(); $i++)
                                <li class="page-item">
                                    <a class="page-link {{ $blogPosts->currentPage() == $i ? 'active' : '' }}"
                                       href="{{ $blogPosts->url($i) }}">
                                        {{ $i }}
                                        @if ($blogPosts->currentPage() == $i)
                                            <span class="visually-hidden">(current)</span>
                                        @endif
                                    </a>
                                </li>
                            @endfor
                        </ul>
                    </div>
                </li>

                <!-- Next Button -->
                <li class="nextlink {{ !$blogPosts->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $blogPosts->nextPageUrl() ?? '#' }}">
                        {{ __('web.user.next') }} <i class="fas fa-regular fa-arrow-right ms-2"></i>
                    </a>
                </li>
            </ul>
        </nav>
    @endif
</div>
<!--/Pagination-->
