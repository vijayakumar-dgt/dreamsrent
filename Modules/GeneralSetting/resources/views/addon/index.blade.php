@extends('admin.admin')

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4 pb-0">

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
                        <h5>{{ __('admin.general_settings.website_settings') }}</h5>
                    </div>
                    <div class="card-body pb-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h6 class="mb-3">{{ __('admin.general_settings.plugin_managers') }}</h6>
                            @if (hasPermission($permissions, 'website_settings', 'create'))

                            <a href="javascript:void(0);" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_plugin"><i class="ti ti-plus me-1"></i>{{ __('admin.general_settings.add_new_plugin') }}</a>
                           @endif
                        </div>
                        <div class="plugin-content">
                            <div class="row">
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="plugin-icons me-2">
                                                        <span><img src="/backend/assets/img/icons/paypal-icons.svg" alt="image" class="img-fluid"></span>
                                                    </div>
                                                    <h6 class="fw-normal fs-13">{{ __('admin.general_settings.google') }}</h6>
                                                </div>
                                                <span class="badge badge-soft-purple d-inline-flex align-items-center">{{ __('admin.general_settings.paypal_version') }}</span>
                                            </div>
                                            <p class="fs-13">{{ __('admin.general_settings.paypal_description') }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if (hasPermission($permissions, 'website_settings', 'delete'))

                                                <span class="fs-14 text-gray-9 d-flex align-items-center"><i class="ti ti-trash me-1"></i>{{ __('admin.common.delete') }}</span>
                                                @endif
                                                @if (hasPermission($permissions, 'website_settings', 'edit'))

                                                <div class="form-check form-check-md form-switch me-2">
                                                    <input class="form-check-input form-label me-2" type="checkbox" role="switch" checked>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <div class="d-flex align-items-center">
                                                    <div class="plugin-icons  me-2">
                                                        <span><img src="/backend/assets/img/icons/google-analytics-icon.svg" alt="image" class="img-fluid"></span>
                                                    </div>
                                                    <h6 class="fw-normal fs-13">{{ __('admin.general_settings.google_analytics') }}</h6>
                                                </div>
                                                <span class="badge badge-soft-purple d-inline-flex align-items-center">{{ __('admin.general_settings.google_analytics_version') }}</span>
                                            </div>
                                            <p class="fs-13">{{ __('admin.general_settings.google_analytics_description') }}</p>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-flex justify-content-between align-items-center">
                                                @if (hasPermission($permissions, 'website_settings', 'delete'))

                                                <span class="fs-14 text-gray-9 d-flex align-items-center"><i class="ti ti-trash me-1"></i>{{ __('admin.common.delete') }}</span>
                                                @endif
                                                @if (hasPermission($permissions, 'website_settings', 'edit'))

                                                <div class="form-check form-check-md form-switch me-2">
                                                    <input class="form-check-input form-label me-2" type="checkbox" role="switch" checked>
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

    </div>

    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Add Plugin -->
<div class="modal fade addmodal" id="add_plugin">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.add_new_plugin') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="custom-datatable-filter table-responsive brandstable country-table">
                    <table class="table" id="currencyTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ __('admin.general_settings.select') }}</th>
                                <th>{{ __('admin.general_settings.image') }}</th>
                                <th>{{ __('admin.rentals.name') }}</th>
                                <th>{{ __('admin.general_settings.version') }}</th>
                                <th>{{ __('admin.general_settings.price') }}</th>
                                <th>{{ __('admin.general_settings.purchase_link') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($modules as $module)
                            <tr>
                                <td class="link">
                                    <input type="radio" name="selected_plugin" value="{{ $module['git_link'] }}">
                                </td>
                                <td>
                                    <img src="{{ $module['module_image'] }}" alt="{{ $module['module_name'] }}" width="50">
                                </td>
                                <td class="name">{{ $module['module_name'] }}</td>
                                <td class="version">{{ $module['module_version'] }}</td>
                                <td class="price">${{ number_format($module['module_price'], 2) }}</td>
                                <td>
                                    <a href="{{ $module['purchase_link'] }}" target="_blank" class="btn btn-sm btn-primary">{{ __('admin.general_settings.buy_now') }}</a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                    <button type="button" class="btn btn-primary install_btn">{{ __('admin.general_settings.add') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Add Plugin -->

<!-- Purchase_plugin -->
<div class="modal fade addmodal" id="purchase_plugin">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="mb-0">{{ __('admin.general_settings.purchase_plugin') }}</h4>
                <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <i class="ti ti-x fs-16"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="module_name" id="module_name">
                <input type="hidden" name="module_price" id="module_price">
                <input type="hidden" name="module_version" id="module_version">
                <input type="hidden" name="git_link" id="git_link">
                <label for="purchase_key">{{ __('admin.general_settings.purchase_key') }}</label>
                <input type="text" class="form-control" name="purchase_key" placeholder="Enter Purchase Key" id="purchase_key" required>
            </div>
            <div class="modal-footer">
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                    <button type="button" class="btn btn-primary purchase_confirm_btn">{{ __('admin.general_settings.install') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Purchase_plugin -->


@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/addon.js') }}"></script>
@endpush
