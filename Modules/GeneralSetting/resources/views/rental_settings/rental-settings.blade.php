@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4 pb-0">

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
                    <div class="card-header">
                        <h5>{{ __('admin.general_settings.rental_settings') }}</h5>
                    </div>
                    <form id="rentalSettingForm">
                        <div class="card-body pb-0">
                            <div class="localization-content mb-3">
                                <div>
                                    <div class="skeleton label-skeleton label-loader mb-3"></div>
                                    <h6 class="mb-3 d-none real-label">{{ __('admin.general_settings.reservation') }}</h6>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.booking') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="booking" name="booking" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="bookingError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.enquiries') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="enquiries" name="enquiries" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="enquiriesError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.reservation') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="reservation" name="reservation" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="reservationError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.minimum_advance_reservation_time') }}</p>
                                        <div>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <div class="d-none real-label">
                                                <select id="minAdvanceReservation" name="minAdvanceReservation" class="select">
                                                    <option value="">{{ __('admin.general_settings.select') }}</option>
                                                    <option value="1 Day">{{ __('admin.general_settings.1_day') }}</option>
                                                    <option value="1 Week">{{ __('admin.general_settings.1_week') }}</option>
                                                    <option value="1 Month">{{ __('admin.general_settings.1_month') }}</option>
                                                </select>
                                            </div>
                                            <span class="text-danger d-none" id="minAdvanceReservationError">{{ __('admin.general_settings.please_select_option') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.maximum_advance_reservation_time') }}</p>
                                        <div>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <div class="d-none real-label">
                                                <select id="maxAdvanceReservation" name="maxAdvanceReservation" class="select">
                                                    <option value="">{{ __('admin.general_settings.select') }}</option>
                                                    <option value="1 Day">{{ __('admin.general_settings.1_day') }}</option>
                                                    <option value="1 Week">{{ __('admin.general_settings.1_week') }}</option>
                                                    <option value="1 Month">{{ __('admin.general_settings.1_month') }}</option>
                                                </select>
                                            </div>
                                            <span class="text-danger d-none" id="maxAdvanceReservationError">{{ __('admin.general_settings.please_select_option') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.cancellation_buffer_time') }}</p>
                                        <div>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <div class="d-none real-label">
                                                <select id="cancellationBuffer" name="cancellationBuffer" class="select">
                                                    <option value="">{{ __('admin.general_settings.select') }}</option>
                                                    <option value="1 Day">{{ __('admin.general_settings.2_day') }}</option>
                                                    <option value="1 Week">{{ __('admin.general_settings.3_day') }}</option>
                                                    <option value="1 Month">{{ __('admin.general_settings.4_day') }}</option>
                                                </select>
                                            </div>
                                            <span class="text-danger d-none" id="cancellationBufferError">{{ __('admin.general_settings.please_select_option') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.reshedule_buffer_time') }}</p>
                                        <div>
                                            <div class="skeleton input-skeleton input-loader"></div>
                                            <div class="d-none real-label">
                                                <select id="rescheduleBuffer" name="rescheduleBuffer" class="select">
                                                    <option value="">Select</option>
                                                    <option value="1 Day">{{ __('admin.general_settings.2_day') }}</option>
                                                    <option value="1 Week">{{ __('admin.general_settings.3_day') }}</option>
                                                    <option value="1 Month">{{ __('admin.general_settings.4_day') }}</option>
                                                </select>
                                            </div>
                                            <span class="text-danger d-none" id="rescheduleBufferError">{{ __('admin.general_settings.please_select_option') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="localization-content border-0">
                                <div>
                                    <div class="skeleton label-skeleton label-loader mb-3"></div>
                                    <h6 class="mb-3 d-none real-label">{{ __('admin.general_settings.vehicles') }}</h6>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.seasonal_pricing') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="seasonalPricing" name="seasonalPricing" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="seasonalPricingError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.faq') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="faq" name="faq" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="faqError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.damages') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="damages" name="damages" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="damagesError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.common.extra_service') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="extraService" name="extraService" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="extraServiceError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                    <div class="localization-list">
                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <p class="text-gray-9 fw-medium d-none real-label">{{ __('admin.general_settings.pricing') }}</p>
                                        <div>
                                            <div class="skeleton toggle-skeleton input-loader"></div>
                                            <div class="form-check form-check-md form-switch d-none real-input">
                                                <input id="pricing" name="pricing" class="form-check-input form-label" type="checkbox" role="switch" checked>
                                            </div>
                                            <span class="text-danger d-none" id="pricingError">{{ __('admin.general_settings.required_field') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <div class="skeleton label-skeleton label-loader me-2"></div>
                                <a href="{{ route('dashboard') }}" class="btn btn-light me-3 d-none real-label" >{{ __('admin.general_settings.cancel') }}</a>
                                @if (hasPermission($permissions, 'rental_settings', 'edit'))
                                <div class="skeleton label-skeleton label-loader"></div>
                                <button type="submit" class="btn btn-primary submitBtn d-none real-label">{{ __('admin.general_settings.save_changes') }}</button>
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
<!-- /Page Wrapper -->

@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/rental-settings.js') }}"></script>
@endpush
