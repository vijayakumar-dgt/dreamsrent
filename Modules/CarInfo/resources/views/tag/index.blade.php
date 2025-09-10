@extends('admin.admin')

@section('meta_title', __('admin.common.tags') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb :title="__('admin.common.tags')" :breadcrumbs="[
                    __('admin.common.tags') => ''
                ]" :buttonText="__('admin.rentals.add_new_tag')" :modalId="'add_tag'" :buttonId="'add_new_tag'"
            :permissionModule="'vehicle_attributes'" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="top-search">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}"
                            id="keyword" name="keyword">
                    </div>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="dropdown">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                        data-bs-toggle="dropdown">
                        <i class="ti ti-badge me-1"></i> <span id="status_text">{{ __('admin.common.status') }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li>
                            <button type="button" class="dropdown-item rounded-1 statusfilter"
                                data-status="1">{{ __('admin.common.active') }}</button>
                        </li>
                        <li>
                            <button type="button" class="dropdown-item rounded-1 statusfilter"
                                data-status="0">{{ __('admin.common.inactive') }}</button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- /Table Header -->
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
            @include('admin.content-loader')
        </div>
        <div class="d-none real-table">
            <div class="custom-datatable-filter table-responsive brandstable">
                <table class="table" id="tagTable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.common.name')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                            @if (hasPermission($permissions, 'vehicle_attributes', 'edit') ||
                            hasPermission($permissions, 'vehicle_attributes', 'delete'))
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
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Add/Edit Tag Start-->
<x-admin.modal className="addmodal" id="add_tag" :title="__('admin.rentals.create_tag')"
    action="{{  route('store_tag') }}" formId="tagForm" method="POST">
    <x-slot name="body">
        <input type="hidden" name="id" id="id">
        <div class="mb-3">
            <label for="tag" class="form-label">{{ __('admin.common.tag') }}<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="tag" id="tag">
            <span id="tag_error" class="text-danger error-text"></span>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="form-check form-check-md form-switch me-2 d-none" id="status_div">
                <label class="form-check-label form-label mt-0 mb-0">
                    <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status"
                        id="status" aria-checked="false">
                    {{ __('admin.common.status') }}
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
            </div>
        </div>
    </x-slot>
</x-admin.modal>
<!-- Add/Edit Tag end -->

<!-- Delete Modal Start-->
<x-admin.delete-modal className="deletemodal" id="delete-modal" action="{{ route('delete_tag') }}"
    formId="deleteTagForm" method="POST" :hiddenInputs="['delete_id' => '']" :title="__('admin.rentals.delete_tag')"
    :description="__('admin.rentals.delete_tag_confirmation')">
</x-admin.delete-modal>
<!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/tag.js') }}"></script>
@endpush
