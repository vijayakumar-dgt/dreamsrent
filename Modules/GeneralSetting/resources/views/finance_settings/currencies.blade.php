@extends('admin.admin')

@section('content')
<div class="page-wrapper">
    <div class="content">

         <!-- Breadcrumb -->
         <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
            <div class="my-auto mb-2">
                <h2 class="mb-1">{{ __('admin.general_settings.payment') }}</h2>
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.payment') }}</li>
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
                            
                                <!-- Table Header -->
                                <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                    <h6>{{ __('admin.general_settings.currencies') }}</h6>
                                    <div>
                                        <div>
                                            @if (hasPermission($permissions, 'finance_settings', 'create'))

                                            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" id="add_new_currency" data-bs-toggle="modal" data-bs-target="#add_currency"><i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_currency') }}</a>
                                       @endif
                                        </div>
                                    </div>
                                </div>
                                <!-- /Table Header -->

                                <div class="custom-datatable-filter table-responsive table-loader">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th><div class="skeleton th-skeleton th-loader"></div></th>
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
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            </tr>
                                            <tr>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                                <td><div class="skeleton data-skeleton data-loader"></div></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Custom Data Table -->
                                <div class="custom-datatable-filter table-responsive brandstable country-table d-none real-table">
                                    <table class="table" id="currencyTable">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>{{ strtoupper(__('admin.general_settings.currency')) }}</th>
                                                <th>{{ strtoupper(__('admin.general_settings.code')) }}</th>
                                                <th>{{ strtoupper(__('admin.general_settings.symbol')) }}</th>
                                                <th>{{ strtoupper(__('admin.general_settings.exchange_rate')) }}</th>
                                                <th>{{ strtoupper(__('admin.general_settings.status')) }}</th>
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
                    </div>
                </div>
            </div>
        </div>
                        
        </div>			
    
    @include('admin.partials.footer')
</div>

<!-- Add Currency -->
<div class="modal fade addmodal" id="add_currency">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <form action="" id="currencyForm">
                @csrf
                <input type="hidden" name="id" id="id">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.add_currency') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.general_settings.currency_name') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="currency_name" id="currency_name">
                    <span id="currency_name_error" class="text-danger error-text"></span>
                </div>                     
                <div class="mb-3">
                    <label class="form-label">{{ __('admin.general_settings.exchange_rate') }} <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" name="exchange_rate" id="exchange_rate">
                    <span id="exchange_rate_error" class="text-danger error-text"></span>
                </div>     
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.code') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="code" id="code">
                            <span id="code_error" class="text-danger error-text"></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.symbol') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="symbol" id="symbol">
                            <span id="symbol_error" class="text-danger error-text"></span>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-between align-items-center w-100" id="modalfootdiv">
                    <div class="form-check form-check-md form-switch me-2 d-none" id="status_div">
                        <label class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status" id="status">
                            Status
                        </label>
                    </div>
                    <div class="d-flex justify-content-center">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.create_new') }}</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    </div>
</div>
<!-- /Add Currency -->
<!-- Delete Currency  -->
<div class="modal fade deletemodal" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="" id="deleteCurrencyForm">
                @csrf
                <input type="hidden" name="id" id="delete_id">
            <div class="modal-body text-center">
                <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                    <i class="ti ti-trash-x fs-26"></i>
                </span>
                <h4 class="mb-1">{{ __('admin.general_settings.delete_currency') }}</h4>
                <p class="mb-3">{{ __('admin.general_settings.delete_currency_confirmation') }}</p>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                    <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.yes_delete') }}</button>
                </div>
            </div>
          </form>
        </div>
    </div>
</div>
 <!-- /Delete Currency -->
@endsection
@push('scripts')
<script src="{{ asset('assets/js/general_setting/currencies.js') }}"></script>
@endpush