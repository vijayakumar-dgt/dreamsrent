@extends('admin.admin')

@section('meta_title', __('admin.general_settings.invoice_settings') . ' || ' . $companyName)

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
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold">{{ __('admin.general_settings.app_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <form id="invoiceSettingForm" @csrf class="d-none real-card">
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{ __('admin.general_settings.invoice_settings') }}</h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="invoice_logo" class="form-label">{{ __('admin.general_settings.invoice_logo') }} <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                                <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                                    <img id="profile_photo_preview" src="{{ uploadedAsset('', 'default2')}}" class="img-fluid" alt="Profile Photo">
                                                </div>
                                                <div class="profile-upload">
                                                    <div class="profile-uploader d-flex align-items-center">
                                                        <div class="drag-upload-btn btn btn-md btn-dark">
                                                            <i class="ti ti-photo-up fs-14"></i>
                                                            {{ __('admin.common.change') }}
                                                            <input type="file" class="form-control image-sign" id="invoice_logo" name="invoice_logo" accept="image/*" >
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14">{{ __('admin.common.recommended_size_is') }} 500px x 500px</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Invoice Prefix -->
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-12">
                                        <label for="invoice_prefix" class="form-label">{{ __('admin.general_settings.invoice_prefix') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="mb-3">
                                            <input type="text" class="form-control" id="invoice_prefix" name="invoice_prefix" placeholder="INV-">
                                            <span class="text-danger" id="invoice_prefix_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <!-- Invoice Due -->
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-12">
                                        <label for="invoice_due" class="form-label">{{ __('admin.general_settings.invoice_due') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="">
                                            <div class="mb-3 d-flex align-items-center">
                                                <select class="form-select" id="invoice_due" name="invoice_due">
                                                    <option value="">{{ __('admin.common.select') }}</option>
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                </select>
                                                <span class="ms-3 text-dark">Days</span>
                                            </div>
                                        </div>
                                        <span class="text-danger" id="invoice_due_error"></span>
                                    </div>
                                </div>
                                <!-- Invoice Round Off -->
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-12">
                                        <label for="invoice_round_off" class="form-label">{{ __('admin.general_settings.invoice_round_off') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="">
                                            <div class="mb-3 d-flex align-items-center">
                                                <select class="form-select" id="invoice_round_off" name="invoice_round_off">
                                                    <option value="">{{ __('admin.common.select') }}</option>
                                                    <option value="5">5</option>
                                                    <option value="10">10</option>
                                                </select>
                                                <div class="ms-3">
                                                    <div class="form-check form-check-md form-switch">
                                                        <input class="form-check-input" type="checkbox" id="round_off_enabled" name="round_off_enabled" role="switch" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <span class="text-danger" id="invoice_round_off_error"></span>
                                    </div>
                                </div>
                                <!-- Show Company Details -->
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-12">
                                        <label for="show_company_details" class="form-label">{{ __('admin.general_settings.show_company_details') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-6 col-sm-12">
                                        <div class="">
                                            <div class="form-check form-check-md form-switch">
                                                <input class="form-check-input" type="checkbox" id="show_company_details" name="show_company_details" role="switch" checked>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Invoice Terms -->
                                <div class="row align-items-center">
                                    <div class="col-md-4 col-sm-12">
                                        <label for="invoice_terms" class="form-label">{{ __('admin.general_settings.invoice_terms') }} <span class="text-danger">*</span></label>
                                    </div>
                                    <div class="col-md-8 col-sm-12">
                                        <div class="">
                                            <div class="mt-3">
                                            <textarea class="form-control" id="invoice_terms" name="invoice_terms" rows="3" placeholder="Enter invoice terms here..."></textarea>
                                            <p>Maximum 60 words</p>
                                            </div>
                                        </div>
                                        <span class="text-danger" id="invoice_terms_error"></span>
                                    </div>
                                </div>
                            </div>
                            <!-- Form Footer -->
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'app_settings', 'edit'))
                                    <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.save_changes') }}</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/invoice-setting.js') }}"></script>
@endpush










