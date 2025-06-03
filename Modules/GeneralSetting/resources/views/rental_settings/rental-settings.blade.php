@extends('admin.admin')

@section('meta_title', __('admin.general_settings.rental') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
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
                            <h5>{{ __('admin.general_settings.rental_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="d-none real-card">
                            <form id="rentalSettingForm">
                                 @csrf
                                <div class="card-body pb-0">
                                    <div class="localization-content mb-3">
                                        <div>
                                            <h6 class="mb-3">{{ __('admin.general_settings.reservation') }}</h6>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium">{{ __('admin.general_settings.booking') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="booking" name="booking" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="bookingError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.general_settings.enquiries') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="enquiries" name="enquiries" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="enquiriesError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.general_settings.reservation') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="reservation" name="reservation" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="reservationError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="localization-content border-0">
                                        <div>
                                            <h6 class="mb-3 ">{{ __('admin.general_settings.vehicles') }}</h6>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.general_settings.seasonal_pricing') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="seasonalPricing" name="seasonalPricing" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="seasonalPricingError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.general_settings.faq') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="faq" name="faq" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="faqError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.general_settings.damages') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="damages" name="damages" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="damagesError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                            <div class="localization-list">
                                                <p class="text-gray-9 fw-medium ">{{ __('admin.common.extra_service') }}</p>
                                                <div>
                                                    <div class="form-check form-check-md form-switch">
                                                        <input id="extraService" name="extraService" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                                    </div>
                                                    <span class="text-danger d-none" id="extraServiceError">{{ __('admin.general_settings.required_field') }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer">
                                    <div class="d-flex justify-content-end">
                                        <a href="{{ route('dashboard') }}" class="btn btn-light me-3 " >{{ __('admin.general_settings.cancel') }}</a>
                                        @if (hasPermission($permissions, 'rental_settings', 'edit'))
                                        <button type="submit" class="btn btn-primary submitBtn">{{ __('admin.general_settings.save_changes') }}</button>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/rental-settings.js') }}"></script>
@endpush
