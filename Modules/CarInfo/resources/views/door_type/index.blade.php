@extends('admin.admin')

@section('meta_title', __('admin.rentals.door_types') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<x-admin.breadcrumb
				:title="__('admin.rentals.door_types')"
				:breadcrumbs="[
					__('admin.rentals.door_types') => ''
				]"
				:buttonText="__('admin.rentals.add_new_door_type')"
				:modalId="'door_type_modal'"
				:buttonId="'add_door_type'"
				:permissionModule="'vehicle_attributes'"
			/>
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<div class="top-search">
						<div class="top-search-group">
							<span class="input-icon">
								<i class="ti ti-search"></i>
							</span>
							<input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
						</div>
					</div>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3 ">
					<input type="hidden" id="sort_by_status">
					<div class="dropdown">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1"></i> <span class="ms-1" id="current_sort_status">{{ __('admin.common.status') }}</span>
						</button>
						<ul class="dropdown-menu  dropdown-menu-end p-2" id="status_filter">
							<li>
								<button type="button" class="dropdown-item rounded-1" data-status="1">{{ __('admin.common.active') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-status="0">{{ __('admin.common.inactive') }}</button>
							</li>
						</ul>
					</div>
				</div>
			</div>
			<!-- /Table Header -->
			<div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
				@include('admin.content-loader')
			</div>
			<!-- Custom Data Table -->
			<div class="custom-datatable-filter table-responsive brandstable d-none real-table">
				<table class="table" id="doorTypeTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.rentals.door_type')) }}</th>
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

	<!-- Add/Edit Door Type -->
	<x-admin.modal className="addmodal"
		id="door_type_modal"
		:title="__('admin.rentals.create_door_type')"
		action="{{  route('doorType.store') }}"
		formId="doorTypeForm"
		method="POST">
		<x-slot name="body">
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
				<label for="door_type" class="form-label">{{ __('admin.rentals.door_type') }}<span class="text-danger"> *</span></label>
				<input type="text" class="form-control" name="door_type" id="door_type">
				<span id="door_type_error" class="text-danger error-text"></span>
			</div>
		</x-slot>
		<x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
				<div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
					<label for="status" class="form-check-label form-label mt-0 mb-0">
					<input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked aria-checked="true">
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
	<!-- /Add/Edit Door Type -->

	<!-- Delete Door Type -->
	<x-admin.delete-modal
		className="deletemodal"
		id="delete-modal"
		action="{{ route('doorType.delete') }}"
		formId="deleteDoorType"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.rentals.delete_door_type')"
		:description="__('admin.rentals.delete_door_type_confirmation')">
	</x-admin.delete-modal>
	<!-- /Delete Door Type -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/door-type.js') }}"></script>
@endpush
