@extends('admin.admin')

@section('meta_title', __('admin.general_settings.testimonials') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <x-admin.breadcrumb 
                :title="__('admin.general_settings.testimonials')" 
                :breadcrumbs="[
                    __('admin.general_settings.testimonials') => ''
                ]"
                :buttonText="__('admin.cms.add_new_testimonial')"
                :modalId="'add_testimonial'"
                :buttonId="'add_testimonial_btn'"
                :permissionModule="'testimonials'" />
            <!-- Table Header -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3">
                    <div class="dropdown me-2">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown">
                            <i class="ti ti-filter me-1"></i><span class="ms-1 sort"
                                id="current_sort">{{ __('admin.common.sort_by') }} : {{ __('admin.common.latest') }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end p-2 sort_by_list">
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option"
                                    data-sort="latest">{{ __('admin.common.latest') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option"
                                    data-sort="ascending">{{ __('admin.common.ascending') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option"
                                    data-sort="descending">{{ __('admin.common.descending') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option"
                                    data-sort="last month">{{ __('admin.common.last_month') }}</a>
                            </li>
                            <li>
                                <a href="javascript:void(0);" class="dropdown-item rounded-1 sort-option"
                                    data-sort="last 7 days">{{ __('admin.common.last_7_days') }}</a>
                            </li>
                        </ul>
                    </div>
                    <div class="dropdown">
                        <a href="#filtercollapse" class="filtercollapse coloumn d-inline-flex align-items-center"
                            data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="filtercollapse">
                            <i class="ti ti-filter me-1"></i> {{ __('admin.common.filter') }}
                        </a>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control search" id="search"
                                placeholder="{{ __('admin.common.search') }}">
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Table Header -->
            <div class="collapse" id="filtercollapse">
                <div class="filterbox mb-3 d-flex align-items-center">
                    <h6 class="me-3">{{ __('admin.common.filters') }}</h6>
                    <div class="dropdown me-3">
                        <a href="javascript:void(0);" class="dropdown-toggle btn btn-white d-inline-flex align-items-center"
                            data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            {{ __('admin.cms.rating') }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg p-2">
                            <li>
                                <div class="top-search m-2 d-none">
                                    <div class="top-search-group">
                                        <span class="input-icon">
                                            <i class="ti ti-search"></i>
                                        </span>
                                        <input type="text" class="form-control"
                                            placeholder="{{ __('admin.common.search') }}">
                                    </div>
                                </div>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox">5 {{ __('admin.common.star') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox">4 {{ __('admin.common.star') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox">3 {{ __('admin.common.star') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox">2 {{ __('admin.common.star') }}
                                </label>
                            </li>
                            <li>
                                <label class="dropdown-item d-flex align-items-center rounded-1">
                                    <input class="form-check-input m-0 me-2" type="checkbox">1 {{ __('admin.common.star') }}
                                </label>
                            </li>
                        </ul>
                    </div>
                    <a href="javascript:void(0);" class="me-2 text-purple links">{{ __('admin.common.apply') }}</a>
                    <a href="javascript:void(0);" class="text-danger links">{{ __('admin.common.clear_all') }}</a>
                </div>
            </div>
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive d-none real-table">
                <table id="testimonialsTable" class="table datatable">
                    <thead class="thead-light">
                        <tr>
                            <th>{{ strtoupper(__('admin.common.name')) }}</th>
                            <th>{{ strtoupper(__('admin.cms.rating')) }}</th>
                            <th>{{ strtoupper(__('admin.cms.review')) }}</th>
                            <th>{{ strtoupper(__('admin.cms.created_date')) }}</th>
                            @if (hasPermission($permissions, 'testimonials', 'edit') || hasPermission($permissions, 'testimonials', 'delete'))
                                <th>{{ strtoupper(__('admin.common.action')) }}</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <!-- Custom Data Table -->
            <div class="table-footer d-none"></div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Testimonial Service -->
    <x-admin.modal className="addmodal" id="add_testimonial" :title="__('admin.cms.add_testimonial')"
        formId="addTestimonials" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            @csrf
            {{-- Image Upload --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                    <div
                        class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark">
                        <img id="testimonial_image_preview" src="{{ uploadedAsset('', 'default2') }}" class="img-fluid"
                            alt="Profile Photo">
                    </div>
                    <div class="profile-upload">
                        <div class="profile-uploader d-flex align-items-center">
                            <div class="drag-upload-btn btn btn-md btn-dark">
                                <i class="ti ti-photo-up fs-14"></i>
                                {{ __('admin.common.upload') }}
                                <input type="file" class="form-control image-sign" id="testimonial_image" accept="image/*"
                                    name="testimonial_image">
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="fs-14">{{ __('admin.common.upload_image_size') }}</p>
                        </div>
                    </div>
                </div>
                <span class="text-danger error-message" id="testimonial_image_error"></span>
            </div>

            {{-- Customer Name --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.customer') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="customer_name" name="customer_name" maxlength="30">
                <span class="text-danger error-message" id="customer_name_error"></span>
            </div>

            {{-- Rating --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.cms.rating') }} <span class="text-danger">*</span></label>
                <select class="select form-control" id="customer_rating" name="customer_rating"
                    data-placeholder="{{ __('admin.common.select') }}">
                    <option value="">{{ __('admin.common.select') }}</option>
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} {{ __('admin.common.star') }}</option>
                    @endfor
                </select>
                <span class="text-danger error-message" id="customer_rating_error"></span>
            </div>

            {{-- Language --}}
            <div class="mb-3 d-none">
                <label class="form-label" for="language">{{__('admin.general_settings.language')}} <span
                        class="text-danger">*</span></label>
                <select class="form-select" id="language" name="language">
                    @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">{{ $language->transLang->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                <span class="text-danger" id="language_error"></span>
            </div>

            {{-- Review --}}
            <div class="mb-0">
                <label class="form-label">{{ __('admin.cms.review') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" id="customer_review" name="customer_review" maxlength="500"></textarea>
                <span class="text-danger error-message" id="customer_review_error"></span>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                    {{ __('admin.common.cancel') }}
                </button>
                <button type="submit" class="btn btn-primary submitbtn">
                    {{ __('admin.common.create_new') }}
                </button>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- /Add Testimonial Service -->

    <!-- Edit Testimonial -->
    <x-admin.modal className="editmodal" id="edit_testimonial" :title="__('admin.cms.edit_testimonial')"
        formId="editTestimonialForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" id="edit_testimonial_id">

            {{-- Image Upload --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                    <div
                        class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark frames p-2">
                        <img id="edit_testimonial_preview" src="{{ uploadedAsset('', 'profile') }}"
                            class="rounded-2 img-fluid" alt="Image">
                    </div>
                    <div class="profile-upload">
                        <div class="profile-uploader d-flex align-items-center">
                            <div class="drag-upload-btn btn btn-md btn-dark">
                                <i class="ti ti-photo-up fs-14"></i>
                                {{ __('admin.common.upload') }}
                                <input type="file" id="edit_testimonial_image" class="form-control image-sign">
                            </div>
                        </div>
                        <div class="mt-2">
                            <p class="fs-14">{{ __('admin.common.upload_image_size') }}</p>
                        </div>
                    </div>
                </div>
                <span id="edit_testimonial_image_error" class="text-danger fs-14"></span>
            </div>

            {{-- Customer Name --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.customer') }} <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="edit_testimonial_name" maxlength="30">
                <span id="edit_testimonial_name_error" class="text-danger fs-14"></span>
            </div>

            {{-- Rating --}}
            <div class="mb-3">
                <label class="form-label">{{ __('admin.cms.rating') }} <span class="text-danger">*</span></label>
                <select class="select form-control" id="edit_testimonial_ratings"
                    data-placeholder="{{ __('admin.common.select') }}">
                    @for($i = 5; $i >= 1; $i--)
                        <option value="{{ $i }}">{{ $i }} {{ __('admin.common.star') }}</option>
                    @endfor
                </select>
                <span id="edit_testimonial_ratings_error" class="text-danger fs-14"></span>
            </div>

            {{-- Language --}}
            <div class="mb-3 d-none">
                <label class="form-label" for="language">{{__('admin.general_settings.language')}} <span
                        class="text-danger">*</span></label>
                <select class="form-select" id="language" name="language">
                    @foreach($languages as $language)
                        <option value="{{ $language->language_id }}">{{ $language->transLang->name ?? 'N/A' }}</option>
                    @endforeach
                </select>
                <span class="text-danger" id="language_error"></span>
            </div>

            {{-- Review --}}
            <div class="mb-0">
                <label class="form-label">{{ __('admin.cms.review') }} <span class="text-danger">*</span></label>
                <textarea class="form-control" id="edit_testimonial_review"></textarea>
                <span id="edit_testimonial_review_error" class="text-danger fs-14"></span>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="form-check form-check-md form-switch me-2">
                    <label class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status"
                            id="edit_testimonial_status">
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                        {{ __('admin.common.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary submitbtn">
                        {{ __('admin.common.update') }}
                    </button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>

    <!-- /Edit Testimonial -->

    <!-- Delete Testimonial -->
    <x-admin.delete-modal className="deletemodal" id="delete_testimonial" action="" formId="deleteTestimonialForm"
        :hiddenInputs="['id' => '']" :title="__('admin.cms.delete_testimonial')"
        :description="__('admin.cms.delete_testimonial_confirmation')">
    </x-admin.delete-modal>
    <!-- /Delete Testimonial -->

@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/testimoials.js') }}"></script>
@endpush