@extends('admin.admin')

@section('meta_title', __('admin.general_settings.faq') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{__('admin.general_settings.faq')}}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{__('admin.general_settings.home')}}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('admin.general_settings.faq')}}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    <div class="mb-2">
                        @if (hasPermission($permissions, 'faq', 'create'))

                        <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#add_FAQ" class="btn btn-primary d-flex align-items-center">
                            <i class="ti ti-plus me-2"></i>{{__('admin.general_settings.add_faq')}}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- Sort Dropdown -->
                    <div class="dropdown">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.general_settings.sort_by') }} : {{ __('admin.general_settings.latest') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="desc">{{ __('admin.general_settings.latest') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="asc">{{ __('admin.general_settings.ascending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="desc">{{ __('admin.general_settings.descending') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="last_month">{{ __('admin.general_settings.last_month') }}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option" data-sort="last_7_days">{{ __('admin.general_settings.last_7_days') }}</a></li>
                        </ul>
                    </div>
                    <!-- Filter Collapse -->
                    <div class="dropdown">
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.general_settings.filter') }}
                        </a>
                    </div>
                    <!-- Language Select -->
                    <div class="">
                        <select class="form-select" id="language_id" name="language_id">
                            @foreach($languages as $language)
                                <option value="{{ $language->language_id }}">
                                    {{ $language->transLang->name ?? 'N/A' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search me-2">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{__('admin.general_settings.filters')}}</h6>
                    <div class="dropdown me-3">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            {{__('admin.general_settings.status')}}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg p-2">
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-status="1">{{__('admin.general_settings.published')}}</a></li>
                            <li><a href="javascript:void(0);" class="dropdown-item rounded-1 filter-option" data-status="0">{{__('admin.general_settings.unpublished')}}</a></li>
                        </ul>
                    </div>
                    <a href="javascript:void(0);" id="applyFilters" class="me-2 text-purple links">{{__('admin.general_settings.apply_filters')}}</a>
                    <a href="javascript:void(0);" id="clearFilters" class="text-danger links">{{__('admin.general_settings.clear_all')}}</a>
                </div>
            </div>
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Real Table (hidden initially) -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table id="faqTable" class="table">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.general_settings.question')) }}</th>
                            <th>{{ strtoupper(__('admin.general_settings.answer'))}}</th>
                            <th>{{ strtoupper(__('admin.common.status'))}}</th>
                            @if (hasPermission($permissions, 'faq', 'edit') || hasPermission($permissions, 'faq', 'delete'))
                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Populated by AJAX -->
                    </tbody>
                </table>
            </div>
            <!-- Table Footer -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add FAQ -->
    <div class="modal fade" id="add_FAQ">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <form id="addFaq">
                    <div class="modal-header">
                        <h5 class="mb-0">{{__('admin.general_settings.add_faq')}}</h5>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Language Field -->
                        <div class="mb-3">
                            <label class="form-label" for="language">{{__('admin.general_settings.language')}} <span class="text-danger">*</span></label>
                            <select class="form-select" id="language" name="language">
                                @foreach($languages as $language)
                                    <option value="{{ $language->language_id }}">
                                        {{ $language->transLang->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger" id="language_error"></span>
                        </div>
                        <!-- Question Field -->
                        <div class="mb-3">
                            <label class="form-label" for="question">{{__('admin.general_settings.question')}} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="question" name="question">
                            <span class="text-danger" id="question_error"></span>
                        </div>
                        <!-- Answer Field -->
                        <div class="mb-0">
                            <label class="form-label" for="answer">{{__('admin.general_settings.answer')}} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="answer" name="answer"></textarea>
                            <span class="text-danger" id="answer_error"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.general_settings.cancel')}}</a>
                            <button type="submit" class="btn btn-primary submitbtn">{{__('admin.general_settings.create_new')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Add FAQ -->

    <!-- Edit FAQ -->
    <div class="modal fade" id="edit_FAQ">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <form id="editFaqForm">
                    <div class="modal-header">
                        <h4 class="mb-0">{{__('admin.general_settings.edit_faq')}}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="id" name="id">
                        <!-- Language -->
                        <div class="mb-3">
                            <label class="form-label" for="editFaqLanguage">{{__('admin.general_settings.language')}} <span class="text-danger">*</span></label>
                            <select id="editFaqLanguage" name="language" class="form-select">
                                @foreach($languages as $language)
                                    <option value="{{ $language->language_id }}">
                                        {{ $language->transLang->name ?? 'N/A' }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="text-danger error-message" id="editFaqLanguageError"></span>
                        </div>
                        <!-- Question -->
                        <div class="mb-3">
                            <label class="form-label" for="editFaqQuestion">{{__('admin.general_settings.question')}} <span class="text-danger">*</span></label>
                            <input type="text" id="editFaqQuestion" name="question" class="form-control">
                            <span class="text-danger error-message" id="editFaqQuestionError"></span>
                        </div>
                        <!-- Answer -->
                        <div class="mb-3">
                            <label class="form-label" for="editFaqAnswer">{{__('admin.general_settings.answer')}} <span class="text-danger">*</span></label>
                            <textarea id="editFaqAnswer" name="answer" class="form-control"></textarea>
                            <span class="text-danger error-message" id="editFaqAnswerError"></span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <!-- Status Toggle -->
                            <div class="form-check form-check-md form-switch me-2">
                                <label class="form-check-label form-label mt-0 mb-0">
                                    <input id="editFaqStatus" name="editFaqStatus" class="form-check-input form-label me-2" type="checkbox" role="switch">
                                    {{__('admin.general_settings.status')}}
                                </label>
                            </div>
                            <!-- Buttons -->
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.general_settings.cancel')}}</a>
                                <button type="submit" class="btn btn-primary savebtn">{{__('admin.general_settings.save_changes')}}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit FAQ -->

    <!-- Delete FAQ -->
    <div class="modal fade" id="delete_FAQ">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="deleteFaq">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{__('admin.general_settings.delete_faq')}}</h4>
                        <p class="mb-3">{{__('admin.general_settings.delete_confirmation')}}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.general_settings.cancel')}}</a>
                            <button type="submit" class="btn btn-primary">{{__('admin.general_settings.yes_delete')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete FAQ -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/faq.js') }}"></script>
@endpush
