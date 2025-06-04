@extends('admin.admin')

@section('meta_title', __('admin.common.state') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<x-admin.breadcrumb 
				:title="__('admin.cms.locations')" 
				:breadcrumbs="[
					__('admin.common.state') => ''
				]"
				:buttonText="__('admin.cms.add_state')"
				:modalId="'state_modal'"
				:buttonId="'add_state'"
				:permissionModule="'cms_locations'"
			/>
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<div class="top-search">
						<div class="top-search-group">
							<span class="input-icon">
								<i class="ti ti-search"></i>
							</span>
							<input type="text" class="form-control" id="search" placeholder="{{ __('admin.common.search') }}">
						</div>
					</div>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<div class="dropdown">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1"></i> {{ __('admin.common.status') }}
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2" id="statusFilter">
							<li>
								<button type="button" class="dropdown-item rounded-1 selectStatus" data-status="1">{{ __('admin.common.active') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1 selectStatus" data-status="0">{{ __('admin.common.inactive') }}</button>
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
				<table class="table" id="stateTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.cms.state_name')) }}</th>
							<th>{{ strtoupper(__('admin.cms.country_name')) }}</th>
							<th>{{ strtoupper(__('admin.common.status')) }}</th>
							@if (hasPermission($permissions, 'cms_locations', 'edit') || hasPermission($permissions, 'cms_locations', 'delete'))
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

	<!-- Add/Edit State Modal -->
	<x-admin.modal className="addmodal" id="state_modal" :title="__('admin.cms.create_state')" formId="stateForm"
		dialogClass="modal-dialog-centered modal-md">
		<x-slot name="body">
			@csrf
			<input type="hidden" name="id" id="id">

			<div class="mb-3">
				<label class="form-label">{{ __('admin.common.state') }}<span class="text-danger"> *</span></label>
				<input type="text" class="form-control" name="name" id="name" maxlength="50">
				<span id="name_error" class="text-danger error-text"></span>
			</div>

			<div class="mb-3">
				<label class="form-label">{{ __('admin.common.country') }}<span class="text-danger"> *</span></label>
				<select class="form-control select2" name="country_id" id="country_id">
					<option value="">{{ __('admin.common.select') }}</option>
					@foreach ($country_ids as $country)
						<option value="{{ $country->id }}">{{ $country->name }}</option>
					@endforeach
				</select>
				<span id="country_id_error" class="text-danger error-text"></span>
			</div>
		</x-slot>

		<x-slot name="footer">
			<div class="d-flex justify-content-between align-items-center w-100">
				<div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
					<label for="status" class="form-check-label form-label mt-0 mb-0">
						<input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch">
						{{ __('admin.common.status') }}
					</label>
				</div>
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">
						{{ __('admin.common.cancel') }}
					</a>
					<button type="submit" class="btn btn-primary submitbtn">
						{{ __('admin.common.create_new') }}
					</button>
				</div>
			</div>
		</x-slot>
	</x-admin.modal>
	<!-- /Add/Edit State Modal -->
	 
	<!-- Delete State Modal -->
	<x-admin.delete-modal className="deletemodal" id="delete-modal" action="" formId="delateState"
		:hiddenInputs="['delete_id' => '']" 
		:title="__('admin.cms.delete_state')" 
		:description="__('admin.cms.state_delete_confirmation')">
	</x-admin.delete-modal>
	<!-- /Delete State Modal -->

@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/state.js') }}"></script>
@endpush