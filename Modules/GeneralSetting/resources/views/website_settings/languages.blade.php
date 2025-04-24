@extends('admin.admin')
@section('content')
<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">
        
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
                                    <a href="javascript:void(0);"class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                                        <i class="ti ti-language me-1"></i><span id="langText">{{ __('admin.general_settings.add_new_language') }}</span>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-lg p-2" id="langDropdownMenu">
                                        @if(!empty($translationLanguages) && count($translationLanguages) > 0)
                                            @foreach($translationLanguages as $translationLanguage)
                                                <li>
                                                    <label class="dropdown-item d-flex align-items-center rounded-1" data-lang="{{ $translationLanguage->id }}" data-lang_title="{{ $translationLanguage->name }}">
                                                        {{ $translationLanguage->name }}
                                                    </label>
                                                </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                </div>      
                                <button class="btn btn-primary" id="addNewLanguage"><i class="ti ti-plus me-1"></i>{{ __('admin.general_settings.add_new_language') }}</button>                          
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
                                        <input type="text" class="form-control" placeholder="Search" id="search" name="search">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="custom-datatable-filter table-responsive table-loader">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <!-- Custom Data Table -->
                        <div class="custom-datatable-filter table-responsive d-none real-table">
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
                                        <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                        @if (hasPermission($permissions, 'website_settings', 'delete'))
                                        <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                        @endif
                                        <th></th>
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

<!-- Delete Language  -->
<div class="modal fade" id="delete-modal">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <form action="" id="deleteForm">
                @csrf
                <input type="hidden" name="id" id="delete_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.general_settings.delete_language') }}</h4>
                    <p class="mb-3">{{ __('admin.general_settings.delete_language_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <button class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.yes_delete') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
 <!-- /Delete Language -->
@endsection
@push('scripts')
    <script src="{{ asset('assets/js/general_setting/languages.js') }}"></script>
@endpush