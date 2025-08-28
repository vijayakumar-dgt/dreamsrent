@extends('admin.admin')

@section('meta_title', __('admin.rentals.reviews') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb
                :title="__('admin.rentals.reviews')"
                :breadcrumbs="[
                    __('admin.rentals.reviews') => ''
                ]"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <input type="hidden" name="sort_by_input" id="sort_by_input">
                    <input type="hidden" name="sort_by_status" id="sort_by_status">
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
                            <input type="text" class="form-control date-range bookingrange" name="sort_by_date" id="sort_by_date" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy" autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table class="table" id="reviewsTable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                            <th>{{ strtoupper(__('admin.rentals.author')) }}</th>
                            <th>{{ strtoupper(__('admin.rentals.review_date')) }}</th>
                            <th>{{ strtoupper(__('admin.rentals.ratings')) }}</th>
                            <th>{{ strtoupper(__('admin.rentals.review')) }}</th>
                            @if (hasPermission($permissions, 'reviews', 'delete') || hasPermission($permissions, 'reviews', 'view'))
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
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Delete Review -->
    <div class="modal fade" id="delete_review">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="reviewDeleteForm">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.rentals.delete_review') }}</h4>
                        <p class="mb-3">{{ __('admin.rentals.delete_review_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary" data-bs-dismiss="modal">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete Review -->

    <!-- View Review -->
    <div class="modal fade" id="view_review">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">{{ __('admin.rentals.view_review') }}</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body pb-1">
                    <p class="mb-3" id="review_text"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- /View Review -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/reviews.js') }}"></script>
@endpush
