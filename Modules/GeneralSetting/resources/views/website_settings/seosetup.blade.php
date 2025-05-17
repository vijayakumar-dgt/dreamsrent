
@extends('admin.admin')

@section('meta_title', __('admin.general_settings.seo_setup_settings') . ' || ' . $companyName)

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
                    <form id="seosetupSettingForm">
                        <div class="card h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            @include('admin.general_settings_loader')
                            <div class="card-body d-none real-card">
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.seo_setup_settings') }}</h6>
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="6">
                                <!-- Meta Title -->
                                <div class="mb-3">
                                    <label class="form-label " for="metaTitle">{{ __('admin.general_settings.meta_title') }}<span class="text-danger ms-1">*</span></label>
                                    <input type="text" id="metaTitle" name="metaTitle" class="form-control">
                                    <span id="metaTitle_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Site Description -->
                                <div class="mb-3">
                                    <label class="form-label" for="siteDescription">{{ __('admin.general_settings.site_description') }}<span class="text-danger ms-1">*</span></label>
                                    <textarea id="siteDescription" name="siteDescription" class="form-control" rows="3"></textarea>
                                    <span id="siteDescription_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Keywords -->
                                <div class="mb-3 pb-3 border-bottom">
                                    <label class="form-label" for="keywords">{{ __('admin.general_settings.keywords') }}<span class="text-danger ms-1">*</span></label>
                                    <div class="">
                                        <input class="input-tags form-control" id="keywords" type="text" data-role="tagsinput" name="keywords" value="">
                                    </div>
                                    <span id="keywords_error" class="text-danger error-text"></span>
                                </div>
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.seo_setup_og_meta') }}</h6>
                                <!-- Meta Image Upload -->
                                <div class="mb-3">
                                    <label class="form-label" for="metaImage">{{ __('admin.general_settings.meta_image') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                            <img id="profile_photo_preview" src="/backend/assets/img/settings/company-logo-01.jpg" class="img-fluid" alt="Profile Photo">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.common.upload') }}
                                                    <input type="file" class="form-control image-sign" id="metaImage" name="metaImage" accept="image/*" onchange="previewImage(event)">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{ __('admin.common.recommended_size_is') }} 500px x 500px</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="metaImage_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Meta Title -->
                                <div class="mb-3">
                                    <label class="form-label" for="metaTitle">{{ __('admin.general_settings.meta_title') }}<span class="text-danger ms-1">*</span></label>
                                    <input type="text" id="ogmetaTitle" name="ogmetaTitle" class="form-control">
                                    <span id="ogmetaTitle_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Site Description -->
                                <div class="mb-3">
                                    <label class="form-label" for="siteDescription">{{ __('admin.general_settings.site_description') }}<span class="text-danger ms-1">*</span></label>
                                    <textarea id="ogsiteDescription" name="ogsiteDescription" class="form-control" rows="3"></textarea>
                                    <span id="ogsiteDescription_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Keywords -->
                                <div class="mb-0">
                                    <label class="form-label" for="keywords">{{ __('admin.general_settings.keywords') }}<span class="text-danger ms-1">*</span></label>
                                    <div class="">
                                        <input class="input-tags form-control" id="ogkeywords" type="text" data-role="tagsinput" name="ogkeywords" value="">
                                    </div>
                                    <span id="ogkeywords_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="card-footer d-none real-card">
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
<script src="{{ asset('backend/assets/js/general_setting/seo-setting.js') }}"></script>
@endpush










