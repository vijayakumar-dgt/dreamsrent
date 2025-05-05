@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        
        <!-- Breadcrumb -->
            <div class="my-auto mb-3 pb-1">
                <a href="{{ route('admin.roles-permisions') }}" class="mb-1 text-gray-9 fw-medium"><i class="ti ti-arrow-left me-1"></i>{{ __('admin.common.back_to_list') }}</a>
            </div>
        <!-- /Breadcrumb -->

        <div class="filterbox mb-3 d-flex align-items-center mb-3">
            <span class="avatar avatar-lg bg-white text-secondary rounded-2 me-2">
                <i class="ti ti-user-shield fs-25 fw-normal"></i>
            </span>
            <div>
                <p class="mb-0">{{ __('admin.user_management.role') }}</p>
                <h6 class="fw-medium">{{ $role->role_name }}</h6>
            </div>
        </div>

        <!-- Custom Data Table -->
        @if (!empty($modules) && count($modules) > 0)
        <form id="permissionForm">
            <input type="hidden" name="role_id" id="role_id" value="{{ $role->id }}">
            @foreach ($modules as $parent)
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6>{{ $parent->module_name }}</h6>
                            <div class="no-sort">
                                <div class="form-check form-check-md">
                                    <input class="form-check-input select_all_permission" type="checkbox" id="select-all{{ $parent->id }}" data-parent_module="{{ $parent->module_slug }}{{ $parent->id }}">
                                    <label class="form-check-label" for="select-all{{ $parent->id }}">{{ __('admin.common.allow_all') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="custom-datatable-filter table-responsive">
                            <table class="table">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ strtoupper(__('admin.common.module')) }}</th>
                                        <th>{{ strtoupper(__('admin.common.create')) }}</th>
                                        <th>{{ strtoupper(__('admin.common.edit')) }}</th>
                                        <th>{{ strtoupper(__('admin.common.delete')) }}</th>
                                        <th>{{ strtoupper(__('admin.common.view')) }}</th>
                                        <th>{{ strtoupper(__('admin.common.allow_all')) }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($parent->childModules as $child)
                                    <tr>
                                        @if ($child->permissions->isEmpty())
                                            <td data-permission_id="" data-module_id="{{ $child->id }}">
                                                <p class="text-gray-9 fw-medium">{{ $child->module_name }}</p>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input permission-checkbox perm-create {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input permission-checkbox perm-edit {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input permission-checkbox perm-delete {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input permission-checkbox perm-view {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox">
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-check form-check-md">
                                                    <input class="form-check-input permission-checkbox perm-allow-all {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox">
                                                </div>
                                            </td>
                                        @else
                                            @foreach ($child->permissions as $permission)
                                                <td data-permission_id="{{ $permission->id }}" data-module_id="{{ $child->id }}">
                                                    <p class="text-gray-9 fw-medium">{{ $child->module_name }}</p>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input permission-checkbox perm-create {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox" {{ $permission->create == 1 ? 'checked' : '' }} value="{{ $permission->create }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input permission-checkbox perm-edit {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox" {{ $permission->edit == 1 ? 'checked' : '' }} value="{{ $permission->edit }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input permission-checkbox perm-delete {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox" {{ $permission->delete == 1 ? 'checked' : '' }} value="{{ $permission->delete }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input permission-checkbox perm-view {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox" {{ $permission->view == 1 ? 'checked' : '' }} value="{{ $permission->view }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="form-check form-check-md">
                                                        <input class="form-check-input permission-checkbox perm-allow-all {{ $parent->module_slug }}{{ $parent->id }}" type="checkbox" {{ $permission->allow_all == 1 ? 'checked' : '' }} value="{{ $permission->allow_all }}">
                                                    </div>
                                                </td>
                                            @endforeach
                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>	
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
            <div class="card mb-0">
                <div class="card-body py-2 my-1">
                    <div class="d-flex justify-content-end align-items-center">
                        <a href="{{ route('admin.roles-permisions') }}" class="btn btn-light me-2">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary me-2 submitbtn">{{ __('admin.common.submit') }}</button>
                    </div>
                </div>
            </div>
        </form>
        @else
            <p>{{ __('admin.user_management.no_permission_available') }}</p>
        @endif
        <!-- Custom Data Table -->
        <div class="table-footer"></div>			
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/permissions.js') }}"></script>
@endpush