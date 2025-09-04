@extends('admin.admin')

@section('meta_title', __('admin.blog.blog_comments') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb :title="__('admin.blog.blog_comments')" :breadcrumbs="[
					__('admin.blog.blog_comments') => ''
				]" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button type="button" id="selectedFilter"
                        class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                        data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.sort_by')}} : <span
                            id="filterText">{{__('admin.blog.latest')}}</span>
                    </button>
                    <ul class="dropdown-menu  dropdown-menu-end p-2">
                        <li>
                            <button type="button" class="dropdown-item rounded-1">{{__('admin.blog.latest')}}</button>
                        </li>
                        <li>
                            <button type="button"
                                class="dropdown-item rounded-1">{{__('admin.blog.ascending')}}</button>
                        </li>
                        <li>
                            <button type="button"
                                class="dropdown-item rounded-1">{{__('admin.blog.descending')}}</button>
                        </li>
                        <li>
                            <button type="button"
                                class="dropdown-item rounded-1">{{__('admin.blog.last_month')}}</button>
                        </li>
                        <li>
                            <button type="button"
                                class="dropdown-item rounded-1">{{__('admin.blog.last_7_days')}}</button>
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
                        <input type="text" class="form-control" id="tableSearch"
                            placeholder="{{__('admin.blog.search')}}">
                    </div>
                </div>
            </div>
        </div>
        <!-- /Table Header -->
        <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
            @include('admin.content-loader')
        </div>
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
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
                    @if(count($comments) != 0)
                    @foreach($comments as $comment)
                    <tr>
                        <td>
                            <p class="text-gray-9 text-truncate">{{$comment->comments}}</p>

                        </td>
                        <td>
                            <p class="text-gray-9">{{ formatDateTime($comment->created_at, false) }}</p>

                        </td>
                        <td>
                            <p class="text-gray-9 text-truncate">{{$comment->title}}</p>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h6 class="fw-semibold text-black">{{$comment->name}}</h6>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="table-footer"></div>
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
                    <button type="button" class="btn btn-light me-3"
                        data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</button>
                    <button type="button" class="btn btn-primary">{{__('admin.blog.yes_delete')}}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/blog-comments.js') }}"></script>
@endpush
