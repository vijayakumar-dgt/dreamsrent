    @extends($layout)

    @section('meta_title', __('web.user.tickets') . ' || ' . $companyName)

    @push('styles')
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/datatables/datatables.min.css') }}">
    <!-- summernote CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.css') }}">

    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap-datetimepicker.min.css') }}">
    @endpush

    @section('content')
    <!-- Breadscrumb Section -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">{{__('web.user.tickets')}}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('web.user.tickets')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadscrumb Section -->

    @include('frontend.user.nav_menu')
    <!-- Page Content -->
    <div class="content">
        <div class="container">
            <!-- Content Header -->
            <div class="content-header d-none">
                <h4>{{ __('admin.support.tickets') }}</h4>
                <div class="mb-2">
                    <button type="button" class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_ticket">
                        <i class="ti ti-plus me-2"></i>{{ __('web.user.add_new_ticket') }}
                    </button>
                </div>
            </div>
            <!-- /Content Header -->
            <!-- Ticket Table -->
            <div class="row">
                <div class="col-lg-12 d-flex">
                    <div class="card flex-fill mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <h5>{{ __('web.user.tickets') }}</h5>
                                </div>
                                <div class="col-md-7 d-flex justify-content-end align-items-center">
                                    <button type="button" class="btn btn-primary btn-sm d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_ticket">
                                        <i class="ti ti-plus me-2"></i>{{ __('web.user.add_new_ticket') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-loader position-relative vh-10">
                                @include('frontend.content-loader')
                            </div>
                            <div class="table-responsive dashboard-table d-none real-table">
                                <table id="ticketTable" class="table">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ strtoupper(__('web.user.ticket_code')) }}</th>
                                            <th>{{ strtoupper(__('web.user.subject')) }}</th>
                                            <th>{{ strtoupper(__('web.user.created_date')) }}</th>
                                            <th>{{ strtoupper(__('web.user.priority')) }}</th>
                                            <th>{{ strtoupper(__('web.user.assignee')) }}</th>
                                            <th>{{ strtoupper(__('web.common.status')) }}</th>
                                            <th>{{ strtoupper(__('web.common.action')) }}</th>
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
            <!-- /Ticket Table -->
            <div class="table-responsive dashboard-table dashboard-table-info d-none real-table">
                <table class="table" id="userTickerTable">
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /Page Content -->

    <div class="modal fade" id="add_ticket">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">{{__('web.user.add_ticket')}}</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="addTicket">
                    <div class="modal-body addticket-model-body">
                        <div class="row">
                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="category" class="form-label">
                                        {{ __('web.user.subject') }} <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        id="category"
                                        name="category"
                                        placeholder="{{ __('web.user.enter_subject') }}">
                                    <span id="category_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <!-- Priority -->
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="priority" class="form-label">{{__('web.user.priority')}} <span class="text-danger">*</span></label>
                                    <select id="priority" name="priority" class="select form-control">
                                        <option value="">{{__('web.common.select')}}</option>
                                        <option value="Low">{{__('web.user.low')}}</option>
                                        <option value="Medium">{{__('web.user.medium')}}</option>
                                        <option value="High">{{__('web.user.high')}}</option>
                                    </select>
                                    <span id="priority_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="description" class="form-label">{{__('web.user.description')}} <span class="text-danger">*</span></label>
                                    <textarea id="description" name="description" class="form-control summernote"></textarea>
                                    <p class="mt-2">{{__('web.user.max_60_words')}}</p>
                                    <span id="description_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                            <!-- Document Upload -->
                            <div class="col-md-12">
                                <label class="form-label">{{__('web.user.document')}}</label>
                                <div class="document-upload text-center br-3 mb-3">
                                    <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}" class="mb-2" alt="img">
                                    <p class="mb-2">
                                        {{__('web.user.drop_file_here')}} <span class="text-info text-decoration-underline">{{__('web.user.browse')}}</span>
                                    </p>
                                    <p class="fs-12 mb-0">{{__('web.user.max_size_50_mb')}}</p>
                                    <input type="file" id="document" name="document[]" class="form-control image-sign" multiple accept=".pdf, .txt, .doc, .docx">
                                    <span id="document_error" class="text-danger error-text"></span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="javascript:void(0);" class="btn btn-dark me-3" data-bs-dismiss="modal">{{__('admin.common.cancel')}}</a>
                            <button type="submit" class="btn btn-primary">{{__('admin.common.create')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Status ticket -->
    <div class="modal fade" id="edit_ticket">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">{{ __('web.user.update_ticket') }}</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs m-2" id="ticketTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="update-tab" data-bs-toggle="tab" data-bs-target="#updateTabPane" type="button" role="tab" aria-controls="updateTabPane" aria-selected="true">
                            {{ __('web.user.update_ticket') }}
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#historyTabPane" type="button" role="tab" aria-controls="historyTabPane" aria-selected="false">
                            {{ __('web.user.ticket_history') }}
                        </button>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <!-- Update Ticket Tab -->
                    <div class="tab-pane fade show active" id="updateTabPane" role="tabpanel" aria-labelledby="update-tab">
                        <form id="editTicketstatus">
                            <div class="modal-body pb-1">
                                <div class="row">
                                    <input type="hidden" name="ticketid" id="ticketid">
                                    <label class="form-label">{{__('web.user.description')}}</label>
                                    <p class="description mb-3" id="description"></p>
                                    <!-- Hidden Status Field -->
                                    <div class="col-md-6 d-none">
                                        <div class="mb-3">
                                            <label class="form-label" for="status">{{ __('web.user.update_status') }} <span class="text-danger">*</span></label>
                                            <select class="select form-control" id="status" name="status">
                                                <option value="">{{ __('web.common.select') }}</option>
                                                <option value="1">{{ __('web.user.ticket_open') }}</option>
                                                <option value="2">{{ __('web.user.ticket_assigned') }}</option>
                                                <option value="3">{{ __('web.user.ticket_in_progress') }}</option>
                                                <option value="4">{{ __('web.user.ticket_closed') }}</option>
                                            </select>
                                            <span class="text-danger error-message" id="statusError"></span>
                                        </div>
                                    </div>
                                    <!-- Reply Field -->
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label" for="reply">{{ __('web.home.reply') }} <span class="text-danger">*</span></label>
                                            <textarea id="reply" name="reply" class="form-control summernote"></textarea>
                                            <span class="text-danger error-message" id="replyError"></span>
                                        </div>
                                        <div class="d-flex justify-content-between flex-wrap">
                                            <p class="mt-2">{{ __('web.user.max_60_words') }}</p>
                                            <div class="mb-2">
                                                <a href="javascript:void(0);" class="btn btn-dark me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                                                <button type="submit" class="btn btn-primary">{{ __('admin.common.update') }}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- Ticket History Tab -->
                    <div class="tab-pane fade" id="historyTabPane" role="tabpanel" aria-labelledby="history-tab">
                        <div class="p-3 ticket_histroy">
                            <!-- Ticket history content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="histroy_ticket">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="mb-0">{{__('web.user.history_ticket')}}</h5>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="editTicketstatus">
                    <div class="modal-body histroy-ticket pb-1">
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.common.cancel')}}</a>
                            <button type="submit" class="btn btn-primary d-none">{{__('admin.user.update')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Edit Status ticket -->

    <!-- Delete  -->
    <div class="modal fade" id="delete_ticket">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form id="delete_ticket_form">
                    <input type="hidden" name="delete_id" id="delete_id">
                    <div class="modal-body text-center">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{__('web.user.delete_ticket')}}</h4>
                        <p class="mb-3">{{__('web.user.are_you_sure_delete_ticket')}}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{__('admin.common.cancel')}}</a>
                            <button type="submit" class="btn btn-primary">{{ __('web.user.yes_delete') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Delete -->
    @endsection

    @push('scripts')
    <!-- Datatable JS -->
    <script src="{{ asset('frontend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/plugins/datatables/datatables.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/user/ticket.js') }}"></script>
    <!-- summernote JS -->
    <script src="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/purify.min.js') }}"></script>
    @endpush