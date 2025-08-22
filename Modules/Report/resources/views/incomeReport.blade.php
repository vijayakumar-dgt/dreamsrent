@extends('admin.admin')

@section('meta_title', __('admin.reports.income') . ' || ' . $companyName)

@section('content')
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <x-admin.breadcrumb 
                :title="__('admin.reports.income')" 
                :breadcrumbs="[
                    __('admin.reports.income') => ''
                ]">
                <x-slot name="toolbar">
                    <div class="mb-2 me-2">
                        <button type="button" id="printButton" class="btn btn-white d-flex align-items-center">
                            <i class="ti ti-printer me-2"></i>{{__('admin.common.print')}}
                        </button>
                    </div>
                    <div class="mb-2">
                        <div class="dropdown">
                            <button type="button" id="exportButton" class="btn btn-dark d-inline-flex align-items-center">
                                <i class="ti ti-upload me-1"></i>{{__('admin.common.export')}}
                            </button>
                        </div>
                    </div>
                </x-slot>
            </x-admin.breadcrumb>
            <!-- Charts -->
            <div class="row border-bottom mb-4">
                <!-- Total Earnings -->
                <div class="col-xl-4">
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="border-end pe-4 me-4">
                                        <p class="mb-0">{{__('admin.reports.total_income')}}</p>
                                        <h6 class="fw-semibold">{{$symbol}}{{$totalIncome}}</h6>
                                    </div>
                                    <div>
                                        <p class="mb-0">{{__('admin.reports.top_earning_vehicle')}}</p>
                                        <h6 class="fw-semibold">{{$vehicle->name ?? '-'}}</h6>
                                    </div>
                                </div>
                                <span class="avatar avatar-md bg-orange rounded-circle">
                                    {{$symbol}}
                                </span>
                            </div>
                            <div class="bg-gray-100 d-inline-flex justify-content-between align-items-center w-100 rounded p-2">
                                <p class="text-gray-500 mb-0 fs-12">{{__('admin.reports.last_week')}}</p>
                                <span class="text-success fs12"><i class="ti ti-arrow-wave-right-up me-1"></i>{{$sign}}{{round(abs($percentageChange), 2)}}%</span>
                            </div>
                        </div>
                    </div>
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="border-end pe-4 me-4">
                                        <p class="mb-0">{{__('admin.reports.total_expenses')}}</p>
                                        <h6 class="fw-semibold">{{$symbol}}0</h6>
                                    </div>
                                    <div>
                                        <p class="mb-0">{{__('admin.reports.highest_expense')}}</p>
                                        <h6 class="fw-semibold">{{__('admin.reports.vehicle_repairs')}}</h6>
                                    </div>
                                </div>
                                <span class="avatar avatar-md bg-success rounded-circle">
                                    <i class="ti ti-stairs-down fs-18"></i>
                                </span>
                            </div>
                            <div class="bg-gray-100 d-inline-flex justify-content-between align-items-center w-100 rounded p-2">
                                <p class="text-gray-500 mb-0 fs-12">{{__('admin.reports.last_week')}}</p>
                                <span class="text-danger fs12"><i class="ti ti-arrow-wave-right-down me-1"></i>0%</span>
                            </div>
                        </div>
                    </div>
                    <div class="card flex-fill mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="border-end pe-4 me-4">
                                        <p class="mb-0">{{__('admin.reports.net_profit')}}</p>
                                        <h6 class="fw-semibold">{{$symbol}}{{$totalIncome}}</h6>
                                    </div>
                                    <div>
                                        <p class="mb-0">{{__('admin.reports.profit_margin')}}</p>
                                        <h6 class="fw-semibold">0%</h6>
                                    </div>
                                </div>
                                <span class="avatar avatar-md bg-info rounded-circle">
                                    <i class="ti ti-stairs-up fs-18"></i>
                                </span>
                            </div>
                            <div class="bg-gray-100 d-inline-flex justify-content-between align-items-center w-100 rounded p-2">
                                <p class="text-gray-500 mb-0 fs-12">{{__('admin.reports.last_week')}}</p>
                                <span class="text-success fs12"><i class="ti ti-arrow-wave-right-up me-1"></i>{{$sign}}{{round(abs($percentageChange), 2)}}%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Total Earnings -->
                <!-- Total Earnings -->
                <div class="col-xl-8 d-flex">
                    <div class="card flex-fill earnings-chart">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <h5>{{__('admin.reports.income')}} </h5>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center me-4">
                                        <span class="chart-color bg-primary me-1"></span>
                                        <p class="fs-13">{{__('admin.reports.income')}}</p>
                                    </div>
                                </div>
                                <div class="dropdown me-2">
                                    <button type="button" class="dropdown-filter dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i> {{__('admin.reports.this_week')}}
                                    </button>
                                    <ul class="dropdown-menu  dropdown-menu-end p-2">
                                        <li>
                                            <button type="button" class="dropdown-item dropdown-item-chat rounded-1">{{__('admin.reports.this_week')}}</button>
                                        </li>
                                        <li>
                                            <button type="button" class=" dropdown-item dropdown-item-chat rounded-1">{{__('admin.reports.last_week')}}</button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="border rounded p-2 me-4 income-summary">
                                    <p class="mb-0 text-gray-5">{{__('admin.reports.income')}} {{__('admin.reports.this_week')}}</p>
                                    <h5>{{$symbol}} <span class="text-success fs-13 fw-semibold">0%</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-0">
                            <div id="income_expense_chart"></div>
                        </div>
                    </div>
                </div>
                <!-- /Total Earnings -->
            </div>
            <!-- /Charts -->
            <div class="coupons-tabs">
                <ul class="nav nav-pills mb-3" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                            href="#income" aria-selected="true">{{__('admin.reports.income')}}</a>
                    </li>
                </ul>
                <div class="tab-content pb-3">
                    <div class="tab-pane show active" id="income" role="tabpanel">
                        <!-- Table Header -->
                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                            <div class="d-flex align-items-center flex-wrap row-gap-3">
                                <div class="dropdown me-2">
                                    <button type="button" class="dropdown-toggle dropdown-toggle-chat btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="selectedFilter">
                                        <i class="ti ti-filter me-1"></i> {{__('admin.common.sort_by')}} : <span >{{__('admin.common.latest')}}</span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        <li><button type="button" class="dropdown-item rounded-1 filter-option" data-filter="latest">{{__('admin.common.latest')}}</button></li>
                                        <li><button type="button" class="dropdown-item rounded-1 filter-option" data-filter="asc">{{__('admin.common.ascending')}}</button></li>
                                        <li><button type="button" class="dropdown-item rounded-1 filter-option" data-filter="desc">{{__('admin.common.descending')}}</button></li>
                                        <li><button type="button" class="dropdown-item rounded-1 filter-option" data-filter="lastMonth">{{__('admin.common.last_month')}}</button></li>
                                        <li><button type="button" class="dropdown-item rounded-1 filter-option" data-filter="last7days">{{__('admin.common.last_7_days')}}</button></li>
                                    </ul>
                                </div>
                                <div class="me-2">
                                    <div class="input-icon-start position-relative topdatepicker">
                                        <span class="input-icon-addon">
                                            <i class="ti ti-calendar"></i>
                                        </span>
                                        <input type="text" class="form-control date-range bookingrange" id="dateRangeFilter" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                                        <i class="ti ti-filter me-1"></i> {{__('admin.common.filter')}}
                                    </a>
                                </div>
                            </div>
                            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                <div class="top-search">
                                    <div class="top-search-group">
                                        <span class="input-icon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="{{__('admin.common.search')}}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Table Header -->
                        <div class="collapse" id="filtercollapse">
                            <div class="filterbox mb-3 d-flex align-items-center">
                                <h6 class="me-3">{{__('admin.common.filters')}}</h6>
                                <div class="dropdown me-2" id="carFilterDropdown">
                                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        {{__('admin.reports.select_vehicles')}}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-lg p-2 custom-scroll">
                                        @foreach($vehicleInfo as $car)
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                <input class="form-check-input m-0 me-2 car-checkbox" type="checkbox" value="{{$car->name}}">{{$car->name}}
                                            </label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="dropdown me-2">
                                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                                        {{__('admin.common.status')}}
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" value="paid">{{__('admin.reports.paid')}}
                                            </label>
                                        </li>
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                <input class="form-check-input m-0 me-2 status-checkbox" type="checkbox" value="pending">{{__('admin.reports.pending')}}
                                            </label>
                                        </li>
                                    </ul>
                                </div>
                                <button type="button" class="text-danger links border-0 bg-transparent" onclick="location.reload();">{{__('admin.common.clear_all')}}</button>
                            </div>
                        </div>
                        <!-- Custom Data Table -->
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table datatable" id="incomeTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                                        <th>{{strtoupper(__('admin.reports.rental_fees'))}}</th>
                                        <th>{{strtoupper(__('admin.reports.additional_services'))}}</th>
                                        <th>{{strtoupper(__('admin.reports.total_income'))}}</th>
                                        <th>{{strtoupper(__('admin.common.date'))}}</th>
                                        <th>{{strtoupper(__('admin.common.status'))}}</th>
                                    </tr>
                                </thead>
                                <tbody id="incomeTableBody">
                                    @foreach($bookingsCount as $booking)
                                    <tr data-date="{{ \Carbon\Carbon::parse($booking->booking_date)->format('Y-m-d') }}" data-total="{{ $booking->final_price }}">
                                        <td>
                                            <input type="hidden" class="car-name" value="{{ $booking->name }}">
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="avatar me-2 flex-shrink-0">
                                                    @php
                                                    $imagePath = $booking->vehicle_image ?? '';
                                                    $filename = basename($imagePath);
                                                    $newpath = 'vehicles/images/small/' . $filename;
                                                    $file = public_path('storage/' . $newpath);
                                                    if (file_exists($file)) {
                                                        $imagePath = $newpath;
                                                    }
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'default') }}" class="rounded-3" alt="Image Preview"></a>
                                                <div>
                                                    <h6><a href="javascript:void(0);" class="fw-semibold fs-14">{{$booking->name}}</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <p class="fs-14 text-gray-9">{{$symbol}}{{ number_format($booking->vehicle_total_price, 2) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9">{{$symbol}}{{ number_format($booking->total_extra_service_price, 2) }}</p>
                                        </td>
                                        <td>
                                            <p class="text-gray-9">{{$symbol}}{{ number_format($booking->final_price, 2) }}</p>
                                        </td>
                                        <td>{{ formatDateTime($booking->booking_date, false) }}</td>
                                        <td>
                                            @php
                                                $isPaid = ($booking->booking_by === 'admin' && ($booking->payment_status === null || $booking->payment_status == 2)) ||
                                                         ($booking->booking_by !== 'admin' && $booking->payment_status == 2);
                                                $statusClass = $isPaid ? 'success' : 'danger';
                                                $statusText = $isPaid ? __('admin.reports.paid') : __('admin.reports.pending');
                                            @endphp
                                            <span class="badge badge-soft-{{ $statusClass }} d-inline-flex align-items-center badge-sm payment-status">
                                                <i class="ti ti-point-filled me-1 text-{{ $statusClass }}"></i>
                                                {{ $statusText }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="p-3">
                                {{ $bookingsCount->links('vendor.pagination.custom-bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="booking-data" data-bookings='@json($bookings)'></div>
        @include('admin.partials.footer')
    </div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/report/income.js') }}"></script>
@endpush