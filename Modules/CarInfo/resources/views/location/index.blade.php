@extends('admin.admin')

@section('meta_title', __('admin.manage.locations') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb 
                :title="__('admin.manage.locations')" 
                :breadcrumbs="[
                    __('admin.manage.locations') => ''
                ]"
                :buttonText="__('admin.manage.add_new_location')"
                :modalId="'add_location'"
                :buttonId="'add_new_location'"
                :permissionModule="'locations'"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i> <span class="statuslabel"> {{ __('admin.common.status') }}</span>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-end p-2">
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-label="{{ __('admin.common.active') }}">{{ __('admin.common.active') }}</button>
                            </li>
                            <li>
                                <button type="button" class="dropdown-item rounded-1" data-label="{{ __('admin.common.inactive') }}">{{ __('admin.common.inactive') }}</button>
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
            <div class="custom-datatable-filter table-responsive brandstable d-none real-table">
                <table class="table" id="locationTable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.manage.location_title')) }}</th>
                            <th>{{ strtoupper(__('admin.common.address')) }}</th>
                            <th>{{ strtoupper(__('admin.common.phone')) }}</th>
                            <th>{{ strtoupper(__('admin.manage.working_days')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                            @if (hasPermission($permissions, 'locations', 'edit') || hasPermission($permissions, 'locations', 'delete'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add/Edit Location Start-->
    <x-admin.modal 
		className="addmodal"
		id="add_location"
        dialogClassName="modal-lg"
        modalBodyClass="custom-modal-body"
		:title="__('admin.manage.add_location')"
		action="{{  route('store_location') }}"
		formId="locationForm"
		method="POST"
		enctype="multipart/form-data">
		<x-slot name="body">
			<input type="hidden" name="id" id="id">
			<input type="hidden" name="language_id" id="language_id">
            <!-- Image with Preview -->
            <div class="mb-3">
                <label for="image" class="form-label">{{ __('admin.common.image') }}</label>
                <div class="d-flex">
                    <div class="d-flex align-items-center justify-content-center avatar avatar-xxxl border border-dashed me-2 flex-shrink-0 text-dark">
                        <img src="{{ uploadedAsset('', 'default') }}" id="image_preview" class="img-fluid d-none" alt="Location Image">
                        <i class="ti ti-photo-plus image_placeholder"></i>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="profile-upload">
                            <div class="profile-uploader d-flex align-items-center">
                                <div class="drag-upload-btn btn btn-md btn-dark">
                                    <i class="ti ti-photo-up fs-14"></i>
                                    {{ __('admin.common.upload') }}
                                    <input type="file" class="form-control image-sign" name="image" id="image">
                                </div>
                            </div>
                            <div class="mt-2">
                                <p class="fs-14">{{ __('admin.common.upload_image_size', ['pixel' => '180*180', 'size' => 2]) }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <span class="text-danger error-text" id="image_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ __('admin.manage.location_title') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="name" id="name">
                <span id="name_error" class="text-danger error-text"></span>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.email')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="email" id="email">
                    <span id="email_error" class="text-danger error-text"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.phone')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control Number" name="mobile" id="mobile">
                    <input type="hidden" name="international_phone_number" id="international_phone_number">
                    <span id="mobile_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">{{ __('admin.common.address')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="address" id="address">
                    <span id="address_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.country')}} <span class="text-danger">*</span></label>
                    <select name="country" class="form-control select2" id="country">

                    </select>
                    <span id="country_error" class="text-danger error-text"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.state')}} <span class="text-danger">*</span></label>
                    <select name="state" class="form-control select2" id="state">

                    </select>
                    <span id="state_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.city')}} <span class="text-danger">*</span></label>
                    <select name="city" id="city" class="form-control select2"></select>
                    <span id="city_error" class="text-danger error-text"></span>
                </div>
                <div class="col-md-6">
                    <label class="form-label">{{ __('admin.common.pincode')}} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="pincode" maxlength="6" id="pincode">
                    <span id="pincode_error" class="text-danger error-text"></span>
                </div>
            </div>
            <p>{{ __('admin.manage.working_days') }} <em class="text-danger">*</em></p>
            <span class="text-danger error-text" id="monday_error"></span>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="monday" type="checkbox" role="switch" name="working_days[]" id="monday">
                        <label class="form-check-label ms-2" for="monday">{{ __('admin.manage.monday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[monday][start]" id="monday_start">
                            <small class="text-danger error-text position-absolute" id="monday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[monday][end]" id="monday_end">
                            <small class="text-danger error-text position-absolute" id="monday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="tuesday" type="checkbox" role="switch" name="working_days[]" id="tuesday">
                        <label class="form-check-label ms-2" for="tuesday">{{ __('admin.manage.tuesday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[tuesday][start]" id="tuesday_start">
                            <small class="text-danger error-text position-absolute" id="tuesday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[tuesday][end]" id="tuesday_end">
                            <small class="text-danger error-text position-absolute" id="tuesday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="wednesday" type="checkbox" role="switch" name="working_days[]" id="wednesday">
                        <label class="form-check-label ms-2" for="wednesday">{{ __('admin.manage.wednesday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[wednesday][start]" id="wednesday_start">
                            <small class="text-danger error-text position-absolute" id="wednesday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[wednesday][end]" id="wednesday_end">
                            <small class="text-danger error-text position-absolute" id="wednesday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="thursday" type="checkbox" role="switch" name="working_days[]" id="thursday">
                        <label class="form-check-label ms-2" for="thursday">{{ __('admin.manage.thursday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[thursday][start]" id="thursday_start">
                            <small class="text-danger error-text position-absolute" id="thursday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[thursday][end]" id="thursday_end">
                            <small class="text-danger error-text position-absolute" id="thursday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="friday" type="checkbox" role="switch" name="working_days[]" id="friday">
                        <label class="form-check-label ms-2" for="friday">{{ __('admin.manage.friday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[friday][start]" id="friday_start">
                            <small class="text-danger error-text position-absolute" id="friday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[friday][end]" id="friday_end">
                            <small class="text-danger error-text position-absolute" id="friday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="saturday" type="checkbox" role="switch" name="working_days[]" id="saturday">
                        <label class="form-check-label ms-2" for="saturday">{{ __('admin.manage.saturday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[saturday][start]" id="saturday_start">
                            <small class="text-danger error-text position-absolute" id="saturday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[saturday][end]" id="saturday_end">
                            <small class="text-danger error-text position-absolute" id="saturday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="form-check form-check-md form-switch me-3">
                        <input class="form-check-input working-day-checkbox" value="sunday" type="checkbox" role="switch" name="working_days[]" id="sunday">
                        <label class="form-check-label ms-2" for="sunday">{{ __('admin.manage.sunday') }}</label>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="position-relative">
                            <input type="time" class="form-control me-2" name="days[sunday][start]" id="sunday_start">
                            <small class="text-danger error-text position-absolute" id="sunday_start_error"></small>
                        </div>
                        <span class="mx-2">{{ __('admin.common.to') }}</span>
                        <div class="position-relative">
                            <input type="time" class="form-control ms-2" name="days[sunday][end]" id="sunday_end">
                            <small class="text-danger error-text position-absolute" id="sunday_end_error"></small>
                        </div>
                    </div>
                </div>
            </div>
		</x-slot>
		<x-slot name="footer">
			<div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2 d-none" id="status_div">
                    <label class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status" id="status">
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
    <!-- Add/Edit Location end -->

    <!-- Delete Modal Start-->
    <x-admin.delete-modal
		className="deletemodal"
		id="delete-modal"
		action="{{ route('delete_location') }}"
		formId="deleteLocation"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.manage.delete_location')"
		:description="__('admin.manage.delete_location_confirmation')">
	</x-admin.delete-modal>
    <!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/location.js') }}"></script>
@endpush