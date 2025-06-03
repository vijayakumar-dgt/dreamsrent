@extends('admin.admin')

@section('meta_title', __('admin.general_settings.company_settings') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper">
        <div class="content">
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
                        <form @csrf id="companySettingForm" enctype="multipart/form-data">
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            @include('admin.general_settings_loader')
                            <div class="card-body d-none real-card">
                                <!-- Company Settings Section -->
                                <div class="localization-content mb-3">
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.company_settings') }}</h6>
                                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="1">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('admin.general_settings.profile_photo') }}</label>
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                                <img src="{{ uploadedAsset('', 'default') }}" id="profile_photo_preview" class="img-fluid" alt="Profile Photo">
                                            </div>
                                            <div class="profile-upload">
                                                <div class="profile-uploader d-flex align-items-center">
                                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                                        <i class="ti ti-photo-up fs-14"></i>
                                                        {{ __('admin.common.change') }}
                                                        <input type="file" class="form-control image-sign" id="company_profile_photo" name="company_profile_photo" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="mt-2">
                                                    <p class="fs-14">{{ __('admin.common.recommended_size_is') }} 500px x 500px</p>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="company_profile_photo_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <!-- Basic Information Section -->
                                <div class="localization-content mb-3">
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.basic_information') }}</h6>
                                    <div class="row">
                                        <!-- Organization Name -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.general_settings.organization_name') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="organization_name" name="organization_name" maxlength="30">
                                                <span id="organization_name_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Owner Name -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.general_settings.owner_name') }} <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="owner_name" name="owner_name" maxlength="30">
                                                <span id="owner_name_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Email Address -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label ">{{ __('admin.general_settings.email_address') }} <span class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="company_email" name="company_email" maxlength="50">
                                                <span id="company_email_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Phone Number -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.phone_number') }} <span class="text-danger">*</span></label>
                                                <div class="">
                                                    <input type="text" class="form-control company_phone" id="company_phone" name="company_phone">
                                                    <input type="hidden" id="international_phone_number" name="international_phone_number">
                                                </div>
                                                <span id="company_phone_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                       
                                       
                                    </div>
                                </div>
                                <!-- Address Information Section -->
                                <div class="">
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.address_information') }}</h6>
                                    <div class="row">
                                        <!-- Address Line -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.general_settings.address_line') }}</label>
                                                <input type="text" class="form-control" id="company_address_line" name="company_address_line" maxlength="100">
                                                <span id="company_address_line_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Country -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.country') }} </label>
                                                <div class="">
                                                    <select name="country" class="form-control select2" id="country"></select>
                                                </div>
                                                <span id="country_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- State -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.state') }} </label>
                                                <div class="">
                                                    <select name="state" class="form-control select2" id="state"></select>
                                                </div>
                                                <span id="state_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- City -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.city') }}</label>
                                                <div class="">
                                                    <select name="city" id="city" class="form-control select2"></select>
                                                </div>
                                                <span id="city_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Postal Code -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('admin.common.postal_code') }}</label>
                                                <input type="text" class="form-control" id="company_postal_code" name="company_postal_code" maxlength="6">
                                                <span id="company_postal_code_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Section -->
                            <div class="card-footer d-none real-card">
                                <div class="d-flex align-items-center justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-2">{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <button type="submit" class="btn btn-primary companysave">{{ __('admin.general_settings.save_changes') }}s</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
	</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/company.js') }}"></script>
<!-- Removed inline JS for Codecanyon compliance -->
@endpush
