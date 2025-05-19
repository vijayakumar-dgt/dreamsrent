@extends('admin.admin')

@section('meta_title',  __('admin.rentals.seat_types') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">		
			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">{{ __('admin.rentals.seat_types') }}</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('admin.rentals.seat_types') }}</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					<div class="mb-2">
						@if (hasPermission($permissions, 'vehicle_attributes', 'create'))
						<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#seat_type_modal" id="add_seat_type" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.rentals.add_new_seat_type') }}</a>
						@endif
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->
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
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1"></i> <span class="ms-1" id="current_sort_status">{{ __('admin.common.status') }}</span>
						</a>
						<ul class="dropdown-menu  dropdown-menu-end p-2" id="status_filter">
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1 statusfilter" data-status="1">{{ __('admin.common.active') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1 statusfilter" data-status="0">{{ __('admin.common.inactive') }}</a>
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
				<table class="table" id="seatTypeTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.common.name')) }}</th>
							<th>{{ strtoupper(__('admin.common.status')) }}</th>
							@if (hasPermission($permissions, 'vehicle_attributes', 'edit') || hasPermission($permissions, 'vehicle_attributes', 'delete'))
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

	<!-- Add Seat -->
	<div class="modal fade addmodal" id="seat_type_modal">
		<div class="modal-dialog modal-dialog-centered modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.common.create_seat_type') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="seatTypeForm">
					@csrf
					<input type="hidden" name="id" id="id">
					<div class="modal-body">                   
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.seat_type') }}<span class="text-danger"> *</span></label>
							<input type="text" class="form-control" name="seat_type" id="seat_type" maxlength="50">
							<span id="seat_type_error" class="text-danger error-text"></span>
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-between align-items-center w-100">
							<div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
								<label for="status" class="form-check-label form-label mt-0 mb-0">
								<input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked>
									{{ __('admin.common.status') }}
								</label>
							</div>
							<div class="d-flex justify-content-center">
								<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
								<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Add Seat -->

	<!-- Delete Seat -->
	<div class="modal fade deletemodal" id="delete-modal">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<form id="delateSeatType">
					@csrf
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.rentals.delete_seat_type') }}</h4>
						<p class="mb-3">{{ __('admin.rentals.delete_seat_type_confirmation') }}</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Delete Seat -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/seat-type.js') }}"></script>
@endpush