@extends('admin.admin')

@section('meta_title', __('admin.general_settings.tax_rates') . ' || ' . $companyName)

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
                                                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="add_tax_rate" data-bs-toggle="modal" data-bs-target="#tax_rate_modal"><i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_tax_rate') }}</a>
                                            @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
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
                                                <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="add_tax_group" data-bs-toggle="modal" data-bs-target="#tax_group_modal"><i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_tax_group') }}</a>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- /Table Header -->
                                    <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
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
    <div class="modal fade addmodal" id="tax_rate_modal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0 modal-title">{{ __('admin.general_settings.create_tax_rate') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="tax_rate_form" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.tax_name') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="tax_name" id="tax_name">
                            <span class="error-text text-danger" id="tax_name_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.tax_rate') }} (%)<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="tax_rate" id="tax_rate">
                            <span class="error-text text-danger" id="tax_rate_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="form-check form-check-md form-switch me-2 d-none statusDiv">
                                <label for="status" class="form-check-label form-label mt-0 mb-0">
                                <input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch">
                                {{ __('admin.common.status') }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                                <button type="submit" class="btn btn-primary submitBtn" data-save="Save">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Tax Rate -->

    <!-- Delete Rate  -->
    <div class="modal fade" id="delete_tax_rate">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form id="delete_tax_rate_form">
                <input type="hidden" name="id" id="delete_tax_rate_id">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.delete_tax_rate') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.delete_tax_rate_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Delete Rate -->

    <!-- Add Tax Group -->
    <div class="modal fade addmodal" id="tax_group_modal">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0 modal-title">{{ __('admin.general_settings.create_tax_group') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="tax_group_form" autocomplete="off">
                    <input type="hidden" name="tax_group_id" id="tax_group_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.tax_group_name') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="tax_group_name" id="tax_group_name">
                            <span class="error-text text-danger" id="tax_group_name_error"></span>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">{{ __('admin.general_settings.sub_taxes') }}<span class="text-danger"> *</span></label>
                            <select type="text" class="form-control select2" name="sub_tax[]" id="sub_tax" multiple data-placeholder="{{ __('Select') }}">
                            </select>
                            <span class="error-text text-danger" id="sub_tax_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="form-check form-check-md form-switch me-2 d-none statusDiv">
                                <label for="group_status" class="form-check-label form-label mt-0 mb-0">
                                <input class="form-check-input form-label me-2 status" id="group_status" type="checkbox" role="switch">
                                {{ __('admin.common.status') }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                                <button type="submit" class="btn btn-primary submitBtn" data-save="Save">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add Tax Group -->

    <!-- Delete Rate  -->
    <div class="modal fade" id="delete_tax_group">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form id="delete_tax_group_form">
                <div class="modal-content">
                    <input type="hidden" name="id" id="delete_tax_group_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.delete_tax_group') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.delete_tax_group_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- /Delete Rate -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/tax-rates.js') }}"></script>
@endpush
