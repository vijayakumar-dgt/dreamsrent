@extends('admin.admin')

@section('meta_title', __('admin.bookings.reservation_details') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="mb-3">
                    <a href="{{ route('reservation.index') }}" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-narrow-left me-2"></i>{{ __('admin.common.reservations') }}</a>
                </div>
                <div class="card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5>{{ __('admin.bookings.reservation_details') }}</h5>
                        <span class="badge bg-orange-transparent">{{ $booking->booking_status_text }}</span>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-tabs-solid custom-nav-tabs mb-3" role="tablist">
                            <li class="nav-item" role="presentation"><a class="nav-link active" href="#solid-tab1" data-bs-toggle="tab" aria-selected="true" role="tab">{{ __('admin.bookings.reservation_info') }}</a></li>
                            <li class="nav-item" role="presentation"><a class="nav-link" href="#solid-tab2" data-bs-toggle="tab" aria-selected="false" role="tab">{{ __('admin.common.history') }}</a></li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active show" id="solid-tab1" role="tabpanel">
                                <div class="border rounded p-3 bg-light mb-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar flex-shrink-0 me-2">
                                                    <img src="{{ $booking->vehicle_image }}" alt="">
                                                </span>
                                                <div>
                                                    <p class="mb-1">{{ $booking->vehicle_type }}</p>
                                                    <h6 class="fs-14">{{ $booking->vehicle_name }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-end">
                                                <p class="mb-1">{{ __('admin.common.price') }}</p>
                                                <h6 class="fs-14">{{ $booking->currency_symbol }}{{ $booking->vehicle_price }}<span class="text-gray-5 fw-normal">/{{ ucfirst($booking->vehicle_price_type ?? $booking->rental_type) }}</span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom mb-3 pb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium fs-14">{{ __('admin.common.start_date') }}</h6>
                                        <p>{{ formatDateTime($booking->start_datetime, true) }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium fs-14">{{ __('admin.common.end_date') }}</h6>
                                        <p>{{ formatDateTime($booking->end_datetime, true) }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.rental_period') }}</h6>
                                        <p>{{ $booking->no_of_days > 1 ? $booking->no_of_days . ' Days' : $booking->no_of_days . ' Day' }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.driving_type') }}</h6>
                                        @if ($booking->booking_by == 'admin')
                                        <p>{{ $booking->driving_type ?? '-' }}</p>
                                        @else
                                        <p>{{ $booking->delivery_type ?? '-' }}</p>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="d-flex align-items-center">
                                                <div class="bg-light p-3 rounded flex-fill mb-3">
                                                    <h6 class="mb-1 fs-14 fw-medium">{{ __('admin.bookings.pickup_location') }}</h6>
                                                    <p>{{ $booking->pickup_location_name }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="bg-light p-3 rounded mb-3">
                                                <h6 class="mb-1 fs-14 fw-medium">{{ __('admin.bookings.return_location') }}</h6>
                                                <p>{{ $booking->drop_location_name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="border-bottom mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div>
                                                <div class="mb-3">
                                                    <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">{{ __('admin.common.customer') }}</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                                        <img src="{{ $booking->customer_image }}" alt="">
                                                    </span>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">
                                                            {{ ucfirst($booking->customer_full_name ?? $booking->customer_user_name) }}
                                                        </h6>
                                                        <p>{{ $booking->customer_phone_number }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($booking->driver_name)
                                        <div class="col-md-6">
                                            <div>
                                                <div class="mb-3">
                                                    <h6 class="d-inline-flex align-items-center fs-14 fw-medium ">{{ __('admin.common.driver') }}</h6>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <span class="avatar avatar-rounded flex-shrink-0 me-2">
                                                        <img src="{{ $booking->driver_image }}" alt="">
                                                    </span>
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ $booking->driver_name }}</h6>
                                                        <p>{{ $booking->driver_phone_number }}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="border-bottom mb-3 pb-2">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium fs-14">{{ __('admin.bookings.pricing_of_vehicle') }}</h6>
                                        <p>{{ $booking->currency_symbol }}{{ $booking->vehicle_total_price }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium d-flex align-items-center fs-14">{{ $booking->extra_service_count }} {{ __('admin.common.extra_services') }}
                                            <a href="javascript:void(0);" class="me-2 ms-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ !empty($booking->extra_service_names) ? implode(', ', $booking->extra_service_names) : '' }}"><i class="ti ti-info-circle-filled"></i></a>
                                        </h6>
                                        <p>{{ $booking->currency_symbol }}{{ $booking->total_extra_service_price }}</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium d-flex align-items-center fs-14">{{ __('admin.bookings.security_deposit') }}
                                        </h6>
                                        <p>{{ $booking->security_deposit ? $booking->currency_symbol . $booking->security_deposit :  $booking->currency_symbol . '0.00' }}</p>
                                    </div>
                                    @if ($booking->driver_name)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium d-flex align-items-center fs-14">{{ __('admin.bookings.driver_price') }}
                                        </h6>
                                        <p>{{ $booking->currency_symbol }}{{ $booking->driver_price }}</p>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h6 class="fw-medium d-flex align-items-center fs-14">{{ $booking->insurance_count }} {{ __('admin.common.insurances') }}
                                            <a href="javascript:void(0);" class="me-2 ms-2" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-original-title="{{ !empty($booking->insurance_benefits_formatted) ? implode(', ', $booking->insurance_benefits_formatted) : '' }}"><i class="ti ti-info-circle-filled"></i></a>
                                        </h6>
                                        <p>{{ $booking->currency_symbol }}{{ $booking->total_insurance_price }}</p>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center justify-content-between">
                                    <h6>{{ __('admin.common.total_price') }}</h6>
                                    <h6>{{ $booking->currency_symbol }}{{ $booking->final_price }}</h6>
                                </div>
                            </div>
                            <div class="tab-pane" id="solid-tab2" role="tabpanel">
                                <div class="border rounded p-3 bg-light mb-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar flex-shrink-0 me-2">
                                                    <img src="{{ $booking->vehicle_image }}" alt="">
                                                </span>
                                                <div>
                                                    <p class="mb-1">{{ $booking->vehicle_type }}</p>
                                                    <h6 class="fs-14">{{ $booking->vehicle_name }}</h6>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-end">
                                                <p class="mb-1">{{ __('admin.common.price') }}</p>
                                                <h6 class="fs-14">{{ $booking->currency_symbol }}{{ $booking->vehicle_price }}<span class="text-gray-5 fw-normal">/{{ ucfirst($booking->vehicle_price_type ?? $booking->rental_type) }}</span></h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-3">{{ __('admin.common.history') }}</h6>
                                    @if ($bookingHistories->count() > 0)
                                    @foreach ($bookingHistories as $history)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                            <h5 class="mb-2">{{ \Carbon\Carbon::parse($history->created_at)->format('d') }}</h5>
                                            <span class="fw-medium fs-12 bg-primary-transparent p-1 d-inline-block rounded-1 text-gray-9">
                                                {{ \Carbon\Carbon::parse($history->created_at)->format('M, Y') }}
                                            </span>
                                        </div>
                                        <div>
                                            <h6 class="fs-14 mb-1">{{ $history->message }}</h6>
                                            <span class="fs-13">{{ \Carbon\Carbon::parse($history->created_at)->format('h:i A') }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                    @else
                                    <p class="fs-13 text-gray-5 text-center">{{ __('admin.common.no_history_found') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center flex-wrap row-gap-3">
                    <a href="javascript:void(0);" class="btn btn-primary me-3 d-none"><i class="ti ti-files me-1"></i>View Invoice</a>
                    <a href="javascript:void(0);" class="btn btn-dark me-3 d-none"><i class="ti ti-calendar me-1"></i>Reschedule</a>
                    @if ($booking->booking_status == 1 || $booking->booking_status == 2 || $booking->booking_status == 4)
                    <a href="javascript:void(0);" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#booking_cancel_modal"><i class="ti ti-x me-1"></i>{{ __('admin.bookings.cancel_booking') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Page Wrapper -->

<div class="modal fade addmodal" id="booking_cancel_modal">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0 modal-title">{{ __('admin.bookings.cancel_booking') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="cancelBookingForm" autocomplete="off">
                @csrf
                <input type="hidden" name="booking_id" id="booking_id" value="{{ $booking->id }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.bookings.cancel_reason') }}<span class="text-danger"> *</span></label>
                                <textarea class="form-control" rows="4" name="cancel_reason" id="cancel_reason"></textarea>
                                <span class="text-danger error-text" id="cancel_reason_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.close') }}</a>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.bookings.cancel_booking') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/booking/reservation-details.js') }}"></script>
@endpush