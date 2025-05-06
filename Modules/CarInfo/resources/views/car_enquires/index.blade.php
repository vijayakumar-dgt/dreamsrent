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
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					<div class="mb-2 me-2 d-none">
						<a href="javascript:void(0);" class="btn btn-white d-flex align-items-center"><i class="ti ti-printer me-2"></i>{{ __('admin.common.print') }}</a>
					</div>
					<div class="me-2 mb-2 d-none">
						<div class="dropdown">
							<a href="javascript:void(0);" class="btn btn-dark d-inline-flex align-items-center">
								<i class="ti ti-upload me-1"></i>{{ __('admin.common.export') }}
							</a>
						</div>
					</div>
					<div class="mb-2 d-none">
						<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_enquiry_modal" id="add_enquiry" class="btn btn-primary d-flex align-items-center">
							<i class="ti ti-plus me-2"></i>{{ __('admin.bookings.add_new_enquiry') }}
						</a>
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<!-- Sort By Dropdown -->
					<div class="dropdown me-2 d-none real-label">
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
						</a>
						<ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-sort" data-value="latest">{{ __('admin.common.latest') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-sort" data-value="ascending">{{ __('admin.common.ascending') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-sort" data-value="descending">{{ __('admin.common.descending') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-sort" data-value="last_7_days">{{ __('admin.common.last_7_days') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-sort" data-value="last_month">{{ __('admin.common.last_month') }}</a></li>
						</ul>
					</div>
					<!-- Date Range Picker -->
					<div class="me-2 d-none">
						<div class="input-icon-start position-relative topdatepicker">
							<span class="input-icon-addon"><i class="ti ti-calendar"></i></span>
							<input type="text" class="form-control date-range enquirerange" name="sort_by_date" id="sort_by_date" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy">
						</div>
					</div>
					<!-- Status Dropdown -->
					<div class="dropdown d-none real-label">
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1 status"></i> {{ __('admin.common.status') }}: <span class="ms-1" id="current_status">{{ __('admin.common.all') }}</span>
						</a>
						<ul class="dropdown-menu dropdown-menu-end p-2">
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-status" data-value="">{{ __('admin.common.all') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-status" data-value="1">{{ __('admin.common.not_opened') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-status" data-value="2">{{ __('admin.common.opened') }}</a></li>
							<li><a href="javascript:void(0);" class="dropdown-item rounded-1 active-status" data-value="3">{{ __('admin.common.closed') }}</a></li>
						</ul>
					</div>
				</div>
				<!-- Search Box -->
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<div class="top-search d-none real-label">
						<div class="top-search-group">
							<span class="input-icon"><i class="ti ti-search"></i></span>
							<input type="text" class="form-control enquiresearch" placeholder="{{ __('admin.common.search') }}">
						</div>
					</div>
				</div>
			</div>
			<!-- /Table Header -->
			<div class="custom-datatable-filter table-responsive table-loader">
				<table class="table table-bordered">
					<thead class="thead-light">
						<tr>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
							<th>
								<div class="skeleton th-skeleton th-loader"></div>
							</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
						</tr>
						<tr>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
							<td>
								<div class="skeleton data-skeleton data-loader"></div>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<!-- Custom Data Table -->
			<div class="custom-datatable-filter table-responsive brandstable d-none real-table">
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

	<!-- Add Driver -->
	<div class="modal fade addmodal" id="add_enquiry_modal">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.bookings.create_enquiry') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="enquiryForm">
					@csrf
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Assigned Cars<span class="text-danger"> *</span></label>
									<select class="select select2" id="assigned_cars" name="assigned_cars[]" data-placeholder="Select" multiple>
										@if ($cars)
											@foreach ($cars as $car)
												<option value="{{ $car->id }}">{{ $car->name }}</option>
											@endforeach
										@endif
									</select>
									<span class="text-danger error-text" id="assigned_cars_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Customer Name<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="customer_name" id="customer_name">
									<span id="customer_name_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Email<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="email" id="email">
									<span id="email_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">Phone Number<span class="text-danger"> *</span></label>
									<input type="text" class="form-control driver_phone_number" name="phone_number" id="phone_number">
									<input type="hidden" id="international_phone_number" name="international_phone_number">
									<span id="phone_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">Enquiry Details<span class="text-danger"> *</span></label>
									<textarea class="form-control" name="enquiry_details" id="enquiry_details"></textarea>
									<span id="enquiry_details_error" class="text-danger error-text"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-primary submitbtn">Create New</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Add Driver -->

	<!-- Edit Driver -->
	<div class="modal fade addmodal" id="edit_enquiry_modal">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">Edit Enquiry</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="editEnquiryForm">
					@csrf
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
										<option value="">Select</option>
										<option value="1">Not Opened</option>
										<option value="2">Opened</option>
										<option value="3">Closed</option>
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
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.update') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Edit Driver -->

	<!-- Delete Driver -->
	<div class="modal fade deletemodal" id="delete-modal">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<form id="enquiryDeleteForm">
					@csrf
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.bookings.delete_enquiry') }}</h4>
						<p class="mb-3">{{ __('admin.bookings.delete_enquiry_confirmation') }}</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.delete') }}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Delete Brand -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/carinfo/enquiry.js') }}"></script>
@endpush
