@extends('admin.admin')

@section('meta_title', __('admin.general_settings.clear_cache') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
	<div class="page-wrapper">
		<div class="content me-0 me-md-0 me-lg-4">
			<x-admin.breadcrumb 
                :title="__('admin.general_settings.settings')" 
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]" 
            />
			<!-- Settings Prefix -->
			<div class="row">
				@include('admin.partials.general_settings_side_menu')
				<div class="col-xl-9">
					<div class="card">
						<div class="card-header">
							<h5>{{ __('admin.general_settings.other_settings') }}</h5>
						</div>
						<div class="card-body">
							<div>
								<div>
									<h6 class="mb-3">{{ __('admin.general_settings.clear_cache') }}</h6>
									<P class="mb-3">{{ __('admin.general_settings.cache_description') }}</P>
									<a href="javascript:void(0);"  data-bs-toggle="modal" data-bs-target="#clear_cache" class="btn btn-primary">{{ __('admin.general_settings.clear_cache') }}</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- /Settings Prefix -->
		</div>
		@include('admin.partials.footer')
	</div>
	<!-- /Page Wrapper -->
	<div class="modal fade deletemodal" id="clear_cache">
		<div class="modal-dialog modal-dialog-centered modal-sm">
			<div class="modal-content">
				<div class="modal-body text-center">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">{{ __('admin.general_settings.clear_cache') }}</h4>
					<p class="mb-3">{{ __('admin.general_settings.want_to_clear_cache') }}</p>
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
						<button type="submit"  id="clear-cache" class="btn btn-primary">{{ __('admin.general_settings.yes_clear_cache') }}</button>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/clear-cache.js') }}"></script>
@endpush
