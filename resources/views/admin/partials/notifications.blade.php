@extends('admin.admin')
@section('content')
<div class="page-wrapper">
    <div class="content">
        <div class="container">
            <div class="content-header">
                <h4>{{__('web.user.notifications')}}</h4>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card ">
                      <div class="card-body">
                        <div class="col-xl-12">
                            <div class="d-flex justify-content-end align-items-center mb-3">
                                <div id="notification_action" class="d-none">
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-secondary me-2" id="mark-all-read"><i class="feather-check"></i> {{__('web.user.mark_all_as_read')}}</a>
                                    <a href="javascript:void(0);" class="btn btn-sm btn-outline-danger" id="deleteAll"><i class="feather-trash-2"></i> {{__('web.user.delete_all')}}</a>
                                </div>
                            </div>
                        
                            <div class="notification-list" id="notification-list">
                                
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="float-end" id="pagination-container">
                                      
                                    </div>
                                </div>
                            </div>                        
                      </div>
                    </div>
                </div>
            </div>
            <!-- /Payments Table -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="delete_notification" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="delete-action">
                    <div class="delete-header">
                        <h4>{{__('web.user.delete_notification')}}</h4>
                        <p>{{__('web.user.are_you_sure')}}</p>
                    </div>
                    <div class="modal-btn">
                        <div class="row">
                            <div class="col-6">
                                <a href="javascript:void(0);" class="btn btn-secondary w-100 deletebtn">
                                    {{__('web.common.delete')}}
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                    {{__('web.common.cancel')}}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="deleteAllNotifications" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="delete-action">
                    <div class="delete-header">
                        <h4>{{__('web.user.delete_all_notifications')}}</h4>
                        <p>{{__('web.user.are_you_sure_delete_all_notifications')}}</p>
                    </div>
                    <div class="modal-btn">
                        <div class="row">
                            <div class="col-6">
                                <a href="javascript:void(0);" class="btn btn-secondary w-100 deleteAllNotifications">
                                    {{__('web.common.delete')}}
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                    {{__('web.common.cancel')}}
                                </a>
                            </div>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script src="{{ asset('assets/js/admin/notifications.js') }}"></script>
@endpush