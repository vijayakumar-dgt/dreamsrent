@extends('admin.admin')

@section('meta_title', __('admin.general_settings.signatures') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
	<div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
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
                            <h5 class="fw-bold">{{ __('admin.general_settings.app_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">{{ __('admin.general_settings.signatures') }}</h6>
                            <div class="d-flex align-items-end justify-content-end flex-wrap row-gap-3 mb-3">
                                
                                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                    @if (hasPermission($permissions, 'app_settings', 'create'))
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#add_signatures" class="btn btn-primary">
                                        <i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_signature') }}
                                    </button>
                                    @endif
                                </div>
                            </div>
                            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                                @include('admin.content-loader')
                            </div>
                            <!-- Real Table (hidden initially) -->
                            <div class="custom-datatable-filter d-none real-table">
                                <table class="table" id="signatureTable">
                                    <thead>
                                        <tr>
                                            <th>{{ strtoupper(__('admin.general_settings.signatures_name')) }}</th>
                                            <th>{{ strtoupper(__('admin.general_settings.signatures')) }}</th>
                                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                            @if (hasPermission($permissions, 'app_settings', 'edit') || hasPermission($permissions, 'app_settings', 'delete'))
                                            <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        <div class="modal fade addmodal" id="add_signatures">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form id="addSignatureForm">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="mb-0">{{ __('admin.general_settings.create_signature') }}</h4>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ti ti-x fs-16"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <!-- Signature Image Upload -->
                                <div class="mb-3">
                                    <label for="signature_image" class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 text-dark frames">
                                            <img id="image_photo_preview" src="{{ asset('backend/assets/img/default-placeholder-image.png') }}" class="img-fluid" alt="Profile Photo">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.common.upload') }}
                                                    <input type="file" class="form-control image-sign" id="signature_image" name="signature_image" accept="image/*" >
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{ __('admin.general_settings.upload_image_size_180_180_within_5MB') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    <span id="signature_image_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Default Checkbox -->
                                <div class="mb-3">
                                    <label class="form-check-label form-label" for="is_default">
                                        <input class="form-check-input form-label" type="checkbox" id="is_default" name="is_default" value="1" checked>
                                        {{ __('admin.general_settings.mark_as_default') }}
                                    </label>
                                    <span id="is_default_error" class="text-danger error-text"></span>
                                </div>
                                <!-- Signature Name -->
                                <div class="mb-3">
                                    <label for="signature_name" class="form-label">{{ __('admin.general_settings.signatures_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="signature_name" name="signature_name" placeholder="Enter signature name">
                                    <span id="signature_name_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                    {{ __('admin.general_settings.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary add_btn">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="modal fade addmodal" id="edit_signature">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <form id="editSignatureForm">
                    <input type="hidden" id="edit_signature_id" name="id">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="mb-0">{{ __('admin.general_settings.edit_signature') }}</h4>
                            <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                                <i class="ti ti-x fs-16"></i>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="mb-3">
                                    <label class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                                    <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                                        <div class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 p-2 flex-shrink-0 text-dark frames">
                                            <img id="edit_signature_preview" src="" class="img-fluid rounded object-fit-contain" alt="img">
                                        </div>
                                        <div class="profile-upload">
                                            <div class="profile-uploader d-flex align-items-center">
                                                <div class="drag-upload-btn btn btn-md btn-dark">
                                                    <i class="ti ti-photo-up fs-14"></i>
                                                    {{ __('admin.common.upload') }}
                                                    <input type="file" id="edit_signature_image" name="signature_image"  accept="image/*"  class="form-control image-sign">
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <p class="fs-14">{{ __('admin.general_settings.upload_image_size_180_180_within_5MB') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-check-label form-label" for="edit_signature_default">
                                        <input class="form-check-input form-label" type="checkbox" id="edit_signature_default" name="is_default" value="1">
                                        {{ __('admin.general_settings.mark_as_default') }}
                                    </label>
                                    <span id="edit_signature_default_error" class="text-danger error-text"></span>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label">{{ __('admin.general_settings.signatures_name') }}<span class="text-danger"> *</span></label>
                                    <input type="text" id="edit_signature_name" name="signature_name" class="form-control">
                                    <span id="edit_signature_name_error" class="error-text text-danger"></span>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer justify-content-between">
                            <div class="form-check form-check-md form-switch me-2">
                                <input type="checkbox" id="edit_signature_status" name="status" class="form-check-input" value="1">
                                <label for="edit_signature_status" class="form-check-label form-label">{{ __('admin.common.status') }}</label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                    {{ __('admin.common.cancel') }}
                                </button>
                                <button type="submit" class="btn btn-primary edit_btn">{{ __('admin.common.save_changes') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <!-- Delete  -->
        <div class="modal fade" id="delete_signature">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body text-center">
                        <form id="deleteSignature">
                            <input type="hidden" name="delete_id" id="delete_id">
                            <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                                <i class="ti ti-trash-x fs-26"></i>
                            </span>
                            <h4 class="mb-1">{{ __('admin.general_settings.delete_signatures') }}</h4>
                            <p class="mb-3">{{ __('admin.general_settings.delete_signatures_description') }}</p>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                                    {{ __('admin.general_settings.cancel') }}
                                </button>
                                <button type="submit" data-bs-dismiss="modal" id="deleteSignature" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection
@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/signature-setting.js') }}"></script>
@endpush










