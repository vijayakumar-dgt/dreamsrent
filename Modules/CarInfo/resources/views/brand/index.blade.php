@extends('admin.admin')

@section('meta_title', __('admin.rentals.brands') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<x-admin.breadcrumb
				:title="__('admin.rentals.brands')"
				:breadcrumbs="[
						__('admin.rentals.brands') => ''
					]"
				:buttonText="__('admin.rentals.add_new_brand')"
				:modalId="'brand_modal'"
				:buttonId="'add_brand'"
				:permissionModule="'vehicle_attributes'" />
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
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<input type="hidden" id="sort_by_status">
					<div class="dropdown">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1"></i> <span class="ms-1" id="current_sort_status">{{ __('admin.common.status') }}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2" id="status_filter">
							<li>
								<button type="button" class="dropdown-item rounded-1" data-status="1">
									{{ __('admin.common.active') }}
								</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-status="0">
									{{ __('admin.common.inactive') }}
								</button>
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
			<div class="custom-datatable-filter table-responsive d-none real-table">
				<table class="table" id="brandTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.common.name')) }}</th>
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

	<!-- Add/Edit Brand -->
	<x-admin.modal 
		className="addmodal"
		id="brand_modal"
		:title="__('admin.rentals.create_brand')"
		action="{{  route('brand.store') }}"
		formId="brandForm"
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
					<span class="text-danger error-text" id="vehicle_category_id_error"></span>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">{{ __('admin.rentals.brand_image') }}<span class="text-danger"> *</span></label>
				<div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
					<div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark">
						<img src="{{ uploadedAsset('', 'default') }}" id="imagePreview" class="img-fluid d-none" alt="Brand Image">
						<i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
					</div>
					<div class="profile-upload">
						<div class="profile-uploader d-flex align-items-center">
							<div class="drag-upload-btn btn btn-md btn-dark">
								<i class="ti ti-photo-up fs-14"></i>
								{{ __('admin.common.upload') }}
								<input type="file" class="form-control image-sign" name="brand_image" id="brand_image">
							</div>
						</div>
						<div class="mt-2">
							<p class="fs-14">{{ __('admin.common.upload_image_size', ['size' => 2]) }}</p>
						</div>
					</div>
					<span class="text-danger error-text" id="brand_image_error"></span>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">{{ __('admin.rentals.brand_icon') }}<span class="text-danger"> *</span></label>
				<div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
					<div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark">
						<img src="{{ uploadedAsset('', 'default') }}" id="iconPreview" class="img-fluid d-none" alt="Brand Icon">
						<i class="ti ti-photo-up text-gray-4 fs-24 upload_icon_2"></i>
					</div>
					<div class="profile-upload">
						<div class="profile-uploader d-flex align-items-center">
							<div class="drag-upload-btn btn btn-md btn-dark">
								<i class="ti ti-photo-up fs-14"></i>
								{{ __('admin.common.upload') }}
								<input type="file" class="form-control image-sign" name="brand_icon" id="brand_icon">
							</div>
						</div>
						<div class="mt-2">
							<p class="fs-14">{{ __('admin.common.upload_brand_icon_size', ['size' => 2]) }}</p>
						</div>
					</div>
					<span class="text-danger error-text" id="brand_icon_error"></span>
				</div>
			</div>
			<div class="mb-3">
				<label class="form-label">{{ __('admin.rentals.brand_name') }}<span class="text-danger"> *</span></label>
				<input type="text" class="form-control" name="brand_name" id="brand_name">
				<span id="brand_name_error" class="text-danger error-text"></span>
			</div>
		</x-slot>
		<x-slot name="footer">
			<div class="d-flex justify-content-between align-items-center w-100">
				<div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
					<label for="status" class="form-check-label form-label mt-0 mb-0">
						<input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked>
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
	<!-- / Add/Edit Brand -->

	<!-- Delete Brand -->
	<x-admin.delete-modal
		className="deletemodal"
		id="delete-modal"
		action="{{ route('brand.delete') }}"
		formId="brandDeleteForm"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.rentals.delete_brand')"
		:description="__('admin.rentals.delete_brand_confirmation')">
	</x-admin.delete-modal>
	<!-- /Delete Brand -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/brand.js') }}"></script>
@endpush