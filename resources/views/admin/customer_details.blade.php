@extends('admin.admin')

@section('meta_title', __('admin.common.customer') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="mb-3">
                        <a href="{{ route('admin.customers') }}" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-left me-1"></i>{{ __('admin.common.customer') }}</a>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="border-bottom mb-3 pb-3">
                                <h5>{{ __('admin.common.basic_details') }}</h5>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <div class="d-flex align-items-center">
                                    <span class="avatar avatar-lg me-3">
                                        <img src="{{ $customer->profile_image }}" alt="img">
                                    </span>
                                    <div>
                                        <h6 class="mb-1">{{ $customer->customer_full_name ?? $customer->username }}</h6>
                                        <div class="d-flex align-items-center">
                                            <p class="mb-0 me-2">{{ __('admin.common.added_on') }} : {{ $customer->added_on }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-3">
                                    <span class="badge badge-md bg-info-transparent">{{ __('admin.manage.license_number') }} : {{ $customer->card_number ?? '-' }}</span>
                                    <span class="badge badge-md bg-orange-transparent">{{ __('admin.manage.valid_till') }} : {{ $customer->valid_date ?? '-' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4 mb-xl-0">
                        <div class="card-header py-0">
                            <ul class="nav nav-tabs nav-tabs-bottom tab-dark">
                                <li class="nav-item">
                                    <a class="nav-link {{ last(request()->segments()) != 'recent-rents' ? 'active' : '' }}" href="#car-info" data-bs-toggle="tab">{{ __('admin.common.overview') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link {{ last(request()->segments()) == 'recent-rents' ? 'active' : '' }}" href="#car-price" data-bs-toggle="tab">{{ __('admin.manage.recent_rents') }}</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#car-service" data-bs-toggle="tab">{{ __('admin.common.history') }}</a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <!-- Overview -->
                                <div class="tab-pane fade {{ last(request()->segments()) != 'recent-rents' ? 'active show' : '' }}" id="car-info">
                                    <div class="border-bottom mb-3 pb-0">
                                        <div class="row">
                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.date_of_birth') }}</h6>
                                                    <p class="fs-13">{{ $customer->dob ?? '-'}}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.gender') }}</h6>
                                                    <p class="fs-13">{{ ucfirst($customer->gender ?? '-')  }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.language') }}</h6>
                                                    <p class="fs-13">{{ $customer->language_name ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.phone_number') }}</h6>
                                                    <p class="fs-13">{{ $customer->phone_number ?? '-' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4 col-sm-6">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.email') }}</h6>
                                                    <p class="fs-13">{{ $customer->email }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-3">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.address') }}</h6>
                                                    <p class="fs-13">{{ $customer->address ?? '-' }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                            <h6>{{ __('admin.common.documents') }}</h6>
                                        </div>
                                        @if ($customer->documents->isNotEmpty() && $customer->documents->count() > 0)
                                            <div class="d-flex align-items-center flex-wrap gap-4">
                                                @foreach ($customer->documents as $document)
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">
                                                            <img src="{{ $document->icon }}" alt="img">
                                                        </span>
                                                        <div>
                                                            <a href="{{ $document->document_url }}" class="fs-14 fw-medium">{{ $document->file_name ?? 'default-image-02.jpg'}}</a>
                                                            <p class="fs-13">{{ $document->size == '0' || $document->size == '' ? '100 KB' : $document->size}}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="fs-13 text-gray-5 text-center">{{ __('admin.common.no_documents_found') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <!-- /Overview -->
                                <!-- Recent Rents -->
                                <div class="tab-pane fade {{ last(request()->segments()) == 'recent-rents' ? 'active show' : '' }}" id="car-price">
                                    @if ($bookings->isNotEmpty())
                                        <div>
                                            @foreach ($bookings as $booking)
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="row gy-3 align-items-center">
                                                            <div class="col-md-12">
                                                                <div class="row  align-items-center">
                                                                    <div class="col-lg-4">
                                                                        <div class="d-flex align-items-center">
                                                                            <span class="avatar flex-shrink-0 me-2">
                                                                                <img src="{{ $booking->vehicle_image_url }}" alt="">
                                                                            </span>
                                                                            <div>
                                                                                <a href="#" class="text-info">{{ $booking->reservation_id }}</a>
                                                                                <h6 class="fs-14">{{ $booking->vehicle_name }}</h6>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div>
                                                                            <h6 class="fs-14 mb-1">{{ __('admin.common.booked_on') }}</h6>
                                                                            <p>{{ $booking->booking_date }}</p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-4">
                                                                        <div>
                                                                            <h6 class="fs-14 mb-1">{{ __('admin.common.amount') }}</h6>
                                                                            <p>{{ $defaultCurrency }}{{ $booking->final_price }}</p>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-3 d-none">
                                                                <div class="d-flex align-items-center icon-list justify-content-end">
                                                                    <a href="#" class="edit-icon me-2"><i class="ti ti-eye"></i></a>
                                                                    <a href="#" class="edit-icon"><i class="ti ti-download"></i></a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <p class="fs-13 text-gray-5 text-center">{{ __('admin.manage.no_rents_found') }}</p>
                                    @endif
                                </div>
                                <!-- /Recent Rents -->
                                <!-- History -->
                                <div class="tab-pane fade" id="car-service">
                                    @if ($bookingHistories->isNotEmpty())
                                        @foreach ($bookingHistories as $history)
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-2 pb-2 border-bottom">
                                            <div class="border rounded text-center flex-shrink-0 p-1 me-2">
                                                <h5 class="mb-2">{{ \Carbon\Carbon::parse($history->created_at)->format('d') }}</h5>
                                                <span class="fw-medium fs-12 bg-primary-transparent p-1 d-inline-block rounded-1 text-gray-9">{{ \Carbon\Carbon::parse($history->created_at)->format('M, Y') }}</span>
                                            </div>
                                            <div class="flex-fill">
                                                <div class="d-flex align-items-center justify-content-between mb-2">
                                                    <h6 class="fs-14 fw-semibold">{{ ucfirst($history->action) }}</h6>
                                                    <span class="fs-13">{{ \Carbon\Carbon::parse($history->created_at)->format('h:i A') }}</span>
                                                </div>
                                                <span class="fs-13">{{ $history->message }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    @else
                                        <p class="fs-13 text-gray-5 text-center">{{ __('admin.common.no_history_found') }}</p>
                                    @endif
                                </div>
                                <!-- /History -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection
