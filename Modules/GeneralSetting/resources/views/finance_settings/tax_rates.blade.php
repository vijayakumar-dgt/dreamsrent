@extends('admin.admin')

@section('meta_title', __('admin.general_settings.tax_rates') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <x-admin.breadcrumb
                :title="__('admin.general_settings.settings')"
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]"
            />
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.finance_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="payment-section">
                                <div class="border-bottom pb-3 first-table">
                                    <!-- Table Header -->
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                        <h6>{{ __('admin.general_settings.tax_rates') }}</h6>
                                        <div>
                                            <div>
                                                @if (hasPermission($permissions, 'finance_settings', 'create'))
                                                    <button type="button"
                                                        class="btn btn-primary d-flex align-items-center" id="add_tax_rate"
                                                        data-bs-toggle="modal" data-bs-target="#tax_rate_modal">
                                                        <i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_tax_rate') }}
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div
                                        class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                        @include('admin.content-loader')
                                    </div>
                                    <!-- Custom Data Table -->
                                    <div class="custom-datatable-filter table-responsive d-none real-table">
                                        <table class="table" id="taxRateTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ strtoupper(__('admin.general_settings.tax_name')) }}</th>
                                                    <th>{{ strtoupper(__('admin.general_settings.tax_rate')) }}</th>
                                                    <th>{{ strtoupper(__('admin.common.created_on')) }}</th>
                                                    @if (hasPermission($permissions, 'finance_settings', 'edit') || hasPermission($permissions, 'finance_settings', 'delete'))
                                                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Custome Data Tabel -->
                                    <div class="table-footer d-none"></div>
                                </div>
                                <div class="pt-3 second-table">
                                    <!-- Table Header -->
                                    <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                        <h6>{{ __('admin.general_settings.tax_group') }}</h6>
                                        <div>
                                            <div>
                                                @if (hasPermission($permissions, 'finance_settings', 'create'))
                                                    <a href="javascript:void(0);"
                                                        class="btn btn-primary d-flex align-items-center" id="add_tax_group"
                                                        data-bs-toggle="modal" data-bs-target="#tax_group_modal"><i
                                                            class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_tax_group') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Table Header -->
                                    <div
                                        class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                        @include('admin.content-loader')
                                    </div>
                                    <!-- Custom Data Table -->
                                    <div class="custom-datatable-filter table-responsive d-none real-table">
                                        <table class="table" id="taxGroupTable">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th>{{ strtoupper(__('admin.general_settings.tax_name')) }}</th>
                                                    <th>{{ strtoupper(__('admin.general_settings.tax_rate')) }}</th>
                                                    <th>{{ strtoupper(__('admin.common.created_on')) }}</th>
                                                    @if (hasPermission($permissions, 'finance_settings', 'edit') || hasPermission($permissions, 'finance_settings', 'delete'))
                                                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                    <!-- Custome Data Tabel -->
                                    <div class="table-footer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Tax Rate -->
    <x-admin.modal className="addmodal" id="tax_rate_modal" :title="__('admin.general_settings.create_tax_rate')"
        formId="tax_rate_form" dialogClass="modal-dialog-centered modal-md" autocomplete="off">
        <x-slot name="body">
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
                <label for="tax_name" class="form-label">{{ __('admin.general_settings.tax_name') }}<span class="text-danger">
                        *</span></label>
                <input type="text" class="form-control" name="tax_name" id="tax_name">
                <span class="error-text text-danger" id="tax_name_error"></span>
            </div>
            <div class="mb-3">
                <label for="tax_rate" class="form-label">{{ __('admin.general_settings.tax_rate') }} (%)<span class="text-danger">
                        *</span></label>
                <input type="text" class="form-control" name="tax_rate" id="tax_rate">
                <span class="error-text text-danger" id="tax_rate_error"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2 d-none statusDiv">
                    <label for="status" class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch"
                            name="status" aria-checked="false">
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3"
                        data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary submitBtn"
                        data-save="Save">{{ __('admin.common.create_new') }}</button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- Delete Tax Rate -->
    <x-admin.delete-modal :config="[
        'className'    => 'deletemodal',
        'id'           => 'delete_tax_rate',
        'formId'       => 'delete_tax_rate_form',
        'hiddenInputs' => ['id' => 'delete_tax_rate_id'],
        'title'        => __('admin.general_settings.delete_tax_rate'),
        'description'  => __('admin.general_settings.delete_tax_rate_confirmation')
    ]"/>

    <!-- Add Tax Group -->
    <x-admin.modal className="addmodal" id="tax_group_modal" :title="__('admin.general_settings.create_tax_group')"
        formId="tax_group_form" dialogClass="modal-dialog-centered modal-md" autocomplete="off">
        <x-slot name="body">
            <input type="hidden" name="tax_group_id" id="tax_group_id">
            <div class="mb-3">
                <label for="tax_group_name" class="form-label">{{ __('admin.general_settings.tax_group_name') }}<span class="text-danger">
                        *</span></label>
                <input type="text" class="form-control" name="tax_group_name" id="tax_group_name">
                <span class="error-text text-danger" id="tax_group_name_error"></span>
            </div>
            <div class="mb-2">
                <label for="sub_tax" class="form-label">{{ __('admin.general_settings.sub_taxes') }}<span class="text-danger">
                        *</span></label>
                <select class="form-control select2" name="sub_tax[]" id="sub_tax" multiple
                    data-placeholder="{{ __('Select') }}">
                </select>
                <span class="error-text text-danger" id="sub_tax_error"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2 d-none statusDiv">
                    <label for="group_status" class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2 status" id="group_status" type="checkbox"
                            role="switch" name="status" aria-checked="false">
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3"
                        data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                    <button type="submit" class="btn btn-primary submitBtn"
                        data-save="Save">{{ __('admin.common.create_new') }}</button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- Delete Tax Group -->
    <x-admin.delete-modal :config="[
        'className'    => 'deletemodal',
        'id'           => 'delete_tax_group',
        'formId'       => 'delete_tax_group_form',
        'hiddenInputs' => ['id' => 'delete_tax_group_id'],
        'title'        => __('admin.general_settings.delete_tax_group'),
        'description'  => __('admin.general_settings.delete_tax_group_confirmation')
    ]"/>

@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/tax-rates.js') }}"></script>
@endpush
