@extends('admin.admin')
@section('content')

<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <div class="mb-4">
            <a href="{{ route('admin.pageIndex') }}" class="d-flex align-items-center"><span class="me-1"><i class="ti ti-arrow-narrow-left"></i></span>{{__('admin.page.pages')}}</a>
        </div>
        <div class="row">
            <div class="add_page col-md-8 pageSectionList shadow-sm">
                <div class="card mb-0">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h5>{{ __('admin.page.add_page') }}</h5>
                        <div>
                            <select name="language_id" id="language_id" class="form-control select">
                                <option value="">Select Lang</option>
                                <option value="1" {{ isset($languageId) && $languageId == 1 ? 'selected' : '' }}>English</option>
                                <option value="2" {{ isset($languageId) && $languageId == 2 ? 'selected' : '' }}>Arabic</option>
                            </select>
                        </div>
                    </div>
                    <form id="editPageForm" autocomplete="off">
                        @csrf
                        <input type="hidden" name="page_id" id="page_id" value="{{ $query->id }}">
                        <input type="hidden" name="parent_id" id="parent_id" value="{{ $query->parent_id }}">
                        <input type="hidden" name="read" id="read" value="{{ $query->read }}">
                        <div class="card-body pb-1">
                            <div class="border-bottom mb-3">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.title') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text" name="title" id="title" placeholder="{{ __('admin.page.title_placeholder') }}" value="{{ $query->page_title }}" class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="title_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.slug') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text"
                                                name="slug"
                                                id="slug"
                                                placeholder="{{ __('admin.page.slug_placeholder') }}"
                                                value="{{ $query->slug }}"
                                                @if($query->read === 'static') disabled @endif
                                            class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="slug_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.keywords') }}</label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input class="input-tags form-control d-none real-input" name="keyword" id="keyword" value="{{ $query->keywords }}" placeholder="{{ __('admin.page.keywords_placeholder') }}" type="text">
                                            <span class="invalid-feedback" id="keyword_error"></span>
                                        </div>
                                    </div>
                                    <div class="textareasContainer" id="draggable-left"></div>
                                    <div class="col-md-12 text-end">
                                        <div class="skeleton label-skeleton label-loader me-2"></div>
                                        <button type="button" id="addTextarea" class="btn btn-primary rounded-0 mb-2 border-1 btn-md d-none real-label">{{ __('admin.page.add_section') }}</button>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <div class="mb-3">
                                    <h6>{{ __('admin.page.seo_settings') }}</h6>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.meta_title') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text" name="mete_title" id="mete_title" placeholder="{{ __('admin.page.meta_title_placeholder') }}" value="{{ $query->seo_title }}" class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="mete_title_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.meta_keywords') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text" name="meta_key" id="meta_key" placeholder="{{ __('admin.page.meta_keywords_placeholder') }}" value="{{ $query->seo_tag }}" class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="meta_key_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.meta_description') }}</label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <textarea rows="4" name="meta_description" id="meta_description" placeholder="{{ __('admin.page.meta_description_placeholder') }}" class="form-control d-none real-input">{{ $query->seo_description }}</textarea>
                                            <span class="invalid-feedback" id="meta_description_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.canonical_url') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text" name="canonical_url" id="canonical_url" placeholder="{{ __('admin.page.canonical_url_placeholder') }}" value="{{ $query->canonical_url }}" class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="canonical_url_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.og_title') }} <span class="text-danger">*</span></label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <input type="text" name="og_title" id="og_title" placeholder="{{ __('admin.page.og_title_placeholder') }}" value="{{ $query->og_title }}" class="form-control d-none real-input">
                                            <span class="invalid-feedback" id="og_title_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <div class="skeleton label-skeleton label-loader me-2"></div>
                                            <label class="form-label d-none real-label">{{ __('admin.page.og_description') }}</label>
                                            <div class="skeleton input-skeleton input-loader me-2"></div>
                                            <textarea rows="4" name="og_description" id="og_description" placeholder="{{ __('admin.page.og_description_placeholder') }}" class="form-control d-none real-input">{{ $query->og_description }}</textarea>
                                            <span class="invalid-feedback" id="og_description_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="skeleton label-skeleton label-loader me-2"></div>
                                        <div class="d-none real-label">
                                            <label class="form-label mb-2">{{ __('admin.page.og_image') }} <span class="text-danger">*</span></label>
                                            <div class="d-flex align-items-center flex-wrap row-gap-3  mb-3">
                                                <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                </div>
                                                <div class="profile-upload">
                                                    <div class="profile-uploader d-flex align-items-center">
                                                        <div class="drag-upload-btn btn btn-md btn-dark">
                                                            <i class="ti ti-photo-up fs-14"></i>
                                                            {{ __('admin.common.upload') }}
                                                            <input type="file" name="og_image" id="og_image" class="form-control image-sign">
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <p class="fs-14"></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex align-items-center justify-content-end">
                                <div class="skeleton label-skeleton label-loader me-2"></div>
                                <a href="javascript:void(0);" class="btn btn-light me-2 d-none real-label">{{ __('admin.page.cancel') }}</a>
                                <div class="skeleton label-skeleton label-loader me-2"></div>
                                <button type="submit" class="btn btn-primary d-none real-label" id="edit-page">{{ __('admin.common.update') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="add_page col-md-4">
                <div class="card mb-0">
                    <div class="card-header">
                        <h5>{{ __('admin.page.section_list') }}</h5>
                    </div>
                    <input type="hidden" name="theme_id" id="theme_id" value="{{ $query->theme_id }}">
                    @php $themeId = $query->theme_id ?? 1; @endphp
                    <div class="d-flex align-items-center justify-content-center setSection">
                        @if ($themeId == 1)
                        <button class="btn btn-primary rounded-0 w-100">{{ __('admin.page.screen_one') }}</button>
                        @elseif ($themeId == 2)
                        <button class="btn btn-primary rounded-0 w-100">{{ __('admin.page.screen_two') }}</button>
                        @endif
                    </div>
                    <div id="cardContainer" class="section-list d-none real-table"></div>
                    <div class="custom-datatable-filter table-responsive table-loader">
                        <table class="table">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-center">
                                        <div class="skeleton th-skeleton th-loader"></div>
                                    </th>
                                    <th>
                                        <div class="skeleton th-skeleton th-loader"></div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($i = 0; $i < 5; $i++)
                                    <tr>
                                    <td>
                                        <div class="skeleton data-skeleton data-loader"></div>
                                    </td>
                                    <td>
                                        <div class="skeleton data-skeleton data-loader"></div>
                                    </td>
                                    </tr>
                                    @endfor
                            </tbody>
                        </table>
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
<script src="{{ asset('assets/js/page/edit.js') }}"></script>
@endpush