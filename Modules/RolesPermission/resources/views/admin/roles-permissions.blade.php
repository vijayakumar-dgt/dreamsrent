@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">

        <!-- Breadcrumb -->
        <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h4 class="mb-1">{{ __('admin.user_management.roles') }}</h4>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.user_management.roles') }}</li>
                    </ol>
                </nav>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                <div class="mb-2">
                    @if (hasPermission($permissions, 'roles_permissions', 'create'))
                    <div class="skeleton label-skeleton label-loader"></div>
                    <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center d-none real-label" id="add_role" data-bs-toggle="modal" data-bs-target="#role_modal"><i class="ti ti-plus me-2"></i>{{ __('admin.user_management.add_new_role') }}</a>
                    @endif
                </div>
            </div>
        </div>
        <!-- /Breadcrumb -->

        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="skeleton label-skeleton label-loader"></div>
                <div class="top-search d-none real-label">
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
						<th class="text-center">
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
					</tr>
				</tbody>
			</table>
		</div>

        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
            <table class="table" id="roleTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{ strtoupper(__('admin.user_management.role')) }}</th>
                        <th>{{ strtoupper(__('admin.user_management.created_date')) }}</th>
                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        @if (hasPermission($permissions, 'roles_permissions', 'edit') || hasPermission($permissions, 'roles_permissions', 'delete'))
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

<!-- Add User -->
<div class="modal fade" id="role_modal">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="mb-0">{{ __('admin.user_management.create_role') }}</h5>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="roleForm" autocomplete="off">
                <input type="hidden" name="id" id="id">
                <div class="modal-body pb-1">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.user_management.role') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="role" id="role">
                        <span class="error-text text-danger" id="role_error"></span>
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
<!-- /Add User -->

<!-- Delete  -->
<div class="modal fade" id="delete_role">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <form id="roleDeleteForm">
            <input type="hidden" name="delete_id" id="delete_id">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.user_management.delete_role') }}</h4>
                    <p class="mb-3">{{ __('admin.user_management.delete_role_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                    </div>
                </div>
        </div>
        </form>
    </div>
</div>
<!-- /Delete -->

@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/roles-permissions.js') }}"></script>
@endpush
