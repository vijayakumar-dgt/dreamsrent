@extends('admin.admin')

@section('meta_title', __('admin.common.enquiries') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<x-admin.breadcrumb
                :title="__('admin.common.enquiries')"
                :breadcrumbs="[
                    __('admin.common.enquiries') => ''
                ]"
            />
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
	<x-admin.modal className="addmodal"
		id="edit_enquiry_modal"
		dialogClassName="modal-lg"
		:title="__('admin.bookings.edit_enquiry')"
		action="{{  route('enquire.update') }}"
		formId="editEnquiryForm"
		method="POST">
       <x-slot name="body">
			<div class="row">
				<div class="col-md-6">
					<div class="mb-3">
						<label for="assigned_cars" class="form-label">{{ __('admin.common.vehicle') }} </label>
						<p class="assigned_cars"></p>
						<span class="text-danger error-text" id="assigned_cars_error"></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="customer_name" class="form-label">{{ __('admin.common.name') }}</label>
						<p class="customer_name"></p>
						<span id="customer_name_error" class="text-danger error-text"></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="email" class="form-label">{{ __('admin.common.email') }}</label>
						<p class="email"></p>
						<span id="email_error" class="text-danger error-text"></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="phone_number" class="form-label">{{ __('admin.common.phone') }}</label>
						<p class="phone_number"></p>
						<span id="phone_number_error" class="text-danger error-text"></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="enquiry_details" class="form-label">{{ __('admin.bookings.enquiry') }}</label>
						<p class="enquiry_details"></p>
						<span id="enquiry_details_error" class="text-danger error-text"></span>
					</div>
				</div>
				<div class="col-md-6">
					<div class="mb-3">
						<label for="status" class="form-label">{{ __('admin.common.status') }}<span class="text-danger"> *</span></label>
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
					<label for="comment" class="form-label">{{ __('admin.common.comments') }}<span class="text-danger"> *</span></label>
					<input type="text" class="form-control" name="comment" id="comment">
					<span id="comment_error" class="text-danger error-text"></span>
				</div>
			</div>
	   </x-slot>
	   <x-slot name="footer">
			<div class="d-flex justify-content-center">
				<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
				<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.update') }}</button>
			</div>
	   </x-slot>
	</x-admin.modal>
	<!-- /Edit Enquiry -->

	<!-- Delete Enquiry -->
	<x-admin.delete-modal
		className="deletemodal"
		id="delete-modal"
		action="{{ route('enquiry.delete') }}"
		formId="enquiryDeleteForm"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.bookings.delete_enquiry')"
		:description="__('admin.bookings.delete_enquiry_confirmation')">
	</x-admin.delete-modal>
	<!-- /Delete Enquiry -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/enquiry.js') }}"></script>
@endpush
