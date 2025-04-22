@extends('admin.admin')
@section('content')
	<!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">

            <div class="mb-3">
                <a href="{{ route('communication.ticket') }}" class="d-inline-flex align-items-center fw-medium"><i class="ti ti-arrow-left me-1"></i>{{ __('admin.common.back_to_list') }}</a>
            </div>

            <div class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                <h4 class="d-flex align-items-center"><i class="ti ti-menu-2 text-secondary fs-24 me-2"></i>{{ __('admin.support.ticket_details') }}</h4>
            </div>

            <!-- Ticket Details -->
            <div class="card mb-0">
                <form id="editTickets">
                    <div class="card-body">

                        <!-- Top Info -->
                        <div class="border br-5 mb-3">
                            <div class="p-3 bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <!-- Skeleton for Ticket ID & Category -->
                                <div class="skeleton section-title-skeleton label-loader"></div>
                                <h6 class="ticket_id d-none real-label"> <span class="text-default category_name"></span></h6>

                                <!-- Status Dropdown Skeleton -->
                                <div class="skeleton input-skeleton input-loader"></div>
                                <div class="dropdown d-none real-label">
                                    <select class="select form-control status" id="status" name="status">
                                        <option value="">{{ __('admin.common.select') }}</option>
                                        <option value="1">{{ __('admin.support.open') }}</option>
                                        <option value="2">{{ __('admin.support.assigned') }}</option>
                                        <option value="3">{{ __('admin.support.inprogress') }}</option>
                                        <option value="4">{{ __('admin.support.closed') }}</option>
                                    </select>
                                </div>
                            </div>

                            <input type="hidden" id="assign_staff" name="assign_staff">

                            <!-- Info Grid -->
                            <div class="p-3">
                                <div class="row row-cols-xl-5 row-cols-md-3 row-cols-sm-2 row-cols-1 row-gap-3">

                                    <div class="col">
                                        <div class="skeleton input-skeleton input-loader"></div>                                        
                                        <h6 class="fs-14 fw-semibold mb-1 d-none real-label">{{ __('admin.common.created_by') }}</h6>
                                        <div class="skeleton input-skeleton input-loader"></div>                                     
                                        <p class="fs-13 user_name d-none real-label"></p>
                                    </div>
                                    <div class="col">
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <h6 class="fs-14 fw-semibold mb-1 d-none real-label">{{ __('admin.support.priority') }}</h6>
                                        <div class="skeleton input-skeleton input-loader"></div>                                     

                                        <span class="badge badge-danger-transparent d-none real-label d-inline-flex align-items-center badge-md rounded-pill">
                                            <i class="ti ti-point-filled me-1 Priority "></i>
                                        </span>
                                    </div>
                                    <div class="col">
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <h6 class="fs-14 fw-semibold mb-1 d-none real-label">{{ __('admin.support.assigned_to') }}</h6>
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <p class="fs-13 assigne_name d-none real-label"></p>
                                    </div>
                                    <div class="col">
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <h6 class="fs-14 fw-semibold mb-1 d-none real-label">{{ __('admin.common.created_at') }}</h6>
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <p class="fs-13 created_at d-none real-label"></p>
                                    </div>
                                    <div class="col">
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <h6 class="fs-14 fw-semibold mb-1  d-none real-label">{{ __('admin.common.last_updated') }}</h6>
                                        <div class="skeleton input-skeleton input-loader"></div>
                                        <p class="fs-13 update_at d-none real-label"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <div class="skeleton label-skeleton label-loader"></div>
                            <h6 class="mb-2 d-none real-label">{{ __('admin.common.description') }}</h6>

                            <div class="skeleton textarea-skeleton input-loader mb-2"></div>
                            <p class="mb-2 ticket_description d-none real-label"></p>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-3">
                            <div class="skeleton label-skeleton label-loader"></div>
                            <h6 class="mb-2 ticket_attachments d-none real-label">{{ __('admin.common.attachments') }}</h6>
                            <div class="skeleton image-skeleton image-loader"></div>
                            <div class="attachmentContainer d-none real-label"></div>
                        </div>

                        <!-- Reply -->
                        <div class="mb-3">
                            <div class="skeleton label-skeleton label-loader"></div>
                            <h6 class="mb-2 d-none real-label">{{ __('admin.common.reply') }}</h6>

                            <div class="skeleton textarea-skeleton input-loader mb-2"></div>
                            <textarea id="reply" name="reply" class="form-control summernote d-none real-label"></textarea>
                            <p class="mt-2 d-none real-label">{{ __('admin.common.maximum_60_words') }}</p>
                            <span class="text-danger error-message d-none real-label" id="replyError"></span>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="skeleton button-skeleton label-loader me-3"></div>
                            <a href="javascript:void(0);" class="btn btn-light me-3 d-none real-label">{{ __('admin.common.cancel') }}</a>
                            <div class="skeleton button-skeleton label-loader"></div>
                            <button type="submit" class="btn btn-primary d-none real-label send_reply_btn">{{ __('admin.support.send_reply') }}</button>
                        </div>

                    </form>

                    <!-- Ticket History -->
                    <div class="mb-3 mt-4">
                        <div class="skeleton label-skeleton label-loader"></div>
                        <h6 class="mb-2 d-none real-label">{{ __('admin.support.ticket_history') }}</h6>
                        <div class="skeleton textarea-skeleton input-loader"></div>
                        <div class="ticket_histroy p-3 d-none real-label"></div>
                    </div>
                </div>
            </div>
            <!-- /Ticket Details -->

        </div>
    </div>
		<!-- /Page Wrapper -->
@endsection
@push('scripts')
<script src="{{ asset('assets/js/communication/adminticketdetails.js') }}"></script>
@endpush
