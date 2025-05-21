@extends('admin.admin')

@section('meta_title', __('admin.cms.menu_management') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ __('admin.cms.menu_management') }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.cms.menu_management') }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    @if (hasPermission($permissions, 'menu_management', 'create'))
                    <div class="mb-2 me-2">
                        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_menu"><i class="ti ti-plus me-2"></i>{{ __('admin.cms.add_new_menu') }}</button>
                    </div>
                    @endif
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</button>
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
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Real Menu Table -->
            <div class="custom-datatable-filter d-none real-table">
                <table id="menuTable" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.cms.menu')) }}</th>
                            <th>{{ strtoupper(__('admin.cms.menu_type')) }}</th>
                            <th>{{ strtoupper(__('admin.cms.created_date')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                            @if (hasPermission($permissions, 'menu_management', 'edit') || hasPermission($permissions, 'menu_management', 'delete'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- Table Footer -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add menu -->
    <div class="modal fade" id="add_menu">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <form id="addMenu">
                    <div class="modal-header">
                        <h5 class="mb-0">{{ __('admin.cms.add_menu') }}</h5>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label" for="language">{{ __('admin.general_settings.language') }} <span class="text-danger">*</span></label>
                            <select class="form-select" id="language" name="language">
                                @foreach($languages as $language)
                                    <option value="{{ $language->language_id }}">
                                        {{ $language->transLang->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="language_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="MenuType">{{ __('admin.cms.menu_type') }}<span class="text-danger">*</span></label>
                            <select id="menu_type" name="menu_type" class="form-select">
                                <option value="header">{{ __('admin.cms.header') }}</option>
                                <option value="footer">{{ __('admin.cms.footer') }}</option>
                            </select>
                            <span class="text-danger error-message" id="menu_typeError"></span>
                        </div>
                        <!-- Menu Name -->
                        <div class="mb-3">
                            <label class="form-label" for="menuName">{{ __('admin.cms.menu_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="menuName" name="menu_name">
                            <span class="text-danger error-message" id="menuNameError"></span>
                        </div>
                        <!-- Permalink -->
                        <div class="mb-3">
                            <label class="form-label" for="menuPermalink">{{ __('admin.cms.permalink') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="menuPermalink" name="menu_permalink">
                            <span class="text-danger error-message" id="menuPermalinkError"></span>
                        </div>
                        <p>{{ __('admin.cms.preview') }}: <span class="text-info">https://www.example.com</span></p>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" id="createMenuBtn">{{ __('admin.common.create_new') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add menu -->

    <!-- Edit menu -->
    <div class="modal fade" id="edit_menu">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <form id="editMenuForm">
                    <div class="modal-header">
                        <h4 class="mb-0">{{ __('admin.cms.edit_menu') }}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="menuId" name="menu_id">
                        <div class="mb-3">
                            <label class="form-label" for="editFaqLanguage">{{ __('admin.general_settings.language') }} <span class="text-danger">*</span></label>
                            <select id="editMenuLanguage" name="language" class="form-select">
                                @foreach($languages as $language)
                                    <option value="{{ $language->language_id }}">
                                        {{ $language->transLang->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message" id="editFaqLanguageError"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="editMenuType">{{ __('admin.cms.menu_type') }}<span class="text-danger">*</span></label>
                            <select id="editMenuType" name="editMenuType" class="form-select">
                                <option value="header">{{ __('admin.cms.header') }}</option>
                                <option value="footer">{{ __('admin.cms.footer') }}</option>
                            </select>
                            <span class="text-danger error-message" id="editMenuTypeError"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="editMenuName">{{ __('admin.cms.menu_name') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editMenuName" name="editMenuName" placeholder="Contact">
                            <span class="text-danger error-message" id="editMenuNameError"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="editMenuPermalink">{{ __('admin.cms.permalink') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="editMenuPermalink" name="editMenuPermalink" value="https://www.example.com/contact/">
                            <span class="text-danger error-message" id="editMenuPermalinkError"></span>
                        </div>
                        <p>{{ __('admin.cms.preview') }}: <span class="text-info">https://www.example.com</span></p>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="form-check form-check-md form-switch me-2">
                                <label class="form-check-label form-label mt-0 mb-0">
                                    <input class="form-check-input form-label me-2" type="checkbox" id="editMenuStatus" name="menu_status" role="switch" checked>
                                    {{ __('admin.common.status') }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                                <button type="submit" class="btn btn-primary savebtn">{{ __('admin.common.save_changes') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit menu -->

    <!-- Delete Modal  -->
    <div class="modal fade" id="delete_menu">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="deleteMenu">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.cms.delete_menu') }}</h4>
                        <p class="mb-3">{{ __('admin.cms.menu_delete_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/menu_management/menulist.js') }}"></script>
@endpush
