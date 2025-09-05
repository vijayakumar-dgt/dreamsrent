@extends('admin.admin')

@section('meta_title', __('admin.general_settings.faq') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb :title="__('admin.general_settings.faq')" :breadcrumbs="[
                    __('admin.general_settings.faq') => ''
                ]"
                :buttonText="__('admin.general_settings.add_faq')"
                :modalId="'add_FAQ'"
                :buttonId="'add_faq_btn'"
                :permissionModule="'faq'"
            />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <!-- Sort Dropdown -->
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.general_settings.sort_by') }} :
                            {{ __('admin.general_settings.latest') }}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2">
                            <li><button type="button" class="dropdown-item rounded-1 sort-option"
                                    data-sort="desc">{{ __('admin.general_settings.latest') }}</button></li>
                            <li><button type="button" class="dropdown-item rounded-1 sort-option"
                                    data-sort="asc">{{ __('admin.general_settings.ascending') }}</button></li>
                            <li><button type="button" class="dropdown-item rounded-1 sort-option"
                                    data-sort="desc">{{ __('admin.general_settings.descending') }}</button></li>
                            <li><button type="button" class="dropdown-item rounded-1 sort-option"
                                    data-sort="last_month">{{ __('admin.general_settings.last_month') }}</button></li>
                            <li><button type="button" class="dropdown-item rounded-1 sort-option"
                                    data-sort="last_7_days">{{ __('admin.general_settings.last_7_days') }}</button></li>
                        </ul>
                    </div>
                    <!-- Filter Collapse -->
                    <div class="dropdown">
                        <button type="button" class="filtercollapse coloumn d-inline-flex align-items-center"
                            data-bs-toggle="collapse"
                            data-bs-target="#filtercollapse"
                            aria-expanded="false"
                            aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
                        </button>
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
                            <input type="text" class="form-control" name="search" id="search"
                                placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{__('admin.general_settings.filters')}}</h6>
                    <div class="dropdown me-3">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            {{__('admin.general_settings.status')}}
                        </button>
                        <ul class="dropdown-menu dropdown-menu-lg p-2">
                            <li><button type="button" class="dropdown-item rounded-1 filter-option"
                                    data-status="1">{{__('admin.general_settings.published')}}</button></li>
                            <li><button type="button" class="dropdown-item rounded-1 filter-option"
                                    data-status="0">{{__('admin.general_settings.unpublished')}}</button></li>
                        </ul>
                    </div>
                    <button type="button" id="applyFilters"
                        class="text-purple links border-0 bg-transparent">{{__('admin.general_settings.apply_filters')}}</button>
                    <button type="button" id="clearFilters"
                        class="text-danger links border-0 bg-transparent">{{__('admin.general_settings.clear_all')}}</button>
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
    <x-admin.modal className="addmodal" id="add_FAQ" :title="__('admin.general_settings.add_faq')" formId="addFaq"
        dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="language">{{ __('admin.general_settings.language') }} <span
                        class="text-danger">*</span></label>
                <select class="form-select" id="language" name="language">
                    @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">
                            {{ $language->transLang->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger error-text" id="language_error"></span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="question">{{ __('admin.general_settings.question') }} <span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control" id="question" name="question">
                <span class="text-danger error-text" id="question_error"></span>
            </div>
            <div class="mb-0">
                <label class="form-label" for="answer">{{ __('admin.general_settings.answer') }} <span
                        class="text-danger">*</span></label>
                <textarea class="form-control" id="answer" name="answer"></textarea>
                <span class="text-danger error-text" id="answer_error"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                <button type="submit"
                    class="btn btn-primary submitbtn">{{ __('admin.general_settings.create_new') }}</button>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Add FAQ -->

    <!-- Edit FAQ -->
    <x-admin.modal className="editmodal" id="edit_FAQ" :title="__('admin.general_settings.edit_faq')" formId="editFaqForm"
        dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
                <label class="form-label" for="editFaqLanguage">{{ __('admin.general_settings.language') }} <span
                        class="text-danger">*</span></label>
                <select id="editFaqLanguage" name="language" class="form-select">
                    @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">
                            {{ $language->transLang->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
                <span class="text-danger error-text" id="editFaqLanguageError"></span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="editFaqQuestion">{{ __('admin.general_settings.question') }} <span
                        class="text-danger">*</span></label>
                <input type="text" id="editFaqQuestion" name="question" class="form-control">
                <span class="text-danger error-text" id="editFaqQuestionError"></span>
            </div>
            <div class="mb-3">
                <label class="form-label" for="editFaqAnswer">{{ __('admin.general_settings.answer') }} <span
                        class="text-danger">*</span></label>
                <textarea id="editFaqAnswer" name="answer" class="form-control"></textarea>
                <span class="text-danger error-text" id="editFaqAnswerError"></span>
            </div>
        </x-slot>
        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2">
                    <label class="form-check-label form-label mt-0 mb-0">
                        <input id="editFaqStatus" name="editFaqStatus" class="form-check-input form-label me-2"
                            type="checkbox" role="switch" aria-checked="false">
                        {{ __('admin.general_settings.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3"
                        data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</button>
                    <button type="submit"
                        class="btn btn-primary savebtn">{{ __('admin.general_settings.save_changes') }}</button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Edit FAQ -->

    <!-- Delete FAQ -->
    <x-admin.delete-modal className="deletemodal" id="delete_FAQ" action="" formId="deleteFaq" :hiddenInputs="['delete_id' => '']" :title="__('admin.general_settings.delete_faq')"
        :description="__('admin.general_settings.delete_confirmation')">
    </x-admin.delete-modal>
    <!-- /Delete FAQ -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/faq.js') }}"></script>
@endpush
