@extends('admin.admin')

@section('meta_title', __('admin.general_settings.bank_accounts') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 pb-0">
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
                            <h6 class="mb-3">{{ __('admin.general_settings.bank_accounts') }}</h6>
                            <!-- Table Header -->
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group">
                                            <span class="input-icon">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-2 me-2">
                                        @if (hasPermission($permissions, 'finance_settings', 'create'))
                                        <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_bank" id="bank_clear">
                                            <i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_account') }}
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- /Table Header -->
                            <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                @include('admin.content-loader')
                            </div>
                            <div class="custom-datatable-filter table-responsive brandstable country-table d-none real-table">
                                <table class="table datatable" id="bankTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('admin.general_settings.name') }}</th>
                                            <th>{{ __('admin.general_settings.bank') }}</th>
                                            <th>{{ __('admin.general_settings.branch') }}</th>
                                            <th>{{ __('admin.general_settings.account_number') }}</th>
                                            <th>{{ __('admin.general_settings.ifsc') }}</th>
                                            <th>{{ __('admin.general_settings.status') }}</th>
                                            @if (hasPermission($permissions, 'finance_settings', 'edit') || hasPermission($permissions, 'finance_settings', 'delete'))
                                            <th>{{ __('admin.common.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Custome Data Tabel -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Add bank -->
<div class="modal fade addmodal" id="add_bank">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0 modal-title">{{ __('admin.general_settings.add_bank_account') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="bankForm">
                @csrf
                <input type="hidden" name="id" id="id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.general_settings.bank_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" id="bank_name" class="form-control">
                        <span class="invalid-feedback" id="bank_name_error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.general_settings.account_number') }} <span class="text-danger">*</span></label>
                        <input type="text" name="account_number" id="account_number" class="form-control NumOnly">
                        <span class="invalid-feedback" id="account_number_error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.general_settings.account_holder_name') }} <span class="text-danger">*</span></label>
                        <input type="text" name="holder_name" id="holder_name" class="form-control">
                        <span class="invalid-feedback" id="holder_name_error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.general_settings.branch') }} <span class="text-danger">*</span></label>
                        <input type="text" name="branch" id="branch" class="form-control">
                        <span class="invalid-feedback" id="branch_error"></span>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ __('admin.general_settings.ifsc') }} <span class="text-danger">*</span></label>
                        <input type="text" name="ifsc" id="ifsc" class="form-control">
                        <span class="invalid-feedback" id="ifsc_error"></span>
                    </div>
                    <div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="default" id="flexCheckChecked">
                            <label class="form-check-label" for="flexCheckChecked">
                                {{ __('admin.general_settings.mark_as_default') }}
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                            {{ __('admin.general_settings.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.create_new') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add bank -->

<!-- Delete -->
<div class="modal fade" id="delete_bank">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form id="delateBank">
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.general_settings.delete_bank_account') }}</h4>
                    <p class="mb-3">{{ __('admin.general_settings.delete_bank_account_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                            {{ __('admin.general_settings.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.yes_delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Delete -->
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/bank.js') }}"></script>
@endpush
