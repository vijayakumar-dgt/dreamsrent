<div class="col-xl-3 col-lg-4 col-sm-12 col-12 theiaStickySidebar">
        <div class="sidebar-heading">
            <h3>{{ __('web.home.what_are_you_looking_for') }}</h3>
        </div>
        <div class="product-search">
            <div class="form-custom">
                <input type="text" class="form-control" id="keyword" placeholder="">
                <span><img src="{{ asset('frontend/assets/img/icons/search.svg') }}" alt="img"></span>
            </div>
        </div>
        <div class="product-availability">
            <h6>{{ __('web.common.availability') }}</h6>
            <div class="status-toggle">
                  <input id="status" class="check" type="checkbox" checked>
                <label for="status" class="checktoggle">checkbox</label>
            </div>
        </div>
        <div class="accord-list">
            <div class="accordion" id="accordionMain1">
                <div class="card-header-new" id="headingOne">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                            {{ __('web.home.brand') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"  data-bs-parent="#accordionExample1">
                    <div class="card-body-chat">
                        <div class="row">
                            <div class="col-md-12">
                                <div id="checkBoxes1">
                                    <div class="selectBox-cont">
                                        @if(!empty($brands) && count($brands) > 0)
                                        @foreach($brands as $brand)
                                        <label class="custom_check w-100">
                                            <input type="checkbox" name="username" value="{{ $brand->id ?? "" }}" class="brands">
                                            <span class="checkmark"></span>  {{ $brand->brand_name ?? "" }}
                                        </label>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain2">
                <div class="card-header-new" id="headingTwo">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                            {{ __('web.home.vehicle_type') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div id="checkBoxes2">
                            <div class="selectBox-cont">
                                @if(!empty($vehicleTypes) && count($vehicleTypes) > 0)
                                @foreach($vehicleTypes as $vehicleType)
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="username" class="vehicle_types" value="{{ $vehicleType['id'] ?? "" }}" @if(request()->category == $vehicleType['id']) checked @endif>
                                    <span class="checkmark"></span>{{ $vehicleType['name'] ?? "" }} ({{ $vehicleType['vehicle_count'] ?? 0 }})
                                </label>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain3">
                <div class="card-header-new" id="headingYear">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseYear" aria-expanded="true" aria-controls="collapseYear">
                            {{__('web.home.year') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseYear" class="collapse" aria-labelledby="headingYear"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div id="checkBoxes02">
                            <div class="selectBox-cont">
                                @if(!empty($years) && count($years) > 0)
                                @foreach($years as $year)
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="username" class="years" value="{{ $year ?? "" }}">
                                    <span class="checkmark"></span>{{ $year ?? "" }}
                                </label>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain04">
                <div class="card-header-new" id="headingfuel">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapsefuel" aria-expanded="true" aria-controls="collapsefuel">
                            {{ __('web.home.fuel_type') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapsefuel" class="collapse" aria-labelledby="headingfuel"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div class="fuel-list">
                            <ul>
                                @if(!empty($fuelTypes) && count($fuelTypes) > 0)
                                @foreach($fuelTypes as $fuelType)
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="color" id="{{ $fuelType->fuel_type ?? ""}}" class="fuel_types" value="{{ $fuelType->id ?? ""}}">
                                        <label for="{{ $fuelType->fuel_type ?? ""}}">{{ $fuelType->fuel_type ?? "" }}</label>
                                    </div>
                                </li>
                                @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain5">
                <div class="card-header-new" id="headingmileage">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapsemileage" aria-expanded="true" aria-controls="collapsemileage">
                            {{ __('web.home.mileage') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapsemileage" class="collapse" aria-labelledby="headingmileage"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div class="fuel-list">
                            <ul>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="mileage" class="mileage" id="limited" value="1">
                                        <label for="limited">{{ __('web.home.limited') }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="mileage" class="mileage" id="unlimited" value="0">
                                        <label for="unlimited">{{ __('web.home.unlimited') }}</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain6">
                <div class="card-header-new" id="headingrental">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapserental" aria-expanded="true" aria-controls="collapserental">
                            {{ __('web.home.rental_type')}}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapserental" class="collapse" aria-labelledby="headingrental"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div class="fuel-list">
                            <ul>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="rental_type" id="any" value="daily">
                                        <label for="any">{{ __('web.home.daily') }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="rental_type" id="day" value="weekly">
                                        <label for="day">{{__('web.home.weekly')}}</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="rental_type" id="hour" value="monthly">
                                        <label for="hour">{{ __('web.home.monthly') }}</label>
                                    </div>
                                </li>
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="rental_type" id="week" value="yearly">
                                        <label for="week">{{ __('web.home.yearly') }}</label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain06">
                <div class="card-header-new" id="headingspec">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapsespec" aria-expanded="true" aria-controls="collapsespec">
                            {{ __('web.home.car_specifications') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapsespec" class="collapse" aria-labelledby="headingspec"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div id="checkBoxes20">
                            <div class="selectBox-cont">
                                @if(!empty($features) && count($features) > 0)
                                @foreach($features as $feature)
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="username" class="features" value="{{ $feature->id ?? ""}}">
                                    <span class="checkmark"></span> {{ $feature->feature ?? "" }}
                                </label>
                                @endforeach
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain7">
                <div class="card-header-new" id="headingColor">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseColor" aria-expanded="true" aria-controls="collapseColor">
                            {{ __('web.home.colors') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseColor" class="collapse" aria-labelledby="headingColor"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div class="theme-colorsset">
                            <ul>
                                @if(!empty($colors) && count($colors) > 0)
                                    @foreach($colors as $color)
                                <li>
                                    <div class="input-themeselects">
                                        <input type="radio" name="color" class="colors" id="{{ $color->name ?? ""}}" value="{{ $color->id ?? ""}}">
                                        <label for="{{ $color->name ?? ""}}" class="{{ $color->name ?? ""}}" style="background-color:{{ $color->value ?? ""}}"></label>
                                    </div>
                                </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain8">
                <div class="card-header-new" id="headingThree">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                            {{ __('web.home.capacity') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseThree" class="collapse" aria-labelledby="headingThree"  data-bs-parent="#accordionExample3">
                    <div class="card-body-chat">
                        <div id="checkBoxes3">
                            <div class="selectBox-cont">
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="bystatus" class="capacity" value="2">
                                    <span class="checkmark"></span> {{ __('web.home.2_seat') }}
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="bystatus" class="capacity" value="4">
                                    <span class="checkmark"></span> {{ __('web.home.4_seat') }}
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="bystatus" class="capacity" value="5">
                                    <span class="checkmark"></span>{{ __('web.home.5_seat') }}
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="bystatus" class="capacity" value="7">
                                    <span class="checkmark"></span> {{ __('web.home.7_seat') }}
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain9">
                <div class="card-header-new" id="headingFour">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                            {{ __('web.home.price') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseFour" class="collapse" aria-labelledby="headingFour"  data-bs-parent="#accordionExample4">
                    <div class="card-body-chat">
                        <div class="filter-range">
                            <input type="text"  class="price-range">
                        </div>
                    </div>
                </div>
            </div>
              <div class="accordion" id="accordionMain4">
                <div class="card-header-new" id="headingtransmiss">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapsetransmission" aria-expanded="true" aria-controls="collapsetransmission">
                            {{ __('web.home.transmission') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapsetransmission" class="collapse" aria-labelledby="headingtransmiss"  data-bs-parent="#accordionExample2">
                    <div class="card-body-chat">
                        <div class="fuel-list">
                            <ul>
                                @if(!empty($transmissions) && count($transmissions) > 0)
                                    @foreach($transmissions as $k => $transmission)
                                <li>
                                    <div class="input-selection">
                                        <input type="radio" name="transmission" id="{{ $transmission->name ?? ""}}" value="{{ $transmission->id ?? ""}}" class="transmissions">
                                        <label for="{{ $transmission->name ?? ""}}">{{ $transmission->name ?? ""}}	</label>
                                    </div>
                                </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="accordion" id="accordionMain10">
                <div class="card-header-new" id="headingFive">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                            {{ __('web.home.rating') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseFive" class="collapse" aria-labelledby="headingFive"  data-bs-parent="#accordionExample5">
                    <div class="card-body-chat">
                        <div id="checkBoxes4">
                            <div class="selectBox-cont">
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="rating" value="5" class="ratings">
                                    <span class="checkmark"></span>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <span class="rating-count">5.0</span>
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="rating" value="4" class="ratings">
                                    <span class="checkmark"></span>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="rating-count">4.0</span>
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="rating" value="3" class="ratings">
                                    <span class="checkmark"></span>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="rating-count">3.0</span>
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="rating" value="2" class="ratings">
                                    <span class="checkmark"></span>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="rating-count">2.0</span>
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="rating" value="1" class="ratings">
                                    <span class="checkmark"></span>
                                    <i class="fas fa-star filled"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <i class="fas fa-star"></i>
                                    <span class="rating-count">1.0</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- <div class="accordion" id="accordionMain11">
                <div class="card-header-new" id="headingSix">
                    <h6 class="filter-title">
                        <a href="javascript:void(0);" class="w-100 collapsed"  data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                            {{ __('web.home.customer_recomendation') }}
                            <span class="float-end"><i class="fa-solid fa-chevron-down"></i></span>
                        </a>
                    </h6>
                </div>
                <div id="collapseSix" class="collapse" aria-labelledby="headingSix"  data-bs-parent="#accordionExample6">
                    <div class="card-body-chat">
                        <div id="checkBoxes5">
                            <div class="selectBox-cont">
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="category">
                                    <span class="checkmark"></span> 70% & up
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="category">
                                    <span class="checkmark"></span> 60% & up
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="category">
                                    <span class="checkmark"></span> 50% & up
                                </label>
                                <label class="custom_check w-100">
                                    <input type="checkbox" name="category">
                                    <span class="checkmark"></span> 40% & up
                                </label>
                                <div class="viewall-Two">
                                    <label class="custom_check w-100">
                                        <input type="checkbox" name="username">
                                        <span class="checkmark"></span>30% & up
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
          <button type="submit" id="filter" class="d-inline-flex align-items-center justify-content-center btn w-100 btn-primary filter-btn">
            <span><i class="feather-filter me-2"></i></span>{{ __('web.common.filter_results') }}
          </button>
        <a href="javascript:void(0);" class="reset-filter">{{ __('web.common.reset_filter') }}</a>
 </div>
