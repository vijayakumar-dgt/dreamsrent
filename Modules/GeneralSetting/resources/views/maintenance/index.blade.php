@extends('admin.admin')

@section('meta_title', __('admin.general_settings.maintenance_mode') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-{{ __('admin.general_settings.settings') }}block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1"></h4>
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
            <!-- Settings Prefix -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-lg-9">
                    <form id="maintenanceSettingsForm">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.maintenance_mode') }}</h6>
                                <!-- Image Upload Field -->
                                <div class="mb-3">
                                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="4">
                                    <label for="maintenance_image" class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                            <img id="profile_photo_preview" src="" class="img-fluid" alt="Profile Photo">
                                            <a href="javascript:void(0);" class="uploadimgtrash btn btn-sm rounded-circle remove-maintenance-image" data-default_image="{{ uploadedAsset('', 'default2')}}">
                                                <i class="ti ti-trash fs-12"></i>
                                            </a>
                                            <input type="hidden" name="is_remove_image" id="is_remove_image" value="0">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.common.change') }}
                                                    <input type="file" class="form-control image-sign" id="maintenance_image" name="maintenance_image" accept="image/*" onchange="previewImage(event)">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{ __('admin.common.recommended_size_is') }} 500px x 500px</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="maintenance_image_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Description Field -->
                                <div class="mb-3">
                                    <label for="maintenance_description" class="form-label">{{ __('admin.common.description') }}</label>
                                    <textarea id="maintenance_description" name="maintenance_description" class="form-control summernote"></textarea>
                                    <span id="maintenance_description_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Status Toggle -->
                                <div class="form-check form-check-md form-switch me-2">
                                    <label class="form-check-label form-label mt-0 mb-0">
                                        <input id="maintenance_status" name="maintenance_status" class="form-check-input form-label me-2" type="checkbox" role="switch" checked>
                                        {{ __('admin.common.status') }}
                                    </label>
                                    <span id="maintenance_status_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{ __('admin.common.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <button type="submit" class="btn btn-primary">{{ __('admin.common.save_changes') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/maintenance-setting.js') }}"></script>
@endpush










