@extends($layout)
@section('content')
    <!-- Breadscrumb Section -->
    <div class="breadcrumb-bar">
        <div class="container">
            <div class="row align-items-center text-center">
                <div class="col-md-12 col-12">
                    <h2 class="breadcrumb-title">{{__('web.user.user_wishlist')}}</h2>
                    <nav aria-label="breadcrumb" class="page-breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="/">{{__('web.home.home')}}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('web.user.user_wishlist')}}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>
    </div>
    <!-- /Breadscrumb Section -->
    @include('frontend.user.nav_menu')
    <div class="content dashboard-content">
        <div class="container">

            <!-- Content Header -->
            <div class="content-header">
                <h4>{{__('web.user.wishlist')}}</h4>
            </div>
            <!-- /Content Header -->

            <div class="row">

                <!-- Wishlist -->
                <div class="col-md-12 data-loader">
                    <div class="row">
                        <div class="list_view-container">
                            <div class="list_view-img-slider-skeleton list_view-skeleton"></div>
                            <div class="list_view-content">
                                <div>
                                    <div class="list_view-title-rating">
                                        <div class="list_view-title-skeleton list_view-skeleton"></div>
                                        <div class="list_view-rating-skeleton d-flex">
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-reviews-skeleton list_view-skeleton"></span>
                                        </div>
                                    </div>
                                    <ul class="list_view-details-group-skeleton">
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="list_view-author-img-skeleton list_view-skeleton"></span>
                                        <span class="list_view-location-skeleton list_view-skeleton"
                                            style="margin-left: 10px;"></span>
                                    </div>
                                    <span class="list_view-btn-skeleton list_view-skeleton"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="list_view-container">
                            <div class="list_view-img-slider-skeleton list_view-skeleton"></div>
                            <div class="list_view-content">
                                <div>
                                    <div class="list_view-title-rating">
                                        <div class="list_view-title-skeleton list_view-skeleton"></div>
                                        <div class="list_view-rating-skeleton d-flex">
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-reviews-skeleton list_view-skeleton"></span>
                                        </div>
                                    </div>
                                    <ul class="list_view-details-group-skeleton">
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="list_view-author-img-skeleton list_view-skeleton"></span>
                                        <span class="list_view-location-skeleton list_view-skeleton"
                                            style="margin-left: 10px;"></span>
                                    </div>
                                    <span class="list_view-btn-skeleton list_view-skeleton"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="list_view-container">
                            <div class="list_view-img-slider-skeleton list_view-skeleton"></div>
                            <div class="list_view-content">
                                <div>
                                    <div class="list_view-title-rating">
                                        <div class="list_view-title-skeleton list_view-skeleton"></div>
                                        <div class="list_view-rating-skeleton d-flex">
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-skeleton"></span>
                                            <span class="list_view-reviews-skeleton list_view-skeleton"></span>
                                        </div>
                                    </div>
                                    <ul class="list_view-details-group-skeleton">
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                        <li class="list_view-skeleton"></li>
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="list_view-author-img-skeleton list_view-skeleton"></span>
                                        <span class="list_view-location-skeleton list_view-skeleton"
                                            style="margin-left: 10px;"></span>
                                    </div>
                                    <span class="list_view-btn-skeleton list_view-skeleton"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 d-none real-table">
                    <div class="wishlist-wrap">
                        <div class="listview-car">

                        </div>
                    </div>
                    <!-- /Wishlist -->
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="{{ asset('frontend/assets/js/custom/user/wishlists.js') }}"></script>
@endpush
