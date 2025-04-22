@extends('admin.admin')

@section('content')
    <!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content me-0 me-md-0 me-lg-4">

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

                <!-- Settings Prefix -->
                <div class="row">
                    @include('admin.partials.general_settings_side_menu')
                    <div class="col-xl-9">
						<div class="card">
							<div class="card-header">
								<h5>{{ __('admin.general_settings.other_settings') }}</h5>
							</div>
                            <div class="card-body pb-0">
                                <div>
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.storage') }}</h6>
                                    <div class="row">
                                        <!-- Local Storage Card -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar avatar-lg bg-gray-100 me-2 flex-shrink-0">
                                                                <img src="/assets/img/icons/storage-icon-03.svg" class="w-auto h-auto" alt="Local Storage Icon">
                                                            </span>
                                                            <div>
                                                                <div class="skeleton label-skeleton label-loader"></div>
                                                                <h6 class="fw-medium fs-14 mb-0 d-none real-label">{{ __('admin.general_settings.local_storage') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="d-flex align-items-center">
                                                            @if (hasPermission($permissions, 'other_settings', 'edit'))

                                                            <div class="skeleton input-skeleton input-loader"></div>
                                                            <div class="form-check form-check-md form-switch d-none real-label">
                                                                <input class="form-check-input me-2" id="local_storage" name="local_storage" type="checkbox" role="switch">
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- AWS Storage Card -->
                                        <div class="col-md-6">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <span class="avatar avatar-lg bg-gray-100 me-2 flex-shrink-0">
                                                                <img src="/assets/img/icons/aws.svg" class="w-auto h-auto" alt="AWS Icon">
                                                            </span>
                                                            <div>
                                                                <div class="skeleton label-skeleton label-loader"></div>
                                                                <h6 class="fw-medium fs-14 mb-0 d-none real-label">{{ __('admin.general_settings.aws') }}</h6>
                                                            </div>
                                                        </div>
                                                        @if (hasPermission($permissions, 'other_settings', 'edit'))

                                                        <div class="d-flex align-items-center">
                                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                                            <a href="#" class="btn btn-icon btn-sm me-2 d-none real-label" data-bs-toggle="modal" data-bs-target="#aws_settings">
                                                                <i class="ti ti-settings fs-20"></i>
                                                            </a>
                                                            <div class="skeleton input-skeleton input-loader"></div>
                                                            <div class="form-check form-check-md form-switch d-none real-label">
                                                                <input class="form-check-input me-2" id="aws_storage" name="aws_storage" type="checkbox" role="switch">
                                                            </div>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
        <!--Add Cronjob -->
		<div class="modal fade addmodal" id="aws_settings">
			<div class="modal-dialog modal-dialog-centered modal-md">
				<div class="modal-content">
                    <form id="awsSettingForm">
                        <div class="modal-header">
                            <h4 class="mb-0">{{ __('admin.general_settings.aws_settings') }}</h4>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ti ti-x fs-16"></i>
                            </button>
                        </div>
                        <div class="modal-body pb-1">
                            <div class="row">
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="8" >
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_access_key" class="form-label">{{ __('admin.general_settings.aws_access_key') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="aws_access_key" name="aws_access_key" class="form-control" placeholder="Enter AWS Access Key">
                                        <span id="aws_access_key_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_secret_key" class="form-label">{{ __('admin.general_settings.secret_key') }}  <span class="text-danger">*</span></label>
                                        <input type="text" id="aws_secret_key" name="aws_secret_key" class="form-control" placeholder="Enter AWS Secret Key">
                                        <span id="aws_secret_key_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_bucket_name" class="form-label">{{ __('admin.general_settings.bucket_name') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="aws_bucket_name" name="aws_bucket_name" class="form-control" placeholder="Enter Bucket Name">
                                        <span id="aws_bucket_name_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_region" class="form-label">{{ __('admin.general_settings.region') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="aws_region" name="aws_region" class="form-control" placeholder="Enter AWS Region">
                                        <span id="aws_region_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="aws_base_url" class="form-label">{{ __('admin.general_settings.base_url') }} <span class="text-danger">*</span></label>
                                        <input type="text" id="aws_base_url" name="aws_base_url" class="form-control" placeholder="Enter Base URL">
                                        <span id="aws_base_url_error" class="text-danger error-text"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.submit') }}</button>
                        </div>
                    </form>
				</div>
			</div>
		</div>
		<!-- /Add Cronjob -->
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/general_setting/storage-setting.js') }}"></script>
@endpush










