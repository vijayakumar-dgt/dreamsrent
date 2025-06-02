@extends('admin.admin')

@section('meta_title', __('admin.common.vehicles') . ' || ' . $companyName)

@section('content')
<div class="page-wrapper">
    <div class="content me-4">
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h4 class="mb-1">{{__('admin.rentals.all_vehicle')}}</h4>
                <nav class="l">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.rentals.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.rentals.all_vehicle') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    @if (hasPermission($permissions, 'vehicles', 'create'))
                    <a href="{{ route('vehicle.vehicleadd') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.rentals.add_new_vehicle') }}</a>
                    @endif
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.page.sort_by') }}: <span id="sortLabel">{{ __('admin.page.latest') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="sortFilter">
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="latest">
                                {{ __('admin.page.latest') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="asc">
                                {{ __('admin.page.ascending') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="desc">
                                {{ __('admin.page.descending') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="last_month">
                                {{ __('admin.page.last_month') }}
                            </button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 sort-option" data-sort="last_7_days">
                                {{ __('admin.page.last_7_days') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="me-2">
                    <div class="input-icon-start position-relative topdatepicker">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <input type="text" name="sort_by_date" id="sort_by_date" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.rentals.filter') }}
                    </a>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-edit-circle me-1"></i> {{ __('admin.rentals.bulk_actions') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li>
                            <button type="button" class="dropdown-item rounded-1" id="deleteSelectedVehicles">
                                {{ __('admin.rentals.delete') }}
                            </button>
                        </li>
                    </ul>
                </div>
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('admin.rentals.search') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{ __('admin.rentals.filter') }}</h6>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.select_cars') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                            @foreach ($vechileName as $value)
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_id" name="vehicle_id" value="{{ $value->id }}">{{ $value->name }}
                                </label>
                            </li>
                            @endforeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.type') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                            @foreach ($vechileType as $value)
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_type_id" name="vehicle_type_id" value="{{ $value->id }}">{{ $value->name }}
                                </label>
                            </li>
                            @endforeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-3">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.location') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                            @foreach ($vechileLocation as $value)
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_location_id" name="vehicle_location_id" value="{{ $value->id }}">{{ $value->name }}
                                </label>
                            </li>
                            @endforeach
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-3">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.status') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-md p-2 statusFilter">
                        <li class="dropdown-item">
                            {{ __('admin.rentals.active') }}
                        </li>
                        <li class="dropdown-item">
                            {{ __('admin.rentals.inactive') }}
                        </li>
                    </ul>
                </div>
                <button type="button" class="border-0 bg-transparent text-purple links" id="applyFilter">{{ __('admin.rentals.apply') }}</button>
                <button type="button" class="text-danger border-0 bg-transparent links" id="clearFilter">{{ __('admin.rentals.clear_all') }}</button>
            </div>
        </div>
        <div class="custom-datatable-filter table-responsive brandstable">
            <table class="table datatable  d-none real-data" id="vehicleListIndex">
                <thead class="thead-light">
                    <tr>
                        <th class="no-sort">
                            <div class="form-check form-check-md">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.base_location')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.price')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.damages')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.is_featured')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.is_recommended')) }}</th>
                        <th>{{ strtoupper(__('admin.rentals.created_date')) }}</th>
                        @if (hasPermission($permissions, 'vehicles', 'edit') || hasPermission($permissions, 'vehicles', 'delete'))
                        <th>{{ strtoupper(__('admin.rentals.status')) }}</th>
                        @endif
                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div id="loader-table" class="position-relative vh-10">
                @include('admin.content-loader')
            </div>
        </div>
        <div class="table-footer d-none"></div>
    </div>
    @include('admin.partials.footer')
</div>
<div class="modal fade deletemodal" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="deleteVehicle">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.common.delete_vehicle') }}</h4>
                    <p class="mb-3">{{ __('admin.common.delete_vehicle_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.yes_delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="status-modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="statusVehicleForm">
                @csrf
                <input type="hidden" name="status_vehicle_id" id="status_vehicle_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-primary rounded-circle text-primary mb-3">
                        <i class="ti ti-refresh fs-26"></i>
                    </span>

                    <p class="mb-3">{{ __("admin.common.update_status_vehicle")}}</p>

                    <select name="vehicle_status" id="vehicle_status" class="form-select select mb-4">
                        <option value="1">{{ __("admin.rentals.active")}}</option>
                        <option value="0">{{__("admin.rentals.inactive")}}</option>
                    </select>

                    <div class="d-flex justify-content-center mt-4">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.common.update_status') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicle.js') }}"></script>
@endpush