@extends('admin.admin')

@section('meta_title', __('admin.finance_accounts.invoices') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.finance_accounts.invoices') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.finance_accounts.invoices') }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    @if (hasPermission($permissions, 'invoices', 'create'))
                    <a href="{{ route('admin.addInvoice') }}" class="btn btn-primary d-inline-flex align-items-center">
                        <i class="ti ti-plus me-1"></i>{{ __('admin.finance_accounts.add_invoice') }}
                    </a>
                @endif
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" id="sortDropdownBtn" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by')}} : {{ __('admin.common.latest') }}
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="sort" data-value="latest">{{ __('admin.common.latest') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="sort" data-value="asc">{{ __('admin.common.ascending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="sort" data-value="desc">{{ __('admin.common.descending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="sort" data-value="last_month">{{ __('admin.common.last_month') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="sort" data-value="last_7_days">{{ __('admin.common.last_7_days') }}</a></li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <a href="javascript:void(0);" id="statusDropdownBtn" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i> {{ __('admin.common.status') }}
                        </a>
                        <ul class="dropdown-menu  dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="status" data-value="paid">{{ __('admin.finance_accounts.paid') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="status" data-value="pending">{{ __('admin.finance_accounts.pending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="status" data-value="overdue">{{ __('admin.finance_accounts.overdue') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-type="status" data-value="unpaid">{{ __('admin.finance_accounts.unpaid') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" id="searchInput" placeholder="{{ __('admin.common.search')}}">
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
                <table class="table" id="invoicesTable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.finance_accounts.invoice_no')) }}</th>
                            <th>{{ strtoupper(__('admin.common.name')) }}</th>
                            <th>{{ strtoupper(__('admin.common.email')) }}</th>
                            <th>{{ strtoupper(__('admin.finance_accounts.created_date')) }}</th>
                            <th>{{ strtoupper(__('admin.finance_accounts.due_date')) }}</th>
                            <th>{{ strtoupper(__('admin.common.amount')) }}</th>
                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                            @if (hasPermission($permissions, 'invoices', 'edit') || hasPermission($permissions, 'invoices', 'delete'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                        <tr data-created="{{ \Carbon\Carbon::parse($invoice->created_at)->format('Y-m-d') }}">
                            <td><a href="javascript:void(0);" class="fs-12 fw-medium">#{{$invoice->invoice_number}}</a></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="avatar avatar-rounded me-2 flex-shrink-0">
                                        <img src="{{ uploadedAsset($invoice->profile_image, 'profile') }}" alt="Image Preview">
                                    </a>
                                    <div>
                                        <h6 class="fs-14"><a href="javascript:void(0);">{{$invoice->full_name}}</a></h6>
                                    </div>
                                </div>
                            </td>
                            <td>{{$invoice->email}}</td>
                            <td>
                                <div>
                                    <p class="mb-0">{{ formatDateTime($invoice->created_at, false) }}</p>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <p class="mb-0">{{ formatDateTime($invoice->to_date, false) }}</p>
                                </div>
                            </td>
                            <td>${{$invoice->grand_total}}</td>
                            <td>
                                @if($invoice->status == "Paid")
                                <span class="badge badge-soft-success d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>
                                    {{__('admin.finance_accounts.paid')}}
                                </span>
                                @elseif ($invoice->status == "Pending")
                                <span class="badge badge-soft-info d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>
                                    {{__('admin.finance_accounts.pending')}}
                                </span>
                                @elseif ($invoice->status == "Overdue")
                                <span class="badge bg-violet-transparent d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>
                                    {{__('admin.finance_accounts.overdue')}}
                                </span>
                                @else
                                <span class="badge badge-soft-danger d-inline-flex align-items-center badge-sm">
                                    <i class="ti ti-point-filled me-1"></i>
                                    {{__('admin.finance_accounts.unpaid')}}
                                </span>
                                @endif
                            </td>
                            @if (hasPermission($permissions, 'invoices', 'edit') || hasPermission($permissions, 'invoices', 'delete'))
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end p-2">
                                        @if (hasPermission($permissions, 'invoices', 'edit'))
                                        <li>
                                           <a class="dropdown-item rounded-1 edit-invoice-btn" href="{{ route('invoices.edit', $invoice->id) }}" data-id="{{$invoice->id}}">
                                                <i class="ti ti-edit me-1"></i>{{ __('admin.common.edit') }}
                                            </a>
                                        </li>
                                        @endif
                                        @if (hasPermission($permissions, 'invoices', 'delete'))
                                        <li>
                                            <a class="dropdown-item rounded-1" id="delete-invoice-btn" href="javascript:void(0);" data-bs-toggle="modal" data-id="{{$invoice->id}}" data-bs-target="#delete_modal">
                                                <i class="ti ti-trash me-1"></i>{{ __('admin.common.delete') }}
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                            @endif
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Delete Modal  -->
    <div class="modal fade" id="delete_modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.finance_accounts.delete_invoice') }}</h4>
                    <p class="mb-3">{{ __('admin.finance_accounts.delete_invoice_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                        <button type="button" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Modal-->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/admin/invoice-index.js') }}"></script>
@endpush
