@extends('admin.admin')

@section('meta_title', __('admin.general_settings.profile') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
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
            <!-- Settings Prefix -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-lg-9">
                    <form action="" id="adminProfileForm" enctype="multipart/form-data">
                        @csrf
                        <div class="card profile-setting-section h-100">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.account_settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <h6 class="fw-bold mb-3 ">{{ __('admin.general_settings.basic_information') }}</h6>
                                <div class="row border-bottom mb-3">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.general_settings.profile_photo') }}</label>
                                            <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                                <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                                    <img id="profile_photo_preview" src="" class="img-fluid" alt="Profile Photo">
                                                </div>
                                                <div class="profile-upload">
                                                    <div class="profile-uploader d-flex align-items-center">
                                                        <div class="drag-upload-btn btn btn-md btn-dark">
                                                            <i class="ti ti-photo-up fs-14"></i>
                                                            {{ __('admin.common.change') }}
                                                            <input type="file" class="form-control image-sign" id="profile_photo" name="profile_photo" accept="image/*" onchange="validateImageSize(this, event)">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14">{{ __('admin.common.recommended_size_is') }}500px x 500px</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="profile_photo_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <input type="hidden" class="form-control" id="id" name="id" value="1">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.common.first_name') }}<span class="text-danger ms-1">*</span></label>
                                            <input type="text" class="form-control" id="first_name" name="first_name" maxlength="30">
                                            <span id="first_name_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.common.last_name') }}<span class="text-danger ms-1">*</span></label>
                                            <input type="text" class="form-control" id="last_name" name="last_name" maxlength="30">
                                            <span id="last_name_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.general_settings.email_address') }}<span class="text-danger ms-1">*</span></label>
                                            <input type="email" class="form-control" id="email" name="email">
                                            <span id="email_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger ms-1">*</span></label>
                                            <div class="">
                                                <input type="text" class="form-control admin_phone" id="admin_phone" name="phone" maxlength="15">
                                                <input type="hidden" id="international_phone_number" name="international_phone_number">
                                            </div>
                                            <span id="admin_phone_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.address_information') }}</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.general_settings.address_line') }}</label>
                                            <input type="text" class="form-control" id="address_line" name="address_line" maxlength="50">
                                            <span id="address_line_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.common.country') }}<span class="text-danger">*</span></label>
                                            <div class="">
                                                <select name="country" class="form-control select2" id="country"></select>
                                            </div>
                                            <span id="country_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label">{{ __('admin.common.state') }} <span class="text-danger">*</span></label>
                                            <div class="">
                                                <select name="state" class="form-control select2" id="state"></select>
                                            </div>
                                            <span id="state_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                            <label class="form-label">{{ __('admin.common.city') }} <span class="text-danger">*</span></label>
                                            <div class="">
                                                <select name="city" id="city" class="form-control select2"></select>
                                            </div>
                                            <span id="city_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div>
                                            <label class="form-label">{{ __('admin.common.postal_code') }}</label>
                                            <input type="text" class="form-control" id="postal_code" name="postal_code" maxlength="6">
                                            <span id="postal_code_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3">{{ __('admin.general_settings.cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.save_changes') }}</button>
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
<script src="{{ asset('backend/assets/js/general_setting/adminprofile.js') }}"></script>
@endpush










