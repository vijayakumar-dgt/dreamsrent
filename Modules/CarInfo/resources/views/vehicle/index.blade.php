@extends('admin.admin')
@section('content')

<div class="page-wrapper">
    <div class="content me-4">

        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <div class="skeleton label-skeleton label-loader"></div>
                <h4 class="mb-1 d-none real-label">{{__('admin.rentals.all_vehicle')}}</h4>
                <div class="skeleton label-skeleton label-loader w-50"></div>
                <nav class=" d-none real-label">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="/admin">{{ __('admin.rentals.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.rentals.all_vehicle') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    @if (hasPermission($permissions, 'vehicles', 'create'))
                    <div class="skeleton label-skeleton label-loader"></div>
                    <a href="{{ route('vehicle.vehicleadd') }}" class="btn btn-primary d-flex align-items-center d-none real-label"><i class="ti ti-plus me-2"></i>{{ __('admin.rentals.add_new_vehicle') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="skeleton label-skeleton label-loader me-2"></div>
                <div class="dropdown me-2 d-none real-label">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.page.sort_by') }}: <span id="sortLabel">{{ __('admin.page.latest') }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2" id="sortFilter">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'latest')">
                                {{ __('admin.page.latest') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'asc')">
                                {{ __('admin.page.ascending') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'desc')">
                                {{ __('admin.page.descending') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'last_month')">
                                {{ __('admin.page.last_month') }}
                            </a>
                        </li>
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" onclick="filterSort(this, 'last_7_days')">
                                {{ __('admin.page.last_7_days') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="me-2">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="input-icon-start position-relative topdatepicker  d-none real-label">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <input type="text" name="sort_by_date" id="sort_by_date" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                    </div>
                </div>
                <div class="dropdown">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center  d-none real-label" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.rentals.filter') }}
                    </a>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center  d-none real-label" data-bs-toggle="dropdown">
                        <i class="ti ti-edit-circle me-1"></i> {{ __('admin.rentals.bulk_actions') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li>
                            <a href="javascript:void(0);" class="dropdown-item rounded-1" id="deleteSelectedVehicles">
                                {{ __('admin.rentals.delete') }}
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="top-search me-2">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="top-search-group  d-none real-label">
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
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.select_cars') }}
                    </a>
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
                        @foreach ($vechileName as $value)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_id" name="vehicle_id" value="{{ $value->id }}">{{ $value->name }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.type') }}
                    </a>
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
                        @foreach ($vechileType as $value)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_type_id" name="vehicle_type_id" value="{{ $value->id }}">{{ $value->name }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown me-3">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.location') }}
                    </a>
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
                        @foreach ($vechileLocation as $value)
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" id="vehicle_location_id" name="vehicle_location_id" value="{{ $value->id }}">{{ $value->name }}
                            </label>
                        </li>
                        @endforeach
                    </ul>
                </div>
                <div class="dropdown me-3">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.rentals.status') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-md p-2 statusFilter">
                        <li class="dropdown-item">
                            {{ __('admin.rentals.active') }}
                        </li>
                        <li class="dropdown-item">
                            {{ __('admin.rentals.inactive') }}
                        </li>
                    </ul>
                </div>
                <a href="javascript:void(0);" class="me-2 text-purple links" id="applyFilter">{{ __('admin.rentals.apply') }}</a>
                <a href="javascript:void(0);" class="text-danger links" id="clearFilter">{{ __('admin.rentals.clear_all') }}</a>
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
                        <th>{{ __('admin.rentals.car') }}</th>
                        <th>{{ __('admin.rentals.base_location') }}</th>
                        <th>{{ __('admin.rentals.price') }}</th>
                        <th>{{ __('admin.rentals.damages') }}</th>
                        <th>{{ __('admin.rentals.is_featured') }}</th>
                        <th>{{ __('admin.rentals.created_date') }}</th>
                        @if (hasPermission($permissions, 'vehicles', 'edit') || hasPermission($permissions, 'vehicles', 'delete'))

                        <th>{{ __('admin.rentals.status') }}</th>

                        @endif
                        <th></th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>

            <table id="loader-table" class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>
                            <div class="skeleton label-skeleton label-loader"></div>
                            <p class="d-none real-data">ID</p>
                        </th>
                        <th>
                            <div class="skeleton label-skeleton label-loader"></div>
                            <p class="d-none real-data">Name</p>
                        </th>
                        <th>
                            <div class="skeleton label-skeleton label-loader"></div>
                            <p class="d-none real-data">Email</p>
                        </th>
                        <th>
                            <div class="skeleton label-skeleton label-loader"></div>
                            <p class="d-none real-data">Role</p>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">1</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">John Doe</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">johndoe@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Admin</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">2</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Jane Smith</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">janesmith@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Manager</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">2</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Jane Smith</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">janesmith@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Manager</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">2</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Jane Smith</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">janesmith@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Manager</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">2</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Jane Smith</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">janesmith@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Manager</p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">2</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Jane Smith</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">janesmith@example.com</p>
                        </td>
                        <td>
                            <div class="skeleton data-skeleton data-loader"></div>
                            <p class="d-none real-data">Manager</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="table-footer  d-none real-label"></div>
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
                    <h4 class="mb-1">Delete Type</h4>
                    <p class="mb-3">Are you sure you want to delete type?</p>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
                        <button type="submit" class="btn btn-primary submitbtn">Yes, Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/add-car.js') }}"></script>
@endpush