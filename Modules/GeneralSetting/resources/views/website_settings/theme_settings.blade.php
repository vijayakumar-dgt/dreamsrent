@extends('admin.admin')

@section('meta_title', __('admin.general_settings.theme_settings') . ' || ' . $companyName)

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
                                <a href="{{ route('dashboard') }}">{{ __('admin.general_settings.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.settings') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                        </div>
                        @include('admin.general_settings_loader')
                        <div class="card-body pb-0 d-none real-card">
                            <h6 class="fw-bold mb-3">{{ __('admin.general_settings.theme_settings') }}</h6>
                            <div class="row gx-3">
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <a href="javascript:void(0);">
                                                <img src="{{ asset('backend/assets/img/theme-01.svg') }}" alt="theme" class="theme-img" data-id="theme_01">
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <label class="form-check-label" for="theme_01" data-id="theme_01">
                                                    <span class="text-gray-9">{{ __('admin.general_settings.theme') }} 1</span>
                                                </label>
                                                <div class="form-check form-check-md">
                                                    <input type="radio" name="theme" id="theme_01" class="form-check-input default_theme" data-id="theme_01">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <a href="javascript:void(0);">
                                                <img src="{{ asset('backend/assets/img/theme-02.svg') }}" alt="theme" class="theme-img" data-id="theme_02">
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <label class="form-check-label" for="theme_02" data-id="theme_02">
                                                    <span class="text-gray-9">{{ __('admin.general_settings.theme') }} 2</span>
                                                </label>
                                                <div class="form-check form-check-md">
                                                    <input type="radio" name="theme" id="theme_02" class="form-check-input default_theme" data-id="theme_02">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <a href="javascript:void(0);">
                                                <img src="{{ asset('backend/assets/img/theme-03.svg') }}" alt="theme" class="theme-img" data-id="theme_03">
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <label class="form-check-label" for="theme_03" data-id="theme_03">
                                                    <span class="text-gray-9">{{ __('admin.general_settings.theme') }} 3</span>
                                                </label>
                                                <div class="form-check form-check-md">
                                                    <input type="radio" name="theme" id="theme_03" class="form-check-input default_theme" data-id="theme_03">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card">
                                        <div class="card-body p-2">
                                            <a href="javascript:void(0);">
                                                <img src="{{ asset('backend/assets/img/theme-04.svg') }}" alt="theme" class="theme-img" data-id="theme_04">
                                            </a>
                                            <div class="d-flex justify-content-between align-items-center mt-2">
                                                <label class="form-check-label" for="theme_04" data-id="theme_04">
                                                    <span class="text-gray-9">{{ __('admin.general_settings.theme') }} 4</span>
                                                </label>
                                                <div class="form-check form-check-md">
                                                    <input type="radio" name="theme" id="theme_04" class="form-check-input default_theme" data-id="theme_04">
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
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/theme-settings.js') }}"></script>
@endpush
