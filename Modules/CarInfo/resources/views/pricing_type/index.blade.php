@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content me-4">
		
		<!-- Breadcrumb -->
		<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
			<div class="my-auto mb-2">
				<h2 class="mb-1">Pricing Types</h2>
				<nav>
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item">
							<a href="index.html">Home</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">Pricing Types</li>
					</ol>
				</nav>
			</div>
			<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
				<div class="mb-2">
					<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#pricing_type_modal" id="add_pricing_type" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>Add New Pricing Type</a>
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
						<input type="text" class="form-control" placeholder="Search">
					</div>
				</div>
			</div>
			<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">               
				<div class="dropdown">
					<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
						<i class="ti ti-badge me-1"></i> Status
					</a>
					<ul class="dropdown-menu  dropdown-menu-end p-2">
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">Active</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">Inactive</a>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<!-- /Table Header -->

		<!-- Custom Data Table -->
		<div class="custom-datatable-filter table-responsive brandstable">
			<table class="table" id="pricingTypeTable">
				<thead class="thead-light">
					<tr>
						<th class="no-sort">
							<div class="form-check form-check-md">
								<input class="form-check-input" type="checkbox" id="select-all">
							</div>
						</th>
						<th>PRICING TYPE</th>
						<th>STATUS</th>
						<th>ACTION</th>
					</tr>
				</thead>
				<tbody>			

				</tbody>
			</table>
		</div>
		<!-- Custom Data Table -->

		<div class="table-footer"></div>			

	</div>	
</div>
<!-- /Page Wrapper -->


<!-- Add Pricing Type -->
<div class="modal fade addmodal" id="pricing_type_modal">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="mb-0 modal-title">Create Pricing Type</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form id="pricingTypeForm">
				@csrf
				<input type="hidden" name="id" id="id">
				<div class="modal-body">                    
					<div class="mb-3">
						<label class="form-label">Pricing Type<span class="text-danger"> *</span></label>
						<input type="text" class="form-control" name="pricing_type" id="pricing_type">
						<span id="pricing_type_error" class="text-danger error-text"></span>
					</div>
				</div>
				<div class="modal-footer">
					<div class="d-flex justify-content-between align-items-center w-100">
						<div class="form-check form-check-md form-switch me-2" id="statusDiv" style="display: none;">
							<label for="status" class="form-check-label form-label mt-0 mb-0">
							<input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked>
								Status
							</label>
						</div>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-primary submitbtn">Create New</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /Add Pricing Type -->

<!-- Delete Pricing Type -->
<div class="modal fade deletemodal" id="delete-modal">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<form id="deletePricingType">
				@csrf
				<input type="hidden" name="delete_id" id="delete_id">
				<div class="modal-body text-center">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">Delete Pricing Type</h4>
					<p class="mb-3">Are you sure you want to delete pricing type?</p>
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">Cancel</a>
						<button type="submit" class="btn btn-primary">Yes, Delete</a>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /Delete Pricing Type -->

@endsection

@push('scripts')
<script src="{{ asset('assets/js/carinfo/pricing-type.js') }}"></script>
@endpush