@extends('admin.admin')

@section('meta_title', __('admin.general_settings.language') . ' || ' . $companyName)

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
                <div class="col-lg-9">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <h6 class="fw-bold">{{ __('admin.general_settings.language') }}</h6>
                            </div>
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    <div class="top-search me-2">
                                        <div class="top-search-group">
                                            <span class="input-icon">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}" id="search">
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex align-items-center">
                                    <a href="{{ route('admin.languages') }}" class="btn btn-dark me-2"><i class="ti ti-arrow-left me-1"></i>{{ __('admin.general_settings.back_to_translation') }}</a>
                                    <a href="javascript:void(0);" class="btn btn-white" id="language" data-id="{{ $language->transLang->code ?? "" }}" data-tab="{{ $tab }}">
                                        <img src="{{ $flag }}" alt="img" class="avatar avatar-sm rounded-circle me-1">
                                        {{ $language->transLang->name ?? "" }}
                                    </a>
                                </div>
                            </div>
                            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                                @include('admin.content-loader')
                            </div>
                            <!-- Custom Data Table -->
                            <div class="custom-datatable-filter table-responsive d-none real-table">
                                <table class="table" id="languageDetailsTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('admin.general_settings.language') }}</th>
                                            <th>{{ __('admin.general_settings.total') }}</th>
                                            <th>{{ __('admin.general_settings.done') }}</th>
                                            <th>{{ __('admin.general_settings.progress') }}</th>
                                            <th>{{ __('admin.common.action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <!-- Custom Data Table -->
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <div class="modal language fade addmodal" id="language_setup">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">{{ __('admin.general_settings.language_setup') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                            <div class="top-search me-2">
                                <div class="top-search-group">
                                    <span class="input-icon">
                                        <i class="ti ti-search"></i>
                                    </span>
                                    <input type="text" class="form-control" placeholder="Search" id="keyword">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-2">
                            <a href="{{ route('admin.languages') }}" class="btn btn-dark me-2"><i class="ti ti-arrow-left me-1"></i>{{ __('admin.general_settings.back_to_translation') }}</a>
                            <a href="javascript:void(0);" class="btn btn-white me-2">
                                <img src="backend/assets/img/flags/uae.svg" alt="img" id="lngicon" class="avatar avatar-sm rounded-circle me-1">
                            <span class="lngTitile"> {{ __('admin.general_settings.arabic') }}</span>
                            </a>
                            <div class="progress-percent">
                                <span class="text-gray-9 fs-10">{{ __('admin.general_settings.progress') }}</span>
                                <div class="d-flex align-items-center">
                                    <div class="progress progress-xs">
                                        <div class="w-60" role="progressbar" id="modalProgressBar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <span class="d-inline-flex fs-12 ms-2 modalProgress">80%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Custom Data Table -->
                    <div class="custom-datatable-filter table-responsive">
                        <table class="table" id="languageSetupTable">
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('admin.general_settings.english') }}</th>
                                    <th class="langTitle">{{ __('admin.general_settings.arabic') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.reservation') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="التحفظات">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.calender') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="تقويم">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.quotation') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="الاقتباسات">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.enquires') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="الاستفسارات">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.units') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="الوحدات">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.people') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="الناس">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.companies') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="شركات">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.drivers') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="السائقين">
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        {{ __('admin.general_settings.locations') }}
                                    </td>
                                    <td>
                                        <input type="text" dir="rtl" class="form-control text-end" value="المواقع">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Custom Data Table -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/language_details.js') }}"></script>
@endpush
