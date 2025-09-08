@extends('admin.admin')

@section('meta_title', __('admin.rentals.extra_services') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb
				:title="__('admin.rentals.extra_services')"
				:breadcrumbs="[
					__('admin.rentals.extra_services') => ''
				]"
				:buttonText="__('admin.rentals.add_new_extra_service')"
				:modalId="'add_extra_service'"
				:buttonId="'add_new_extra_service'"
				:permissionModule="'extra_service'"
			/>
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}" id="keyword">
                        </div>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i><span class="status_label">{{ __('admin.common.status') }}</span>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-end p-2">
                            <li>
                                <button type="button" class="dropdown-item rounded-1 status_option" data-label="Active" data-id="1">{{ __('admin.common.active') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1 status_option" data-label="Inactive" data-id="0">{{ __('admin.common.inactive') }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <div class="d-none real-table">
                <div class="custom-datatable-filter table-responsive">
                    <table class="table" id="ExtraServiceTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ strtoupper(__('admin.common.name')) }}</th>
                                <th>{{ strtoupper(__('admin.common.icon')) }}</th>
                                <th>{{ strtoupper(__('admin.common.image')) }}</th>
                                <th>{{ strtoupper(__('admin.common.description')) }}</th>
                                <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                @if (hasPermission($permissions, 'extra_service', 'edit') || hasPermission($permissions, 'extra_service', 'delete'))
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
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add/Edit Extra Service Start-->
    <x-admin.modal
		className="addmodal"
		id="add_extra_service"
		:title="__('admin.rentals.create_extra_service')"
		action="{{  route('store_extra_service') }}"
		formId="extraServiceForm"
		method="POST"
		enctype="multipart/form-data">
		<x-slot name="body">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="language_id" id="language_id">
            <div class="mb-3">
                <label for="name" class="form-label">{{ __('admin.common.name') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name">
                <span id="name_error" class="text-danger error-text"></span>
            </div>
            <!-- Icon with preview-->
            <div class="row mb-3">
                <label for="icon" class="form-label">{{ __('admin.common.icon') }} <span class="text-danger icon_asterisk">*</span></label>
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxxl border border-dashed me-2 flex-shrink-0 text-dark">
                        <img src="{{ uploadedAsset('', 'default') }}" id="icon_preview" class="img-fluid d-none" alt="Icon">
                        <i class="ti ti-photo-plus icon_placeholder"></i>
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
                    <div class="profile-upload">
                        <div class="profile-uploader d-flex align-items-center">
                            <div class="drag-upload-btn btn btn-md btn-dark">
                                <i class="ti ti-photo-up fs-14"></i>
                                {{ __('admin.common.upload') }}
                                <input type="file" class="form-control image-sign" name="icon" id="icon">
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="fs-14">{{ __('admin.common.upload_icon_size') }}</p>
                        </div>
                    </div>
                </div>
                <span class="text-danger error-text" id="icon_error"></span>
            </div>
            <!-- Image with Preview -->
            <div class="row mb-3">
                <label for="image" class="form-label">{{ __('admin.common.image') }} <span class="text-danger icon_asterisk">*</span></label>
                <div class="col-md-4">
                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxxl border border-dashed me-2 flex-shrink-0 text-dark">
                        <img src="{{ uploadedAsset('', 'default') }}" id="image_preview" class="img-fluid d-none" alt="Extra Service">
                        <i class="ti ti-photo-plus image_placeholder"></i>
                    </div>
                </div>
                <div class="col-md-8 d-flex align-items-center">
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
            <!-- Description -->
            <div class="row mb-3">
                <label for="description" class="form-label">{{ __('admin.common.description') }} <span class="text-danger">*</span></label>
                <div class="col-md-12">
                    <div class="mb-0">
                        <textarea class="form-control" name="description" id="description"></textarea>
                    </div>
                </div>
                <span class="text-danger error-text" id="description_error"></span>
            </div>
		</x-slot>
		<x-slot name="footer">
			<div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
                    <label for="status" class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2 status" name="status" id="status" type="checkbox" role="switch" checked aria-checked="true">
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
                </div>
            </div>
		</x-slot>
	</x-admin.modal>
    <!-- Add/Edit Extra Service end -->

    <!-- Delete Modal Start-->
    <x-admin.delete-modal
		className="deletemodal"
		id="delete-modal"
		action="{{ route('delete_extra_service') }}"
		formId="deleteExtraService"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.rentals.delete_extra_service')"
		:description="__('admin.rentals.delete_extra_service_confirmation')">
	</x-admin.delete-modal>
    <!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/extra-services.js') }}"></script>
@endpush
