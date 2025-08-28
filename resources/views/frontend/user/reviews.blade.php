@extends($layout)

@push('styles')
<!-- Datatable CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/datatables/datatables.min.css') }}">
@endpush

@section('content')
    <!-- Breadcrumb Section -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">{{ __('web.user.user_reviews') }}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{ __('web.home.home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_reviews') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb Section -->

@include('frontend.user.nav_menu')

<!-- Page Content -->
<div class="content">
    <div class="container">

        <div class="row">
            <!-- Reviews -->
            <div class="col-lg-12">
                <div class="card mb-0">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <h5>
                                    {{ __('web.user.all_reviews') }}
                                    <span id="totalReviewsCount" class="badge bg-success">0</span>
                                </h5>
                            </div>
                            <div class="col-auto d-flex">
                                <div class="filter-group">
                                    <div class="sort-week sort">
                                        <div class="dropdown dropdown-action">
                                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="datefilter_text">{{__('web.common.filter_by')}}</span> <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item datefilter active" href="javascript:void(0);" data-id="">
                                                    {{ __('web.common.filter_by') }}
                                                </a>
                                                <a class="dropdown-item datefilter" href="javascript:void(0);" data-id="this_week">
                                                    {{ __('web.common.this_week') }}
                                                </a>
                                                <a class="dropdown-item datefilter" href="javascript:void(0);" data-id="this_month">
                                                    {{ __('web.common.this_month') }}
                                                </a>
                                                <a class="dropdown-item datefilter" href="javascript:void(0);" data-id="last30">
                                                    {{ __('web.user.last_days', ['count' => 30]) }}
                                                </a>
                                                <a class="dropdown-item datefilter" href="javascript:void(0);" data-id="custom" data-bs-toggle="modal" data-bs-target="#custom_date">
                                                    {{ __('web.common.custom') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="sort-relevance sort">
                                        <div class="dropdown dropdown-action">
                                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="sortfilter_text">{{ __('web.common.sort_by_asc') }}</span> <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item active sort-filter" data-id="asc" href="javascript:void(0);">
                                                    {{ __('web.common.sort_by_asc') }}
                                                </a>
                                                <a class="dropdown-item sort-filter" data-id="desc" href="javascript:void(0);">
                                                    {{ __('web.common.sort_by_desc') }}
                                                </a>
                                                <a class="dropdown-item sort-filter" data-id="alphabet" href="javascript:void(0);">
                                                    {{ __('web.common.sort_by_alpha') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-search">
                                    <div id="tablefilter" class="me-0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="custom-datatable-filter table-responsive table-loader vh-10">
                            @include('frontend.content-loader')
                        </div>
                        <div class="table-responsive dashboard-table d-none real-table">
                            <table class="table" id="reviewsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{ __('web.user.vehicle_name') }}</th>
                                        <th>{{ __('web.user.review') }}</th>
                                        <th>{{ __('web.user.ratings') }}</th>
                                        <th>{{ __('web.common.action') }}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Reviews -->
        </div>
    </div>
</div>
<!-- /Page Content -->

<!-- Delete Modal -->
<div class="modal new-modal fade" id="delete_modal" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="reviewDeleteForm">
                @csrf
                <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body">
                    <div class="delete-action">
                        <div class="delete-header">
                            <h4>{{ __('web.user.delete_reviews') }}</h4>
                            <p>{{ __('web.user.are_you_sure') }}</p>
                        </div>
                        <div class="modal-btn">
                            <div class="row">
                                <div class="col-6">
                                    <button type="submit" class="btn btn-secondary w-100">
                                        {{ __('web.common.delete') }}
                                    </button>
                                </div>
                                <div class="col-6">
                                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                        {{ __('web.common.cancel') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- /Delete Modal -->

<!-- Custom Date Modal -->
<div class="modal new-modal fade" id="custom_date" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('web.common.custom_date') }}</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <form action="#">
                    <div class="modal-form-group">
                        <label for="custom_from_date">{{ __('web.common.start_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="custom_from_date">
                    </div>
                    <div class="modal-form-group">
                        <label for="custom_to_date">{{ __('web.common.end_date') }} <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="custom_to_date">
                    </div>
                    <span class="text-danger error-text" id="custom_date_error"></span>
                    <div class="modal-btn modal-btn-sm text-end">
                        <a href="javascript:void(0);" id="apply-custom-filter" class="btn btn-primary">
                            {{ __('web.common.apply') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- /Custom Date Modal -->

<!-- View review -->
<div class="modal new-modal fade" id="view_review" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">{{ __('web.common.view') }}</h4>
                <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-3" id="review_text"></p>
            </div>
        </div>
    </div>
</div>
<!-- /View Review -->
@endsection

@push('scripts')
<!-- Datatable JS -->
<script src="{{ asset('frontend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('frontend/assets/plugins/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('frontend/assets/js/custom/user/reviews.js') }}"></script>
@endpush
