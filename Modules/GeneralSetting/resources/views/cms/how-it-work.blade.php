@extends('admin.admin')

@section('meta_title', __('admin.general_settings.how_it_works') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h4 class="mb-1">{{__('admin.general_settings.how_it_works')}}</h4>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{__('admin.general_settings.home')}}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('admin.general_settings.how_it_works')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <!-- Settings Prefix -->
            <div class="row">
                <div class="col-lg-12">
                    <form id="howItWorkForm">
                        <div class="card mb-0">
                            <!-- Card Header -->
                            <div class="card-header">
                                <h5 class="fw-bold">{{__('admin.general_settings.cms')}}</h5>
                            </div>
                            <!-- Card Body -->
                            <div class="card-body">
                                <h6 class="fw-bold mb-3">{{__('admin.general_settings.how_it_works')}}</h6>
                                <input type="hidden" name="group_id" id="group_id" class="form-control" value="10">
                                <div class="mb-3">
                                    <label class="form-label" for="language">{{__('admin.general_settings.language')}} <span class="text-danger">*</span></label>
                                    <select class="form-select" id="language" name="language" >
                                        @foreach($languages as $language)
                                            <option value="{{ $language->language_id }}">
                                                {{ $language->transLang->name ?? 'N/A' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <span class="text-danger" id="language_error"></span>
                                </div>
                                <div class="mb-0">
                                    <label for="howitwork_description" class="form-label">{{__('admin.general_settings.description')}}</label>
                                    <textarea id="howitwork_description" name="howitwork_description" class="form-control summernote"></textarea>
                                    <span id="howitwork_description_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="d-flex justify-content-end">
                                    <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.general_settings.cancel')}}</a>
                                    
                                    @if (hasPermission($permissions, 'how_it_work', 'edit'))
                                    <button type="submit" class="btn btn-primary  submitbtn">{{__('admin.general_settings.save_changes')}}</button>
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
<script src="{{ asset('backend/assets/js/general_setting/how-it-work.js') }}"></script>
@endpush
