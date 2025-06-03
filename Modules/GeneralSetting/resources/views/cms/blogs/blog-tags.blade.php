@extends('admin.admin')

@section('meta_title', __('admin.blog.blog_tags') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <x-admin.breadcrumb
            :title="__('admin.blog.blog_tags')"
            :breadcrumbs="[
					__('admin.blog.blog_tags') => ''
				]"
            :buttonText="__('admin.blog.add_tag')"
            :modalId="'add_Tag'"
            :buttonId="'addTagButton'"
            :permissionModule="'blogs'" />
        <!-- Table Header -->
        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
            <div class="d-flex align-items-center flex-wrap row-gap-3">
                <div class="dropdown me-2">
                    <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown" id="selectedFilterText">
                        <i class="ti ti-filter me-1"></i> {{__('admin.blog.sort_by')}} : <span>{{__('admin.blog.latest')}}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end p-2">
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-tag" data-sort="latest">{{__('admin.blog.latest')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-tag" data-sort="asc">{{__('admin.blog.ascending')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-tag" data-sort="desc">{{__('admin.blog.descending')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-tag" data-sort="last_month">{{__('admin.blog.last_month')}}</button></li>
                        <li><button type="button" class="dropdown-item rounded-1 sort-option-tag" data-sort="last_7_days">{{__('admin.blog.last_7_days')}}</button></li>
                    </ul>
                </div>
            </div>
            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                <div class="top-search me-2">
                    <div class="top-search-group">
                        <span class="input-icon">
                            <i class="ti ti-search"></i>
                        </span>
                        <input type="text" class="form-control" id="searchInputTag" placeholder="{{__('admin.blog.search')}}">
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
            <table class="table" id="blogTagTable">
                <thead class="thead-light">
                    <tr>
                        <th>{{__('admin.blog.TAGS')}}</th>
                        <th>{{__('admin.blog.CREATED_DATE')}}</th>
                        <th>{{__('admin.blog.STATUS')}}</th>
                        @if (hasPermission($permissions, 'blogs', 'edit') || hasPermission($permissions, 'blogs', 'delete'))
                        <th></th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @foreach($tags as $tag)
                    <tr data-created="{{ $tag->created_at }}" data-name="{{ strtolower($tag->name) }}">
                        <td>
                            <span class="text-gray-9">{{$tag->name}}</span>
                        </td>
                        <td>
                            <span class="text-gray-9">{{ \Carbon\Carbon::parse($tag->created_at)->format('d M Y') }}</span>
                        </td>
                        <td>
                            @if($tag->status == 1)
                            <span class="badge badge-md badge-soft-success"><i class="ti ti-point-filled"></i>{{__('admin.blog.published')}}</span>
                            @endif
                            @if($tag->status == 0)
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
                                            data-id="{{ $tag->id }}"
                                            data-name="{{ $tag->name }}"
                                            data-status="{{ $tag->status }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#edit_Tag">
                                            <i class="ti ti-edit me-1"></i>{{__('admin.blog.edit')}}
                                        </a>
                                    </li>
                                    @endif
                                    @if (hasPermission($permissions, 'blogs', 'delete'))
                                    <li>
                                        <a class="dropdown-item rounded-1 open-delete-modal" href="javascript:void(0);" data-bs-toggle="modal" data-id="{{ $tag->id }}" data-bs-target="#delete_Tag"><i class="ti ti-trash me-1"></i>{{__('admin.blog.delete')}}</a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Custom Data Table -->
        <div class="table-footer"></div>
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->

<!-- Add Tag -->
<x-admin.modal className="addmodal" id="add_Tag" :title="__('admin.blog.add_tag')">
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
            <label class="form-label">{{__('admin.blog.tag')}} <span class="text-danger">*</span></label>
            <input type="text" name="name" id="add_tag_name" class="form-control" required>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</button>
            <button type="submit" id="create_tag_btn" class="btn btn-primary">{{__('admin.blog.create_new')}}</button>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Add Tag -->

<!-- Edit Tag -->
<x-admin.modal className="addmodal" id="edit_Tag" :title="__('admin.blog.edit_tag')">
    <x-slot name="body">
        <div>
            <input type="hidden" id="edit_tag_id">
            <label class="form-label">{{__('admin.blog.tag')}} <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="edit_tag_name">
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="form-check form-check-md form-switch me-2">
                <label class="form-check-label form-label mt-0 mb-0">
                    <input class="form-check-input form-label me-2" id="edit_tag_status" type="checkbox" role="switch">
                    {{__('admin.blog.status')}}
                </label>
            </div>
            <div class="d-flex justify-content-center">
                <button class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.blog.cancel')}}</button>
                <button class="btn btn-primary" id="update_tag_btn">{{__('admin.blog.update')}}</button>
            </div>
        </div>
    </x-slot>
</x-admin.modal>
<!-- /Edit Tag -->

<!-- Delete Tag -->
<x-admin.delete-modal
        className="deletemodal"
        id="delete_Tag"
        :title="__('admin.blog.delete_tag')"
        :hiddenInputs="['delete_tag_id' => '']"
        :description="__('admin.blog.are_you_sure_you_want_to_delete_this_tag')">
</x-admin.delete-modal>
<!-- /Delete Tag -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/blog-tags.js') }}"></script>
@endpush