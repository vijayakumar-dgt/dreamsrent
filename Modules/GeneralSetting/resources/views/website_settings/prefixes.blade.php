@extends('admin.admin')

@section('content')
    <!-- Page Wrapper -->
		<div class="page-wrapper">
			<div class="content me-0 me-md-0 me-lg-4">

                <!-- Breadcrumb -->
				<div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
					<div class="my-auto mb-2">
						<h4 class="mb-1">{{ __('admin.general_settings.settings') }}</h4>
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
					<div class="col-lg-9">
                        <form id="prefixesSettingForm" autocomplete="off">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.prefixes') }}</h6>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="reservation_prefix">{{ __('admin.common.reservations') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="reservation_prefix" id="reservation_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="reservation_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="quotation_prefix">{{ __('admin.common.quotations') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="quotation_prefix" id="quotation_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="quotation_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="enquiry_prefix">{{ __('admin.common.enquiries') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="enquiry_prefix" id="enquiry_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="enquiry_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="company_prefix">{{ __('admin.common.companies') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="company_prefix" id="company_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="company_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="inspection_prefix">{{ __('admin.common.inspections') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="inspection_prefix" id="inspection_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="inspection_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="invoice_prefix">{{ __('admin.common.invoice') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="invoice_prefix" id="invoice_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="invoice_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="report_prefix">{{ __('admin.common.reports') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="report_prefix" id="report_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="report_prefix_error"></span>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="skeleton label-skeleton label-loader"></div>
                                                <label class="form-label d-none real-label" for="customer_prefix">{{ __('admin.common.customers') }}<span class="text-danger"> *</span></label>
                                                <div class="skeleton input-skeleton input-loader"></div>
                                                <input type="text" name="customer_prefix" id="customer_prefix" class="form-control d-none real-input" placeholder="{{ __('admin.general_settings.enter_prefix') }}" maxlength="10">
                                                <span class="text-danger error-text" id="customer_prefix_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer">
                                    <div class="d-flex justify-content-end">
                                        <div class="skeleton label-skeleton label-loader me-2"></div>
                                        <a href="{{ route('dashboard') }}" class="btn btn-light me-3 d-none real-label" >{{ __('admin.common.cancel') }}</a>
                                        @if (hasPermission($permissions, 'website_settings', 'edit'))

                                        <div class="skeleton label-skeleton label-loader"></div>
                                        <button type="submit" class="btn btn-primary submitBtn d-none real-label">{{ __('admin.common.save_changes') }}</button>
                                   @endif
                                    </div>
                                </div>
                            </div>
                        </form>
					</div>
				</div>
                <!-- /Settings Prefix -->
            </div>
            @include('admin.partials.footer')
		</div>
		<!-- /Page Wrapper -->
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/general_setting/prefixes-setting.js') }}"></script>
@endpush










