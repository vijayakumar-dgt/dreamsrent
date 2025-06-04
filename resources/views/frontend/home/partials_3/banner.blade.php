@php 
    $section_content = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(2, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
    $vehicle_types = $data['vehicle_types'] ?? [];
    $vehicle_models = $data['vehicle_models'] ?? [];
    $locations = $data['locations'] ?? [];
@endphp
<!-- Banner -->
<section class="banner-section banner-slider">		
    <div class="container">
        <div class="home-banner">		
            <div class="row align-items-center">					    
                <div class="col-lg-7" data-aos="fade-down">
                    <h1>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "" }} </span></h1>
                    <h4>{{ $section_content[0]->description ?? ""}}</h4>
                    <div class="banner-search">
                        <form action="{{ route('list') }}" class="form-block d-flex align-items-center">
                            <div class="search-input">
                                <div class="input-block">
                                    <label>{{ __('web.home.any_type') }}</label>
                                    <select class="select" name="category">
                                        <option value="">{{ __('web.common.select') }}</option>
                                        @if(!empty($vehicle_types) && count($vehicle_types) > 0)
                                        @foreach ($vehicle_types as $vehicle_type)
                                            <option value="{{ $vehicle_type->id }}">{{ $vehicle_type->name ?? "" }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="search-input">
                                <div class="input-block">
                                    <label>{{ __('web.home.model') }}</label>
                                    <select class="select" name="vm">
                                        <option value="">{{ __('web.common.select') }}</option>
                                        @if(!empty($vehicle_models) && count($vehicle_models) > 0)
                                        @foreach ($vehicle_models as $vehicle_model)
                                            <option value="{{ $vehicle_model->id }}">{{ $vehicle_model->name ?? "" }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="search-input">
                                <div class="input-block">
                                    <label>{{ __('web.user.location') }}</label>
                                    <select class="select" name="pickuplocation">
                                        <option value="">{{ __('web.common.select') }}</option>
                                        @if(!empty($locations) && count($locations) > 0)
                                        @foreach ($locations as $location)
                                            <option value="{{ $location->name }}">{{ $location->name ?? "" }}</option>
                                        @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="search-btn">
                                <button class="btn btn-primary" type="submit"><i class="bx bx-search-alt"></i>{{ __('web.user.search') }}</button>
                            </div>
                        </form>
                    </div>
                    <p>{{ __('web.home.theme_3_banner_text') }}</p>
                    <div class="customer-list">
                        <div class="users-wrap">
                            <ul class="users-list">
                                @if(!empty($section_content[0]->customer_images) && count($section_content[0]->customer_images) > 0)
                                @foreach ($section_content[0]->customer_images as $avatar)
                                <li>
                                    <img src="{{ $avatar }}" class="img-fluid aos" alt="bannerimage">
                                </li>
                                @endforeach
                                @endif
                            </ul>
                            <div class="customer-info">
                                <h4>{{ $section_content[0]->customer_count ?? 0}} + {{ __('web.home.customers') }}</h4>
                                <p>{{ __('web.home.has_used_rental') }} </p>
                            </div>
                        </div>
                        <div class="view-all">
                            <a href="{{ route('list') }}" class="btn btn-view d-inline-flex align-items-center">{{ __('web.home.rent_a_bike') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>	
    </div>
    <div class="banner-image">
        <div class="banner-bg-img"   data-aos="fade-left">
            <img src="{{ $section_content[0]->thumbnail_image ?? "" }}" class="img-fluid" alt="img">
            <div class="banner-bg">
                <img src="{{ asset('frontend/assets/img/bg/ban-bg.png') }}" class="img-fluid" alt="img">
            </div>
        </div>
    </div>
    <div class="banner-bgs">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-01.png') }}" class="shape-01 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-02.png') }}" class="shape-02 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-03.png') }}" class="shape-03 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-04.png') }}" class="shape-04 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-05.png') }}" class="shape-05 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-06.png') }}" class="shape-06 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-07.png') }}" class="shape-07 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-05.png') }}" class="shape-08 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-06.png') }}" class="shape-09 img-fluid" alt="img">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-03.png') }}" class="shape-10 img-fluid" alt="img">
    </div>
</section>
<!-- /Banner -->