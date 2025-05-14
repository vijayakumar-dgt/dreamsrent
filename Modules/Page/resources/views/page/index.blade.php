@extends('admin.admin')

@section('meta_title', __('admin.page.pages') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{__('admin.page.pages')}}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{__('admin.page.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('admin.page.pages')}}</li>
                    </ol>
                </nav>
            </div>
            <div class="skeleton label-skeleton label-loader me-2"></div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap  d-none real-label">
                <div class="mb-2">
                    <div>
                        @if (hasPermission($permissions, 'page', 'create'))
                        <a href="{{ route('admin.addPage') }}" class="btn btn-primary btn-md d-inline-flex justify-content-center align-items-center">
                            <i class="ti ti-plus me-1"></i>{{__('admin.page.add_page')}}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="skeleton label-skeleton label-loader me-2"></div>
                <div class="dropdown me-2 d-none real-label">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.page.sort_by') }}: <span id="sortLabel">{{ __('admin.page.latest') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="sortFilter">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'latest')">
                                {{ __('admin.page.latest') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'asc')">
                                {{ __('admin.page.ascending') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'desc')">
                                {{ __('admin.page.descending') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'last_month')">
                                {{ __('admin.page.last_month') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'last_7_days')">
                                {{ __('admin.page.last_7_days') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="skeleton label-skeleton label-loader me-2"></div>
                <div class="dropdown me-2 d-none real-label">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-badge me-1"></i> {{ __('admin.page.status') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="statusFilter">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterPages(this, 1)">
                                {{ __('admin.page.published') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterPages(this, 0)">
                                {{ __('admin.page.unpublished') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="skeleton label-skeleton label-loader me-2"></div>
                <div class="d-none real-label">
                    <select class="form-select select" id="language_id" name="language_id" onchange="filterlang()">
                        <option value="">{{ __('admin.page.select') }}</option>
                        @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">
                            {{ $language->transLang->name ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="skeleton label-skeleton label-loader me-2"></div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3 d-none real-label">
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" name="search" id="search" class="form-control" placeholder="{{ __('admin.page.search')}}">
                    </div>
                </div>
            </div>
        </div>
        <!-- /Table Header -->
        <input type="hidden" name="lang_id" id="lang_id" value="{{ $authUser->language_id }}">
        <div class="custom-datatable-filter table-responsive table-loader">
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th class="text-center">
                            <div class="skeleton th-skeleton th-loader"></div>
                        </th>
                        <th>
                            <div class="skeleton th-skeleton th-loader"></div>
                        </th>
                        <th>
                            <div class="skeleton th-skeleton th-loader"></div>
                        </th>
                        <th>
                            <div class="skeleton th-skeleton th-loader"></div>
                        </th>
                        <th>
                            <div class="skeleton th-skeleton th-loader"></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
            <table class="table datatable" id="page_datatable">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.cms.page')) }}</th>
                        <th>{{ strtoupper(__('admin.page.page_slug')) }}</th>
                        <th>{{ strtoupper(__('admin.common.last_updated')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        @if (hasPermission($permissions, 'page', 'edit') || hasPermission($permissions, 'page', 'delete'))
                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <div class="table-footer d-none"></div>
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Delete Modal  -->
<div class="modal fade" id="delete_page">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center">
                <form id="deletePage">
					@csrf
                    <input type="hidden" name="delete_id" id="delete_id">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">Delete Page</h4>
                    <p class="mb-3">Are you sure you want to delete page?</p>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Delete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/page/list.js') }}"></script>
@endpush