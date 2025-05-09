@extends('admin.admin')

@section('meta_title', __('admin.manage.drivers') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h2 class="mb-1">{{ __('admin.manage.drivers') }}</h2>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('admin.manage.drivers') }}</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					<div class="mb-2 me-2 d-none">
						<div class="skeleton label-skeleton label-loader"></div>
						<button type="button" class="btn btn-white d-flex align-items-center d-none real-label"><i class="ti ti-printer me-2"></i>{{ __('admin.common.print') }}</button>
					</div>
					<div class="me-2 mb-2 d-none">
						<div class="skeleton label-skeleton label-loader"></div>
						<div class="dropdown d-none real-label">
							<button type="button" class="btn btn-dark d-inline-flex align-items-center">
								<i class="ti ti-upload me-1"></i>{{ __('admin.common.export') }}
							</a>
						</div>
					</div>
					<div class="mb-2">
						@if (hasPermission($permissions, 'drivers', 'create'))
						<div class="skeleton label-skeleton label-loader"></div>
						<button type="button" data-bs-toggle="modal" data-bs-target="#add_driver_modal" id="add_driver" class="btn btn-primary d-flex align-items-center d-none real-label"><i class="ti ti-plus me-2"></i>{{ __('admin.manage.add_new_driver') }}</button>
						@endif
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<input type="hidden" name="sort_by_input" id="sort_by_input">
					<input type="hidden" name="sort_by_status" id="sort_by_status">
					<div class="skeleton label-skeleton label-loader me-2"></div>
					<div class="dropdown me-2 d-none real-label">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</button>
							</li>
						</ul>
					</div>
					<div class="skeleton label-skeleton label-loader me-2"></div>
					<div class="me-2 d-none real-input">
						<div class="input-icon-start position-relative topdatepicker">
							<span class="input-icon-addon">
								<i class="ti ti-calendar"></i>
							</span>
							<input type="text" class="form-control date-range bookingrange" name="sort_by_date" id="sort_by_date" value="" placeholder="dd/mm/yyyy - dd/mm/yyyy">
						</div>
					</div>
					<div class="skeleton label-skeleton label-loader"></div>
					<div class="dropdown me-2 d-none real-input">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-badge me-1"></i> <span class="ms-1" id="current_sort_status">{{ __('admin.common.status') }}</span>
						</button>
						<ul class="dropdown-menu  dropdown-menu-end p-2" id="statusList">
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="1">{{ __('admin.common.active') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1" data-sort="0">{{ __('admin.common.inactive') }}</button>
							</li>
						</ul>
					</div>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
					<div class="skeleton label-skeleton label-loader me-2"></div>
					<div class="dropdown me-2 d-none real-label">
						<button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-edit me-1"></i> {{ __('admin.common.bulk_actions') }}
						</button>
						<ul class="dropdown-menu dropdown-menu-end p-2">
							<li>
								<button type="button" class="dropdown-item rounded-1 bulk_status_change" data-status="1">{{ __('admin.common.active') }}</button>
							</li>
							<li>
								<button type="button" class="dropdown-item rounded-1 bulk_status_change" data-status="0">{{ __('admin.common.inactive') }}</button>
							</li>
						</ul>
					</div>
					<div class="skeleton label-skeleton label-loader"></div>
					<div class="top-search d-none real-input">
						<div class="top-search-group">
							<span class="input-icon">
								<i class="ti ti-search"></i>
							</span>
							<input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
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
						</tr>
					</tbody>
				</table>
			</div>
			<!-- Custom Data Table -->
			<div class="custom-datatable-filter table-responsive brandstable real-table d-none">
				<table class="table" id="driverTable">
					<thead class="thead-light">
						<tr>
							<th class="no-sort">
								<div class="form-check form-check-md">
									<input class="form-check-input" type="checkbox" id="select-all">
								</div>
							</th>
							<th>{{ strtoupper(__('admin.manage.drivers')) }}</th>
							<th>{{ strtoupper(__('admin.common.email')) }}</th>
							<th>{{ strtoupper(__('admin.manage.licence_no')) }}</th>
							<th>{{ strtoupper(__('admin.manage.expiry_date')) }}</th>
							<th>{{ strtoupper(__('admin.common.status')) }}</th>
							@if (hasPermission($permissions, 'drivers', 'edit') || hasPermission($permissions, 'drivers', 'delete'))
							<th>{{ strtoupper(__('admin.common.action')) }}</th>
							@endif
						</tr>
					</thead>
					<tbody>

					</tbody>
				</table>
			</div>
			<!-- Custom Data Table -->
			<div class="table-footer"></div>
		</div>
		@include('admin.partials.footer')
	</div>
	<!-- /Page Wrapper -->

	<!-- Add Driver -->
	<div class="modal fade addmodal" id="add_driver_modal">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.manage.create_driver') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="driverForm" autocomplete="off">
					@csrf
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
									<div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
										<div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
											<img id="imagePreview" src="" class="img-fluid rounded d-none">
											<i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
										</div>
										<div class="profile-upload">
											<div class="profile-uploader d-flex align-items-center">
												<div class="drag-upload-btn btn btn-md btn-dark">
													<i class="ti ti-photo-up fs-14"></i>
													{{ __('admin.common.upload') }}
													<input type="file" class="form-control image-sign" name="image" id="image">
												</div>
											</div>
											<div class="mt-2">
												<p class="fs-14">{{ __('admin.common.upload_image_size', ['size' => 2]) }}</p>
											</div>
										</div>
									</div>
									<span class="text-danger error-text" id="image_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.driver_name') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="driver_name" id="driver_name">
									<span id="driver_name_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.gender') }}<span class="text-danger"> *</span></label>
									<select class="select" id="gender" name="gender" data-placeholder="{{ __('admin.common.select') }}">
										<option value="">{{ __('admin.common.select') }}</option>
										<option value="male">{{ __('admin.common.male') }}</option>
										<option value="female">{{ __('admin.common.female') }}</option>
										<option value="other">{{ __('admin.common.other') }}</option>
									</select>
									<span class="text-danger error-text" id="gender_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control driver_phone_number" name="phone_number" id="phone_number">
									<input type="hidden" id="international_phone_number" name="international_phone_number">
									<span id="phone_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="email" id="email">
									<span id="email_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.address') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="address" id="address">
									<span id="address_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.assigned_vehicles') }}<span class="text-danger"> *</span></label>
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
							<h6 class="fs-16 fw-medium mb-2">{{ __('admin.manage.licence_details') }}</h6>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.card_number') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="card_number" id="card_number">
									<span id="card_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.date_of_issue') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control date_of_issue" name="date_of_issue" id="date_of_issue" placeholder="dd-mm-yyyy">
									<span id="date_of_issue_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.valid_date') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control valid_date" name="valid_date" id="valid_date" placeholder="dd-mm-yyyy">
									<span id="valid_date_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.documents') }}</label><span class="text-danger"> *</span></label>
									<div class="document-upload text-center br-3 mb-3">
										<img src="{{ asset('/backend/assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
										<p class="mb-2">{{ __('admin.common.drop_your_files_here_or') }} <span class="text-info text-decoration-underline">{{ __('admin.common.browse') }}</span></p>
										<p class="fs-12 mb-0">{{ __('admin.common.maximum_size', ['size' => 5]) }}</p>
										<input type="file" class="form-control image-sign" name="documents[]" id="documents" multiple="">
									</div>
									<span id="documents_error" class="text-danger error-text"></span>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-center">
							<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
							<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Add Driver -->

	<!-- Edit Driver -->
	<div class="modal fade addmodal" id="edit_driver_modal">
		<div class="modal-dialog modal-dialog-centered modal-lg">
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="mb-0 modal-title">{{ __('admin.manage.edit_driver') }}</h4>
					<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
						<i class="ti ti-x fs-16"></i>
					</button>
				</div>
				<form id="editDriverForm" autocomplete="off">
					@csrf
					<input type="hidden" name="id" id="id">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
									<div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
										<div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
											<img id="editImagePreview" src="" class="img-fluid rounded d-none">
											<i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
										</div>
										<div class="profile-upload">
											<div class="profile-uploader d-flex align-items-center">
												<div class="drag-upload-btn btn btn-md btn-dark">
													<i class="ti ti-photo-up fs-14"></i>
													{{ __('admin.common.upload') }}
													<input type="file" class="form-control image-sign" name="image" id="edit_image">
												</div>
											</div>
											<div class="mt-2">
												<p class="fs-14">{{ __('admin.common.upload_image_size', ['size' => 2]) }}</p>
											</div>
										</div>
									</div>
									<span class="text-danger error-text" id="edit_image_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.driver_name') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="driver_name" id="edit_driver_name">
									<span id="edit_driver_name_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.gender') }}<span class="text-danger"> *</span></label>
									<select class="select" id="edit_gender" name="gender" data-placeholder="{{ __('admin.common.select') }}">
										<option value="">{{ __('admin.common.select') }}</option>
										<option value="male">{{ __('admin.common.male') }}</option>
										<option value="female">{{ __('admin.common.female') }}</option>
										<option value="other">{{ __('admin.common.other') }}</option>
									</select>
									<span class="text-danger error-text" id="edit_gender_error"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control edit_driver_phone_number" name="phone_number" id="edit_phone_number">
									<input type="hidden" id="edit_international_phone_number" name="international_phone_number">
									<span id="edit_phone_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="email" id="edit_email">
									<span id="edit_email_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.address') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="address" id="edit_address">
									<span id="edit_address_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-6">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.assigned_vehicles') }}<span class="text-danger"> *</span></label>
									<select class="select select2" id="edit_assigned_cars" name="assigned_cars[]" data-placeholder="Select" multiple>
										@if ($cars)
											@foreach ($cars as $car)
												<option value="{{ $car->id }}">{{ $car->name }}</option>
											@endforeach
										@endif
									</select>
									<span class="text-danger error-text" id="edit_assigned_cars_error"></span>
								</div>
							</div>
							<h6 class="fs-16 fw-medium mb-2">{{ __('admin.manage.licence_details') }}</h6>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.card_number') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control" name="card_number" id="edit_card_number">
									<span id="edit_card_number_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.date_of_issue') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control date_of_issue" name="date_of_issue" id="edit_date_of_issue" placeholder="dd-mm-yyyy">
									<span id="edit_date_of_issue_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-4">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.manage.valid_date') }}<span class="text-danger"> *</span></label>
									<input type="text" class="form-control valid_date" name="valid_date" id="edit_valid_date" placeholder="dd-mm-yyyy">
									<span id="edit_valid_date_error" class="text-danger error-text"></span>
								</div>
							</div>
							<div class="col-md-12">
								<div class="mb-3">
									<label class="form-label">{{ __('admin.common.documents') }}</label><span class="text-danger"> *</span></label>
									<div class="document-upload text-center br-3 mb-3">
										<img src="{{ asset('/backend/assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
										<p class="mb-2">{{ __('admin.common.drop_your_files_here_or') }} <span class="text-info text-decoration-underline">{{ __('admin.common.browse') }}</span></p>
										<p class="fs-12 mb-0">{{ __('admin.common.maximum_size', ['size' => 5]) }}</p>
										<input type="file" class="form-control image-sign" name="documents[]" id="edit_documents" multiple="">
									</div>
									<input type="hidden" name="removed_documents" id="removed_documents">
									<span id="edit_documents_error" class="text-danger error-text"></span>
								</div>
								<div class="mb-3">
									<div class="d-flex justify-content-start document-preview-container">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<div class="d-flex justify-content-between align-items-center w-100">
							<div class="form-check form-check-md form-switch me-2">
								<label for="status" class="form-check-label form-label mt-0 mb-0">
								<input class="form-check-input form-label me-2 status" id="status" name="status" type="checkbox" role="switch">
									{{ __('admin.common.status') }}
								</label>
							</div>
							<div class="d-flex justify-content-center">
								<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
								<button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.save_changes') }}</button>
							</div>
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
				<form id="driverDeleteForm">
					@csrf
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.manage.delete_driver') }}</h4>
						<p class="mb-3">{{ __('admin.manage.delete_driver_confirmation') }}</p>
						<div class="d-flex justify-content-center">
							<button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</a>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Delete Driver -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/carinfo/driver.js') }}"></script>
@endpush
