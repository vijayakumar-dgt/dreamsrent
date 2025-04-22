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
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                    </div>
                    <form id="ai_configuration_form">
                        <div class="card-body pb-0">
                            <h6 class="fw-bold mb-3">{{ __('admin.general_settings.ai_configuration') }}</h6>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label mb-0 d-none real-label">{{ __('admin.general_settings.api_key') }}<span class="text-danger ms-1">*</span></label>
                                </div>
                                <div class="col-md-4">
                                    <div class="skeleton input-skeleton input-loader"></div>
                                    <input type="text" class="form-control d-none real-input" name="ai_api_key" id="ai_api_key">
                                    <span class="error-text text-danger" id="ai_api_key_error"></span>
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label mb-0 d-none real-label">{{ __('admin.general_settings.enable_ai_globally') }}</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <div class="form-check form-check-md form-switch d-none real-label">
                                        <input class="form-check-input form-label" type="checkbox" name="ai_global_status" id="ai_global_status" role="switch">
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label mb-0 d-none real-label">{{ __('admin.general_settings.enable_ai_admin') }}</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <div class="form-check form-check-md form-switch d-none real-label">
                                        <input class="form-check-input form-label" type="checkbox" role="switch" name="ai_admin_status" id="ai_admin_status">
                                    </div>
                                </div>
                            </div>
                            <div class="row align-items-center mb-3">
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <label class="form-label mb-0 d-none real-label">{{ __('admin.general_settings.enable_ai_user') }}</label>
                                </div>
                                <div class="col-md-4">
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <div class="form-check form-check-md form-switch d-none real-label">
                                        <input class="form-check-input form-label" type="checkbox" role="switch" name="ai_user_status" id="ai_user_status">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-end">
                                <div class="skeleton label-skeleton label-loader me-2"></div>
                                <a href="javascript:void(0);" class="btn btn-light me-3 d-none real-label" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                                @if (hasPermission($permissions, 'website_settings', 'edit'))

                                <div class="skeleton label-skeleton label-loader"></div>
                                <button type="submit" class="btn btn-primary submitBtn d-none real-label">{{ __('admin.common.save_changes') }}</button>
                            @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Settings Prefix -->
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/general_setting/ai-configuration.js') }}"></script>
@endpush
