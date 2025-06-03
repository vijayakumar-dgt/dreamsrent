@extends('admin.admin')

@section('meta_title', __('admin.common.reservations') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb 
                :title="__('admin.bookings.all_reservations')" 
                :breadcrumbs="[
                    __('admin.bookings.all_reservations') => ''
                ]"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <input type="hidden" id="sort_by_input">
                    <div class="dropdown me-2 ">
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
                    <button type="button" class="text-purple links border-0 bg-transparent" id="apply_filter">{{ __('admin.common.apply') }}</button>
                    <button type="button" class="text-danger links border-0 bg-transparent" id="reset_filter">{{ __('admin.common.clear_all') }}</button>
                </div>
            </div>
            <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
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
                            @if (hasPermission($permissions, 'reservations', 'edit') || hasPermission($permissions, 'reservations', 'delete') || hasPermission($permissions, 'reservations', 'view'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none"></div>
        </div>
    </div>
    <!-- /Page Wrapper -->

    <!-- Delete Modal  -->
    <x-admin.delete-modal
        className="deletemodal"
        id="delete_modal"
        action="{{ route('reservation.delete') }}"
        formId="reservation_delete_form"
        method="POST"
        :hiddenInputs="['delete_id' => '']"
        :title="__('admin.bookings.delete_reservation')"
        :description="__('admin.bookings.delete_reservation_confirmation')">
    </x-admin.delete-modal>
    <!-- /Delete Modal-->

    <!-- Complete Modal  -->
    <x-admin.delete-modal
        className="deletemodal"
        id="complete_modal"
        action="{{ route('reservation.complete') }}"
        formId="reservation_complete_form"
        method="POST"
        :hiddenInputs="['compelete_id' => '']"
        :title="__('admin.bookings.complete_booking')"
        deleteBtnText="{{ __('admin.common.yes_complete') }}"
        modalIconClass="ti ti-circle-check fs-26"
        :description="__('admin.bookings.complete_reservation_confirmation')">
    </x-admin.delete-modal>
    <!-- /Complete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/booking/reservation.js') }}"></script>
@endpush