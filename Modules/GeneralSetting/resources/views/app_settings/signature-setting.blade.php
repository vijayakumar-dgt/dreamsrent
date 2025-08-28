@extends('admin.admin')

@section('meta_title', __('admin.general_settings.signatures') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <x-admin.breadcrumb
                :title="__('admin.general_settings.settings')"
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]"
            />
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
                                        <button type="button" id="add_signature_btn" data-bs-toggle="modal" data-bs-target="#add_signatures"
                                            class="btn btn-primary">
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
                                            @if (
                                                    hasPermission($permissions, 'app_settings', 'edit') ||
                                                    hasPermission($permissions, 'app_settings', 'delete')
                                                )
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
        <x-admin.modal className="addmodal" id="add_signatures" :title="__('admin.general_settings.create_signature')"
            formId="addSignatureForm">
            <x-slot name="body">
                <div class="row">
                    <!-- Signature Image Upload -->
                    <div class="mb-3">
                        <label for="signature_image" class="form-label">{{ __('admin.common.image') }} <span
                                class="text-danger">*</span></label>
                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                            <div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 border flex-shrink-0 text-dark">
                                <img id="image_photo_preview" src="{{ uploadedAsset('', 'default') }}" class="img-fluid" alt="Signature">
                                <i class="ti ti-photo-up text-gray-4 fs-24 upload_icon"></i>
                            </div>
                            <div class="profile-upload">
                                <div class="profile-uploader d-flex align-items-center">
                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                        <i class="ti ti-photo-up fs-14"></i>
                                        {{ __('admin.common.upload') }}
                                        <input type="file" class="form-control image-sign" id="signature_image"
                                            name="signature_image" accept="image/*">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="fs-14">{{ __('admin.general_settings.upload_image_size_180_180_within_5MB') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <span id="signature_image_error" class="text-danger error-text"></span>
                    </div>
                    <!-- Default Checkbox -->
                    <div class="mb-3">
                        <label for="is_default" class="form-check-label form-label" for="is_default">
                            <input class="form-check-input form-label" type="checkbox" id="is_default" name="is_default" value="1">
                            {{ __('admin.general_settings.mark_as_default') }}
                        </label>
                        <span id="is_default_error" class="text-danger error-text"></span>
                    </div>
                    <!-- Signature Name -->
                    <div class="mb-3">
                        <label for="signature_name" class="form-label">{{ __('admin.general_settings.signatures_name') }}
                            <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="signature_name" name="signature_name"
                            placeholder="Enter signature name">
                        <span id="signature_name_error" class="text-danger error-text"></span>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                        {{ __('admin.general_settings.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary add_btn">
                        {{ __('admin.common.create_new') }}
                    </button>
                </div>
            </x-slot>
        </x-admin.modal>

        <x-admin.modal className="addmodal" id="edit_signature" :title="__('admin.general_settings.edit_signature')"
            formId="editSignatureForm" dialogClass="modal-dialog-centered modal-md">
            <x-slot name="body">
                <input type="hidden" id="edit_signature_id" name="id" />
                <div class="row">
                    <!-- Signature Image Upload -->
                    <div class="mb-3">
                        <label for="edit_signature_image" class="form-label">{{ __('admin.common.image') }} <span class="text-danger">*</span></label>
                        <div class="d-flex align-items-center flex-wrap row-gap-3 mb-3">
                            <div
                                class="d-flex align-items-center justify-content-center avatar avatar-xxl border me-3 flex-shrink-0 text-dark">
                                <img src="{{ uploadedAsset('', 'default') }}" class="img-fluid"
                                    id="edit_signature_preview" alt="img">
                            </div>
                            <div class="profile-upload">
                                <div class="profile-uploader d-flex align-items-center">
                                    <div class="drag-upload-btn btn btn-md btn-dark">
                                        <i class="ti ti-photo-up fs-14"></i>
                                        {{ __('admin.common.upload') }}
                                        <input type="file" id="edit_signature_image" name="signature_image" accept="image/*"
                                            class="form-control image-sign">
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <p class="fs-14">{{ __('admin.general_settings.upload_image_size_180_180_within_5MB') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Default Checkbox -->
                    <div class="mb-3">
                        <label for="edit_signature_default" class="form-check-label form-label" for="edit_signature_default">
                            <input class="form-check-input form-label" type="checkbox" id="edit_signature_default"
                                name="is_default" value="1">
                            {{ __('admin.general_settings.mark_as_default') }}
                        </label>
                        <span id="edit_signature_default_error" class="text-danger error-text"></span>
                    </div>
                    <!-- Signature Name -->
                    <div class="mb-0">
                        <label for="edit_signature_name" class="form-label">{{ __('admin.general_settings.signatures_name') }} <span
                                class="text-danger">*</span></label>
                        <input type="text" id="edit_signature_name" name="signature_name" class="form-control">
                        <span id="edit_signature_name_error" class="error-text text-danger"></span>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <div class="form-check form-check-md form-switch me-2">
                        <input type="checkbox" id="edit_signature_status" name="status" class="form-check-input" value="1">
                        <label for="edit_signature_status"
                            class="form-check-label form-label">{{ __('admin.common.status') }}</label>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                            {{ __('admin.common.cancel') }}
                        </button>
                        <button type="submit" class="btn btn-primary edit_btn">
                            {{ __('admin.common.save_changes') }}
                        </button>
                    </div>
                </div>
            </x-slot>
        </x-admin.modal>

        <!-- Delete  -->
        <x-admin.delete-modal className="deletemodal" id="delete_signature" action="" formId="deleteSignature"
            :hiddenInputs="['delete_id' => '']" :title="__('admin.general_settings.delete_signatures')"
            :description="__('admin.general_settings.delete_signatures_description')">
        </x-admin.delete-modal>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection
@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/signature-setting.js') }}"></script>
@endpush
