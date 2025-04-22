@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content me-4">

		<!-- Breadcrumb -->
		<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
			<div class="my-auto mb-2">
				<h2 class="mb-1">{{ __('admin.support.announcements') }}</h2>
				<nav>
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">{{ __('admin.support.announcements') }}</li>
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
				<div class="mb-2">
					@if (hasPermission($permissions, 'announcements', 'create'))
                    <div class="skeleton label-skeleton label-loader"></div>
					<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_announcement_modal" id="add_announcement" class="btn btn-primary d-flex align-items-center d-none real-label"><i class="ti ti-plus me-2"></i>{{ __('admin.support.add_new_announcement') }}</a>
				@endif
				</div>
			</div>
		</div>
		<!-- /Breadcrumb -->

		<!-- Table Header -->
		<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
			<div class="d-flex align-items-center flex-wrap row-gap-3">
				<div class="skeleton label-skeleton label-loader me-2"></div>
				<div class="dropdown me-2 d-none real-label">
					<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
						<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : {{ __('admin.common.latest') }}
					</a>
					<ul class="dropdown-menu  dropdown-menu-end p-2">
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.latest') }}</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.ascending') }}</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.descending') }}</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.last_month') }}</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.last_7_days') }}</a>
						</li>
					</ul>
				</div>
				<div class="me-2 d-none">
					<div class="input-icon-start position-relative topdatepicker">
						<span class="input-icon-addon">
							<i class="ti ti-calendar"></i>
						</span>
						<input type="text" class="form-control date-range bookingrange" placeholder="dd/mm/yyyy - dd/mm/yyyy">
					</div>
				</div>
				<div class="skeleton label-skeleton label-loader"></div>
				<div class="dropdown d-none real-label">
					<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
						<i class="ti ti-badge me-1"></i> {{ __('admin.common.status') }}
					</a>
					<ul class="dropdown-menu  dropdown-menu-end p-2">
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.active') }}</a>
						</li>
						<li>
							<a href="javascript:void(0);" class="dropdown-item rounded-1">{{ __('admin.common.inactive') }}</a>
						</li>
					</ul>
				</div>
			</div>
			<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
				<div class="skeleton label-skeleton label-loader"></div>
				<div class="top-search me-2 d-none real-label">
					<div class="top-search-group">
						<span class="input-icon">
							<i class="ti ti-search"></i>
						</span>
						<input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
					</div>
				</div>
			</div>
		</div>
		<!-- /Table Header -->

		<div class="custom-datatable-filter table-responsive table-loader">
			<table class="table">
				<thead>
					<tr>
						<th><div class="skeleton th-skeleton th-loader"></div></th>
						<th><div class="skeleton th-skeleton th-loader"></div></th>
						<th><div class="skeleton th-skeleton th-loader"></div></th>
						<th><div class="skeleton th-skeleton th-loader"></div></th>
						<th><div class="skeleton th-skeleton th-loader"></div></th>
					</tr>
				</thead>
				<tbody>
					<tr>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
					</tr>
					<tr>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
						<td><div class="skeleton data-skeleton data-loader"></div></td>
					</tr>
				</tbody>
			</table>
		</div>

		<!-- Custom Data Table -->
		<div class="custom-datatable-filter table-responsive brandstable d-none real-table">
			<table class="table" id="announcementTable">
				<thead class="thead-light">
					<tr>
						<th>{{ __('admin.common.date') }}</th>
						<th>{{ __('admin.support.announcements') }}</th>
						<th>{{ __('admin.common.type') }}</th>
						<th>{{ __('admin.common.status') }}</th>
						@if (hasPermission($permissions, 'announcements', 'edit') || hasPermission($permissions, 'announcements', 'delete'))
						<th>{{ __('admin.common.action') }}</th>
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
<div class="modal fade addmodal" id="add_announcement_modal">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="mb-0 modal-title">{{ __('admin.support.create_announcement') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form id="announcementForm" autocomplete="off">
				@csrf
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.support.announcement_title') }}<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="announcement_title" id="announcement_title">
								<span id="announcement_title_error" class="text-danger error-text"></span>
							</div>
						</div>
                        <div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.support.announcement_type') }}<span class="text-danger"> *</span></label>
								<select class="select select2" id="announcement_type" name="announcement_type" data-placeholder="{{ __('admin.common.select') }}">
									@if ($announcement_types)
										@foreach ($announcement_types as $announcement_type)
											<option value="{{ $announcement_type->id }}">{{ $announcement_type->name }}</option>
										@endforeach
									@endif
								</select>
								<span class="text-danger error-text" id="announcement_type_error"></span>
							</div>
						</div>
                        <div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.common.user') }}<span class="text-danger"> *</span></label>
								<select class="select select2" id="user_type" name="user_type" data-placeholder="{{ __('admin.common.select') }}">
									<option value="">{{ __('admin.common.select') }}</option>
									<option value="user">{{ __('admin.common.user') }}</option>
									<option value="admin">{{ __('admin.common.admin') }}</option>
								</select>
								<span class="text-danger error-text" id="user_type_error"></span>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.common.description') }}<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="description" id="description"></textarea>
								<span id="description_error" class="text-danger error-text"></span>
							</div>
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
<!-- /Add Driver -->

<!-- Edit Driver -->
<div class="modal fade addmodal" id="edit_announcement_modal">
	<div class="modal-dialog modal-dialog-centered modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="mb-0 modal-title">{{ __('admin.support.edit_announcement') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form id="editAnnouncementForm" autocomplete="off">
				@csrf
				<input type="hidden" name="id" id="id">
				<div class="modal-body">
                    <div class="row">
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.support.announcement_title') }}<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="edit_announcement_title" id="edit_announcement_title">
								<span id="edit_announcement_title_error" class="text-danger error-text"></span>
							</div>
						</div>
                        <div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.support.announcement_type') }}<span class="text-danger"> *</span></label>
								<select class="select select2" id="edit_announcement_type" name="edit_announcement_type" data-placeholder="{{ __('admin.common.select') }}">
									@if ($announcement_types)
										@foreach ($announcement_types as $announcement_type)
											<option value="{{ $announcement_type->id }}">{{ $announcement_type->name }}</option>
										@endforeach
									@endif
								</select>
								<span class="text-danger error-text" id="announcement_type_error"></span>
							</div>
						</div>
                        <div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.common.user') }}<span class="text-danger"> *</span></label>
								<select class="select select2" id="edit_user_type" name="edit_user_type" data-placeholder="{{ __('admin.common.select') }}">
									<option value="">{{ __('admin.common.select') }}</option>
									<option value="user">{{ __('admin.common.user') }}</option>
									<option value="admin">{{ __('admin.common.admin') }}</option>
								</select>
								<span class="text-danger error-text" id="edit_user_type_error"></span>
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.common.description') }}<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="edit_description" id="edit_description"></textarea>
								<span id="description_error" class="text-danger error-text"></span>
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
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary savebtn">{{ __('admin.common.save_changes') }}</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /Edit Driver -->

<!-- Delete Driver -->
<div class="modal fade deletemodal" id="delete_announcement_modal">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<form id="annoncementDeleteForm">
				@csrf
				<input type="hidden" name="delete_id" id="delete_id">
				<div class="modal-body text-center">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">{{ __('admin.support.delete_announcement') }}</h4>
					<p class="mb-3">{{ __('admin.support.delete_announcement_description') }}</p>
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
<script src="{{ asset('assets/js/communication/accouncement.js') }}"></script>

@endpush
