@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ __('admin.reports.earning_report') }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.reports') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2 me-2">
                    <a href="javascript:void(0);" class="btn btn-white d-flex align-items-center btn-print">
                        <i class="ti ti-printer me-2"></i>{{ __('admin.common.print') }}
                    </a>
                </div>
                <div class="mb-2">
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center btn-export">
                            <i class="ti ti-upload me-1"></i>{{ __('admin.common.export') }}
                        </a>
                    </div>
                </div>

            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Charts -->
        <div class="row">
            <!-- Total Earnings -->
            <div class="col-xl-12 d-flex">
                <div class="row flex-fill earnings-report">
                    <div class="col-md-6 col-xl-3 d-flex">
                        <div class="card flex-fill position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-2  border-bottom border-gray">
                                    <div>
                                        <span class="fs-14 fw-normal text-truncate mb-1">{{ __('admin.reports.total_earnings') }}</span>
                                        <h5>{{$symbol}}{{$totalIncome}}</h5>
                                    </div>
                                    <a href="javascript:void(0);" class="avatar avatar-md avatar-rounded bg-orange border border-primary">
                                        <span class="text-primary"><i class="ti ti-currency-dollar text-white"></i></span>
                                    </a>
                                </div>
                                <p class="fs-12 fw-normal d-flex align-items-center justify-content-center text-truncate mt-2">
                                    @if($sign == '+')
                                    <span class="text-success fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                    @if($sign == '-')
                                    <span class="text-danger fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 d-flex">
                        <div class="card flex-fill position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-2  border-bottom border-gray">
                                    <div>
                                        <span class="fs-14 fw-normal text-truncate mb-1">{{ __('admin.reports.revenue_breakdown') }}</span>
                                        <h5>{{$symbol}}{{$grandTotal}}</h5>
                                    </div>
                                    <a href="javascript:void(0);" class="avatar avatar-md avatar-rounded bg-success border border-success">
                                        <span class="text-primary"><i class="ti ti-chart-donut-4 text-white"></i></span>
                                    </a>
                                </div>
                                <p class="fs-12 fw-normal d-flex align-items-center justify-content-center text-truncate mt-2">
                                    @if($signbreak == '+')
                                    <span class="text-success fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageBreakChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                    @if($signbreak == '-')
                                    <span class="text-danger fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageBreakChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 d-flex">
                        <div class="card flex-fill position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-2  border-bottom border-gray">
                                    <div>
                                        <span class="fs-14 fw-normal text-truncate mb-1">{{ __('admin.reports.net_profit') }}</span>
                                        <h5>{{$symbol}}{{$totalIncome}}</h5>
                                    </div>
                                    <a href="javascript:void(0);" class="avatar avatar-md avatar-rounded bg-info border border-info">
                                        <span class="text-primary"><i class="ti ti-stairs-up text-white"></i></span>
                                    </a>
                                </div>
                                <p class="fs-12 fw-normal d-flex align-items-center justify-content-center text-truncate mt-2">
                                    @if($sign == '+')
                                    <span class="text-success fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                    @if($sign == '-')
                                    <span class="text-danger fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xl-3 d-flex">
                        <div class="card flex-fill position-relative">
                            <div class="card-body">
                                <div class="d-flex align-items-center justify-content-between pb-2  border-bottom border-gray">
                                    <div>
                                        <span class="fs-14 fw-normal text-truncate mb-1">{{ __('admin.reports.top_performing_vehicles') }}</span>
                                        <h5>{{$vehicle->name ?? ''}} : {{$symbol}}{{$topEarningCarTotal ?? ''}}</h5>
                                    </div>
                                    <a href="javascript:void(0);" class="avatar avatar-md avatar-rounded bg-danger border border-danger">
                                        <span class="text-primary"><i class="ti ti-car text-white"></i></span>
                                    </a>
                                </div>
                                <p class="fs-12 fw-normal d-flex align-items-center justify-content-center text-truncate mt-2">
                                    @if($signCar == '+')
                                    <span class="text-success fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageCarChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                    @if($signCar == '-')
                                    <span class="text-danger fs-12 d-flex align-items-center me-1">
                                        <i class="ti ti-arrow-wave-right-up me-1"></i>{{$percentageCarChangeFormatted}}
                                    </span> {{ __('admin.reports.from_last_month') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Total Earnings -->

            <!-- Total Earnings -->
            <div class="col-xl-8 d-flex">
                <div class="card flex-fill earnings-chart">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center ">
                                <span class="avatar avatar-md avatar-rounded bg-orange-transparent border-orange me-2"><i class="ti ti-currency-dollar text-orange"></i></span>
                                <h5>{{__('admin.reports.total_earnings')}} </h5>
                            </div>
                            <div class="earning-square d-flex align-items-center">
                                <span class="me-2"></span>
                                <p class="fs-12 text-gray-5">{{__('admin.reports.earnings')}}</p>

                            </div>
                        </div>
                    </div>
                    <div class="card-body py-0">
                        <div id="expense-analysis"></div>
                    </div>
                </div>
            </div>
            <!-- /Total Earnings -->

            <!-- Total Earnings -->
            <div class="col-xl-4 d-flex">
                <div class="card flex-fill earnings-chart">
                    <div class="card-header border-0 pb-0">
                        <div class="d-flex flex-wrap justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="avatar avatar-md avatar-rounded bg-success-transparent border-success me-2">
                                    <i class="ti ti-currency-dollar text-success"></i>
                                </span>
                                <h5>{{__('admin.reports.earnings_breakdown')}}</h5>
                            </div>
                        </div>
                    </div>
                    <div class="card-body py-0">
                        <div id="project-report"></div>
                        <div>
                            <ul class="breakdown-reports" id="breakdown-list"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Total Earnings -->

        </div>
        <!-- /Charts -->

        <!-- Table Header -->
        <div>
            <h5 class="mb-3">{{__('admin.reports.earnings')}}</h5>
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" id="filterDropdown" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span id="filterText">{{ __('admin.common.latest') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-filter="latest">{{ __('admin.common.latest') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-filter="ascending">{{ __('admin.common.ascending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-filter="descending">{{ __('admin.common.descending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-filter="last_month">{{ __('admin.common.last_month') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-filter="last_7_days">{{ __('admin.common.last_7_days') }}</a></li>
                        </ul>
                    </div>

                    <div class="me-2">
                        <div class="input-icon-start position-relative topdatepicker">
                            <span class="input-icon-addon">
                                <i class="ti ti-calendar"></i>
                            </span>
                            <input type="text" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                        </div>
                    </div>
                    <div class="dropdown">
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i> {{__('admin.common.filter')}}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- /Table Header -->


        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{__('admin.common.filters')}}</h6>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{__('admin.reports.payment_method')}}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input payment-filter m-0 me-2" type="checkbox" value="cod">{{__('admin.common.cod')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input payment-filter m-0 me-2" type="checkbox" value="paypal">{{__('admin.common.paypal')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input payment-filter m-0 me-2" type="checkbox" value="stripe">{{__('admin.common.stripe')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input payment-filter m-0 me-2" type="checkbox" value="wallet">{{__('admin.common.wallet')}}
                            </label>
                        </li>
                    </ul>



                </div>

                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{__('admin.common.status')}}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="in progress">{{__('admin.common.in_progress')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="confirmed">{{__('admin.common.confirmed')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="rejected">{{__('admin.common.rejected')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="booked">{{__('admin.common.booked')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="completed">{{__('admin.common.completed')}}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input status-filter m-0 me-2" type="checkbox" value="cancelled">{{__('admin.common.cancelled')}}
                            </label>
                        </li>
                    </ul>

                </div>
                <a href="javascript:void(0);" class="text-danger links" onclick="location.reload();">{{__('admin.common.clear_all')}}</a>
            </div>
        </div>

        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive brandstable country-table">
            <table class="table datatable" id="earningTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.common.customer')) }}</th>
                        <th>{{ strtoupper(__('admin.common.amount')) }}</th>
                        <th>{{ strtoupper(__('admin.reports.payment_method')) }}</th>
                        <th>{{ strtoupper(__('admin.common.date')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bookings as $booking)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <a href="customer-details.html" class="avatar avatar-rounded me-2 flex-shrink-0">
                                    @php
                                    $imagePath = 'storage/' . $booking->profile_image;
                                    $defaultImage = asset('custom/img/default-profile.png');
                                    @endphp

                                    <img src="{{ file_exists(public_path($imagePath)) ? asset($imagePath) : $defaultImage }}" alt="img"></a>
                                <div>
                                    <h6 class="fs-14 fw-semibold">{{$booking->name ?? ''}}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <p class="text-gray-9">{{$symbol}}{{$booking->final_price}}</p>
                        </td>
                        <td>
                            <p class="text-gray-9">{{$booking->payment_type ?? '-'}}</p>
                        </td>
                        <td>
                            <p class="text-gray-9">{{ \Carbon\Carbon::parse($booking->booking_date)->format('d M Y') }}</p>
                        </td>
                        <td>
                            @php
                            $statusLabels = [
                            1 => ['text' => 'In Progress', 'color' => 'success'],
                            2 => ['text' => 'Confirmed', 'color' => 'success'],
                            3 => ['text' => 'Rejected', 'color' => 'danger'],
                            4 => ['text' => 'Booked', 'color' => 'success'],
                            5 => ['text' => 'Completed', 'color' => 'success'],
                            6 => ['text' => 'Cancelled', 'color' => 'danger']
                            ];

                            $status = $statusLabels[$booking->booking_status] ?? ['text' => 'Unknown', 'color' => 'secondary'];
                            @endphp

                            <span class="badge badge-soft-{{ $status['color'] }} d-inline-flex align-items-center badge-sm">
                                <i class="ti ti-point-filled me-1 text-{{ $status['color'] }}"></i> {{ $status['text'] }}
                            </span>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/report/earning.js') }}"></script>
@endpush