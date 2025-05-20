@extends('admin.admin')

@section('meta_title', __('admin.common.enquiries') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">{{ __('admin.common.enquiries') }}</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.enquiries') }}</li>
						</ol>
					</nav>
				</div>
			</div>
			<!-- /Breadcrumb -->
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<!-- Sort By Dropdown -->
					<div class="dropdown me-2">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
							<li><button type="button" class="dropdown-item rounded-1 active-sort" data-value="latest">{{ __('admin.common.latest') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-sort" data-value="ascending">{{ __('admin.common.ascending') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-sort" data-value="descending">{{ __('admin.common.descending') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-sort" data-value="last_7_days">{{ __('admin.common.last_7_days') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-sort" data-value="last_month">{{ __('admin.common.last_month') }}</button></li>
						</ul>
					</div>					
					<!-- Status Dropdown -->
					<div class="dropdown">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1 status"></i> {{ __('admin.common.status') }}: <span class="ms-1" id="current_status">{{ __('admin.common.all') }}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2">
							<li><button type="button" class="dropdown-item rounded-1 active-status" data-value="">{{ __('admin.common.all') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-status" data-value="1">{{ __('admin.common.not_opened') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-status" data-value="2">{{ __('admin.common.opened') }}</button></li>
							<li><button type="button" class="dropdown-item rounded-1 active-status" data-value="3">{{ __('admin.common.closed') }}</button></li>
						</ul>
					</div>
				</div>
				<!-- Search Box -->
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<div class="top-search">
						<div class="top-search-group">
							<span class="input-icon"><i class="ti ti-search"></i></span>
							<input type="text" class="form-control enquiresearch" placeholder="{{ __('admin.common.search') }}">
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
				<table class="table" id="enquiryTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
							<th>{{ strtoupper(__('admin.common.name')) }}</th>
							<th>{{ strtoupper(__('admin.common.email')) }}</th>
							<th>{{ strtoupper(__('admin.common.phone')) }}</th>
							<th>{{ strtoupper(__('admin.common.date')) }}</th>
							<th>{{ strtoupper(__('admin.bookings.enquiry')) }}</th>
							<th>{{ strtoupper(__('admin.common.status')) }}</th>
							@if (hasPermission($permissions, 'enquiries', 'edit') || hasPermission($permissions, 'enquiries', 'delete'))
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

	<!-- Edit Enquiry -->
	<div class="modal fade addmodal" id="edit_enquiry_modal">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.bookings.edit_enquiry') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="editEnquiryForm">
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.vehicle') }}   <span class="text-danger"> *</span></label>
									<p class="assigned_cars"></p>
									<span class="text-danger error-text" id="assigned_cars_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.name') }}<span class="text-danger"> *</span></label>
									<p class="customer_name"></p>
									<span id="customer_name_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
									<p class="email"></p>
									<span id="email_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.phone') }}<span class="text-danger"> *</span></label>
									<p class="phone_number"></p>
									<span id="phone_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.bookings.enquiry') }}<span class="text-danger"> *</span></label>
									<p class="enquiry_details"></p>
									<span id="enquiry_details_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.status') }}<span class="text-danger"> *</span></label>
									<select id="status" name="status" class="select form-control">
										<option value="">{{ __('admin.common.select') }}</option>
										<option value="1">{{ __('admin.common.not_opened') }}</option>
										<option value="2">{{ __('admin.common.opened') }}</option>
										<option value="3">{{ __('admin.common.closed') }}</option>
									</select>
									<span id="status_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="mb-3">
								<input type="hidden"  class="form-control id" name="id" id="id">
								<label class="form-label">{{ __('admin.common.comments') }}<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="comment" id="comment">
								<span id="comment_error" class="text-danger error-text"></span>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-center">
							<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
							<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.update') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Edit Enquiry -->
	
	<!-- Delete Enquiry -->
	<div class="modal fade deletemodal" id="delete-modal">
		<div class="modal-dialog modal-dialog-centered modal-sm"> 
			<div class="modal-content">
				<form id="enquiryDeleteForm"> 
					<input type="hidden" name="_token" value="{{ csrf_token() }}">
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.bookings.delete_enquiry') }}</h4>
						<p class="mb-3">{{ __('admin.bookings.delete_enquiry_confirmation') }}</p>
						<div class="d-flex justify-content-center"> 
							<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
						</div> 
					</div>
				</form>
			</div> 
		</div> 
	</div> 
	<!-- /Delete Enquiry -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/enquiry.js') }}"></script>
@endpush
