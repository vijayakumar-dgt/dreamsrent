@extends('admin.admin')

@section('meta_title', __('admin.general_settings.logo_favicon_settings') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.general_settings.settings') }}</h4>
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
                    <form id="logoSettingForm">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <div class="skeleton section-title-skeleton label-loader"></div>
                                <h6 class="fw-bold mb-3 d-none real-label">{{ __('admin.general_settings.logo_settings') }}</h6>
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="16">
                                <!-- Logo Image Upload -->
                                <div class="mb-3">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label d-none real-label" for="logo_image">{{ __('admin.general_settings.logo') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="skeleton image-skeleton image-loader"></div>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xxl logo-large-size border rounded d-flex align-items-center justify-content-center p-2 me-2 d-none real-label">
                                                    <img src="" alt="Logo" id="logo_photo_preview" class="real-input">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="skeleton button-skeleton label-loader"></div>
                                            <div class="profile-uploader d-flex align-items-center d-none real-label">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="logo_image" name="logo_image" accept="image/*" onchange="previewImage(event, 'logo_photo_preview', 151, 26)">
                                                </div>
                                            </div>
                                            <div class="skeleton text-skeleton label-loader"></div>
                                            <div class="mt-2 d-none real-label">
                                                <p class="fs-14">  {{ __('admin.general_settings.logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="logo_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label d-none real-label" for="metaImage"> {{ __('admin.general_settings.favicon') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="skeleton image-skeleton image-loader"></div>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xxl logo-large-size border rounded d-flex align-items-center justify-content-center p-2 me-2 d-none real-label">
                                                    <img src="" alt="Logo" id="favicon_photo_preview" class="real-input">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="skeleton button-skeleton label-loader"></div>
                                            <div class="profile-uploader d-flex align-items-center d-none real-label">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="favicon_image" name="favicon_image" accept="image/*" onchange="previewImage(event, 'favicon_photo_preview', 128, 128)">
                                                </div>
                                            </div>
                                            <div class="skeleton text-skeleton label-loader"></div>
                                            <div class="mt-2 d-none real-label">
                                                <p class="fs-14"> {{ __('admin.general_settings.favicon_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="favicon_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label d-none real-label" for="small_image">{{ __('admin.general_settings.small_icon') }}<span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="skeleton image-skeleton image-loader"></div>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xxl logo-large-size border rounded d-flex align-items-center justify-content-center p-2 me-2 d-none real-label">
                                                    <img src="" alt="Logo" id="small_icon_photo_preview" class="real-input">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="skeleton button-skeleton label-loader"></div>
                                            <div class="profile-uploader d-flex align-items-center d-none real-label">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="small_image" name="small_image" accept="image/*" onchange="previewImage(event, 'small_icon_photo_preview', 35, 35)">
                                                </div>
                                            </div>
                                            <div class="skeleton text-skeleton label-loader"></div>
                                            <div class="mt-2 d-none real-label">
                                                <p class="fs-14">{{ __('admin.general_settings.small_logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="small_image_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-3">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label d-none real-label" for="dark_logo_image">{{ __('admin.general_settings.dark_logo') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="skeleton image-skeleton image-loader"></div>
                                        <div class="d-flex justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-xxl logo-large-size border rounded d-flex align-items-center justify-content-center p-2 me-2 d-none real-label">
                                                    <img src="" alt="Logo" id="dark_logo_preview" class="real-input">
                                                </span>
                                            </div>
                                        </div>
                                        <div class="profile-upload">
                                            <div class="skeleton button-skeleton label-loader"></div>
                                            <div class="profile-uploader d-flex align-items-center d-none real-label">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.general_settings.upload') }}
                                                    <input type="file" id="dark_logo" name="dark_logo" accept="image/*" onchange="previewImage(event, 'dark_logo_preview', 151, 26)">
                                                </div>
                                            </div>
                                            <div class="skeleton text-skeleton label-loader"></div>
                                            <div class="mt-2 d-none real-label">
                                                <p class="fs-14">   {{ __('admin.general_settings.logo_recommend_size') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="dark_logo_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <div class="skeleton button-skeleton label-loader me-3"></div>
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3 d-none real-label" >{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <div class="skeleton button-skeleton label-loader"></div>
                                    <button type="submit" class="btn btn-primary d-none real-label">{{ __('admin.general_settings.save_changes') }}</button>
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










