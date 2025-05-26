@extends('admin.admin')

@section('meta_title',  __('admin.rentals.cylinders') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb 
				:title="__('admin.rentals.cylinders')" 
				:breadcrumbs="[
					__('admin.rentals.cylinders') => ''
				]"
				:buttonText="__('admin.rentals.add_new_cylinder')"
				:modalId="'add_cylinder'"
				:buttonId="'add_new_cylinder'"
				:permissionKey="'vehicle_attributes'"
			/>
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}" id="search">
                        </div>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i> <span id="status_text">{{ __('admin.common.status') }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li>
                                <button type="button" class="dropdown-item rounded-1 statusfilter" data-status="1">{{ __('admin.common.active') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1 statusfilter" data-status="0">{{ __('admin.common.inactive') }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Custom Data Table -->
            <div class="d-none real-table">
                <div class="custom-datatable-filter table-responsive brandstable">
                    <table class="table" id="cylinderTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ strtoupper(__('admin.rentals.cylinder_type')) }}</th>
                                <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                @if (hasPermission($permissions, 'vehicle_attributes', 'edit') || hasPermission($permissions, 'vehicle_attributes', 'delete'))
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
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add/Edit Cylinder Type Start-->
    <div class="modal fade addmodal" id="add_cylinder">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mb-0">{{ __('admin.rentals.add_new_cylinder') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form action="" id="cylinderForm">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.rentals.cylinder_type') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="cylinder_type" id="cylinder_type">
                            <span id="cylinder_type_error" class="text-danger error-text"></span>
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
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add/Edit Cylinder Type End -->

    <!-- Delete Modal Start-->
    <div class="modal fade deletemodal" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="" id="deleteCylinder">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.rentals.delete_cylinder_type') }}</h4>
                        <p class="mb-3">{{ __('admin.rentals.delete_cylinder_type_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <button type="submit" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/cylinder.js') }}"></script>
@endpush