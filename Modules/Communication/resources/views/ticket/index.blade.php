@extends('admin.admin')

@section('meta_title', __('admin.support.tickets') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb
            :title="__('admin.support.tickets')"
            :breadcrumbs="[
                    __('admin.support.tickets') => ''
                ]" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
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
                            <button type="button" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</b>
                        </li>
                    </ul>
                </div>
                <div class="dropdown">
                    <button type="button"
                        class="filtercollapse coloumn d-inline-flex align-items-center"
                        data-bs-toggle="collapse"
                        data-bs-target="#filtercollapse"
                        aria-expanded="false"
                        aria-controls="filtercollapse">
                        <i class="ti ti-filter me-1"></i> {{ __('admin.common.filters') }}
                        <span class="badge badge-xs rounded-pill bg-danger ms-2">0</span>
                    </button>
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
        <div class="collapse" id="filtercollapse">
            <div class="filterbox mb-3 d-flex align-items-center">
                <h6 class="me-3">{{ __('admin.common.filters') }}</h6>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.support.priority') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control filter-search" data-filter="priority" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="priority[]" value="Low">{{ __('admin.common.low') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="priority[]" value="Medium">{{ __('admin.common.medium') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="priority[]" value="High">{{ __('admin.common.high') }}
                            </label>
                        </li>
                    </ul>
                </div>
                <div class="dropdown me-2">
                    <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.common.status') }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-lg p-2">
                        <li>
                            <div class="top-search m-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control filter-search" data-filter="status" placeholder="{{ __('admin.common.search') }}">
                                </div>
                            </div>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="status[]" value="1">{{ __('admin.general_settings.open') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="status[]" value="2">{{ __('admin.support.assigned') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="status[]" value="3">{{ __('admin.rentals.inprogress') }}
                            </label>
                        </li>
                        <li>
                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                <input class="form-check-input m-0 me-2" type="checkbox" name="status[]" value="4">{{ __('admin.support.closed') }}
                            </label>
                        </li>
                    </ul>
                </div>
                <a href="javascript:void(0);" class="me-2 text-purple links">{{ __('admin.common.apply') }}</a>
                <a href="javascript:void(0);" class="text-danger links">{{ __('admin.common.clear_all') }}</a>
            </div>
        </div>
        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
            @include('admin.content-loader')
        </div>
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
            <table id="adminTicketTable" class="table">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.support.tickets_code')) }}</th>
                        <th>{{ strtoupper(__('admin.common.created_by')) }}</th>
                        <th>{{ strtoupper(__('admin.common.subject')) }}</th>
                        <th>{{ strtoupper(__('admin.cms.created_date')) }}</th>
                        <th>{{ strtoupper(__('admin.support.priority')) }}</th>
                        <th>{{ strtoupper(__('admin.support.assignee')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        @if (hasPermission($permissions, 'tickets', 'edit') || hasPermission($permissions, 'tickets', 'delete') || hasPermission($permissions, 'tickets', 'view'))
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
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Edit Ticket Status -->
<x-admin.modal className="editmodal" id="edit_ticket" :title="__('admin.support.update_ticket')"
    formId="editTicketstatus" dialogClass="modal-dialog-centered modal-md">
    <x-slot name="body">
        <input type="hidden" name="ticketid" id="ticketid">
        <div class="mb-3">
            <label class="form-label" for="assignStaff">
                {{ __('admin.support.assign_staff') }} <span class="text-danger">*</span>
            </label>
            <select class="select form-control" id="assignStaff" name="assign_staff"
                data-placeholder="{{ __('admin.common.select') }}">
                <option value="">{{ __('admin.common.select') }}</option>
                @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                @endforeach
            </select>
            <span class="text-danger error-message" id="assignStaffError"></span>
        </div>
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                {{ __('admin.common.cancel') }}
            </button>
            <button type="submit" class="btn btn-primary submitbtn">
                {{ __('admin.common.update') }}
            </button>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Edit Ticket Status -->

<!-- Ticket History -->
<x-admin.modal className="historymodal" id="histroy_ticket" title="Histroy Ticket"
    formId="editTicketstatus" dialogClass="modal-dialog-centered modal-lg">
    <x-slot name="body">
        <div class="histroy-ticket pb-1"></div>
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary d-none">Update</button>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Ticket History -->
<!-- Delete Ticket -->
<x-admin.delete-modal :config="[
    'className'    => 'deletemodal',
    'id'           => 'delete_ticket',
    'formId'       => 'delete_ticket_form',
    'hiddenInputs' => ['delete_id' => ''],
    'title'        => __('admin.support.delete_ticket'),
    'description'  => __('admin.support.delete_tickets_description')
]"/>
<!-- /Delete Ticket -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/communication/adminticket.js') }}"></script>
@endpush
