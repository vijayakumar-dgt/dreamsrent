@extends('admin.admin')

@section('meta_title', __('admin.common.users') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb 
                :title="__('admin.common.users')" 
                :breadcrumbs="[
                    __('admin.common.users') => ''
                ]"
                :buttonText="__('admin.user_management.add_new_user')"
                :modalId="'add_user_modal'"
                :buttonId="'add_user'"
                :permissionModule="'users'"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <input type="hidden" name="sort_by_input" id="sort_by_input">
                    <div class="dropdown me-2">
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
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{ __('admin.common.filters') }}</h6>
                    <div class="dropdown me-2">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            {{ __('admin.user_management.roles') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg p-2" id="role_list">
                            <li>
                                <div class="top-search m-2">
                                    <div class="top-search-group">
                                        <span class="input-icon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                                    </div>
                                </div>
                            </li>
                            <div class="custom-scroll">
                                @if ($roles)
                                    @foreach ($roles as $role)
                                        <li>
                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                <input class="form-check-input m-0 me-2 role_checkbox" type="checkbox" value="{{ $role->id }}">{{ $role->role_name }}
                                            </label>
                                        </li>
                                    @endforeach
                                @else
                                    <li>
                                        <p class="text-center">{{ __('admin.common.no_data_found') }}</p>
                                    </li>
                                @endif
                            </div>
                        </ul>
                    </div>
                    <button type="button" class="text-purple links border-0 bg-transparent" id="apply_filter">{{ __('admin.common.apply') }}</button>
                    <button type="button" class="text-danger links border-0 bg-transparent" id="reset_filter">{{ __('admin.common.clear_all') }}</button>
                </div>
            </div>
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table class="table" id="userTable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.common.user')) }}</th>
                            <th>{{ strtoupper(__('admin.common.phone_number')) }}</th>
                            <th>{{ strtoupper(__('admin.common.email')) }}</th>
                            <th>{{ strtoupper(__('admin.user_management.roles')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                            @if (hasPermission($permissions, 'users', 'edit') || hasPermission($permissions, 'users', 'delete'))
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

    <!-- Add User -->
    <x-admin.modal 
		className="addmodal"
		id="add_user_modal"
        dialogClassName="modal-lg"
		:title="__('admin.user_management.create_user')"
		action="{{  route('admin.save-user') }}"
		formId="userForm"
		method="POST"
        enctype="multipart/form-data">
		<x-slot name="body">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
                    <div class="d-flex align-items-center flex-wrap row-gap-3">
                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
                            <img src="{{ uploadedAsset('', 'profile') }}" class="img-fluid rounded d-none" id="imagePreview" alt="Profile Image">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.first_name') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="first_name" id="first_name">
                        <span class="text-danger error-text" id="first_name_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.last_name') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="last_name" id="last_name">
                        <span class="text-danger error-text" id="last_name_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.user_management.role') }}<span class="text-danger"> *</span></label>
                        <select class="form-control role" name="role_id" id="role_id" data-placeholder="{{ __('admin.common.select') }}">
                            <option value="">{{ __('admin.common.select') }}</option>
                            @if ($roles)
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="text-danger error-text" id="role_id_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control user_phone_number" id="phone_number" name="phone_number">
                        <input type="hidden" id="international_phone_number" name="international_phone_number">
                        <span id="phone_number_error" class="text-danger error-text"></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
                        <input class="form-control" type="text" name="email" id="email">
                        <span id="email_error" class="text-danger error-text"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.password') }}<span class="text-danger"> *</span></label>
                        <div class="pass-group">
                            <input type="password" class="pass-inputs form-control" name="password" id="password">
                            <span class="ti toggle-passwords ti-eye-off"></span>
                        </div>
                        <span class="error-text text-danger" id="password_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.confirm_password') }}<span class="text-danger"> *</span></label>
                        <div class="pass-group">
                            <input type="password" class="form-control pass-inputa" name="confirm_password" id="confirm_password">
                            <span class="ti toggle-passworda ti-eye-off"></span>
                        </div>
                        <span class="error-text text-danger" id="confirm_password_error"></span>
                    </div>
                </div>
            </div>
		</x-slot>
		<x-slot name="footer">
			<div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
            </div>
		</x-slot>
	</x-admin.modal>
    <!-- /Add User -->

    <!-- Edit User -->
    <x-admin.modal 
		className="addmodal"
		id="edit_user_modal"
        dialogClassName="modal-lg"
		:title="__('admin.user_management.edit_user')"
		action="{{  route('admin.save-user') }}"
		formId="editUserForm"
		method="POST"
        enctype="multipart/form-data">
		<x-slot name="body">
            <input type="hidden" name="id" id="id">
            <div class="row">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.common.image') }}<span class="text-danger"> *</span></label>
                    <div class="d-flex align-items-center flex-wrap row-gap-3">
                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
                            <img src="{{ uploadedAsset('', 'profile') }}" class="img-fluid rounded d-none" id="editImagePreview" alt="Profile Image">
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
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.first_name') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="first_name" id="edit_first_name">
                        <span class="text-danger error-text" id="edit_first_name_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.last_name') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control" name="last_name" id="edit_last_name">
                        <span class="text-danger error-text" id="edit_last_name_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.user_management.role') }}<span class="text-danger"> *</span></label>
                        <select class="form-control edit_role" name="role_id" id="edit_role_id" data-placeholder="{{ __('admin.common.select') }}">
                            <option value="">{{ __('admin.common.select') }}</option>
                            @if ($roles)
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            @endif
                        </select>
                        <span class="text-danger error-text" id="edit_role_id_error"></span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.phone_number') }}<span class="text-danger"> *</span></label>
                        <input type="text" class="form-control edit_user_phone_number" id="edit_phone_number" name="phone_number">
                        <input type="hidden" id="edit_international_phone_number" name="international_phone_number">
                        <span id="edit_phone_number_error" class="text-danger error-text"></span>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.common.email') }}<span class="text-danger"> *</span></label>
                        <input class="form-control" type="text" name="email" id="edit_email">
                        <span id="edit_email_error" class="text-danger error-text"></span>
                    </div>
                </div>
            </div>
		</x-slot>
		<x-slot name="footer">
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
		</x-slot>
	</x-admin.modal>
    <!-- /Edit User -->

    <!-- Delete  -->
    <x-admin.delete-modal
		className="deletemodal"
		id="delete_modal"
		action="{{ route('admin.user-delete') }}"
		formId="deleteUserForm"
		method="POST"
		:hiddenInputs="['delete_id' => '']"
		:title="__('admin.user_management.delete_user')"
		:description="__('admin.user_management.delete_user_confirmation')">
	</x-admin.delete-modal>
    <!-- /Delete -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/user.js') }}"></script>
@endpush
