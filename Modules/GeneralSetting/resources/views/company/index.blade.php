@extends('admin.admin')

@section('meta_title', __('admin.general_settings.company_settings') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper">
        <div class="content">
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
                        <form id="companySettingForm" enctype="multipart/form-data">
                            <div class="card-header">
                                <div class="skeleton header-skeleton label-loader"></div>
                                <h5 class="fw-bold d-none real-label">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            <div class="card-body">
                                <!-- Company Settings Section -->
                                <div class="localization-content mb-3">
                                    <div class="skeleton section-title-skeleton label-loader"></div>
                                    <h6 class="fw-bold mb-3 d-none real-label">{{ __('admin.general_settings.company_settings') }}</h6>
                                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="1">
                                    <div class="mb-3">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <label class="form-label d-none real-label">{{ __('admin.general_settings.profile_photo') }}</label>
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                            <div class="skeleton image-skeleton image-loader"></div>
                                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames d-none real-label">
                                                <img id="profile_photo_preview" src="" class="img-fluid" alt="Profile Photo">
                                            </div>
                                            <div class="profile-upload">
                                                <div class="skeleton button-skeleton label-loader"></div>
                                                <div class="profile-uploader d-flex align-items-center d-none real-label">
                                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                                        <i class="ti ti-photo-up fs-14"></i>
                                                        {{ __('admin.common.change') }}
                                                        <input type="file" class="form-control image-sign" id="company_profile_photo" name="company_profile_photo" accept="image/*">
                                                    </div>
                                                </div>
                                                <div class="skeleton text-skeleton label-loader"></div>
                                                <div class="mt-2 d-none real-label">
                                                    <p class="fs-14">{{ __('admin.common.recommended_size_is') }} 500px x 500px</p>
                                                </div>
                                            </div>
                                        </div>
                                        <span id="company_profile_photo_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <!-- Basic Information Section -->
                                <div class="localization-content mb-3">
                                    <div class="skeleton section-title-skeleton label-loader"></div>
                                    <h6 class="fw-bold mb-3 d-none real-label">{{ __('admin.general_settings.basic_information') }}</h6>
                                    <div class="row">
                                        <!-- Organization Name -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.organization_name') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" class="form-control d-none real-label" id="organization_name" name="organization_name" maxlength="30">
                                                <span id="organization_name_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Owner Name -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.owner_name') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" class="form-control d-none real-label" id="owner_name" name="owner_name" maxlength="30">
                                                <span id="owner_name_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Email Address -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.email_address') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="email" class="form-control d-none real-label" id="company_email" name="company_email" maxlength="50">
                                                <span id="company_email_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Phone Number -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.common.phone_number') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <input type="text" class="form-control company_phone" id="company_phone" name="company_phone">
                                                    <input type="hidden" id="international_phone_number" name="international_phone_number">
                                                </div>
                                                <span id="company_phone_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Industry -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.industry') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton select-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <select class="form-control select" id="industry" name="industry">
                                                        <option value="">{{ __('admin.common.select') }}</option>
                                                        @foreach($industries as $industry)
                                                            <option value="{{ $industry->id }}">{{ $industry->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span id="industry_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Team Size -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.team_size') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton select-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <select class="form-control select" id="team_size" name="team_size">
                                                        <option value="">{{ __('admin.common.select') }}</option>
                                                        @foreach($teamSizes as $teamSize)
                                                            <option value="{{ $teamSize->id }}">{{ $teamSize->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <span id="team_size_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Address Information Section -->
                                <div class="">
                                    <div class="skeleton section-title-skeleton label-loader"></div>
                                    <h6 class="fw-bold mb-3 d-none real-label">{{ __('admin.general_settings.address_information') }}</h6>
                                    <div class="row">
                                        <!-- Address Line -->
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.general_settings.address_line') }}</label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" class="form-control d-none real-label" id="company_address_line" name="company_address_line" maxlength="100">
                                                <span id="company_address_line_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Country -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.common.country') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton select-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <select name="country" class="form-control select2" id="country"></select>
                                                </div>
                                                <span id="country_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- State -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.common.state') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton select-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <select name="state" class="form-control select2" id="state"></select>
                                                </div>
                                                <span id="state_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- City -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.common.city') }} <span class="text-danger">*</span></label>
                                                <div class="skeleton select-skeleton input-loader"></div>
                                                <div class="d-none real-label">
                                                    <select name="city" id="city" class="form-control select2"></select>
                                                </div>
                                                <span id="city_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                        <!-- Postal Code -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label">{{ __('admin.common.postal_code') }}</label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" class="form-control d-none real-label" id="company_postal_code" name="company_postal_code" maxlength="6">
                                                <span id="company_postal_code_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Footer Section -->
                            <div class="card-footer">
                                <div class="d-flex align-items-center justify-content-end">
                                    <div class="skeleton button-skeleton label-loader me-2"></div>
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-2 d-none real-label">{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'website_settings', 'edit'))
                                    <div class="skeleton button-skeleton label-loader"></div>
                                    <button type="submit" class="btn btn-primary companysave d-none real-label">{{ __('admin.general_settings.save_changes') }}s</button>
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
@endpush










