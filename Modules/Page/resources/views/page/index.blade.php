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
                            <a href="{{ route('dashboard') }}">{{__('admin.common.home')}}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{__('admin.page.pages')}}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
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
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }}: <span id="sortLabel">{{ __('admin.common.latest') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="sortFilter">
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="latest">
                                {{ __('admin.common.latest') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="asc">
                                {{ __('admin.common.ascending') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="desc">
                                {{ __('admin.common.descending') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="last_month">
                                {{ __('admin.common.last_month') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="last_7_days">
                                {{ __('admin.common.last_7_days') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-badge me-1"></i> {{ __('admin.common.status') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="statusFilter">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" data-status="1">
                                {{ __('admin.cms.published') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" data-status="0">
                                {{ __('admin.cms.unpublished') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="">
                    <select class="form-select select" id="language_id" name="language_id">
                        <option value="">{{ __('admin.page.select') }}</option>
                        @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">
                            {{ $language->transLang->name ?? 'N/A' }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
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
        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
            @include('admin.content-loader')
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
