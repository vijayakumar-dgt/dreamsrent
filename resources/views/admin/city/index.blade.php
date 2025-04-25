@extends('admin.admin')
@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
	<div class="content me-4">

		<!-- Breadcrumb -->
		<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
			<div class="my-auto mb-2">
				<h2 class="mb-1">{{ __('admin.cms.locations') }}</h2>
				<nav>
					<ol class="breadcrumb mb-0">
						<li class="breadcrumb-item">
							<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
						</li>
						<li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.city') }}</li>
					</ol>
				</nav>
			</div>
			<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
				@if (hasPermission($permissions, 'cms_locations', 'create'))
				<div class="mb-2">
					<a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#city_modal" id="add_city" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.cms.add_city') }} </a>
				</div>
				@endif
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
						<input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
					</div>
				</div>
			</div>
			<div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
				<div class="dropdown">
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
			<table class="table" id="cityTable">
				<thead class="thead-light">
					<tr>
						<th>{{ strtoupper(__('admin.cms.city_name')) }}</th>
						<th>{{ strtoupper(__('admin.cms.state_name')) }}</th>
						<th>{{ strtoupper(__('admin.cms.country_name')) }}</th>
						<th>{{ strtoupper(__('admin.common.status')) }}</th>
						@if (hasPermission($permissions, 'cms_locations', 'edit') || hasPermission($permissions, 'cms_locations', 'delete'))
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
<div class="modal fade addmodal" id="city_modal">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="mb-0 modal-title">{{ __('admin.cms.create_city') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form id="cityForm">
				@csrf
				<input type="hidden" name="id" id="id">
				<div class="modal-body">
					<div class="mb-3">
						<label class="form-label">{{ __('admin.common.city') }}<span class="text-danger"> *</span></label>
						<input type="text" class="form-control" name="name" id="name" maxlength="50">
						<span id="name_error" class="text-danger error-text"></span>
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('admin.general_settings.state') }}<span class="text-danger"> *</span></label>
						<select class="form-control select2" name="state_id" id="state_id">
							<option value="">{{ __('admin.general_settings.select_state') }}</option>
							@foreach ($state_ids as $state)
							<option value="{{ $state->id }}">{{ $state->name }}</option>
							@endforeach
						</select>
						<span id="state_id_error" class="text-danger error-text"></span>
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
			<form id="delateCity">
				@csrf
				<input type="hidden" name="delete_id" id="delete_id">
				<div class="modal-body text-center">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">{{ __('admin.cms.delete_city') }}</h4>
					<p class="mb-3">{{ __('admin.cms.city_delete_confirmation') }}</p>
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
<script src="{{ asset('assets/js/city.js') }}"></script>
@endpush