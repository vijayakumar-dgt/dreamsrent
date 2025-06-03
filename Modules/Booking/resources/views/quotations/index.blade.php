@extends('admin.admin')

@section('meta_title', __('admin.common.quotations') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h4 class="mb-1">{{ __('admin.common.quotations') }}</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.quotations') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
                <div class="mb-2">
                    @if (hasPermission($permissions, 'quotations', 'create'))
                    <a href="{{ route('quotations.create') }}" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.bookings.add_new_quotation') }}</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <input type="hidden" id="sort_by_input">
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
                        <li>
                            <button type="button" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</button>
                        </li>
                    </ul>
                </div>
                <div class="me-2">
                    <div class="input-icon-start position-relative topdatepicker">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                        <input type="text" class="form-control date-range bookingrange" name="sort_by_date" id="sort_by_date" placeholder="dd/mm/yyyy - dd/mm/yyyy">
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
                    </a>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="top-search">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" name="overall_search" id="overall_search" placeholder="{{ __('admin.common.search') }}">
                    </div>
                </div>
            </div>
        </div>
        <!-- /Table Header -->
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{ __('admin.common.filter') }}</h6>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.bookings.pickup_location') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2" id="pickUpLocationList">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" name="pickup_location_search" id="pickup_location_search" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.bookings.drop_off_location') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2" id="dropOffLocationList">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" id="drop_off_location_search" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <div class="custom-scroll">
                        </div>
                    </ul>
                </div>
                <div class="dropdown me-3">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        <i class="ti ti-badge me-1"></i>{{ __('admin.common.status') }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-lg p-2" id="statusList">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" name="status_search" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="1">{{ __('admin.common.in_progress') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="2">{{ __('admin.common.confirmed') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="3">{{ __('admin.common.rejected') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="4">{{ __('admin.common.booked') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="5">{{ __('admin.common.completed') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="6">{{ __('admin.common.cancelled') }}
                            </label>
                        </li>
                    </ul>
                </div>
                <button type="button" class="text-purple links border-0 bg-transparent" id="apply_filter">{{ __('admin.common.apply') }}</a>
                    <button type="button" class="text-danger links border-0 bg-transparent" id="reset_filter">{{ __('admin.common.clear_all') }}</a>
            </div>
        </div>
        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
            @include('admin.content-loader')
        </div>
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
            <table class="table" id="reservationTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                        <th>{{ strtoupper(__('admin.common.customer')) }}</th>
                        <th>{{ strtoupper(__('admin.bookings.pickup_details')) }}</th>
                        <th>{{ strtoupper(__('admin.bookings.drop_off_details')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        @if (hasPermission($permissions, 'quotations', 'edit') || hasPermission($permissions, 'quotations', 'delete') || hasPermission($permissions, 'quotations', 'view'))
                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
        <!-- Custom Data Table -->
        <div class="table-footer d-none"></div>
    </div>
</div>
<!-- /Page Wrapper -->

<!-- Delete Modal  -->
<x-admin.modal
    id="delete_modal"
    className="deletemodal"
    size="sm"
    formId="quotation_delete_form"
    method="POST"
    :hasForm="true"
    :hasHeader="false"
    :hasFooter="false">
    <x-slot name="body">
        <input type="hidden" name="delete_id" id="delete_id">
        <div class="text-center">
            <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                <i class="ti ti-trash-x fs-26"></i>
            </span>
            <h4 class="mb-1">{{ __('admin.bookings.delete_quotation') }}</h4>
            <p class="mb-3">{{ __('admin.bookings.delete_quotation_confirmation') }}</p>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                    {{ __('admin.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary">
                    {{ __('admin.common.yes_delete') }}
                </button>
            </div>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Delete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/quotations/quotations.js') }}"></script>
@endpush