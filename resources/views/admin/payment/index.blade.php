@extends('admin.admin')

@section('meta_title', __('admin.finance_accounts.payments') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{__('admin.finance_accounts.payments')}}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{__('admin.common.home')}}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('admin.finance_accounts.payments')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="skeleton label-skeleton label-loader me-2"></div>
                    <div class="dropdown me-2 sortByClass d-none real-label">
                        <a href="javascript:void(0);" class="dropdown-toggle dropdown-toggles btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="selectedSort">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span id="currentSortText">{{ __('admin.common.latest') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option sort-optionss active" data-sort="latest">{{ __('admin.common.latest') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option sort-optionss" data-sort="asc">{{ __('admin.common.ascending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option sort-optionss" data-sort="desc">{{ __('admin.common.descending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option sort-optionss" data-sort="last_month">{{ __('admin.common.last_month') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option sort-optionss" data-sort="last_7_days">{{ __('admin.common.last_7_days') }}</a></li>
                        </ul>
                    </div>
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="dropdown d-none real-label">
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}<span class="badge badge-xs rounded-pill bg-danger ms-2">0</span>
                        </a>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="skeleton label-skeleton label-loader"></div>    
                    <div class="top-search me-2 d-none real-label">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search')}}">
                        </div>
                    </div>
                

                </div>
            </div>
            <!-- /Table Header -->
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{ __('admin.finance_accounts.payment_method') }}</h6>
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <span class="badge badge-xs rounded-pill bg-success me-2"></span>{{ __('admin.finance_accounts.payment_method') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg p-2 filyerPaymentType">
                            @foreach ($GetPayments as $payment)
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox" value="{{ $payment }}">
                                    {{ strtoupper(str_replace('_', ' ', $payment)) }}
                                </label>
                            </li>
                            @endforeach
                        </ul>

                    </div>
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                        {{ __('admin.common.status') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg p-2 filyerPaymentStatus">
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" value="2" type="checkbox">{{ __('admin.finance_accounts.completed') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" value="3" type="checkbox">{{ __('admin.finance_accounts.pending') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" value="1" type="checkbox">{{ __('admin.finance_accounts.open') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" value="4" type="checkbox">{{ __('admin.finance_accounts.closed') }}
                                </label>
                            </li>
                        </ul>
                    </div>
                    <a href="javascript:void(0);" class="me-2 text-purple links applyFilter">{{ __('admin.common.apply') }}</a>
                    <a href="javascript:void(0);" class="text-danger links clearFilter">{{ __('admin.common.clear') }}</a>
                </div>
            </div>
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table class="table datatable" id="paymentInfoData">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.finance_accounts.transaction_id')) }}</th>
                            <th>{{ strtoupper(__('admin.common.name')) }}</th>
                            <th>{{ strtoupper(__('admin.common.amount')) }}</th>
                            <th>{{ strtoupper(__('admin.finance_accounts.payment_method')) }}</th>
                            <th>{{ strtoupper(__('admin.common.date')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <div class="custom-datatable-filter table-responsive table-loader">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th class="text-center">
                                <div class="skeleton th-skeleton th-loader"></div>
                            </th>
                            <th>
                                <div class="skeleton th-skeleton th-loader"></div>
                            </th>
                            <th>
                                <div class="skeleton th-skeleton th-loader"></div>
                            </th>
                            <th>
                                <div class="skeleton th-skeleton th-loader"></div>
                            </th>
                            <th>
                                <div class="skeleton th-skeleton th-loader"></div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                            <td>
                                <div class="skeleton data-skeleton data-loader"></div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none real-label"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/payment.js') }}"></script>
@endpush