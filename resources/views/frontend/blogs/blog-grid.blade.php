@extends($layout)
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('web.blog.blog_grid')}}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{__('web.blog.blogs')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('web.blog.blog_grid')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->
<!-- Blog Grid-->
<div class="blog-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="row">
                    @foreach($blogPosts as $blogPost)
                    <div class="col-lg-6 col-md-6 d-lg-flex">
                        <div class="blog grid-blog">
                            <div class="blog-image">
                                <a href="/blog-details/{{$blogPost->slug}}">
                                    <img class="img-fluid" src="{{asset('storage/' . $blogPost->image)}}" alt="Post Image">
                                </a>
                            </div>
                            <div class="blog-content">
                                <p class="blog-category">
                                    <a href="javascript:void(0)"><span>{{$blogPost->category}}</span></a>
                                </p>
                                <h3 class="blog-title">
                                    <a href="/blog-details/{{$blogPost->slug}}">{{$blogPost->title}}</a>
                                </h3>
                                <p class="blog-description">{{ Str::limit(strip_tags($blogPost->description), 250, '...') }}</p>
                                <ul class="meta-item">
                                    <li>
                                        <div class="post-author">
                                            <div class="post-author-img">
                                                @php
                                                    $imagePath = 'storage/' . $blogPost->profile_image;
                                                    $defaultImage = asset('/backend/assets/img/default-profile.png');
                                                @endphp
                                                <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="author">
                                            </div>
                                            <a href="javascript:void(0)">
                                                <span><a href="javascript:void(0)"><span>{{$blogPost->customer}}</span></a></span>
                                            </a>
                                        </div>
                                    </li>
                                    <li class="date-icon">
                                        <i class="fa-solid fa-calendar-days"></i>
                                        <span>{{ \Carbon\Carbon::parse($blogPost->created_at)->format('d M Y') }}</span>
                                    </li>
                                </ul>
                                <a href="/blog-details/{{$blogPost->slug}}" class="viewlink btn btn-primary">
                                    {{__('web.blog.read_more')}} <i class="feather-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <!--Pagination-->
                <div class="pagination">
                    {{ $blogPosts->links('pagination::bootstrap-5') }}
                </div>
                <!--/Pagination-->
            </div>
            <div class="col-lg-4 theiaStickySidebar">
                <div class="rightsidebar">
                    <div class="card">
                        <h4>
                            <img src="/backend/assets/img/icons/details-icon.svg" alt="details-icon"> {{__('web.blog.filter')}}
                        </h4>
                        <div class="filter-content looking-input input-block mb-0">
                            <input type="text" id="blogSearch" class="form-control" placeholder="To Search type and hit enter">
                        </div>
                    </div>
                    <div class="card">
                        <h4>
                            <img src="/backend/assets/img/icons/category-icon.svg" alt="details-icon"> {{__('web.blog.categories')}}
                        </h4>
                        <ul class="blogcategories-list">
                            @foreach($categories as $category)
                            <li><a href="javascript:void(0)">{{$category->name}}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card tags-widget">
                        <h4>
                            <i class="feather-tag"></i> {{__('web.blog.tags')}}
                        </h4>
                        <ul class="tags">
                            @foreach($tags as $tag)
                            <li>{{$tag->name}}</li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card mb-0">
                        <h4>
                            <i class="feather-tag"></i>{{__('web.blog.top_article')}}
                        </h4>
                        @foreach($latestblogs as $latest)
                        <div class="article">
                            <div class="article-blog">
                                <a href="/blog-details/{{$latest->id}}">
                                    @php
                                        $imagePath = 'storage/' . $latest->image;
                                        $defaultImage = asset('/backend/assets/img/default-profile.png');
                                    @endphp
                                    <img class="img-fluid" src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Post Image">
                                </a>
                            </div>
                            <div class="article-content">
                                <h5>
                                    <a href="/blog-details/{{$latest->id}}">{{$latest->title}}</a>
                                </h5>
                                <div class="article-date">
                                    <i class="fa-solid fa-calendar-days"></i>
                                    <span>{{ \Carbon\Carbon::parse($latest->created_at)->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Blog Grid-->
@endsection