@extends('admin.admin')

@section('meta_title', __('admin.general_settings.system_backup') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
            __('admin.general_settings.settings') => ''
        ]" />
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
                                    <a href="javascript:void(0);" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#generate">{{ __('admin.general_settings.generate_backup') }}</a>
                                @endif
                            </div>
                            <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                @include('admin.content-loader')
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

    <!-- Generate Backup Modal -->
    <x-admin.modal className="deletemodal" id="generate" :title="__('admin.general_settings.generate_backup')" dialogClass="modal-dialog-centered modal-sm">
        <x-slot name="body">
            @csrf
            <div class="text-center">
                <span class="avatar avatar-lg bg-primary-transparent rounded-circle text-primary mb-3">
                    <i class="ti ti-folders fs-26"></i>
                </span>
                <h4 class="mb-1">{{ __('admin.general_settings.generate_backup') }}</h4>
                <p class="mb-3">{{ __('admin.general_settings.confirmation_generate_backup') }}</p>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                    {{ __('admin.general_settings.cancel') }}
                </button>
                <a href="{{ route('system-backup') }}" class="btn btn-primary">
                    {{ __('admin.general_settings.generate') }}
                </a>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Generate Backup Modal -->

    <!-- Delete  -->
    <x-admin.delete-modal className="deletemodal" id="delete_backup" formId="deleteSystemDbBackup"
        :hiddenInputs="['delete_id' => '']" :title="__('admin.general_settings.delete_backup')"
        :description="__('admin.general_settings.confirmation_delete_backup')"
        cancelText="{{ __('admin.general_settings.cancel') }}" submitText="{{ __('admin.general_settings.yes_delete') }}">
    </x-admin.delete-modal>

    <!-- /Delete -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/system-settings.js') }}"></script>
@endpush
