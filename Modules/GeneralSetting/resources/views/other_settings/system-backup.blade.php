@extends('admin.admin')

@section('meta_title', __('admin.general_settings.system_backup') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ __('admin.general_settings.settings') }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.settings') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.other_settings') }}</h5>
                        </div>
                        <div class="card-body ">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-3">{{ __('admin.general_settings.system_backup') }}</h6>
                                @if (hasPermission($permissions, 'other_settings', 'create'))
                                <div class="skeleton label-skeleton label-loader"></div>
                                <a href="javascript:void(0);" class="btn btn-primary d-none real-label" data-bs-toggle="modal" data-bs-target="#generate">{{ __('admin.general_settings.generate_backup') }}</a>
                                @endif
                            </div>
                            <div class="custom-datatable-filter table-responsive table-loader">
                                <table class="table">
                                    <thead>
                                        <tr>
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
                                        </tr>
                                        <tr>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        </tr>
                                        <tr>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        </tr>
                                        <tr>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="custom-datatable-filter table-responsive d-none real-table">
                                <table class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ strtoupper(__('admin.general_settings.file_name')) }}</th>
                                            <th>{{ strtoupper(__('admin.general_settings.created_on')) }}</th>
                                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="system-backup-list">
                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Generate  -->
    <div class="modal fade deletemodal" id="generate">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form action="">
                        <span class="avatar avatar-lg bg-primary-transparent rounded-circle text-primary mb-3">
                            <i class="ti ti-folders fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.generate_backup') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.confirmation_generate_backup') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <a href="{{ route('system-backup') }}" class="btn btn-primary">{{ __('admin.general_settings.generate') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Generate -->

    <!-- Delete  -->
    <div class="modal fade deletemodal" id="delete_backup">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="deleteSystemDbBackup">
                        <input type="hidden" name="delete_id" id="delete_id">

                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.delete_backup') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.confirmation_delete_backup') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.yes_delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/system-settings.js?v=1') }}"></script>
@endpush

