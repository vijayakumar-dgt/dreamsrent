@extends('admin.admin')

@section('meta_title', __('admin.rentals.maintenance') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">{{ __('admin.rentals.maintenance') }}</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('admin.rentals.maintenance') }}</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					
					<div class="mb-2">
						@if (hasPermission($permissions, 'maintenance', 'create'))
						<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#maintenance_modal" id="add_maintenance" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.rentals.add_new_maintenance') }}</a>
					@endif
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->

			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<input type="hidden" id="sort_by_input">
					<div class="dropdown me-2">
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
						</a>
						<ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</a>
							</li>
						</ul>
					</div>
					<div class="me-2">
						<div class="input-icon-start position-relative topdatepicker">
							<span class="input-icon-addon">
								<i class="ti ti-calendar"></i>
							</span>
							<input type="text" class="form-control date-range bookingrange" name="sort_by_date" id="sort_by_date" placeholder="dd/mm/yyyy - dd/mm/yyyy">
						</div>
					</div>                    
					<div class="dropdown">
						<a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
						</a>
					</div>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<div class="top-search">
						<div class="top-search-group">
							<span class="input-icon">
								<i class="ti ti-search"></i>
							</span>
							<input type="text" class="form-control" name="overall_search" id="overall_search" placeholder="{{ __('admin.common.search') }}">
						</div>
					</div>
				</div>
			</div>
			<!-- /Table Header -->

			<div class="collapse" id="filtercollapse">
				<div class="filterbox mb-3 d-flex align-items-center">
					<h6 class="me-3">{{ __('admin.common.filters') }}</h6>
					<div class="dropdown me-3">
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
							<i class="ti ti-badge me-1"></i>{{ __('admin.common.status') }}
						</a>
						<ul class="dropdown-menu dropdown-menu-lg p-2" id="statusList">
							<li>
								<div class="top-search m-2">
									<div class="top-search-group">
										<span class="input-icon">
											<i class="ti ti-search"></i>
										</span>
										<input type="text" class="form-control" name="status_search" placeholder="{{ __('admin.common.search') }}">
									</div>
								</div>
							</li>
							<li>
								<label class="dropdown-item d-flex align-items-center rounded-1">
									<input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="1">{{ __('admin.common.planned') }}
								</label>
							</li>
							<li>
								<label class="dropdown-item d-flex align-items-center rounded-1">
									<input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="2">{{ __('admin.common.in_progress') }}
								</label>
							</li>
							<li>
								<label class="dropdown-item d-flex align-items-center rounded-1">
									<input class="form-check-input m-0 me-2 status_checkbox" type="checkbox" value="3">{{ __('admin.common.completed') }}
								</label>
							</li>
						</ul>
					</div>
					<a href="javascript:void(0);" class="me-2 text-purple links" id="apply_filter">{{ __('admin.common.apply') }}</a>
					<a href="javascript:void(0);" class="text-danger links" id="reset_filter">{{ __('admin.common.clear_all') }}</a>
				</div>
			</div>

			<div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
				@include('admin.content-loader')
			</div>

			<!-- Custom Data Table -->
			<div class="custom-datatable-filter table-responsive d-none real-table">
				<table class="table" id="maintenanceTable">
					<thead class="thead-light">
						<tr>
							<th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
							<th>{{ strtoupper(__('admin.common.start_date')) }}</th>
							<th>{{ strtoupper(__('admin.common.end_date')) }}</th>
							<th>{{ strtoupper(__('admin.common.odometer')) }}</th>
							<th>{{ strtoupper(__('admin.common.status')) }}</th>
							@if (hasPermission($permissions, 'maintenance', 'edit') || hasPermission($permissions, 'maintenance', 'delete'))

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

	<!-- Add Maintenance -->
	<div class="modal fade addmodal" id="maintenance_modal">
		<div class="modal-dialog modal-dialog-centered modal-md">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.rentals.maintenance') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="maintenanceForm" autocomplete="off">
					@csrf
					<input type="hidden" name="id" id="id">
					<div class="modal-body">
						<div class="row">     
							<div class="col-md-6">
								<label class="form-label">{{ __('admin.common.vehicle') }}<span class="text-danger"> *</span></label>
								<select class="select" id="vehicle_id" name="vehicle_id" data-placeholder="{{ __('admin.common.select') }}">
									<option value="">{{ __('admin.common.select') }}</option>
									@if ($vehicles)
										@foreach ($vehicles as $vehicle)
											<option value="{{ $vehicle->id }}">{{ $vehicle->name }}</option>
										@endforeach
									@endif
								</select>
								<span class="text-danger error-text" id="vehicle_id_error"></span>
							</div>    
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.odometer') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="odometer" id="odometer">
									<span id="odometer_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">                    
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.start_date') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control custom_date_picker" name="start_date" id="start_date" placeholder="dd-mm-yyyy">
									<span id="start_date_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">                    
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.end_date') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control custom_date_picker" name="end_date" id="end_date" placeholder="dd-mm-yyyy">
									<span id="end_date_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.details') }}<span class="text-danger"> *</span></label>
									<textarea class="form-control" rows="4"  name="details" id="details"></textarea>
									<span id="details_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-12">
								<label class="form-label">{{ __('admin.common.status') }}<span class="text-danger"> *</span></label>
								<select class="select" id="status" name="status" data-placeholder="{{ __('admin.common.select') }}">
									<option value="">{{ __('admin.common.select') }}</option>
									<option value="1">{{ __('admin.common.planned') }}</option>
									<option value="2">{{ __('admin.common.in_progress') }}</option>
									<option value="3">{{ __('admin.common.completed') }}</option>
								</select>
								<span class="text-danger error-text" id="status_error"></span>
							</div>    
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Add Brand -->

	<!-- Delete Brand -->
	<div class="modal fade deletemodal" id="delete-modal">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<form id="maintenanceDeleteForm">
					@csrf
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.rentals.delete_maintenance') }}</h4>
						<p class="mb-3">{{ __('admin.rentals.delete_maintenance_confirmation') }}</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Delete Brand -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/carinfo/maintenance.js') }}"></script>
@endpush