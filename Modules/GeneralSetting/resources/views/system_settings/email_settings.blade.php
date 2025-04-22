@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 pb-0">

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

        <div class="row">
            @include('admin.partials.general_settings_side_menu')
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('admin.general_settings.system_settings') }}</h5>
                    </div>
                    <div class="card-body pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-3">{{ __('admin.general_settings.email_settings') }}</h6>
                            @if (hasPermission($permissions, 'system_settings', 'create'))

                            <div class="skeleton button-skeleton button-loader mb-3"></div>
                            <a href="#" class="btn btn-primary mb-3 d-none real-button" id="send_test_email_btn" data-bs-toggle="modal" data-bs-target="#testmail"><i class="ti ti-send me-1"></i>{{ __('admin.general_settings.send_test_email') }}</a>
                       @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6 d-flex">
                                <div class="card flex-fill card-loader">
                                    <div class="skeleton card-skeleton"></div>
                                </div>
                                <div class="card flex-fill d-none real-card">
                                    <div class="card-body">
                                    <div class="border-bottom mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg bg-light me-2 p-2">
                                                    <img src="/assets/img/settings/phpmail.svg" class="img-fluid" alt="img">
                                                </span>
                                                <p class="text-gray-9">{{ __('admin.general_settings.php_mailer') }}</p>
                                            </div>
                                            <span class="badge badge-dark-transparent text-gray-9 status-text phpmail-status-text">
                                                <i class="ti ti-point-filled text-danger"></i>
                                                {{ __('admin.general_settings.disconnected') }}
                                            </span>
                                        </div>
                                        <p class="fs-13 mb-3">{{ __('admin.general_settings.php_mailer_info') }}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <a href="javascript:coid(0);" class="fw-medium text-gray-9 d-flex align-items-center mb-0 configure-btn" data-bs-toggle="modal" data-bs-target="#phpmailersettings">
                                            <i class="ti ti-settings me-1"></i>
                                            {{ __('admin.general_settings.configure') }}
                                        </a>
                                        @endif
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-switch" type="checkbox" role="switch" name="phpmail" id="phpmail_status">
                                        </div>
                                        @endif
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex">
                                <div class="card flex-fill card-loader">
                                    <div class="skeleton card-skeleton"></div>
                                </div>
                                <div class="card flex-fill d-none real-card">
                                    <div class="card-body">
                                    <div class="border-bottom mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg bg-light me-2 p-2">
                                                    <img src="/assets/img/settings/smtp.svg" class="img-fluid" alt="img">
                                                </span>
                                                <p class="text-gray-9">{{ __('admin.general_settings.smtp') }}</p>
                                            </div>
                                            <span class="badge badge-dark-transparent text-gray-9 status-text smtp-status-text">
                                                <i class="ti ti-point-filled text-danger"></i>
                                                {{ __('admin.general_settings.disconnected') }}
                                            </span>
                                        </div>
                                        <p class="fs-13 mb-3">{{ __('admin.general_settings.smtp_info') }}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <a href="javascript:coid(0);" class="fw-medium text-gray-9 d-flex align-items-center mb-0 configure-btn" data-bs-toggle="modal" data-bs-target="#smtpsettings">
                                            <i class="ti ti-settings me-1"></i>
                                            {{ __('admin.general_settings.configure') }}
                                        </a>
                                        @endif
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-switch" type="checkbox" role="switch" name="smtp" id="smtp_status">
                                        </div>
                                        @endif
                                    </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 d-flex">
                                <div class="card flex-fill card-loader">
                                    <div class="skeleton card-skeleton"></div>
                                </div>
                                <div class="card flex-fill d-none real-card">
                                    <div class="card-body">
                                    <div class="border-bottom mb-3">
                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-lg bg-light me-2 p-2">
                                                    <img src="/assets/img/settings/sendgrid.svg" class="img-fluid" alt="img">
                                                </span>
                                                <p class="text-gray-9">{{ __('admin.general_settings.send_grid') }}</p>
                                            </div>
                                            <span class="badge badge-dark-transparent text-gray-9 status-text sendgrid-status-text">
                                                <i class="ti ti-point-filled text-danger"></i>
                                                {{ __('admin.general_settings.disconnected') }}
                                            </span>
                                        </div>
                                        <p class="fs-13 mb-3">{{ __('admin.general_settings.send_grid_info') }}</p>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <a href="javascript:coid(0);" class="fw-medium text-gray-9 d-flex align-items-center mb-0 configure-btn" data-bs-toggle="modal" data-bs-target="#sendgrid">
                                            <i class="ti ti-settings me-1"></i>
                                            {{ __('admin.general_settings.configure') }}
                                        </a>
                                        @endif
                                        @if (hasPermission($permissions, 'system_settings', 'edit'))

                                        <div class="form-check form-switch">
                                            <input class="form-check-input status-switch" type="checkbox" role="switch" name="sendgrid" id="sendgrid_status">
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
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

  <!-- Add php mailer -->
<div class="modal fade addmodal" id="phpmailersettings">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.php_mailer') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="php_mailer_form">
                <div class="modal-body pb-1">
                    <div class="row">

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.from_email_address') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="phpmail_from_email" id="phpmail_from_email">
                                <span class="error-text text-danger" id="phpmail_from_email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.email_password') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="password" name="phpmail_password" id="phpmail_password">
                                <span class="error-text text-danger" id="phpmail_password_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.from_email_name') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="phpmail_from_name" id="phpmail_from_name">
                                <span class="error-text text-danger" id="phpmail_from_name_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary submitBtn" data-submit="{{ __('submit') }}">{{ __('admin.common.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add php mailer -->

<!-- Add Testmail -->
<div class="modal fade addmodal" id="testmail">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.send_test_email') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="test_mail_form">
                <div class="modal-body pb-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.email_address') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="email_address" id="email_address">
                                <span class="error-text text-danger" id="email_address_error"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary submitBtn">{{ __('admin.common.send') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add Testmail -->

<!-- Add smtp -->
<div class="modal fade addmodal" id="smtpsettings">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.smtp') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="smtp_form">
                <div class="modal-body pb-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.from_email_address') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="smtp_from_email" id="smtp_from_email">
                                <span class="error-text text-danger" id="smtp_from_email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.email_password') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="password" name="smtp_password" id="smtp_password">
                                <span class="error-text text-danger" id="smtp_password_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.from_email_name') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="smtp_from_name" id="smtp_from_name">
                                <span class="error-text text-danger" id="smtp_from_name_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.email_host') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="smtp_host" id="smtp_host">
                                <span class="error-text text-danger" id="smtp_host_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.port') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="smtp_port" id="smtp_port">
                                <span class="error-text text-danger" id="smtp_port_error"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary submitBtn">{{ __('admin.common.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add smtp -->

<!-- Add sendgrid -->
<div class="modal fade addmodal" id="sendgrid">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.send_grid') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <form id="sendgrid_form">
                <div class="modal-body pb-1">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.from_email_address') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="sendgrid_from_email" id="sendgrid_from_email">
                                <span class="error-text text-danger" id="sendgrid_from_email_error"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label">{{ __('admin.general_settings.sendgrid_key') }}<span class="text-danger"> *</span></label>
                                <input class="form-control" type="text" name="sendgrid_key" id="sendgrid_key">
                                <span class="error-text text-danger" id="sendgrid_key_error"></span>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary submitBtn">{{ __('admin.common.submit') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Add sendgrid -->

@endsection

@push('scripts')
    <script src="{{ asset('assets/js/general_setting/email-settings.js') }}"></script>
@endpush
