@extends('admin.admin')

@section('meta_title', __('admin.general_settings.language') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">
        <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]" />
        <!-- Language -->
        <div class="row">
            @include('admin.partials.general_settings_side_menu')
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h6 class="fw-bold">{{ __('admin.common.language') }}</h6>
                            @if (hasPermission($permissions, 'website_settings', 'create'))
                            <div class="d-flex align-items-center">
                                <div class="dropdown me-3">
                                    <a href="javascript:void(0);"
                                        class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                                        data-bs-toggle="dropdown">
                                        <i class="ti ti-language me-1"></i><span
                                            id="langText">{{ __('admin.general_settings.add_new_language') }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-lg p-2 custom-scroll" id="langDropdownMenu">
                                        @if(!empty($translationLanguages) && count($translationLanguages) > 0)
                                        @foreach($translationLanguages as $translationLanguage)
                                        <li>
                                            <button type="button"
                                                class="dropdown-item d-flex align-items-center rounded-1"
                                                data-lang="{{ $translationLanguage->id }}"
                                                data-lang_title="{{ $translationLanguage->name }}">
                                                {{ $translationLanguage->name }}
                                            </button>
                                        </li>
                                        @endforeach
                                        @endif
                                    </ul>
                                </div>
                                <button class="btn btn-primary" id="addNewLanguage"><i
                                        class="ti ti-plus me-1"></i>{{ __('admin.general_settings.add_new_language') }}</button>
                            </div>
                            @endif
                        </div>
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                <div class="top-search me-2">
                                    <div class="top-search-group">
                                        <span class="input-icon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('admin.common.search') }}" id="search" name="search">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                            @include('admin.content-loader')
                        </div>
                        <!-- Custom Data Table -->
                        <div class="custom-datatable-filter d-none real-table overflow-hidden">
                            <table class="table datatable" id="languageTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ strtoupper(__('admin.common.language')) }}</th>
                                        <th>{{ strtoupper(__('admin.general_settings.code')) }}</th>
                                        @if (hasPermission($permissions, 'website_settings', 'edit'))
                                        <th>{{ strtoupper(__('admin.general_settings.rtl')) }}</th>
                                        <th>{{ strtoupper(__('admin.general_settings.default')) }}</th>
                                        @endif
                                        <th>{{ strtoupper(__('admin.general_settings.total')) }}</th>
                                        <th>{{ strtoupper(__('admin.general_settings.done')) }}</th>
                                        <th>{{ strtoupper(__('admin.general_settings.progress')) }}</th>
                                        @if (hasPermission($permissions, 'website_settings', 'edit'))
                                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                        @endif
                                        <th></th>
                                        @if (hasPermission($permissions, 'website_settings', 'delete'))
                                        <th></th>
                                        @endif
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
        <!-- /Language -->
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Delete Language  -->
<x-admin.delete-modal className="deletemodal" id="delete-modal" action="" formId="deleteForm"
    :hiddenInputs="['id' => 'delete_id']" :title="__('admin.general_settings.delete_language')"
    :description="__('admin.general_settings.delete_language_confirmation')">
</x-admin.delete-modal>
<!-- /Delete Language -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/languages.js') }}"></script>
@endpush
