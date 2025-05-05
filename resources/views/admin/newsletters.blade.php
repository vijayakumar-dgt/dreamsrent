@extends('admin.admin')

@section('meta_title', __('admin.others.newsletters') . ' || ' . $companyName)

@section('content')
	<!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-4">
			<!-- Breadcrumb -->
			<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
				<div class="my-auto mb-2">
					<h4 class="mb-1">{{ __('admin.others.newsletters') }}</h4>
					<nav>
						<ol class="breadcrumb mb-0">
							<li class="breadcrumb-item">
								<a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
							</li>
							<li class="breadcrumb-item active" aria-current="page">{{ __('admin.others.newsletters') }}</li>
						</ol>
					</nav>
				</div>
				<div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
					<div class="mb-2">
						@if (hasPermission($permissions, 'newsletters', 'edit'))
						<div class="skeleton label-skeleton label-loader"></div>
						<a href="javascript:void(0);" id="send_newsletter" class="btn btn-primary d-flex align-items-center d-none real-label"><i class="ti ti-mail me-2"></i>{{ __('admin.others.send_newsletter') }}</a>
						@endif
					</div>
				</div>
			</div>
			<!-- /Breadcrumb -->
			<!-- Table Header -->
			<div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
				<div class="d-flex align-items-center flex-wrap row-gap-3">
					<div class="skeleton label-skeleton label-loader me-2"></div>
					<div class="dropdown me-2 d-none real-label">
						<a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
							<i class="ti ti-filter me-1"></i> {{ __('admin.common.sort_by') }} : <span class="ms-1" id="current_sort">{{ __('admin.common.latest') }}</span>
						</a>
						<ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="latest">{{ __('admin.common.latest') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="ascending">{{ __('admin.common.ascending') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="descending">{{ __('admin.common.descending') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last month">{{ __('admin.common.last_month') }}</a>
							</li>
							<li>
								<a href="javascript:void(0);" class="dropdown-item rounded-1" data-sort="last 7 days">{{ __('admin.common.last_days', ['no' => 7]) }}</a>
							</li>
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
							<input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
						</div>
					</div>
				</div>
			</div>
			<!-- /Table Header -->
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
						</tr>
					</tbody>
				</table>
			</div>
			<!-- Custom Data Table -->
			<div class="custom-datatable-filter table-responsive d-none real-table">
				<table class="table" id="newsletterTable">
					<thead class="thead-light">
						<tr>
							<th class="no-sort">
								<div class="form-check form-check-md">
									<input class="form-check-input" type="checkbox" id="select-all">
								</div>
							</th>
							<th>{{ ucfirst(__('admin.common.email')) }}</th>
							<th>{{ ucfirst(__('admin.common.date')) }}</th>
							@if (hasPermission($permissions, 'newsletters', 'delete'))
							<th>{{ ucfirst(__('admin.common.action')) }}</th>
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
	<div class="modal fade" id="delete_modal">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<form id="newsletterDeleteForm">
					<input type="hidden" name="delete_id" id="delete_id">
					<div class="modal-body text-center">
						<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
							<i class="ti ti-trash-x fs-26"></i>
						</span>
						<h4 class="mb-1">{{ __('admin.others.delete_newsletter') }}</h4>
						<p class="mb-3">{{ __('admin.others.delete_newsletter_confirmation') }}</p>
						<div class="d-flex justify-content-center">
							<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
							<button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- /Delete -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/others/newsletter.js') }}"></script>
@endpush