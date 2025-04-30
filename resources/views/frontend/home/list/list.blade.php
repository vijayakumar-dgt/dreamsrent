@extends($layout)
@push('styles')
<!-- Rangeslider CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/ion-rangeslider/css/ion.rangeSlider.min.css') }}">
@endpush
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar vehiclelist-brudgrumb">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12" id="general-settings" data-allow_booking="{{ $allowBooking }}" data-allow_enquiry="{{ $allowEnquiries }}">
                <h2 class="breadcrumb-title">{{__('web.home.car_listings')}}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{ __('web.home.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="/vehicles">{{ __('web.common.vehicles') }}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('web.home.car_listings') }}</li>
                    </ol>
                </nav>							
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->
<!-- Search -->	
<div class="section-search page-search"> 
    <div class="container">	  
        <div class="search-box-banner">
            <ul class="align-items-center">
                <li class="column-group-main">
                    <div class="input-block">
                        <label>{{ __('web.home.pickup_location') }}</label>												
                        <div class="group-img">
                            <input type="text" class="form-control" autocomplete="off" value="{{ $pickuplocation }}" id="pickuplocation" placeholder="{{ __('web.home.location_place_holder') }}">
                            <span><i class="feather-map-pin"></i></span>
                            <ul class="suggestions-list" id="pickup-suggestions"></ul>
                        </div>
                    </div>
                    <input type="hidden" id="initialPickupId" value="{{ $initialPickupLocation->id ?? '' }}">
                    <input type="hidden" id="initialPickupName" value="{{ $initialPickupLocation->name ?? '' }}">
                </li>
                <li class="column-group-main">						
                    <div class="input-block">																	
                        <label>{{ __('web.home.pickup_date') }}</label>
                    </div>
                    <div class="input-block-wrapp">
                        <div class="input-block date-widget">												
                            <div class="group-img">
                            <input type="text" class="form-control listpickupdate" autocomplete="off" id="pickupdate" name="pickupdate" value="{{ $pickupdate }}" placeholder="dd-mm-yyyy">
                            <span><i class="feather-calendar"></i></span>
                            </div>
                        </div>
                        <div class="input-block time-widge">											
                            <div class="group-img">
                            <input type="text" class="form-control listtimepicker" autocomplete="off" id="pickuptime" name="pickuptime" value="{{ $pickuptime }}" placeholder="hh:mm">
                            <span><i class="feather-clock"></i></span>
                            </div>
                        </div>
                    </div>	
                </li>
                <li class="column-group-main">						
                    <div class="input-block">																	
                        <label>{{ __('web.home.return_date') }}</label>
                    </div>
                    <div class="input-block-wrapp">
                        <div class="input-block date-widge">												
                            <div class="group-img">
                            <input type="text" class="form-control listreturndate"  id="returndate" autocomplete="off" name="returndate" value="{{ $returndate }}" placeholder="dd-mm-yyyy">
                            <span><i class="feather-calendar"></i></span>
                            </div>
                        </div>
                        <div class="input-block time-widge">											
                            <div class="group-img">
                            <input type="text" class="form-control listreturntimepicker" autocomplete="off" id="returntime" name="returntime" value="{{ $returntime }}" placeholder="hh:mm">
                            <span><i class="feather-clock"></i></span>
                            </div>
                        </div>
                    </div>	
                </li>
                <li class="column-group-last">
                    <div class="input-block">
                        <div class="search-btn">
                            <button class="btn search-button" type="button" id="filterbtn"> <i class="fa fa-search" aria-hidden="true"></i>{{ __('web.home.search') }}</button>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>	
</div>	
<!-- /Search -->
<!-- Sort By -->	
<div class="sort-section">
    <div class="container">
        <div class="sortby-sec">
            <div class="sorting-div">
                <div class="row d-flex align-items-center">
                    <div class="col-xl-4 col-lg-3 col-sm-12 col-12">
                        <div class="count-search">
                            <p id="total_vehicles"></p>
                        </div>
                    </div>
                    <div class="col-xl-8 col-lg-9 col-sm-12 col-12">
                        <div class="product-filter-group">
                            <div class="sortbyset">
                                <ul>
                                    <li>
                                        <span class="sortbytitle">{{__('web.home.show')}} : </span>
                                        <div class="sorting-select select-one">
                                            <select class="form-control select" id="pageLength">
                                                <option value="12">12</option>
                                                <option value="18">18</option>
                                                <option value="24">24</option>
                                            </select>
                                        </div>
                                    </li>
                                    <li>
                                        <span class="sortbytitle">{{__('web.home.sort_by')}} </span>
                                        <div class="sorting-select select-two">
                                            <select class="form-control select" id="sortBy">
                                                <option value="latest">{{__('web.home.newest')}}</option>
                                                <option value="low_to_high">{{__('web.home.low_to_high')}}</option>
                                                <option value="high_to_low">{{__('web.home.high_to_low')}}</option>
                                            </select>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="grid-listview">
                                <ul>
                                    <li>
                                        <a href="javascript:void(0);" class="active viewType" id="gridView" data-view="grid">
                                            <i class="feather-grid"></i>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" class="viewType" id="listView" data-view="list">
                                            <i class="feather-list"></i>
                                        </a>
                                    </li>
                                   
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- /Sort By -->	
<!-- Car Grid View -->
<section class="section car-listing pt-0">
    <div class="container">
        <div class="row">
            @include('frontend.home.list.filter')
            <div class="col-lg-9 grid_loader_div d-none">
                <div class="row">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="col-xxl-4 col-lg-6 col-md-6 col-12 grid-list-skeleton">
                            <div class="listing-item">
                                <div class="listing-img">
                                    <div class="img-slider-skeleton skeleton"></div>
                                    <div class="fav-item justify-content-end">
                                        <span class="img-count-skeleton skeleton"></span>
                                        <span class="fav-icon-skeleton skeleton"></span>
                                    </div>
                                    <span class="featured-text-skeleton skeleton"></span>
                                </div>
                                <div class="listing-content">
                                    <div class="listing-features d-flex align-items-end justify-content-between">
                                        <div class="list-rating-skeleton">
                                            <h3 class="listing-title-skeleton skeleton"></h3>
                                            <div class="list-rating-icons">
                                                @for ($j = 0; $j < 5; $j++)
                                                    <span class="star-skeleton skeleton"></span>
                                                @endfor
                                                <span class="reviews-skeleton skeleton"></span>
                                            </div>
                                        </div>
                                        <div class="list-km-skeleton skeleton"></div>
                                    </div>
                                    <div class="listing-details-group">
                                        @for ($k = 0; $k < 2; $k++)
                                            <ul>
                                                @for ($l = 0; $l < 3; $l++)
                                                    <li class="skeleton-detail skeleton"></li>
                                                @endfor
                                            </ul>
                                        @endfor
                                    </div>
                                    <div class="listing-location-detailsx">
                                        <div class="input-skeleton skeleton"></div>
                                    </div>
                                    <div class="listing-location-detailsx mt-3">
                                        <div class="input-skeleton skeleton"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="col-xl-9 col-lg-8 col-sm-12 col-12 d-none list_loader_div">
                <div class="row">
                    @for ($i = 0; $i < 3; $i++)
                        <div class="list_view-container">
                            <div class="list_view-img-slider-skeleton list_view-skeleton"></div>
                            <div class="list_view-content">
                                <div>
                                    <div class="list_view-title-rating">
                                        <div class="list_view-title-skeleton list_view-skeleton"></div>
                                        <div class="list_view-rating-skeleton d-flex">
                                            @for ($j = 0; $j < 5; $j++)
                                                <span class="list_view-skeleton"></span>
                                            @endfor
                                            <span class="list_view-reviews-skeleton list_view-skeleton"></span>
                                        </div>
                                    </div>
                                    <ul class="list_view-details-group-skeleton">
                                        @for ($k = 0; $k < 4; $k++)
                                            <li class="list_view-skeleton"></li>
                                        @endfor
                                    </ul>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="list_view-author-img-skeleton list_view-skeleton"></span>
                                        <span class="list_view-location-skeleton list_view-skeleton ml-2"></span>
                                    </div>
                                    <span class="list_view-btn-skeleton list_view-skeleton"></span>
                                </div>
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
            <div class="col-lg-9 listCardDiv d-none">
                <div class="row">
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/home/list.js?v=1.1') }}"></script>

<!-- Rangeslider JS -->
<script src="{{ asset('frontend/assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js') }}"></script>
<script src="{{ asset('frontend/assets/plugins/ion-rangeslider/js/custom-rangeslider.js') }}"></script>

<!-- Sticky Sidebar JS -->
<script src="{{ asset('frontend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('frontend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>	
@endpush