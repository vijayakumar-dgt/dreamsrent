@extends('admin.admin')

@section('meta_title', __('admin.general_settings.storage') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <x-admin.breadcrumb
                :title="__('admin.general_settings.settings')"
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]"
            />
            <!-- Settings Prefix -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.other_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="card-body pb-0 d-none real-card">
                            <div>
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.storage') }}</h6>
                                <div class="row">
                                    <!-- Local Storage Card -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-lg bg-gray-100 me-2 flex-shrink-0">
                                                            <img src="/backend/assets/img/icons/storage-icon-03.svg"
                                                                class="w-auto h-auto" alt="Local Storage Icon">
                                                        </span>
                                                        <div>
                                                            <h6 class="fw-medium fs-14 mb-0">
                                                                {{ __('admin.general_settings.local_storage') }}</h6>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        @if (hasPermission($permissions, 'other_settings', 'edit'))
                                                            <div class="form-check form-check-md form-switch">
                                                                <input class="form-check-input me-2" id="local_storage"
                                                                    name="local_storage" type="checkbox" role="switch" aria-checked="false">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- AWS Storage Card -->
                                    <div class="col-md-6">
                                        <div class="card">
                                            <div class="card-body">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <span class="avatar avatar-lg bg-gray-100 me-2 flex-shrink-0">
                                                            <img src="/backend/assets/img/icons/aws.svg"
                                                                class="w-auto h-auto" alt="AWS Icon">
                                                        </span>
                                                        <div>
                                                            <h6 class="fw-medium fs-14 mb-0">
                                                                {{ __('admin.general_settings.aws') }}</h6>
                                                        </div>
                                                    </div>
                                                    @if (hasPermission($permissions, 'other_settings', 'edit'))
                                                        <div class="d-flex align-items-center">
                                                            <a href="#" class="btn btn-icon btn-sm me-2" data-bs-toggle="modal"
                                                                data-bs-target="#aws_settings">
                                                                <i class="ti ti-settings fs-20"></i>
                                                            </a>
                                                            <div class="form-check form-check-md form-switch">
                                                                <input class="form-check-input me-2" id="aws_storage"
                                                                    name="aws_storage" type="checkbox" role="switch" aria-checked="false">
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
    <!--Add Cronjob -->
    <x-admin.modal className="addmodal" id="aws_settings" :title="__('admin.general_settings.aws_settings')"
        formId="awsSettingForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" name="group_id" id="group_id" value="8">

            <div class="row">
                <div class="col-md-12 mb-3">
                    <label for="aws_access_key" class="form-label">
                        {{ __('admin.general_settings.aws_access_key') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aws_access_key" name="aws_access_key" class="form-control">
                    <span id="aws_access_key_error" class="text-danger error-text"></span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="aws_secret_key" class="form-label">
                        {{ __('admin.general_settings.secret_key') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aws_secret_key" name="aws_secret_key" class="form-control">
                    <span id="aws_secret_key_error" class="text-danger error-text"></span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="aws_bucket_name" class="form-label">
                        {{ __('admin.general_settings.bucket_name') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aws_bucket_name" name="aws_bucket_name" class="form-control">
                    <span id="aws_bucket_name_error" class="text-danger error-text"></span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="aws_region" class="form-label">
                        {{ __('admin.general_settings.region') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aws_region" name="aws_region" class="form-control">
                    <span id="aws_region_error" class="text-danger error-text"></span>
                </div>

                <div class="col-md-12 mb-3">
                    <label for="aws_base_url" class="form-label">
                        {{ __('admin.general_settings.base_url') }} <span class="text-danger">*</span>
                    </label>
                    <input type="text" id="aws_base_url" name="aws_base_url" class="form-control">
                    <span id="aws_base_url_error" class="text-danger error-text"></span>
                </div>
            </div>
        </x-slot>
        <x-slot name="footer">
            <button type="button" class="btn btn-light me-2"
                data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
        </x-slot>
    </x-admin.modal>
    <!-- /Add Cronjob -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/storage-setting.js') }}"></script>
@endpush
