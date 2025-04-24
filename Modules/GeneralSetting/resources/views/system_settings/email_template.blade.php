@extends('admin.admin')
@section('content')
<div class="page-wrapper">
    <div class="content me-0 pb-0 me-lg-4">

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
                        <h5 class="fw-bold">{{ __('admin.general_settings.system_settings') }}</h5>
                    </div>
                    <div class="card-body">

                        <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                            <div class="d-flex align-items-center flex-wrap row-gap-3">
                                <h6 class="fw-bold mb-0">{{ __('admin.general_settings.email_templates') }}</h6>
                            </div>
                            @if (hasPermission($permissions, 'system_settings', 'create'))

                            <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">
                                <a href="javascript:void(0);" data-bs-toggle="modal" id="add_new_template" data-bs-target="#add_email" class="btn btn-primary d-flex align-items-center"><i class="ti ti-plus me-2"></i>{{ __('admin.general_settings.add_new_template') }}</a>
                            </div>
                            @endif
                        </div>
                        <div class="custom-datatable-filter table-responsive table-loader">
                            <table class="table table-bordered">
                                <thead class="thead-light">
                                    <tr>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                        <th>
                                            <div class="skeleton data-skeleton label-loader"></div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                        <td>
                                            <div class="skeleton data-skeleton data-loader"></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="custom-datatable-filter d-none real-table">
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
                                    <!-- Skeleton loader rows -->
                                    <tr>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                    </tr>
                                    <tr>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                    </tr>
                                    <tr>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>

                                    </tr>
                                    <tr>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>

                                    </tr>
                                    <tr>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>
                                        <td><div class="skeleton data-skeleton data-loader"></div></td>

                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                        <div class="table-footer"></div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Settings Prefix -->
    </div>
    @include('admin.partials.footer')
    
    <div class="modal fade addmodal" id="add_email">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form action="" id="mailTemplateForm">
                        @csrf
                        <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <h4 class="mb-0">{{ __('admin.general_settings.create_template') }}</h4>
                        <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="ti ti-x fs-16"></i>
                        </button>
                    </div>
                    <div class="modal-body ">
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="mb-0">
                                    <label class="form-label">{{ __('admin.general_settings.template_name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="title"  id="title">
                                    <span id="title_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="mb-0">
                                    <label class="form-label">{{ __('admin.general_settings.notification_type') }}  <span class="text-danger">*</span></label>
                                    <select class="form-select select" name="notification_type" id="notification_type">
                                        <option value="">Select Notification Type</option>
                                        @if(!empty($notificationTypes) && count($notificationTypes) > 0)
                                        @foreach($notificationTypes as $notificationType)
                                        <option value="{{$notificationType->id}}">{{$notificationType->title}}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                    <span id="notification_type_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="mb-0">
                                    <label class="form-label">{{ __('admin.general_settings.subject') }}  <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="subject"  id="subject">
                                    <span id="subject_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-lg-12">
                                <div class="mb-0">
                                    <label for="form-label">{{ __('admin.general_settings.tags') }} </label>
                                    <div class="placeholders" id="placeholders">
                                        @if(!empty($tags) && count($tags) > 0)
                                        @foreach($tags as $tag)
                                        <span class="var_placeholder btn btn-light text-info  btn-sm" data-placeholder="{{$tag->title}}">{<?=$tag->title?>}</span>
                                        @endforeach
                                        @endif
                                    </div>
                                    <span id="placeholder_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <label for="form-label">{{ __('admin.general_settings.description') }}  <em class="text-danger">*</em></label>
                                        <textarea name="description" id="description" cols="30" rows="5" class="form-control">

                                        </textarea>
                                </div>
                                <span id="description_error" class="text-danger error-text"></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-0">
                                    <label for="form-label">{{ __('admin.general_settings.sms_content') }}  <em class="text-danger">*</em></label>
                                        <textarea name="sms_content" id="sms_content" cols="30" rows="5" class="form-control"></textarea>
                                </div>
                                <span id="sms_content_error" class="text-danger error-text"></span>
                            </div>
                        </div>
                        <div class="row">
                             <div class="col-md-12">
                                 <div class="mb-0">
                                     <label for="form-label">{{ __('admin.general_settings.notification_content') }} <em class="text-danger">*</em></label>
                                         <textarea name="notification_content" id="notification_content" cols="30" rows="5" class="form-control" required></textarea>
                                 </div>
                                 <span id="notification_content_error" class="text-danger error-text"></span>
                             </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100" id="modalfootdiv">
                            <div class="form-check form-check-md form-switch me-2 d-none"  id="status_div">
                                <label class="form-check-label form-label mt-0 mb-0">
                                <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status" id="status">
                                {{ __('admin.general_settings.status') }} 
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }} </a>
                                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.general_settings.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade addmodal" id="view_template">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0" id="view_template_title">{{ __('admin.general_settings.preview_template') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body ">
                    <div class="row">
                        <div class="col-lg-12" id="preview_box">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Delete  -->
    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="deleteForm" action="">
                        @csrf
                        <input type="hidden" name="id" id="delete_id">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.delete_email_template') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.delete_email_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.general_settings.cancel') }}</a>
                            <button type="submit" data-bs-dismiss="modal" class="btn btn-primary submitbtn">{{ __('admin.general_settings.yes_delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete -->
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/js/general_setting/email_template.js') }}"></script>
@endpush
