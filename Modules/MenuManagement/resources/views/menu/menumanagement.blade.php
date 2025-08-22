@extends('admin.admin')

@section('meta_title', __('admin.cms.menu_management') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <div class="edit-menu-header">
                <p><a href="{{ route('admin.menu') }}" class="d-flex align-items-center"><i class="ti ti-arrow-narrow-left me-1"></i>{{__('admin.common.back_to_list')}}</a></p>
            </div>
            <div class="filterbox menu-filter">
                <div>
                    <h4 class="d-flex align-items-center"><span class="me-1"><i class="ti ti-menu-2 text-secondary fw-normal"></i></span>{{__('admin.cms.menu_management')}}</h4>
                </div>
            </div>
            <div class="card mb-0">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="menu_name" class="form-label fw-normal">{{__('admin.cms.select_menu_you_want_to_edit')}}</label>
                            <select class="select" id="menu_name" name="menu_name">
                                @foreach($menus as $menu)
                                    <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row row-gap-4">
                        <div class="col-md-8">
                            <div class="card mb-0">
                                <div class="card-header d-none ">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <label for="nav-menu" class="form-label me-2 mb-0">{{__('admin.cms.menu_name')}}</label>
                                            <input type="text" name="nav-menu" class="form-control" value="nav-menu">
                                        </div>
                                        <div>
                                            <a href="javascript:void(0);" class="btn btn-primary">{{__('admin.cms.save_menu')}}</a>
                                        </div>
                                    </div>
                                </div>
                                <form id="menuManagement">
                                    <div class="card-body">
                                        <div class="edit-menu-header">
                                            <h5 class="mb-2">{{__('admin.cms.menu_structure')}}</h5>
                                            <p class="text-gray-9">{{__('admin.cms.menu_structure_description')}}</p>
                                        </div>
                                        <div class="edit-menu-list">
                                            <ol class="list-group sortable-list list-group-numbered" id="simple-list">
                                            </ol>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <div class="d-flex align-items-center justify-content-start">
                                            <a href="{{ route('admin.menu') }}" class="btn btn-light me-2">{{__('admin.common.cancel')}}</a>
                                            <button type="submit" class="btn btn-primary">{{__('admin.common.save_changes')}}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <!-- Right section for pages & custom links -->
                        <div class="col-md-4">
                            <div class="menu-right">
                                <div class="accordion" id="accordionright">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapserightone" aria-expanded="true" aria-controls="collapserightone">
                                                {{__('admin.cms.pages')}}
                                            </button>
                                        </h2>
                                        <div id="collapserightone" class="accordion-collapse collapse show" data-bs-parent="#accordionright">
                                            <div class="accordion-body p-0">
                                                <ul id="page-list" class="">
                                                    @foreach($pages as $page)
                                                        <li>
                                                            <label class="dropdown-item d-flex align-items-center rounded-1">
                                                                <input class="form-check-input page-checkbox m-0 me-2" type="checkbox" data-title="{{ $page->page_title }}" data-link="{{ $page->slug }}">
                                                                {{ $page->page_title }}
                                                            </label>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                                <div class="menu-rightfooter border border-top">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label class="dropdown-item d-flex align-items-center rounded-1">
                                                            <input id="select-all" class="form-check-input m-0 me-2" type="checkbox">
                                                            {{__('admin.cms.select_all')}}
                                                        </label>
                                                        <a href="javascript:void(0);" class="p-2" id="add-to-menu">{{__('admin.cms.add_to_menu')}}</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- Custom Links -->
                                <div class="accordion mb-0" id="accordionright2">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapserighttwo" aria-expanded="true" aria-controls="collapserighttwo">
                                                {{__('admin.cms.custom_link')}}
                                            </button>
                                        </h2>
                                        <div id="collapserighttwo" class="accordion-collapse collapse show" data-bs-parent="#accordionright2">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <label for="customUrl" class="form-label">{{__('admin.cms.url')}} <span class="text-danger">*</span></label>
                                                    <input type="text" id="customUrl" class="form-control" value="http://">
                                                </div>
                                                <div class="mb-2">
                                                    <label for="customLabel" class="form-label">{{__('admin.cms.label')}}</label>
                                                    <input type="text" id="customLabel" class="form-control" placeholder="Enter Label">
                                                </div>
                                                <div class="menu-rightfooter border border-top">
                                                    <div class="d-flex align-items-center justify-content-end">
                                                        <a href="javascript:void(0);" class="p-2 add-custom-menu">{{__('admin.cms.add_to_menu')}}</a>
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
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<!-- Sortable JS -->
<script src="{{ asset('backend/assets/plugins/sortablejs/Sortable.js') }}"></script>
<!-- Internal Sortable JS -->
<script src="{{ asset('backend/assets/js/sortable.js') }}"></script>
<script src="{{ asset('backend/assets/js/menu_management/menumanagement.js') }}"></script>
@endpush
