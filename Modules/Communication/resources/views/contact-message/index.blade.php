@extends('admin.admin')
@section('content')
	<!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.support.contact_messages') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.support.contact_messages') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->

            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="dropdown sort-dropdown me-2 d-none real-label">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center sort-dropdown-toggle"
                           data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1 sort"></i> {{ __('admin.common.sort_by_latest') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="latest">{{ __('admin.common.latest') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="ascending">{{ __('admin.common.ascending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="descending">{{ __('admin.common.descending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="last_month">{{ __('admin.common.last_month') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="last_7_days">{{ __('admin.common.last_7_days') }}</a></li>
                        </ul>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="skeleton label-skeleton label-loader"></div>
                    <div class="top-search d-none real-label">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control search-input" placeholder="{{ __('admin.common.search') }}">
                        </div>
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
                            <th><div class="skeleton th-skeleton th-loader"></div></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><div class="skeleton data-skeleton data-loader"></div></td>
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
                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                        </tr>
                        <tr>
                            <td><div class="skeleton data-skeleton data-loader"></div></td>
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
                            <td><div class="skeleton data-skeleton data-loader"></div></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table id="contactTable" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.support.from')) }}</th>
                            <th>{{ strtoupper(__('admin.common.phone')) }}</th>
                            <th>{{ strtoupper(__('admin.common.email')) }}</th>
                            <th>{{ strtoupper(__('admin.support.created_date')) }}</th>
                            <th>{{ strtoupper(__('admin.support.message')) }}</th>
                            @if (hasPermission($permissions, 'contact_messages', 'delete'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
        	<!-- Delete  -->
	<div class="modal fade" id="delete_contact">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-body text-center">
                    <form id="contactDeleteForm">
				        <input type="hidden" name="delete_id" id="delete_id">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.support.delete_message') }}</h4>
                        <p class="mb-3">{{ __('admin.support.delete_message_description') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </form>
				</div>
			</div>
		</div>
	</div>
	<!-- /Delete -->

@endsection
@push('scripts')
<script src="{{ asset('assets/js/communication/contact.js') }}"></script>
@endpush
