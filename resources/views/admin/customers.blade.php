@extends('admin.admin')

@section('meta_title', __('admin.common.customers') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.common.customers') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.customers') }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2 me-2 d-none">
                        <div class="skeleton label-skeleton label-loader"></div>
                        <a href="javascript:void(0);" class="btn btn-white d-flex align-items-center d-none real-label"><i class="ti ti-printer me-2"></i>{{ __('admin.common.print') }}</a>
                    </div>
                    <div class="mb-2 me-2 d-none">
                        <div class="dropdown">
                            <div class="skeleton label-skeleton label-loader"></div>
                            <a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center d-none real-label">
                                <i class="ti ti-upload me-1"></i>{{ __('admin.common.export') }}
                            </a>
                        </div>
                    </div>
                    <div class="mb-2">
                        @if (hasPermission($permissions, 'customers', 'create'))
                        <div class="skeleton label-skeleton label-loader"></div>
                        <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center d-none real-label" id="add_customer" data-bs-toggle="modal" data-bs-target="#add_customer_modal"><i class="ti ti-plus me-2"></i>{{ __('admin.manage.add_new_customer') }}</a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <input type="hidden" name="sort_by_input" id="sort_by_input">
                    <input type="hidden" name="sort_by_status" id="sort_by_status">
                    <div class="skeleton label-skeleton label-loader me-2"></div>
                    <div class="dropdown me-2 d-none real-label">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="skeleton label-skeleton label-loader me-2"></div>
                    <div class="me-2 d-none real-input">
                        <div class="input-icon-start position-relative topdatepicker">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar"></i>
                            </span>
                            <input type="text" class="form-control date-range bookingrange" name="sort_by_date" id="sort_by_date" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="dropdown">
                        <div class="skeleton label-skeleton label-loader"></div>
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center d-none real-label" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i>{{ __('admin.common.filter') }}<span class="badge badge-xs rounded-pill bg-danger ms-2">0</span>
                        </a>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    @if (hasPermission($permissions, 'customers', 'delete'))
                    <div class="dropdown me-2">
                        <div class="skeleton label-skeleton label-loader"></div>
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center d-none real-label" data-bs-toggle="dropdown">
                            <i class="ti ti-edit-circle me-1"></i>{{ __('admin.common.bulk_actions') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2" id="bulk_actions">
                            @if (hasPermission($permissions, 'customers', 'delete'))
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1" id="bulk_delete">{{ __('admin.common.delete') }}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                    @endif
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="top-search d-none real-label">
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
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{ __('admin.common.filters') }}</h6>
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            {{ __('admin.common.language') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg p-2" id="language_list">
                            <li>
                                <div class="top-search m-2">
                                    <div class="top-search-group">
                                        <span class="input-icon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                    </div>
                                </div>
                            </li>
                            <div class="custom-scroll">
                                @if ($languages)
                                    @foreach ($languages as $language)
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                <input class="form-check-input m-0 me-2 language_checkbox" type="checkbox" value="{{ $language->language_id }}">{{ $language->transLang->name }}
                                            </label>
                                        </li>
                                    @endforeach
                                @endif
                            </div>
                        </ul>
                    </div>
                    <a href="javascript:void(0);" class="me-2 text-purple links" id="apply_filter">{{ __('admin.common.apply') }}</a>
                    <a href="javascript:void(0);" class="text-danger links" id="reset_filter">{{ __('admin.common.clear_all') }}</a>
                </div>
            </div>
            <div class="custom-datatable-filter table-responsive table-loader">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
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
                <table class="table" id="customerTable">
                    <thead class="thead-light">
                        <tr>
                            <th class="no-sort">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input" type="checkbox" id="select-all">
                                </div>
                            </th>
                            <th>{{ strtoupper(__('admin.common.customer')) }}</th>
                            <th>{{ strtoupper(__('admin.common.email')) }}</th>
                            <th>{{ strtoupper(__('admin.common.language')) }}</th>
                            <th>{{ strtoupper(__('admin.common.documents')) }}</th>
                            <th>{{ strtoupper(__('admin.common.rents')) }}</th>
                            @if (hasPermission($permissions, 'customers', 'edit') || hasPermission($permissions, 'customers', 'delete') || hasPermission($permissions, 'customers', 'view'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Customer -->
    <div class="modal fade" id="add_customer_modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="customerForm" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="mb-0">{{ __('admin.manage.create_customer') }}</h5>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body pb-1 customer-modal-scroll">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
                                        <img src="" class="img-fluid rounded d-none" id="imagePreview" alt="img">
                                        <i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
                                    </div>
                                    <div class="profile-upload">
                                        <div class="profile-uploader d-flex align-items-center">
                                            <div class="drag-upload-btn btn btn-md btn-dark">
                                                <i class="ti ti-photo-up fs-14"></i>
                                                {{ __('admin.common.upload') }}
                                                <input type="file" class="form-control image-sign" name="image" id="image">
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="fs-14">{{ __('admin.common.upload_image_size', ['size' => 2]) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-danger error-text" id="image_error"></span>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.username') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="username" id="username">
                                    <span class="text-danger error-text" id="username_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.first_name') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="first_name" id="first_name">
                                    <span class="text-danger error-text" id="first_name_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.last_name') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="last_name" id="last_name">
                                    <span class="text-danger error-text" id="last_name_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.date_of_birth') }}<span class="text-danger"> *</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control dob" name="dob" id="dob" placeholder="dd-mm-yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <span class="text-danger error-text" id="dob_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.gender') }}<span class="text-danger"> *</span></label>
                                        <select class="form-control select" id="gender" name="gender" data-placeholder="{{ __('admin.common.select') }}">
                                            <option value="">{{ __('admin.common.select') }}</option>
                                            <option value="male">{{ __('admin.common.male') }}</option>
                                            <option value="female">{{ __('admin.common.female') }}</option>
                                            <option value="other">{{ __('admin.common.other') }}</option>
                                        </select>
                                        <span class="text-danger error-text" id="gender_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.language') }}<span class="text-danger"> *</span></label>
                                    <select class="form-control language" id="language" name="language" data-placeholder="{{ __('admin.common.select') }}">
                                        <option value="">{{ __('admin.common.select') }}</option>
                                        @if (!empty($languages))
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->transLang->id }}">{{ $language->transLang->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger error-text" id="language_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control customer_phone_number" id="phone_number" name="phone_number">
                                    <input type="hidden" id="international_phone_number" name="international_phone_number">
                                    <span id="phone_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
                                    <input class="form-control" type="text" name="email" id="email">
                                    <span id="email_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.address') }}<span class="text-danger"> *</span></label>
                                    <input class="form-control" type="text" name="address" id="address">
                                    <span id="address_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <h6 class="fs-16 fw-medium mb-2">{{ __('admin.manage.licence_details') }}</h6>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.card_number') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="card_number" id="card_number">
                                    <span id="card_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.date_of_issue') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control date_of_issue" name="date_of_issue" id="date_of_issue" placeholder="dd-mm-yyyy">
                                    <span id="date_of_issue_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.valid_date') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control valid_date" name="valid_date" id="valid_date" placeholder="dd-mm-yyyy">
                                    <span id="valid_date_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.documents') }}</label><span class="text-danger"> *</span></label>
                                    <div class="document-upload text-center br-3 mb-3">
                                        <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
                                        <p class="mb-2">{{ __('admin.common.drop_your_files_here_or') }} <span class="text-info text-decoration-underline">{{ __('admin.common.browse') }}</span></p>
                                        <p class="fs-12 mb-0">{{ __('admin.common.maximum_size', ['size' => 5]) }}</p>
                                        <input type="file" class="form-control image-sign" name="documents[]" id="documents" multiple="">
                                    </div>
                                    <span id="documents_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Add Customer -->

    <!-- Edit Customer -->
    <div class="modal fade" id="edit_customer_modal">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form id="editCustomerForm" autocomplete="off">
                <input type="hidden" name="id" id="id">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="mb-0">{{ __('admin.manage.create_customer') }}</h5>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body pb-1">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
                                        <img src="" class="img-fluid rounded d-none" id="editImagePreview" alt="img">
                                        <i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
                                    </div>
                                    <div class="profile-upload">
                                        <div class="profile-uploader d-flex align-items-center">
                                            <div class="drag-upload-btn btn btn-md btn-dark">
                                                <i class="ti ti-photo-up fs-14"></i>
                                                {{ __('admin.common.upload') }}
                                                <input type="file" class="form-control image-sign" name="image" id="edit_image">
                                            </div>
                                        </div>
                                        <div class="mt-2">
                                            <p class="fs-14">{{ __('admin.common.upload_image_size', ['size' => 2]) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <span class="text-danger error-text" id="edit_image_error"></span>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.username') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="username" id="edit_username">
                                    <span class="text-danger error-text" id="edit_username_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.first_name') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="first_name" id="edit_first_name">
                                    <span class="text-danger error-text" id="edit_first_name_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.last_name') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="last_name" id="edit_last_name">
                                    <span class="text-danger error-text" id="edit_last_name_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.date_of_birth') }}<span class="text-danger"> *</span></label>
                                    <div class="input-icon-end position-relative">
                                        <input type="text" class="form-control dob" name="dob" id="edit_dob" placeholder="dd-mm-yyyy">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                    </div>
                                    <span class="text-danger error-text" id="edit_dob_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.gender') }}<span class="text-danger"> *</span></label>
                                        <select class="form-control select" id="edit_gender" name="gender" data-placeholder="{{ __('admin.common.select') }}">
                                            <option value="">{{ __('admin.common.select') }}</option>
                                            <option value="male">{{ __('admin.common.male') }}</option>
                                            <option value="female">{{ __('admin.common.female') }}</option>
                                            <option value="other">{{ __('admin.common.other') }}</option>
                                        </select>
                                        <span class="text-danger error-text" id="edit_gender_error"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.language') }}<span class="text-danger"> *</span></label>
                                    <select class="form-control edit_language" id="edit_language" name="language" data-placeholder="{{ __('admin.common.select') }}">
                                        <option value="">{{ __('admin.common.select') }}</option>
                                        @if (!empty($languages))
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->transLang->id }}">{{ $language->transLang->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <span class="text-danger error-text" id="edit_language_error"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control edit_customer_phone_number" id="edit_phone_number" name="phone_number">
                                    <input type="hidden" id="edit_international_phone_number" name="international_phone_number">
                                    <span id="edit_phone_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
                                    <input class="form-control" type="text" name="email" id="edit_email">
                                    <span id="edit_email_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.address') }}<span class="text-danger"> *</span></label>
                                    <input class="form-control" type="text" name="address" id="edit_address">
                                    <span id="edit_address_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <h6 class="fs-16 fw-medium mb-2">{{ __('admin.manage.licence_details') }}</h6>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.card_number') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control" name="card_number" id="edit_card_number">
                                    <span id="edit_card_number_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.date_of_issue') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control date_of_issue" name="date_of_issue" id="edit_date_of_issue" placeholder="dd-mm-yyyy">
                                    <span id="edit_date_of_issue_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.manage.valid_date') }}<span class="text-danger"> *</span></label>
                                    <input type="text" class="form-control valid_date" name="valid_date" id="edit_valid_date" placeholder="dd-mm-yyyy">
                                    <span id="edit_valid_date_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.documents') }}</label><span class="text-danger"> *</span></label>
                                    <div class="document-upload text-center br-3 mb-3">
                                        <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
                                        <p class="mb-2">{{ __('admin.common.drop_your_files_here_or') }} <span class="text-info text-decoration-underline">{{ __('admin.common.browse') }}</span></p>
                                        <p class="fs-12 mb-0">{{ __('admin.common.maximum_size', ['size' => 5]) }}</p>
                                        <input type="file" class="form-control image-sign" name="documents[]" id="edit_documents" multiple="">
                                    </div>
                                    <input type="hidden" name="removed_documents" id="removed_documents">
                                    <span id="edit_documents_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <div class="d-flex justify-content-start document-preview-container">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.save_changes') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Edit Customer -->

    <!-- Delete Customer -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="deleteCustomerForm">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.manage.delete_customer') }}</h4>
                        <p class="mb-3">{{ __('admin.manage.delete_customer_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete Customer -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/customer.js') }}"></script>
@endpush
