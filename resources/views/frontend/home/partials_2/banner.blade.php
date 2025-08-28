    <section class="banner-section-four ">
        <div class="container">
            <div class="home-banner">
            <div class="row align-items-center">
                    <div class="col-lg-5" data-aos="fade-down">
                        <div class="banner-content">
                            <h1>{{ $section['section_title'] ?? "" }}</h1>
                            <p>{{ $section['section_content'][0]->description ?? "" }}
                            </p>
                            <div class="customer-list">
                                <div class="users-wrap">
                                    <ul class="users-list">
                                        @if(!empty($section['section_content'][0]->customer_images) && count($section['section_content'][0]->customer_images) > 0)
                                            @foreach($section['section_content'][0]->customer_images as $image)
                                        <li>
                                            <img src="{{ $image }}" class="img-fluid aos" alt="User Profile">
                                        </li>
                                            @endforeach
                                        @endif
                                    </ul>
                                    <div class="customer-info">
                                        <h4>{{ $section['section_content'][0]->customer_count }}+ {{ __('web.home.customers') }}</h4>
                                        <p>{{ __('web.home.has_used_rental') }} </p>
                                    </div>
                                </div>
                                <div class="view-all d-flex align-items-center gap-3">
                                    <a href="/vehicles" class="btn btn-primary d-inline-flex align-items-center">{{ __('web.home.rent_now') }}<i class="bx bx-right-arrow-alt ms-1"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7">
                        <div class="banner-image">
                            <div class="banner-img" data-aos="fade-down">
                                <div class="amount-icon">
                                    <span class="day-amt">
                                        <p>{{ __('web.home.starts_from') }}</p>
                                        <h6>$650 <span> /{{ __('web.home.day') }}</span></h6>
                                    </span>
                                </div>
                                <span class="rent-tag"><i class="bx bxs-circle"></i>{{ __('web.home.available_for_rent') }}</span>
                                <img src="{{ $section['section_content'][0]->thumbnail_image }}" class="img-fluid" alt="img">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @php
            $searchSection = $content_sections->where('section_type', 'search_section')->first();
            @endphp
            @if(!empty($searchSection))
            <div class="banner-search custom-banner-serch-item">
                <form action="{{ route('list') }}" method="GET" class="form-block d-flex align-items-center">
                    <div class="search-input">
                        <div class="input-block">
                            <label for="pickup-location-input">{{ __('web.home.pickup_location') }}</label>
                            <div class="group-img position-relative">
                                    <input type="text" name="pickuplocation" id="pickup-location-input" autocomplete="off" class="form-control" placeholder="{{ __('web.home.location_place_holder') }}">
                                    <span><i class="feather-map-pin"></i></span>
                                    <ul class="suggestions-list" id="pickup-suggestions"></ul>
                                </div>
                        </div>
                    </div>
                    <div class="search-input">
                        <div class="input-block">
                            <label for="drop-location-input">{{ __('web.user.drop_location') }}</label>
                            <div class="group-img position-relative">
                                <input type="text" name="droplocation" id="drop-location-input" autocomplete="off" class="form-control" placeholder="{{ __('web.home.location_place_holder') }}">
                                <span><i class="feather-map-pin"></i></span>
                                <ul class="suggestions-list" id="drop-suggestions"></ul>
                            </div>
                        </div>
                    </div>
                    <div class="search-input">
                        <div class="input-block">
                            <label for="pickupdatetime">{{ __('web.home.pickup_date_time') }}</label>
                            <div class="input-wrap">
                                    <input type="text" id="pickupdatetime" name="pickupdatetime" class="form-control flatpickr-pickupadtetime" autocomplete="off" placeholder="YYYY-MM-DD HH:MM">
                                    <span class="input-icon"><i class="bx bx-chevron-down"></i></span>
                            </div>
                        </div>
                    </div>
                    <div class="search-input input-end">
                        <div class="input-block">
                            <label for="returndatetime">{{ __('web.home.return_date_time') }}</label>
                            <div class="input-wrap">
                                    <input type="text" id="returndatetime" name="returndatetime" class="form-control flatpickr-dropdatetime" autocomplete="off" placeholder="YYYY-MM-DD HH:MM">
                                    <span class="input-icon"><i class="bx bx-chevron-down"></i></span>
                                </div>
                        </div>
                    </div>
                    <div class="search-btn">
                        <button class="btn btn-primary searchbtn" type="submit"><i class="bx bx-search-alt"></i></button>
                    </div>
                </form>
            </div>
            @endif
        </div>
        <div class="banner-bgs">
            <img src="{{ asset('/frontend/assets/img/bg/banner-bg-01.png') }}" class="bg-01 img-fluid" alt="img">
        </div>
    </section>
