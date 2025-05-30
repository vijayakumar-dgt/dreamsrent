@extends('admin.admin')

@section('meta_title', __('admin.general_settings.email_templates') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 pb-0 me-lg-4">
            <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
            __('admin.general_settings.settings') => ''
        ]" />
            <!-- Email Templates -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="fw-bold">{{ __('admin.general_settings.system_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                                <div class="d-flex align-items-center flex-wrap row-gap-3">
                                    <h6 class="fw-bold mb-0">{{ __('admin.general_settings.email_templates') }}</h6>
                                </div>
                                @if (hasPermission($permissions, 'system_settings', 'create'))
                                    <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                        <a href="javascript:void(0);" data-bs-toggle="modal" id="add_new_template"
                                            data-bs-target="#add_email" class="btn btn-primary d-flex align-items-center"><i
                                                class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_template') }}</a>
                                    </div>
                                @endif
                            </div>
                            <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                @include('admin.content-loader')
                            </div>
                            <div class="custom-datatable-filter table-responsive d-none real-table">
                                <table class="table" id="emailTemplateTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ __('admin.general_settings.template_name') }}</th>
                                            <th>{{ __('admin.general_settings.created_on') }}</th>
                                            <th>{{ __('admin.general_settings.status') }}</th>
                                            @if (hasPermission($permissions, 'system_settings', 'edit') || hasPermission($permissions, 'system_settings', 'delete'))
                                                <th>{{ __('admin.common.action') }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Email Templates -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <x-admin.modal className="addmodal" id="add_email" :title="__('admin.general_settings.create_template')"
        formId="mailTemplateForm" dialogClass="modal-dialog-centered modal-lg">
        <x-slot name="body">
            @csrf
            <input type="hidden" name="id" id="id">
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label class="form-label">{{ __('admin.general_settings.template_name') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="title" id="title">
                    <span id="title_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label class="form-label">{{ __('admin.general_settings.notification_type') }} <span
                            class="text-danger">*</span></label>
                    <select class="form-select select" name="notification_type" id="notification_type">
                        <option value="">{{ __('Select Notification Type') }}</option>
                        @if(!empty($notificationTypes) && count($notificationTypes) > 0)
                            @foreach($notificationTypes as $notificationType)
                                <option value="{{ $notificationType->id }}">{{ $notificationType->title }}</option>
                            @endforeach
                        @endif
                    </select>
                    <span id="notification_type_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label class="form-label">{{ __('admin.general_settings.subject') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="subject" id="subject">
                    <span id="subject_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-lg-12">
                    <label>{{ __('admin.general_settings.tags') }}</label>
                    <div class="placeholders" id="placeholders">
                        @if(!empty($tags) && count($tags) > 0)
                            @foreach($tags as $tag)
                                <span class="var_placeholder btn btn-light text-info btn-sm"
                                    data-placeholder="{{ $tag->title }}">{{ $tag->title }}</span>
                            @endforeach
                        @endif
                    </div>
                    <span id="placeholder_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>{{ __('admin.general_settings.description') }} <em class="text-danger">*</em></label>
                    <textarea name="description" id="description" cols="30" rows="5" class="form-control"></textarea>
                    <span id="description_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>{{ __('admin.general_settings.sms_content') }} <em class="text-danger">*</em></label>
                    <textarea name="sms_content" id="sms_content" cols="30" rows="5" class="form-control"></textarea>
                    <span id="sms_content_error" class="text-danger error-text"></span>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>{{ __('admin.general_settings.notification_content') }} <em class="text-danger">*</em></label>
                    <textarea name="notification_content" id="notification_content" cols="30" rows="5" class="form-control"
                        required></textarea>
                    <span id="notification_content_error" class="text-danger error-text"></span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100" id="modalfootdiv">
                <div class="form-check form-check-md form-switch me-2 d-none" id="status_div">
                    <label class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status"
                            id="status">
                        {{ __('admin.general_settings.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn-light me-3"
                        data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                    <button type="submit"
                        class="btn btn-primary submitbtn savebtn">{{ __('admin.general_settings.create_new') }}</button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>

    <x-admin.modal className="addmodal" id="view_template" :title="__('admin.general_settings.preview_template')"
        dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <div class="row">
                <div class="col-lg-12" id="preview_box">

                </div>
            </div>
        </x-slot>
         <x-slot name="footer">
            
         </x-slot>
    </x-admin.modal>


    <!-- Delete  -->
    <x-admin.delete-modal className="deletemodal" id="delete-modal" action="" formId="deleteForm" :hiddenInputs="['id' => 'delete_id']" :title="__('admin.general_settings.delete_email_template')"
        :description="__('admin.general_settings.delete_email_confirmation')">
        <x-slot name="body">
            <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                <i class="ti ti-trash-x fs-26"></i>
            </span>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-center">
                <a href="javascript:void(0);" class="btn btn-light me-3"
                    data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.yes_delete') }}</button>
            </div>
        </x-slot>
    </x-admin.delete-modal>

    <!-- /Delete -->
@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/email_template.js') }}"></script>
@endpush