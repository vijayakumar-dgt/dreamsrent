@extends('admin.admin')

@section('meta_title', __('admin.general_settings.sitemap') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <x-admin.breadcrumb
                :title="__('admin.general_settings.settings')"
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]"
            />
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
                                            <input type="text" class="form-control"
                                                placeholder="{{ __('admin.common.search') }}" name="keyword" id="keyword">
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            @if (hasPermission($permissions, 'other_settings', 'create'))
                                                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center"
                                                    data-bs-toggle="modal" data-bs-target="#add_sitemap"><i
                                                        class="ti ti-plus me-2"></i>{{ __('admin.common.add_new') }}</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- /Table Header -->
                                <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                    @include('admin.content-loader')
                                </div>
                                <div class="custom-datatable-filter table-responsive d-none real-table">
                                    <table class="table" id="sitemapTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ __('admin.cms.url') }}</th>
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
    <x-admin.modal className="addmodal" id="add_sitemap" :title="__('admin.general_settings.create_sitemap')"
        formId="sitemapForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            @csrf
            <div class="mb-0">
                <label for="url" class="form-label">{{ __('admin.general_settings.sitemap_url') }} <span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control" name="url" id="url">
                <span id="url_error" class="text-danger error-text"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.submit') }}</button>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Add Sitemap -->

    <!-- Delete Sitemap -->
    <x-admin.delete-modal :config="[
        'className'    => 'deletemodal',
        'id'           => 'delete-modal',
        'formId'       => 'deleteForm',
        'action'       => '',
        'hiddenInputs' => ['id' => ''],
        'title'        => __('admin.general_settings.delete_sitemap'),
        'description'  => __('admin.general_settings.want_to_delete_sitemap')
    ]"/>
    <!-- /Delete Sitemap -->
@endsection
@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/sitemap.js') }}"></script>
@endpush
