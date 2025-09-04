@extends('admin.admin')

@section('meta_title', __('admin.general_settings.logo_favicon_settings') . ' || ' . $companyName)

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
                <div class="col-lg-9">
                    <form id="logoSettingForm">
                        @csrf
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            @include('admin.general_settings_loader')
                            <div class="card-body d-none real-card">
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.logo_settings') }}</h6>
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="16">
                                <!-- Logo Image Upload -->
                                <div class="mb-3">
                                    <label class="form-label" for="logo_image">{{ __('admin.general_settings.logo') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="logo-large-size border rounded d-flex align-items-center justify-content-center p-3 me-2 ">
                                                    <img src="{{ uploadedAsset('', 'default2') }}" class="real-input" id="logo_photo_preview" alt="Logo">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="logo_image" name="logo_image" accept="image/*" >
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">  {{ __('admin.general_settings.logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="logo_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="metaImage"> {{ __('admin.general_settings.favicon') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="logo-large-size border rounded d-flex align-items-center justify-content-center p-3 me-2">
                                                    <img src="{{ uploadedAsset('', 'default2') }}" class="real-input" id="favicon_photo_preview" alt="Logo">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="favicon_image" name="favicon_image" accept="image/*" >
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14"> {{ __('admin.general_settings.favicon_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="favicon_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="small_image">{{ __('admin.general_settings.small_icon') }}<span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="logo-large-size border rounded d-flex align-items-center justify-content-center p-3 me-2">
                                                    <img src="{{ uploadedAsset('', 'default2') }}" class="real-input" id="small_icon_photo_preview" alt="Logo">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="small_image" name="small_image" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{ __('admin.general_settings.small_logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="small_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label" for="dark_logo_image">{{ __('admin.general_settings.dark_logo') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="logo-large-size border rounded d-flex align-items-center justify-content-center p-3 me-2">
                                                    <img src="{{ uploadedAsset('', 'default2') }}" class="real-input" id="dark_logo_preview" alt="Logo">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="dark_logo" name="dark_logo" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">   {{ __('admin.general_settings.logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="dark_logo_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="card-footer d-none real-card">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.save_changes') }}</button>
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
<script src="{{ asset('backend/assets/js/general_setting/logo-setting.js') }}"></script>
@endpush










