@extends('admin.admin')

@section('meta_title', __('admin.blog.blog_categories') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb
            :title="__('admin.blog.blog_categories')"
            :breadcrumbs="[
					__('admin.blog.blog_categories') => ''
				]"
            :buttonText="__('admin.blog.add_category')"
            :modalId="'add_Category'"
            :buttonId="'addCategoryButton'"
            :permissionModule="'blogs'" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="selectedFilterTextCategory">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.sort_by')}} : <span>{{__('admin.blog.latest')}}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-category" data-sort="latest">{{__('admin.blog.latest')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-category" data-sort="asc">{{__('admin.blog.ascending')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-category" data-sort="desc">{{__('admin.blog.descending')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-category" data-sort="last_month">{{__('admin.blog.last_month')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-category" data-sort="last_7_days">{{__('admin.blog.last_7_days')}}</button></li>
                    </ul>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInputCategory" placeholder="{{__('admin.blog.search')}}">
                    </div>
                </div>
            </div>
        </div>
        <!-- /Table Header -->
        <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
            @include('admin.content-loader')
        </div>
        <!-- Custom Data Table -->
        <div class="custom-datatable-filter table-responsive d-none real-table">
            <table class="table blogCategoryTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{__('admin.blog.CATEGORY')}}</th>
                        <th>{{__('admin.blog.NUMBER_OF_BLOGS')}}</th>
                        <th>{{__('admin.blog.CREATED_DATE')}}</th>
                        <th>{{__('admin.blog.STATUS')}}</th>
                        @if (hasPermission($permissions, 'blogs', 'edit') || hasPermission($permissions, 'blogs', 'delete'))
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @if(count($categories) != 0)
                    @foreach($categories as $category)
                    <tr data-created="{{ $category->created_at }}" data-name="{{ strtolower($category->name) }}">
                        <td>
                            <span class="text-gray-9">{{$category->name}}</span>
                        </td>
                        <td>
                            <?php
                            if (isset($category)) {
                                $count = Modules\GeneralSetting\Models\BlogPost::where('category', $category->id)->count();
                            } else {
                                $count = 0;
                            }
                            ?>
                            <span class="text-gray-9">{{$count}}</span>
                        </td>
                        <td>
                            <span class="text-gray-9">{{ \Carbon\Carbon::parse($category->created_at)->format('d M Y') }}</span>
                        </td>
                        <td>
                            @if($category->status == 1)
                            <span class="badge badge-md badge-soft-success"><i class="ti ti-point-filled"></i>{{__('admin.blog.published')}}</span>
                            @endif
                            @if($category->status == 0)
                            <span class="badge badge-md badge-soft-danger"><i class="ti ti-point-filled"></i>{{__('admin.blog.unpublish')}}</span>
                            @endif
                        </td>
                        @if (hasPermission($permissions, 'blogs', 'edit') || hasPermission($permissions, 'blogs', 'delete'))
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end p-2">
                                    @if (hasPermission($permissions, 'blogs', 'edit'))
                                    <li>
                                        <a class="dropdown-item rounded-1 open-edit-modal"
                                            href="javascript:void(0);"
                                            data-id="{{ $category->id }}"
                                            data-name="{{ $category->name }}"
                                            data-status="{{ $category->status }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit_Category">
                                            <i class="ti ti-edit me-1"></i>{{__('admin.blog.edit')}}
                                        </a>
                                    </li>
                                    @endif
                                    @if (hasPermission($permissions, 'blogs', 'delete'))
                                    <li>
                                        <a class="dropdown-item rounded-1 open-delete-modal" href="javascript:void(0);" data-bs-toggle="modal" data-id="{{ $category->id }}" data-bs-target="#delete_Category"><i class="ti ti-trash me-1"></i>{{__('admin.blog.delete')}}</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                    @elseif(count($categories) == 0)
                    <tr>
                        <td></td>
                        <td class="text-center">{{ __('admin.blog.no_data_found') }}</td>
                        <td></td>
                        <td></td>
                        @if (hasPermission($permissions, 'blogs', 'edit') || hasPermission($permissions, 'blogs', 'delete'))
                        <td></td>
                        @endif
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Custom Data Table -->
        <div class="table-footer"></div>
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Add Category -->
<x-admin.modal className="addmodal" id="add_Category" :title="__('admin.blog.add_category')">
    <x-slot name="body">
        <!-- Language Field -->
        <div class="mb-3">
            <label class="form-label" for="language">{{__('admin.general_settings.language')}} <span class="text-danger">*</span></label>
            <select class="form-select" id="add_language" name="language_id">
                @foreach($languages as $language)
                <option value="{{ $language->language_id }}">
                    {{ $language->transLang->name ?? 'N/A' }}
                </option>
                @endforeach
            </select>
            <span class="text-danger" id="language_error"></span>
        </div>
        <div>
            <label class="form-label">{{__('admin.blog.category')}} <span class="text-danger">*</span></label>
            <input type="text" name="name" id="add_category_name" class="form-control" required>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</a>
            <button type="submit" id="create_category_btn" class="btn btn-primary">{{__('admin.blog.create_new')}}</button>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Add Category -->

<!-- Edit Category -->
<x-admin.modal className="addmodal" id="edit_Category" :title="__('admin.blog.edit_category')">
    <x-slot name="body">
        <div>
            <input type="hidden" id="edit_category_id">
            <label class="form-label">{{__('admin.blog.category')}}<span class="text-danger"> *</span></label>
            <input type="text" class="form-control" id="edit_category_name">
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="form-check form-check-md form-switch me-2">
                <label class="form-check-label form-label mt-0 mb-0">
                    <input class="form-check-input form-label me-2" id="edit_category_status" type="checkbox" role="switch">
                    {{__('admin.blog.status')}}
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</button>
                <button class="btn btn-primary" id="update_category_btn">{{__('admin.blog.update')}}</button>
            </div>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Edit Category -->

<!-- Delete Category -->
<x-admin.delete-modal
        className="deletemodal"
        id="delete_Category"
        :title="__('admin.blog.delete_category')"
        :hiddenInputs="['delete_category_id' => '']"
        :description="__('admin.blog.are_you_sure_you_want_to_delete_this_category')">
</x-admin.delete-modal>
<!-- /Delete Category -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/blog-category.js') }}"></script>
@endpush