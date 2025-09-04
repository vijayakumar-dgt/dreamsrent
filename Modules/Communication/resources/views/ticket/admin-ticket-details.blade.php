@extends('admin.admin')

@section('meta_title', __('admin.support.ticket_details') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-4">
        <div class="mb-3">
            <a href="{{ route('communication.ticket') }}" class="d-inline-flex align-items-center fw-medium">
                <i class="ti ti-arrow-left me-1"></i>{{ __('admin.common.back_to_list') }}
            </a>
        </div>
        <div class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
            <h4 class="d-flex align-items-center">
                <i class="ti ti-menu-2 text-secondary fs-24 me-2"></i>{{ __('admin.support.ticket_details') }}
            </h4>
        </div>
        <!-- Ticket Details -->
        <div class="card mb-0">
            <form id="editTickets">
                <div class="card-body">
                    <!-- Top Info -->
                    <div class="border br-5 mb-3">
                        <div class="p-3 bg-light d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="ticket_id">
                                <span class="text-default category_name"></span>
                            </div>
                            <div class="dropdown">
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
                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.created_by') }}</h6>
                                    <p class="fs-13 user_name"></p>
                                </div>
                                <div class="col">
                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.support.priority') }}</h6>
                                    <span class="badge badge-danger-transparent d-inline-flex align-items-center badge-md rounded-pill">
                                        <i class="ti ti-point-filled me-1 Priority"></i>
                                    </span>
                                </div>
                                <div class="col">
                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.support.assigned_to') }}</h6>
                                    <p class="fs-13 assigne_name"></p>
                                </div>
                                <div class="col">
                                    <h6 class="fs-14 fw-semibold mb-1">{{ __('admin.common.created_at') }}</h6>
                                    <p class="fs-13 created_at"></p>
                                </div>
                                <div class="col">
                                    <h6 class="fs-14 fw-semibold mb-1 ">{{ __('admin.common.last_updated') }}</h6>
                                    <p class="fs-13 update_at"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Description -->
                    <div class="mb-3">
                        <h6 class="mb-2 ">{{ __('admin.common.description') }}</h6>
                        <p class="mb-2 ticket_description"></p>
                    </div>
                    <!-- Attachments -->
                    <div class="mb-3">
                        <h6 class="mb-2 ticket_attachments">{{ __('admin.common.attachments') }}</h6>
                        <div class="attachmentContainer"></div>
                    </div>

                    <!-- Reply -->
                    <div class="mb-3">
                        <h6 class="mb-2">{{ __('admin.common.reply') }}</h6>
                        <textarea id="reply" name="reply" class="form-control summernote"></textarea>
                        <p class="mt-2">{{ __('admin.common.maximum_60_words') }}</p>
                        <span class="text-danger error-message" id="replyError"></span>
                    </div>
                    <!-- Action Buttons -->
                    <div class="d-flex align-items-center justify-content-end">
                        <a href="{{ route('communication.ticket') }}" class="btn btn-light me-3">{{ __('admin.common.cancel') }}</a>
                        <button type="submit" class="btn btn-primary send_reply_btn">{{ __('admin.support.send_reply') }}</button>
                    </div>
                </div>
            </form>
            <!-- Ticket History -->
            <div class="p-3">
                <h6 class="">{{ __('admin.support.ticket_history') }}</h6>
                <div class="ticket_histroy"></div>
            </div>
        </div>
        <!-- /Ticket Details -->
    </div>
</div>
<!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/communication/adminticketdetails.js') }}"></script>
@endpush
