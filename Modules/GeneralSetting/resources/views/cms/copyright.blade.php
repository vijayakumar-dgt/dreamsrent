@extends('admin.admin')

@section('meta_title', __('admin.general_settings.copyright') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{ __('admin.general_settings.settings') }}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{__('admin.general_settings.home')}}</a>
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
                    <form id="copyRightForm">
                        <div class="card h-100">
                            <!-- Card Header -->
                            <div class="card-header">
                                <h5 class="fw-bold">{{ __('admin.general_settings.website_settings') }}</h5>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{__('admin.general_settings.copyright')}}</h6>
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="20">
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
                                <div class="mb-3">
                                    <label for="copy_right_description" class="form-label">{{__('admin.general_settings.description')}}</label>
                                    <div class="d-none real-label">
                                        <textarea id="copy_right_description" name="copy_right_description" class="form-control summernote"></textarea>
                                    </div>
                                    <span id="copy_right_description_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <!-- Card Footer -->
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{__('admin.general_settings.cancel')}}</a>
                                    @if (hasPermission($permissions, 'copyright', 'edit'))
                                    <button type="submit" class="btn btn-primary submitbtn">{{__('admin.general_settings.save_changes')}}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/copy-right.js') }}"></script>
@endpush
