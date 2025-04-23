@extends('admin.admin')

@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 me-md-0 me-lg-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h4 class="mb-1">{{__('admin.blog.blogs')}}</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/admin">{{__('admin.blog.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('admin.blog.blogs')}}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="p-1 border rounded bg-white me-3 mb-2">
                    <a href="javascript:void(0);" id="listViewBtn" class="p-1 rounded d-inline-flex align-items-center justify-content-center me-1">
                        <i class="ti ti-list-tree"></i>
                    </a>
                    <a href="javascript:void(0);" id="gridViewBtn" class="p-1 rounded text-white bg-primary d-inline-flex align-items-center justify-content-center">
                        <i class="ti ti-layout-grid fs-14"></i>
                    </a>
                </div>
                <div class="mb-2">
                    @if (hasPermission($permissions, 'blogs', 'create'))
                    <a href="/admin/content/add-blog" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{__('admin.blog.add_blogs')}}</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.sort_by')}} : <span id="selectedFilter">{{__('admin.blog.latest')}}</span>
                    </a>
                    <ul id="sortDropdown" class="dropdown-menu dropdown-menu-end p-2">
                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="latest">{{__('admin.blog.latest')}}</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="asc">{{__('admin.blog.ascending')}}</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="desc">{{__('admin.blog.descending')}}</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="last_month">{{__('admin.blog.last_month')}}</a></li>
                        <li><a href="javascript:void(0);" class="dropdown-item rounded-1" data-filter="last_7_days">{{__('admin.blog.last_7_days')}}</a></li>
                    </ul>
                </div>

                <div class="dropdown">
                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.filter')}}
                    </a>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInput" placeholder="{{__('admin.blog.search')}}">
                    </div>
                </div>
            </div>
        </div>
        <!-- /Table Header -->

        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{__('admin.blog.filter')}}</h6>
                <div class="dropdown me-3">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                    {{__('admin.blog.category')}}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        @foreach($categories as $category)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 category-checkbox" type="checkbox" value="{{$category->id}}">
                                {{$category->name}}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>

        <!-- Blogs -->
        <div class="row blogs-cover grid-view" id="blogList">
            @foreach($blogPosts as $blogPost)
            <div class="col-lg-4 col-md-6 blog-item"
                data-date="{{ $blogPost->created_at }}"
                data-title="{{ $blogPost->title }}"
                data-category="{{ $blogPost->category }}">

                <div class="card blog-item-1">
                    <div class="card-body p-0">
                        <div class="blog-img">
                            <a href="/admin/content/blog-details/{{$blogPost->slug}}">
                            @php
                                    $imagePath = 'storage/' . $blogPost->image;
                                    $defaultImage = asset('custom/img/default-image-02.jpg');
                                    @endphp

                                    <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Image">
                            </a>
                            <div class="edit-delete-btns d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    @if (hasPermission($permissions, 'blogs', 'edit'))
                                    <a href="javascript:;" data-id="{{ $blogPost->id }}" id="blog-edit" class="blog-edit me-2"><i class="ti ti-edit"></i></a>
                                    @endif
                                    @if (hasPermission($permissions, 'blogs', 'delete'))
                                    <a href="javascript:void(0);" class="blog-delete" data-bs-toggle="modal" data-id="{{ $blogPost->id }}" data-bs-target="#delete_blogs"><i class="ti ti-trash"></i></a>
                                    @endif
                                </div>
                                <span class="badge badge-info badge-md">
                                    <?php
                                    $category = Modules\GeneralSetting\Models\BlogCategory::where('id', $blogPost->category)->first();
                                    ?>
                                    {{$category->name ?? '-'}}
                                </span>
                            </div>
                        </div>


                        <div class="blog-content">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);">
                                    @php
                                    $imagePath = 'storage/' . $blogPost->profile_image;
                                    $defaultImage = asset('custom/img/default-profile.png');
                                    @endphp

                                    <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="Image" class="avatar avatar-sm rounded-circle me-1">
                                    </a>
                                    <a href="javascript:void(0);" class="fs-16">{{$blogPost->name}}</a>
                                </div>
                                <span class="d-flex align-items-center fs-16">
                                    <i class="ti ti-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($blogPost->created_at)->format('d M Y') }}
                                </span>
                            </div>
                            <h5><a href="/admin/content/blog-details/{{$blogPost->slug}}">{{$blogPost->title}}</a></h5>
                        </div>
                    </div>


                </div>
            </div>
            @endforeach

        </div>
        <div class="d-flex align-items-center justify-content-center mt-3">
            @if(count($blogPosts) != 0)
                <a href="javascript:void(0);" class="load-btn btn btn-primary">
                    <i class="ti ti-loader me-1"></i> {{__('admin.blog.load_more')}}
                </a>
             @else
             <p>{{__('admin.blog.load_more')}}</p>
             @endif
        </div>
        <!-- Blogs -->
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Delete Blogs -->
<div class="modal fade" id="delete_blogs">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash fs-26"></i>
                </span>
                <h4 class="mb-1">{{__('admin.blog.delete_blog')}}</h4>
                <p class="mb-3">{{__('admin.blog.are_you_sure_you_want_to_delete_blog')}}?</p>
                <input type="hidden" id="delete_blog_id">
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</a>
                    <a href="javascript:void(0);" class="btn btn-primary" id="confirmDelete">{{__('admin.blog.yes_delete')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Blogs -->

@endsection

@push('scripts')
<script src="{{ asset('assets/js/general_setting/blog.js') }}"></script>

@endpush
