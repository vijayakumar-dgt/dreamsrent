@extends('admin.admin')

@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{__('admin.blog.blog_comments')}}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/admin">{{__('admin.blog.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('admin.blog.blog_comments')}}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" id="selectedFilter" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.sort_by')}} : <span id="filterText">{{__('admin.blog.latest')}}</span>
                    </a>
                    <ul class="dropdown-menu  dropdown-menu-end p-2">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">{{__('admin.blog.latest')}}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">{{__('admin.blog.ascending')}}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">{{__('admin.blog.descending')}}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">{{__('admin.blog.last_month')}}</a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1">{{__('admin.blog.last_7_days')}}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" id="tableSearch" placeholder="{{__('admin.blog.search')}}">
                        </div>
                </div>
                <div class="dropdown">
                    <a href="javascript:void(0);" class="dropdown-toggle coloumn btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-layout-board me-1"></i> {{__('admin.blog.columns')}}
                    </a>
                    <div class="dropdown-menu dropdown-menu-lg p-2">
                        <ul>
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                                    <span class="d-inline-flex align-items-center"><i class="ti ti-grip-vertical me-1"></i>{{__('admin.blog.REVIEW')}}</span>

                                </div>
                            </li>
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                                    <span><i class="ti ti-grip-vertical me-1"></i>{{__('admin.blog.CREATED_DATE')}} </span>

                                </div>
                            </li>
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                                    <span><i class="ti ti-grip-vertical me-1"></i>{{__('admin.blog.BLOG')}}</span>

                                </div>
                            </li>
                            <li>
                                <div class="dropdown-item d-flex align-items-center justify-content-between rounded-1">
                                    <span><i class="ti ti-grip-vertical me-1"></i>{{__('admin.blog.CUSTOMER')}}</span>

                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
        <!-- /Table Header -->

        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive">
            <table class="table custom-blog-table" id="blogCommentTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{__('admin.blog.REVIEW')}}</th>
                        <th>{{__('admin.blog.CREATED_DATE')}} </th>
                        <th>{{__('admin.blog.BLOG')}}</th>
                        <th>{{__('admin.blog.CUSTOMER')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($comments as $comment)
                    <tr>
                        <td>
                            <p class="text-gray-9 text-truncate">{{$comment->comments}}</p>

                        </td>
                        <td>
                            <p class="text-gray-9">{{ \Carbon\Carbon::parse($comment->comment_date)->format('d M Y') }}</p>

                        </td>
                        <td>
                            <p class="text-gray-9 text-truncate">{{$comment->title}}</p>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="fw-semibold"><a href="javascript:void(0);">{{$comment->name}}</a></h6>
                                </div>
                            </div>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Delete Modal  -->
<div class="modal fade" id="delete_page">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">{{__('admin.blog.delete_page')}}</h4>
                <p class="mb-3">{{__('admin.blog.are_you_sure_you_want_to_delete_page')}}?</p>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</a>
                    <a href="pages.html" class="btn btn-primary">{{__('admin.blog.yes_delete')}}</a>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal-->

@endsection
@push('scripts')
<script src="{{ asset('assets/js/general_setting/blog-comments.js') }}"></script>

@endpush