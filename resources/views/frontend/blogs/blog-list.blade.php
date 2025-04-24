@extends($layout)
@section('content')

<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{__('web.blog.blog_list')}}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item"><a href="#">{{__('web.blog.blogs')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('web.blog.blog_list')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

<!-- Blog List-->
<div class="blog-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="row" id="blogListContainer">
                    @include('frontend.blogs.partials.blogs-list', ['blogPosts' => $blogPosts])
                </div>

            </div>
            <div class="col-lg-4">
                <div class="rightsidebar">
                    <div class="card">
                        <h4><img src="assets/img/icons/details-icon.svg" alt="details-icon"> {{__('web.blog.filter')}}</h4>
                        <div class="filter-content looking-input input-block mb-0">
                            <input type="text" id="blogSearch" class="form-control" placeholder="{{__('web.blog.to_search_type_and_hit_enter')}}">
                        </div>
                    </div>
                    <div class="card">
                        <h4><img src="assets/img/icons/category-icon.svg" alt="details-icon"> {{__('web.blog.categories')}}</h4>
                        <ul class="blogcategories-list">
                            @if(count($categories) != 0)
                            @foreach($categories as $category)
                            <li><a href="javascript:void(0)" class="category-filter" data-category="{{ $category->name }}">{{ $category->name }}</a></li>
                            @endforeach
                            @else
                            <p class="no-datas mt-3">{{__('web.blog.no_data_found')}}</p>
                            @endif
                        </ul>
                    </div>
                    <div class="card tags-widget">
                        <h4><i class="feather-tag"></i> {{__('web.blog.tags')}}</h4>
                        <ul class="tags">
                            @if(count($tags) != 0)
                            @foreach($tags as $tag)
                            <li>{{$tag->name}} </li>
                            @endforeach
                            @else
                            <p class="no-datas mt-3">{{__('web.blog.no_data_found')}}</p>
                            @endif
                        </ul>
                    </div>
                    <div class="card mb-0">
                        <h4><i class="feather-tag"></i>{{__('web.blog.top_article')}}</h4>
                        @if(count($latestblogs) != 0)
                        @foreach($latestblogs as $latest)
                        <div class="article">
                            <div class="article-blog">
                                <a href="/blog-details/{{$latest->slug}}">
                                    @php
                                    $imagePath = 'storage/' . $latest->image;
                                    $defaultImage = asset('custom/img/default-profile.png');
                                    @endphp

                                    <img class="img-fluid" src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Post Image">
                                </a>
                            </div>
                            <div class="article-content">
                                <h5><a href="/blog-details/{{$latest->slug}}">{{$latest->title}}</a></h5>
                                <div class="article-date">
                                    <i class="fa-solid fa-calendar-day"></i>
                                    <span>{{ \Carbon\Carbon::parse($latest->created_at)->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <p class="no-datas mt-3">{{__('web.blog.no_data_found')}}</p>
                        @endif
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
