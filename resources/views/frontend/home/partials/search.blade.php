        <!-- Search -->
        <div class="section-search">
            <div class="container">
                <div class="search-box-banner">
                    <form action="{{ route('list') }}" method="GET">
                        <ul class="align-items-center">
                            <li class="column-group-main position-relative">
                                <div class="input-block">
                                    <label>{{ __('web.home.pickup_location') }}</label>
                                    <div class="group-img position-relative">
                                        <input type="text" name="pickuplocation" id="pickup-location-input" autocomplete="off" class="form-control" placeholder="{{ __('web.home.location_place_holder') }}">
                                        <span><i class="feather-map-pin"></i></span>
                                        <ul class="suggestions-list" id="pickup-suggestions"></ul>
                                    </div>
                                </div>
                            </li>
                            <li class="column-group-main">
                                <div class="input-block">
                                    <label>{{ __('web.home.pickup_date') }}</label>
                                </div>
                                <div class="input-block-wrapp">
                                    <div class="input-block date-widget">
                                        <div class="group-img">
                                        <input type="text" class="form-control homepickupdate" name="pickupdate" id="pickupdate" placeholder="dd-mm-yyyy" autocomplete="off">
                                        <span><i class="feather-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="input-block time-widge">
                                        <div class="group-img">
                                        <input type="text" class="form-control hometimepicker" name="pickuptime" id="pickuptime" placeholder="hh:mm" autocomplete="off">
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
                                        <input type="text" class="form-control homereturndate" name="returndate" id="returndate" placeholder="dd-mm-yyyy" autocomplete="off">
                                        <span><i class="feather-calendar"></i></span>
                                        </div>
                                    </div>
                                    <div class="input-block time-widge">
                                        <div class="group-img">
                                        <input type="text" class="form-control homereturntimepicker" name="returntime" id="returntime" placeholder="hh:mm" autocomplete="off">
                                        <span><i class="feather-clock"></i></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                            <li class="column-group-last">
                                <div class="input-block">
                                    <div class="search-btn">
                                        <button class="btn search-button searchbtn" type="submit"> <i class="fa fa-search" aria-hidden="true"></i>{{__('web.home.search')}}</button>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </form>
                </div>
            </div>
        </div>
        <!-- /Search -->
