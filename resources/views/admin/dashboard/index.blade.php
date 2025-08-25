@extends('admin.admin')

@section('meta_title', __('admin.dashboard.dashboard') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content pb-0">
            <x-admin.breadcrumb
                :title="__('admin.dashboard.dashboard')"
                :breadcrumbs="[
                    __('admin.dashboard.admin_dashboard') => ''
                ]"
            />
            <div class="row">
                <div class="col-xl-8 d-flex flex-column">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="row align-items-center row-gap-3">
                                <div class="col-sm-7">
                                    <h4 class="mb-1">{{ __('admin.dashboard.welcome') }}, {{ getCurrentUserFullname() ?? '' }} </h4>
                                    <p>{{count($carTypes)}}+ {{ __('admin.dashboard.budget_friendly_cars_available_for_the_rents') }}</p>
                                    <div class="d-flex align-items-center flex-wrap gap-4 mb-3">
                                        <div>
                                            <p class="mb-1">{{ __('admin.dashboard.total_no_of_cars') }}</p>
                                            <h3>{{count($carTypes)}}</h3>
                                        </div>
                                        <div class="cars-count">
                                            <p class="rent-count mb-2"><span class="fw-semibold text-gray-9">{{$bookingCount}}</span> {{ __('admin.dashboard.in_rental') }}</p>
                                            <p class="upcoming-count"><span class="fw-semibold text-gray-9">{{$upcomingCount}}</span> {{ __('admin.dashboard.upcoming') }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-wrap">
                                        @if (hasPermission($permissions, 'reservations', 'view') && isAccessMenu('reservation'))
                                        <a href="{{route('reservation.index')}}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-eye me-1"></i>{{ __('admin.dashboard.reservations') }}</a>
                                        @endif
                                        @if (hasPermission($permissions, 'vehicles', 'create'))
                                        <a href="{{ route('vehicle.vehicleadd') }}" class="btn btn-dark d-flex align-items-center"><i class="ti ti-plus me-1"></i>{{ __('admin.dashboard.add_new_car') }}</a>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-5">
                                    <img src="{{asset('/backend/assets/img/icons/car.svg')}}" alt="Vehicle Preview">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <div class="border-bottom mb-2 pb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-secondary-100 text-secondary me-2">
                                                <i class="ti ti-calendar-time fs-14"></i>
                                            </span>
                                            <p>{{ __('admin.dashboard.total_reservations') }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <h5 class="mb-1">{{count($booking)}}</h5>
                                            @if($sign == '+')
                                            <p><span class="text-success fw-semibold">{{$percentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                            @if($sign == '-')
                                            <p><span class="text-danger fw-semibold">{{$percentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <div class="border-bottom mb-2 pb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-orange-100 text-orange me-2">
                                                <i class="ti ti-moneybag fs-14"></i>
                                            </span>
                                            <p>{{ __('admin.dashboard.total_earnings') }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <h5 class="mb-1">{{$symbol}}{{$amount}}</h5>
                                            @if($amountSymbol == '+')
                                            <p><span class="text-success fw-semibold">{{$amountPercentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                            @if($amountSymbol == '-')
                                            <p><span class="text-danger fw-semibold">{{$amountPercentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 d-flex">
                            <div class="card flex-fill">
                                <div class="card-body">
                                    <div class="border-bottom mb-2 pb-2">
                                        <div class="d-flex align-items-center">
                                            <span class="avatar avatar-sm bg-violet-100 text-violet me-2">
                                                <i class="ti ti-car fs-14"></i>
                                            </span>
                                            <p>{{ __('admin.dashboard.total_cars') }}</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between gap-2">
                                        <div>
                                            <h5 class="mb-1">{{count($carTypes)}}</h5>
                                            @if($carSymbol == '+')
                                            <p><span class="text-success fw-semibold">{{$carPercentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                            @if($carSymbol == '-')
                                            <p><span class="text-danger fw-semibold">{{$carPercentageChange}}</span> {{ __('admin.dashboard.from_last_week') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Newly Added Cars -->
                @if (haspermission($permissions, 'vehicles', 'view'))
                <div class="col-xl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h5>{{ __('admin.dashboard.newly_added_cars') }}</h5>
                                <a href="{{ route('vehicle.list') }}" class="text-decoration-underline fw-medium">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            @if(count($carTypes) != 0)
                            <div class="mb-2">
                                @php
                                $imagePath = ($carTypes[0]->vehicle_image ?? " ");
                                @endphp
                                <img src="{{ uploadedAsset($imagePath, 'default2') }}" class="dash-height rounded w-100" alt="Vehicle">
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div>
                                    <h6 class="fs-14 fw-semibold">{{$carTypes[0]->name ?? ""}}</h6>
                                </div>
                                <h6 class="fs-14 fw-semibold">{{$symbol}}{{ json_decode($carTypes[0]->vehicle_price, true)[0]['daily'] ?? '0' }} <span class="fw-normal text-gray-5">/{{ __('admin.dashboard.from_last_week') }}day</span></h6>
                            </div>
                            <div class="row g-2 justify-content-center">
                                <div class="col-sm-4 col-6 d-flex">
                                    <div class="bg-light p-2 br-5 flex-fill text-center">
                                        <h6 class="fs-14 fw-semibold">{{ __('admin.dashboard.fuel_type') }}</h6>
                                        <span class="fs-13">{{$carTypes[0]->fuel_type ?? ""}}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 d-flex">
                                    <div class="bg-light p-2 br-5 flex-fill text-center">
                                        <h6 class="fs-14 fw-semibold">{{ __('admin.dashboard.passengers') }}</h6>
                                        <span class="fs-13">{{$carTypes[0]->passenger_capacity ?? ""}}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4 col-6 d-flex">
                                    <div class="bg-light p-2 br-5 flex-fill text-center">
                                        <h6 class="fs-14 fw-semibold">{{ __('admin.dashboard.driving_type') }}</h6>
                                        <span class="fs-13">{{$carTypes[0]->driving_name ?? ""}}</span>
                                    </div>
                                </div>
                            </div>
                            @else
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="text-muted no-datas">{{ __('admin.blog.no_data_found') }}</span>
                            </div>
                            @endif
                        </div>

                    </div>
                </div>
                @endif
                <!-- /Newly Added Cars -->
            </div>
            <div class="row">
                @if (haspermission($permissions, 'reservations', 'view') && isAccessMenu('reservation'))
                <!-- Recent Reservations -->
                <div class="col-xl-12 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <h5>{{ __('admin.dashboard.recent_reservations') }}</h5>
                                <a href="{{route('reservation.index')}}" class="text-decoration-underline fw-medium">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table custom-table1">
                                    @if(count($reservations) != 0)
                                    @foreach($reservations as $reservation)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div target="_blank" class="avatar flex-shrink-0">
                                                    @php
                                                    $imagePath = $reservation->vehicle_image ?? "";
                                                    $filename = basename($imagePath);
                                                    $newpath = 'vehicles/images/small/' . $filename;
                                                    $file = public_path('storage/' . $newpath);
                                                    if (file_exists($file)) {
                                                        $imagePath = $newpath;
                                                    }
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'default') }}" class="admin-vehicle-image" alt="Vehicle">
                                                </div>
                                                <?php
                                                $start = \Carbon\Carbon::parse($reservation->start_datetime);
                                                $end = \Carbon\Carbon::parse($reservation->end_datetime);

                                                $reservation->day_count = $start->diffInDays($end) + 1;
                                                ?>
                                                <div class="flex-grow-1 ms-2">
                                                    <p class="d-flex align-items-center fs-13 text-default mb-1">{{number_format($reservation->day_count, 0)}} {{ __('admin.dashboard.days') }}<i class="ti ti-circle-filled text-primary fs-5 mx-1"></i>{{$reservation->driving_name}}</p>
                                                    <h6 class="fs-14 fw-semibold mb-1">{{$reservation->name}}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-1 mb-1">
                                                <?php
                                                $pickLocation = Illuminate\Support\Facades\DB::table('locations')->where('id', $reservation->pickup_location)->select('name')->first();
                                                ?>
                                                <h6 class="fs-14 fw-semibold">{{$pickLocation->name ?? ''}}</h6>
                                                <span class="connect-line"></span>
                                                <?php
                                                $returnLocation = Illuminate\Support\Facades\DB::table('locations')->where('id', $reservation->return_location)->select('name')->first();
                                                ?>
                                                <h6 class="fs-14 fw-semibold">{{$returnLocation->name ?? ''}}</h6>
                                            </div>
                                            <p class="fs-13 text-default">{{ formatDateTime($reservation->start_datetime, false)}}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <h6 class="fs-14 fw-semibold">{{$symbol}}{{ json_decode($reservation->vehicle_price)[0]->daily ?? 0 }}<span class="fw-normal text-default">/{{ __('admin.dashboard.day') }}</span></h6>
                                                <div class="avatar avatar-sm">
                                                    @php
                                                    $imagePath = $reservation->profile_image ?? "";
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'profile') }}" class="rounded-circle" alt="Profile">
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center no-datas">{{ __('admin.blog.no_data_found') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Customers -->
                @endif
            </div>
            <div class="row">
                @if (haspermission($permissions, 'customers', 'view'))
                <!-- Customers -->
                <div class="col-xl-4 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <h5>{{ __('admin.common.customers') }}</h5>
                                <a href="{{ route('admin.customers') }}" class="text-decoration-underline fw-medium">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table custom-table1">
                                    @foreach($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar flex-shrink-0">
                                                    @php
                                                    $imagePath = $user->profile_image ?? "";
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'profile') }}" class="rounded-circle" alt="Profile">
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="fs-14 fw-semibold mb-1"><a href="{{ route('admin.customer-details', $user->encrypted_id) }}" target="_blank">{{ucfirst($user->name)}}</a></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <?php
                                            $bookingCount = Illuminate\Support\Facades\DB::table('bookings')->where('customer_id', $user->id)->count();
                                            ?>
                                            <p class="fs-13 mb-1 text-default">{{ __('admin.dashboard.no_of_bookings') }}</p>
                                            <h6 class="fs-14 fw-semibold">{{$bookingCount}}</h6>
                                        </td>
                                    </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Customers -->
                @endif
                @if (haspermission($permissions, 'income_vs_expense', 'view'))
                <!-- Income & Expenses -->
                <div class="col-xl-8 d-flex">
                    <div class="card flex-fill earnings-chart">
                        <div class="card-header border-0 pb-0">
                            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
                                <h5>{{ __('admin.dashboard.income') }}</h5>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center me-4">
                                        <span class="chart-color bg-primary me-1"></span>
                                        <p class="fs-13">{{ __('admin.dashboard.income') }}</p>
                                    </div>
                                </div>
                                <div class="dropdown me-2">
                                    <a href="javascript:void(0);" class="dropdown-filter dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-calendar me-1"></i> {{ __('admin.dashboard.this_week') }}
                                    </a>
                                    <ul class="dropdown-menu  dropdown-menu-end p-2">
                                        <li>
                                            <a href="javascript:void(0);" class="dropdown-item dropdown-item-chat rounded-1">{{ __('admin.dashboard.this_week') }}</a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0);" class=" dropdown-item dropdown-item-chat rounded-1">{{ __('admin.dashboard.last_week') }}</a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="border rounded p-2 me-4 income-summary">
                                    <p class="mb-0 text-gray-5">{{ __('admin.dashboard.income_this_week') }}</p>
                                    <h5>{{$symbol}} <span class="text-success fs-13 fw-semibold">0%</span></h5>
                                </div>
                            </div>
                        </div>
                        <div class="card-body py-0">
                            <div id="income_expense_chart"></div>
                        </div>
                    </div>
                </div>
                <!-- /Income & Expenses -->
                @endif
            </div>
            <div class="row">
                @if (haspermission($permissions, 'maintenance', 'view'))
                <!-- Maintenance -->
                <div class="col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <h5>{{ __('admin.dashboard.maintenance') }}</h5>
                                <a href="{{ route('maintenance.index') }}" class="text-decoration-underline fw-medium">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table custom-table1">
                                    @if(count($maintenances) != 0)
                                    @foreach($maintenances as $maintenance)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <a href="javascript:void(0);" class="avatar flex-shrink-0">
                                                    @php
                                                        $imagePath = $maintenance->vehicle_image ?? "";
                                                        $filename = basename($imagePath);
                                                        $newpath = 'vehicles/images/small/' . $filename;
                                                        $file = public_path('storage/' . $newpath);
                                                        if (file_exists($file)) {
                                                            $imagePath = $newpath;
                                                        }
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'default') }}" class="admin-vehicle-image" alt="Vehicle">
                                                </a>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="fs-14 fw-semibold mb-1"><a href="javascript:void(0);">{{$maintenance->name}}</a></h6>
                                                    <p class="fs-13 text-default">{{$maintenance->model_name}}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            <p class="fs-13 mb-1 text-default">{{ __('admin.dashboard.odometer') }}</p>
                                            <h6 class="fs-14 fw-semibold">
                                                @if ($maintenance->odometer) 
                                                    {{$maintenance->odometer}} {{ __('admin.dashboard.km') }} 
                                                @else 
                                                -
                                                @endif
                                            </h6>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center no-datas">{{ __('admin.blog.no_data_found') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Maintenance -->
                @endif
                @if (haspermission($permissions, 'reservations', 'view') && isAccessMenu('reservation'))
                <!-- Reservation Statistics -->
                <div class="col-xl-4 d-none">
                    <div class="card flex-fill">
                        <div class="card-body pb-0">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3">
                                <h5 class="mb-1">{{ __('admin.dashboard.reservation_statistics') }}</h5>
                                <a href="{{ route('reservation.index') }}" class="text-decoration-underline fw-medium mb-1">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div id="statistics_chart"></div>
                        </div>
                    </div>
                </div>
                <!-- /Reservation Statistics -->
                @endif
                @if (haspermission($permissions, 'drivers', 'view'))
                <!-- Drivers -->
                <div class="col-xl-6 d-flex">
                    <div class="card flex-fill">
                        <div class="card-body pb-1">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-1">
                                <h5>{{ __('admin.dashboard.drivers') }}</h5>
                                <a href="{{ route('driver.index') }}" class="text-decoration-underline fw-medium">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div class="table-responsive">
                                <table class="table custom-table1">
                                    @if(count($drivers) != 0)
                                    @foreach($drivers as $driver)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar flex-shrink-0">
                                                    @php
                                                        $imagePath = $driver->image ?? "";
                                                    @endphp
                                                    <img src="{{ uploadedAsset($imagePath, 'profile') }}" class="rounded-circle" alt="Driver Profile">
                                                </div>
                                                <div class="flex-grow-1 ms-2">
                                                    <h6 class="fs-14 fw-semibold mb-1">{{$driver->driver_name}}</h6>
                                                    <p class="fs-13 text-default">{{ __('admin.dashboard.no_of_raids') }} : {{ $driver->total_bookings }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-end">
                                            @if($driver->currently_in_ride == 1)
                                            <span class="badge badge-md bg-success-transparent d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-6 me-2"></i> {{ __('admin.dashboard.in_ride') }}</span>
                                            @endif
                                            @if($driver->currently_in_ride != 1)
                                            <span class="badge badge-md bg-danger-transparent d-inline-flex align-items-center"><i class="ti ti-circle-filled fs-6 me-2"></i> {{ __('admin.dashboard.not_in_ride') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td colspan="5" class="text-center no-datas">{{ __('admin.blog.no_data_found') }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Drivers -->
                @endif
            </div>
            <div class="row">
                @if (haspermission($permissions, 'invoices', 'view'))
                <!-- Recent Invoices -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-1 mb-3">
                                <h5 class="mb-1">{{ __('admin.dashboard.recent_invoices') }}</h5>
                                <a href="{{ route('admin.invoice') }}" class="text-decoration-underline fw-medium mb-1">{{ __('admin.dashboard.view_all') }}</a>
                            </div>
                            <div class="custom-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('admin.dashboard.INVOICE_NO') }}</th>
                                            <th>{{ __('admin.dashboard.NAME') }}</th>
                                            <th>{{ __('admin.dashboard.EMAIL') }}</th>
                                            <th>{{ __('admin.dashboard.CREATED_DATE') }}</th>
                                            <th>{{ __('admin.dashboard.DUE_DATE') }}</th>
                                            <th>{{ __('admin.dashboard.INVOICE_AMOUNT') }}</th>
                                            <th>{{ __('admin.dashboard.STATUS') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($invoices) != 0)
                                        @foreach($invoices as $invoice)
                                        <tr>
                                            <td><div class="fs-12 fw-medium">#{{$invoice->invoice_number}}</div></td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar avatar-rounded me-2 flex-shrink-0">
                                                        @php
                                                        $imagePath = $invoice->profile_image ?? "";
                                                        @endphp
                                                        <img src="{{ uploadedAsset($imagePath, 'profile') }}" alt="Customer Profile">
                                                    </div>
                                                    <div>
                                                        <h6 class="fs-14 fw-semibold">{{$invoice->full_name}}</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{$invoice->email}}</td>
                                            <td>
                                                <div>
                                                   <p class="mb-0">{{ formatDateTime($invoice->created_at, false)}}</p>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <p class="mb-0">{{ formatDateTime($invoice->to_date, false) }}</p>
                                                </div>
                                            </td>
                                            <td>${{$invoice->grand_total}}</td>
                                            <td>
                                                @if($invoice->status == "Paid")
                                                <span class="badge badge-soft-success d-inline-flex align-items-center badge-sm">
                                                    <i class="ti ti-point-filled me-1"></i>
                                                    {{$invoice->status}}
                                                </span>
                                                @else
                                                <span class="badge badge-soft-danger d-inline-flex align-items-center badge-sm">
                                                    <i class="ti ti-point-filled me-1"></i>
                                                    {{$invoice->status}}
                                                </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        @else
                                        <tr>
                                            <td colspan="7" class="text-center">{{ __('admin.blog.no_data_found') }}</td>
                                        </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Recent Invoices -->
                @endif
            </div>
            <div id="chart-data"
                data-times='@json($times)'
                data-booking-date='@json($times)'
                data-series='@json($series)'
                data-dates='@json($dates)'
                data-categories='@json($formattedDates)'
                data-bookings='@json($chartbooking)'>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/dashboard.js') }}"></script>
@endpush
