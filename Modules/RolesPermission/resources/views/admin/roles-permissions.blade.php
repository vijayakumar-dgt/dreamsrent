@extends('admin.admin')

@section('meta_title', __('admin.user_management.roles') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb
                :title="__('admin.user_management.roles')"
                :breadcrumbs="[
                    __('admin.user_management.roles') => ''
                ]"
                :buttonText="__('admin.user_management.add_new_role')"
                :modalId="'role_modal'"
                :buttonId="'add_role'"
                :permissionModule="'roles_permissions'"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-end flex-wrap row-gap-3 mb-3">
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}" aria-label="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
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

    <!-- Add/Edit Role -->
    <x-admin.modal
        className="addmodal"
        id="role_modal"
        :title="__('admin.user_management.create_role')"
        action="{{ route('admin.role.store') }}"
        formId="roleForm"
        method="POST">
        <x-slot name="body">
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
                <label class="form-label" for="role">{{ __('admin.user_management.role') }}<span class="text-danger"> *</span></label>
                <input type="text" class="form-control" name="role" id="role" aria-describedby="role_error" required>
                <span class="error-text text-danger" id="role_error" role="alert"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
                    <label for="status" class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2 status" id="status" name="status" type="checkbox" role="switch" checked aria-checked="true">
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal" aria-label="{{ __('admin.common.cancel') }}">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary submitbtn" aria-label="{{ __('admin.common.create_new') }}">{{ __('admin.common.create_new') }}</button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Add/Edit Role -->

    <!-- Delete  -->
    <x-admin.delete-modal
        className="deletemodal"
        id="delete_role"
        action="{{ route('admin.role.delete') }}"
        formId="roleDeleteForm"
        method="POST"
        :hiddenInputs="['delete_id' => '']"
        :title="__('admin.user_management.delete_role')"
        :description="__('admin.user_management.delete_role_confirmation')">
    </x-admin.delete-modal>
    <!-- /Delete -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/roles-permissions.js') }}"></script>
@endpush
