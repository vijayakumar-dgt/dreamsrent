@extends('admin.admin')

@section('meta_title', __('admin.rentals.vehicle_types') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb
            :title="__('admin.rentals.vehicle_types')"
            :breadcrumbs="[
					__('admin.rentals.vehicle_types') => ''
				]"
            :buttonText="__('admin.rentals.add_new_vehicle_type')"
            :modalId="'add_type'"
            :buttonId="'add_new_type'"
            :permissionModule="'vehicle_attributes'" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="top-search me-2">
                <div class="top-search-group">
                    <span class="input-icon">
                        <i class="ti ti-search"></i>
                    </span>
                    <input type="text" class="form-control" name="search" id="search" placeholder="{{__('admin.common.search')}}">
                </div>
            </div>
            <div class="dropdown">
                <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                    <i class="ti ti-badge me-1"></i><span id="status_text"> {{__('admin.common.status')}} </span>
                </button>
                <ul class="dropdown-menu  dropdown-menu-end p-2">
                    <li>
                        <button type="button" class="dropdown-item rounded-1 status_filter" data-status="1">{{__('admin.common.active')}}</button>
                    </li>
                    <li>
                        <button type="button" class="dropdown-item rounded-1 status_filter" data-status="0">{{__('admin.common.inactive')}}</button>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /Table Header -->
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
            @include('admin.content-loader')
        </div>
        <div class="custom-datatable-filter d-none real-table">
            <table class="table" id="carTypeTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.common.name')) }}</th>
                        <th>{{ strtoupper(__('admin.common.icon')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        @if (hasPermission($permissions, 'vehicle_attributes', 'edit') || hasPermission($permissions, 'vehicle_attributes', 'delete'))
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

<!-- Add/Edit Type Start-->
<x-admin.modal className="addmodal"
    id="add_type"
    :title="__('admin.rentals.create_type')"
    action="{{  route('storetype') }}"
    formId="typeForm"
    method="POST"
    enctype="multipart/form-data">
    <x-slot name="body">
        <input type="hidden" name="id" id="id">
        <input type="hidden" name="language_id" id="language_id">
        <div class="col-md-12">
            <div class="mb-3">
                <label class="form-label">{{ __('admin.rentals.category') }} <span class="text-danger">*</span></label>
                <select name="vehicle_category_id" class="form-control select" id="vehicle_category_id">
                    <option value="">{{ __('admin.rentals.select') }}</option>
                    @foreach($category as $CategoryValues)
                    <option value="{{ $CategoryValues->id }}">{{ ucwords(strtolower($CategoryValues->name)) }}</option>
                    @endforeach
                </select>
                <span class="invalid-feedback" id="vehicle_category_id_error"></span>
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">{{ __('admin.common.name') }} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" id="name">
            <span id="name_error" class="text-danger error-text"></span>
        </div>
        <div class="row mb-3">
            <label for="icon" class="form-label">{{ __('admin.common.icon') }} <span class="text-danger icon_asterisk">*</span></label>
            <div class="col-md-4">
                <div class="d-flex align-items-center justify-content-center avatar avatar-xxxl border border-dashed me-2 flex-shrink-0 text-dark frames">
                    <img src="{{ uploadedAsset('', 'default') }}" id="icon_preview" class="img-contain rounded d-none" alt="Icon">
                    <i class="ti ti-photo-plus icon_placeholder"></i>
                </div>
            </div>
            <div class="col-md-8 d-flex align-items-center">
                <div class="profile-upload">
                    <div class="profile-uploader d-flex align-items-center">
                        <div class="drag-upload-btn btn btn-md btn-dark">
                            <i class="ti ti-photo-up fs-14"></i>
                            {{ __('admin.common.upload') }}
                            <input type="file" class="form-control image-sign" name="icon" id="icon">
                        </div>
                    </div>
                    <div class="mt-2">
                        <p class="fs-14">{{ __('admin.rentals.icon_dimension') }}</p>
                    </div>
                </div>
            </div>
            <span class="text-danger error-text" id="icon_error"></span>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-between align-items-center w-100" id="submit_div">
            <div class="form-check form-check-md form-switch me-2 d-none" id="status_div">
                <label class="form-check-label form-label mt-0 mb-0">
                    <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status" id="status">
                    {{ __('admin.common.status') }}
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
            </div>
        </div>
    </x-slot>
</x-admin.modal>
<!-- Add/Edit Type end -->

<!-- Delete Modal Start-->
<x-admin.delete-modal
    className="deletemodal"
    id="delete-modal"
    action="{{ route('deletetype') }}"
    formId="deleteType"
    method="POST"
    :hiddenInputs="['delete_id' => '']"
    :title="__('admin.rentals.delete_vehicle_type')"
    :description="__('admin.rentals.delete_vehicle_type_confirmation')">
</x-admin.delete-modal>
<!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/types.js') }}"></script>
@endpush