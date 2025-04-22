@extends('admin.admin')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
			<div class="content">

				<!-- Page Header -->
				<div class="d-md-flex d-block align-items-center justify-content-between mb-3">
					<div class="my-auto mb-2">
						<h3 class="page-title mb-1">Pricing Types</h3>
						<nav>
							<ol class="breadcrumb mb-0">
								<li class="breadcrumb-item">
									<a href="#">Home</a>
								</li>
								<li class="breadcrumb-item active" aria-current="page">Pricing Types</li>
							</ol>
						</nav>
					</div>
					<div class="d-flex my-xl-auto right-content align-items-center flex-wrap">
						<div class="mb-2">
							<a href="#" class="btn btn-primary d-flex align-items-center" id="add_pricing_type" data-bs-toggle="modal"
								data-bs-target="#pricing_type_modal"><i class="ti ti-square-rounded-plus me-2"></i>Add Pricing Type</a>
						</div>
					</div>
				</div>
				<!-- /Page Header -->
				<div class="card">
					<div class="card-body p-0 py-3">
						<div class="custom-datatable-filter table-responsive">
							<table class="table" id="pricingTypeTable">
								<thead class="thead-light">
									<tr>
										<th class="no-sort">
											<div class="form-check form-check-md">
												<input class="form-check-input" type="checkbox" id="select-all">
											</div>
										</th>
										<th>Pricing Type</th>
										<th>Status</th>
										<th>Action</th>
									</tr>
								</thead>
								<tbody>
									
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- /Page Wrapper -->

		<!-- Add/Edit DoorType -->
		<div class="modal fade" id="pricing_type_modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title">Add Pricing Type</h4>
						<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal"
							aria-label="Close">
							<i class="ti ti-x"></i>
						</button>
					</div>
                    <form id="pricingTypeForm">
                        @csrf
						<input type="hidden" name="id" id="id">
						<div class="modal-body">
							<div class="row">
								<div class="col-md-12">
									<div class="mb-3">
										<label class="form-label">Pricing Type</label>
										<input type="text" class="form-control" name="pricing_type" id="pricing_type">
                                        <span id="pricing_type_error" class="text-danger error-text"></span>
									</div>
								</div>
								<div class="col-md-12" id="statusDiv" style="display: none">
									<div class="mb-0">
										<div class="modal-satus-toggle d-flex align-items-center justify-content-between">
											<div class="status-title">
												<h5>{{ __('Status') }}</h5>
											</div>
											<div class="status-toggle modal-status">
												<input type="checkbox" id="status" name="status" class="check" checked>
												<label for="status" class="checktoggle"> </label>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="modal-footer">
							<a href="#" class="btn btn-light me-2" data-bs-dismiss="modal">Cancel</a>
							<button type="submit" class="btn btn-primary submitbtn">Save</button>
						</div>
					</form>
				</div>
			</div>
		</div>

		<!-- Delete Modal -->
		<div class="modal fade" id="delete-modal">
			<div class="modal-dialog modal-dialog-centered">
				<div class="modal-content">
					<form id="deletePricingType">
						@csrf
						<input type="hidden" name="delete_id" id="delete_id">
						<div class="modal-body text-center">
							<span class="delete-icon">
								<i class="ti ti-trash-x"></i>
							</span>
							<h4>Delete</h4>
							<p>Are you sure you want to delete this item?</p>
							<div class="d-flex justify-content-center">
								<a href="javascript:void(0);" class="btn btn-light me-3"
									data-bs-dismiss="modal">Cancel</a>
								<button type="submit" class="btn btn-primary">Yes, Delete</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
		<!-- /Delete Modal -->
@endsection

@push('scripts')
<script src="{{ asset('assets/js/carinfo/pricing-type.js') }}"></script>
@endpush