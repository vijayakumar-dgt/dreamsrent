    @extends($layout)
    @push('styles')
    <!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/datatables/datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/fullcalendar.min.css') }}">
    @endpush
    @section('content')
    <!-- Breadscrumb Section -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">{{__('web.user.user_bookings')}}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('web.user.user_bookings')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadscrumb Section -->
    @include('frontend.user.nav_menu')
    <div class="content">
        <div class="container">
            <!-- Content Header -->
            <div class="content-header d-flex align-items-center justify-content-between">
                <h4>{{__('web.user.my_bookings')}}</h4>
                <ul class="booking-nav">
                    <li><a href="javascript:void(0);" class="active booking_view" data-view="list"><i class="fa-solid fa-list"></i></a></li>
                    <li><a href="javascript:void(0);" class="booking_view" data-view="calendar"><i class="fa-solid fa-calendar-days"></i></a></li>
                </ul>
            </div>
            <!-- /Content Header -->
            <!-- Sort By -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="sorting-info">
                        <div class="row d-flex align-items-center">
                            <div class="col-xl-7 col-lg-8 col-sm-12 col-12">
                                <div class="booking-lists">
                                    <ul class="nav">
                                        <li><a class="active status_filter" href="javascript:void(0);" data-status="">{{__('web.user.all_bookings')}}</a></li>
                                        <li><a href="javascript:void(0);" class="status_filter" data-status="upcomming">{{__('web.user.upcomming')}}</a></li>
                                        <li><a href="javascript:void(0);" class="status_filter" data-status="1">{{__('web.common.inprogress')}}</a></li>
                                        <li><a href="javascript:void(0);" class="status_filter" data-status="5">{{__('web.common.completed')}}</a></li>
                                        <li><a href="javascript:void(0);" class="status_filter" data-status="6">{{__('web.common.cancelled')}}</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="col-xl-5 col-lg-4 col-sm-12 col-12">
                                <div class="filter-group">
                                    <div class="sort-week sort">
                                        <div class="dropdown dropdown-action">
                                            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="datefilter_text">{{__('web.common.filter_by')}}</span> <i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button type="button" class="dropdown-item datefilter active" data-id="">{{__('web.common.filter_by')}}</button>
                                                <button type="button" class="dropdown-item datefilter" data-id="this_week">{{__('web.common.this_week')}}</button>
                                                <button type="button" class="dropdown-item datefilter" data-id="this_month">{{__('web.common.this_month')}}</button>
                                                <button type="button" class="dropdown-item datefilter" data-id="last30">{{ __('web.user.last_days', ['count' => 30]) }}</button>
                                                <button type="button" class="dropdown-item datefilter" data-id="custom" data-bs-toggle="modal" data-bs-target="#custom_date">{{__('web.common.custom')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="sort-relevance sort" id="sort_filter">
                                        <div class="dropdown dropdown-action">
                                            <button type="button" class="btn dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="sortfilter_text">{{__('web.common.sort_by_asc')}}</span><i class="fas fa-chevron-down"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <button type="button" class="dropdown-item active sort-filter" data-id="asc">{{__('web.common.sort_by_asc')}}</button>
                                                <button type="button" class="dropdown-item sort-filter" data-id="desc">{{__('web.common.sort_by_desc')}}</button>
                                                <button type="button" class="dropdown-item sort-filter" data-id="alphabet">{{__('web.common.sort_by_alpha')}}</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Sort By -->
            <div class="row">
                <!-- All Bookings -->
                <div class="col-lg-12 d-flex" id="booking_list">
                    <div class="card flex-fill mb-0">
                        <div class="card-header">
                            <div class="row align-items-center">
                                <div class="col-md-5">
                                    <h5>{{__('web.user.all_bookings')}} <span id="totalBookingCount" class="badge bg-success">{{ $totalBookingCount }}</span></h5>
                                </div>
                                <div class="col-md-7 text-md-end">
                                    <div class="table-search">
                                        <div id="tablefilter"></div>
                                        <a href="/vehicles" class="btn btn-add mb-0"><i class="feather-plus-circle"></i>{{__('web.user.add_booking')}}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="custom-datatable-filter table-responsive table-loader vh-10">
                                @include('frontend.content-loader')
                            </div>
                            <div class="table-responsive dashboard-table d-none real-table">
                                <table class="table" id="bookingTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="booking-headers">{{__('web.user.booking_id')}}</th>
                                            <th>{{__('web.user.vehicle_name')}}</th>
                                            <th>{{__('web.common.rental_type')}}</th>
                                            <th>{{__('web.user.pickup_del_location')}}</th>
                                            <th>{{ __('web.user.drop_location') }}</th>
                                            <th>{{__('web.user.booked_on')}}</th>
                                            <th>{{__('web.common.total')}}</th>
                                            <th>{{__('web.common.status')}}</th>
                                            <th>{{__('web.common.action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /All Bookings -->
                <!-- Full Calendar -->
                <div class="row">
                     <div class="card calendar-loader position-relative d-none">
                           <div class="card-body vh-15">
                               @include('frontend.content-loader')
                           </div>
                     </div>
                </div>
                <div class="row d-none real-calendar" id="calendar_view">
                    <div class="col-lg-12">
                        <div class="card calendar-card mb-0 ">
                            <div class="card-body vh-15">
                                <div id="fullcalendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /Full Calendar -->
            </div>
            <!-- /Dashboard -->
        </div>
    </div>
    <!-- View Booking Details -->
    <div class="modal new-modal multi-step fade" id="booking_details" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <div class="booking-header">
                        <div class="booking-img-wrap">
                            <div class="book-img">
                                <img src="" alt="img" class="bk-img">
                            </div>
                            <div class="book-info">
                                <h6 class="bk-name"></h6>
                                <p><i class="feather-map-pin"></i> <span class="bk-location">{{__('web.user.location')}} : Miami St, Destin, FL 32550, USA</span></p>
                            </div>
                        </div>
                        <div class="book-amount">
                            <p>{{__('web.common.total')}} {{__('web.common.amount')}}</p>
                            <h6><span class="bk-amount"></span><a href="javascript:void(0);"></a></h6>
                        </div>
                    </div>
                    <div class="booking-group">
                        <div class="booking-wrapper">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">{{ __('web.user.booking') }} {{ __('web.user.details') }}</h5>
                                <div class="modal_footer">

                                </div>
                            </div>
                            <div class="row text-start mb-3">
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.booking')}} {{__('web.user.type')}}</h6>
                                        <p class="bk-type"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.common.rental_type')}}</h6>
                                        <p class="bk-rental"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.extra_services')}}</h6>
                                        <p class="bk-extra-service"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.delivery')}}</h6>
                                        <p class="bk-pickup-location"></p>
                                        <p class="bk-start-date"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.dropoff')}}</h6>
                                        <p class="bk-drop-location"></p>
                                        <p class="bk-end-date"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.common.status')}}</h6>
                                        <div class="bk-status"><span class="badge badge-light-secondary"></span></div>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.booked_on')}}</h6>
                                        <p class="bk-booked-on"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.common.start_date')}}</h6>
                                        <p class="bk-start-date"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.common.end_date')}}</h6>
                                        <p class="bk-end-date"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="booking-wrapper">
                            <div class="booking-title">
                                <h6>{{__('web.user.personal_details')}}</h6>
                            </div>
                            <div class="row">
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.details')}}</h6>
                                        <p class="user-name"></p>
                                        <p class="user-phone"></p>
                                        <p class="user-email"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.address')}}</h6>
                                        <p class="user-address"></p>
                                    </div>
                                </div>
                                <div class="col-lg-4 col-md-6">
                                    <div class="booking-view">
                                        <h6>{{__('web.user.passengers')}}</h6>
                                        <p class="user-passengers"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="cancel-reason-section"></div>
                    </div>
                    <div class="modal-btn modal-btn-sm text-end">
                        <button class="btn btn-light" data-bs-dismiss="modal">{{ __('web.common.close')}}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Booking Details Modal -->
    <!-- Cancel Ride Modal -->
    <div class="modal new-modal fade" id="cancel_ride" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('web.user.cancel_reason')}}</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form action="" id="cancelRideForm">
                        <input type="hidden" name="booking_id" id="booking_id">
                        <div class="modal-item">
                            <label>{{__('web.user.reason')}} <span class="text-danger">*</span></label>
                            <textarea class="form-control cancel-reason" rows="4" cols="30"></textarea>
                            <span class="text-danger error-text cancel-reason-error"></span>
                        </div>
                        <div class="modal-btn modal-btn-sm text-end">
                            <button class="btn btn-secondary" data-bs-dismiss="modal" type="button">{{__('web.common.cancel')}}</button>
                            <button type="submit" class="btn btn-primary submitbtn">{{__('web.common.submit')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal new-modal order-success-modal fade" id="ride_completed" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="order-success-info">
                        <span class="order-success-icon">
                            <img src="{{ asset('frontend/assets/img/icons/check-icon.svg') }}" alt="Icon">
                        </span>
                        <h4>{{__('web.common.successful')}}</h4>
                        <p>{{__('web.common.your_ride_completed')}}</p>
                        <div class="modal-btn">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                {{__('web.user.go_to')}} {{__('web.user.dashboard')}} <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Start Ride Modal -->
    <div class="modal new-modal order-success-modal fade" id="ride_started" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="order-success-info">
                        <span class="order-success-icon">
                            <img src="{{ asset('frontend/assets/img/icons/check-icon.svg') }}" alt="Icon">
                        </span>
                        <h4>{{__('web.common.successful')}}</h4>
                        <p>{{__('web.common.ride_started')}}</p>
                        <div class="modal-btn">
                            <a href="{{ route('user.dashboard') }}" class="btn btn-secondary">
                                {{__('web.user.go_to')}} {{__('web.user.dashboard')}} <i class="feather-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Start Ride Modal -->

    <!-- Delete Modal -->
    <div class="modal new-modal fade" id="delete_modal" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="delete-action">
                        <div class="delete-header">
                            <h4>{{__('web.common.delete')}} {{__('web.user.booking')}}</h4>
                            <p>{{__('web.common.want_to_delete')}}</p>
                        </div>
                        <div class="modal-btn">
                            <form action="" id="deleteForm">
                                <input type="hidden" name="delete_id" id="delete_id">
                                <div class="row">
                                    <div class="col-6">
                                        <button type="submit" id="deletebooking" data-bs-dismiss="modal" class="btn btn-secondary w-100 submitbtn">
                                            {{__('web.common.delete')}}
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn btn-primary w-100">
                                            {{__('web.common.cancel')}}
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete Modal -->

    <!-- Custom Date Modal -->
    <div class="modal new-modal fade" id="custom_date" data-keyboard="false" data-backdrop="static">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('web.common.custom_date')}}</h4>
                    <button type="button" class="close-btn" data-bs-dismiss="modal"><span>×</span></button>
                </div>
                <div class="modal-body">
                    <form action="#">
                        <div class="modal-form-group">
                            <label>{{__('web.common.start_date')}} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="custom_from_date">
                        </div>
                        <div class="modal-form-group">
                            <label>{{__('web.common.end_date')}} <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="custom_to_date">
                        </div>
                        <span class="text-danger error-text" id="custom_date_error"></span>
                        <div class="modal-btn modal-btn-sm text-end">
                            <a href="javascript:void(0);" id="apply-custom-filter" class="btn btn-primary">
                                {{__('web.common.apply')}}
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Custom Date Modal -->
    @endsection
    @push('scripts')
    <!-- Datatable JS -->
    <script src="{{ asset('frontend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/plugins/datatables/datatables.min.js') }}"></script>

    <!-- Fullcalendar JS -->
    <script src="{{ asset('frontend/assets/js/moment.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/plugins/fullcalendar/index.global.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/user/bookings.js') }}"></script>
    @endpush