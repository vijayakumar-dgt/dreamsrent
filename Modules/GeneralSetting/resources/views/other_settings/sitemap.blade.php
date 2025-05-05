@extends('admin.admin')
@section('content')
<div class="page-wrapper">
    <div class="content me-4 pb-0">

         <!-- Breadcrumb -->
         <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ __('admin.general_settings.settings') }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.settings') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <div class="row">
            @include('admin.partials.general_settings_side_menu')
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('admin.general_settings.other_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-section">
                            <h6 class="mb-3">{{ __('admin.general_settings.sitemap') }}</h6>
                                <!-- Table Header -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group">
                                            <span class="input-icon">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}" name="keyword" id="keyword">
                                        </div>
                                    </div>   
                                    <div>
                                        <div>
                                            @if (hasPermission($permissions, 'other_settings', 'create'))

                                            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_sitemap"><i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new') }}</a>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- /Table Header -->
                                <!-- Custom Data Table --> 
                                <div class="custom-datatable-filter table-responsive table-loader">
                                    <table class="table table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>
                                                    <div class="skeleton data-skeleton label-loader"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton data-skeleton label-loader"></div>
                                                </th>
                                                <th>
                                                    <div class="skeleton data-skeleton label-loader"></div>
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
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="custom-datatable-filter table-responsive d-none real-table">
                                    <table class="table" id="sitemapTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('admin.general_settings.url') }}</th>
                                                <th>{{ __('admin.general_settings.file_name') }}</th>
                                                @if (hasPermission($permissions, 'other_settings', 'edit') || hasPermission($permissions, 'other_settings', 'delete'))

                                                <th>{{ __('admin.common.action') }}</th>
                                                @endif
                                            </tr>
                                        </thead>
                                        <tbody>                                                                                                 																	                                                                                              																	
                                        </tbody>
                                    </table>
                                </div>
                                <!-- Custome Data Tabel -->
                                <div class="table-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </div>	
    @include('admin.partials.footer')
</div>

 <!-- Add Sitemap -->
 <div class="modal fade addmodal" id="add_sitemap">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form action="" id="sitemapForm">
                @csrf
                <div class="modal-header">
                    <h4 class="mb-0">{{ __('admin.general_settings.create_sitemap') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="mb-0">
                        <label class="form-label">{{ __('admin.general_settings.sitemap_url') }} <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="url" id="url">
                        <span id="url_error" class="text-danger error-text"></span>
                    </div>                                   
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                         <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                         <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Sitemap -->
<!-- Delete Sitemap  -->
<div class="modal fade" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="" id="deleteForm">
                @csrf
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.general_settings.delete_sitemap') }}</h4>
                    <p class="mb-3">{{ __('admin.general_settings.want_to_delete_sitemap') }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.yes_delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- /Delete Sitemap -->
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/sitemap.js') }}"></script>
@endpush