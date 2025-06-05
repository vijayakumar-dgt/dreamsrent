@php 
    $sectionContent = $section['section_content'];
    $sectionContent = isset($section['section_content'][0]) ? $section['section_content'][0] : [];
    $vehicle_types = $data['vehicle_types'] ?? [];
    $locations = $data['locations'] ?? [];
@endphp
<!-- Banner -->
<section class="banner-section banner-sec-two banner-slider">	
    <div class="banner-img-slider owl-carousel">
        @if(!empty($sectionContent->thumbnail_images) && count($sectionContent->thumbnail_images) > 0)
            @foreach($sectionContent->thumbnail_images as $image)
        <div class="slider-img">
            <img src="{{ $image }}" alt="Img">
        </div>
            @endforeach
        @endif
    </div>	
    <div class="container">
        <div class="home-banner">		
            <div class="row align-items-center">					    
                <div class="col-md-12">
                    <div class="hero-sec-contents">
                        <div class="banner-title">
                            <h1>{{ $sectionContent->label ?? "" }} 
                                <span>{{ $sectionContent->higlight_label ?? "" }}</span>
                            </h1>
                            <p>{{ $sectionContent->description}}</p>
                        </div>
                        <div class="banner-form">
                            <form action="{{ route('list') }}">
                                <div class="banner-search-list">
                                    <div class="input-block">
                                        <label><i class="bx bx-map"></i>{{ __('web.user.location') }}</label>
                                        <select class="select" name="pickuplocation">
                                           <option value="">{{ __('web.common.select') }}</option>
                                            @if(!empty($locations) && count($locations) > 0)
                                            @foreach ($locations as $location)
                                                <option value="{{ $location->name }}">{{ $location->name ?? "" }}</option>
                                            @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bx-calendar"></i>{{ __('web.home.pickup_date') }}</label>
                                        <div class="date-widget">												
                                            <div class="group-img">
                                            <input type="text" class="form-control datetimepicker" placeholder="DD-MM-YYYY" name="pickupdate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bx-calendar"></i>{{ __('web.home.return_date') }}</label>
                                        <div class="date-widget">												
                                            <div class="group-img">
                                            <input type="text" class="form-control datetimepicker" placeholder="DD-MM-YYYY" name="returndate">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bxs-ship"></i>{{ __('web.home.yacht_type') }}</label>
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
                                <div class="input-block-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bx bx-search-alt me-2"></i> {{ __('web.common.search') }}
                                    </button>
                                </div>
                            </form>
                        </div>
                        <div class="banner-user-group text-center">
                            <ul>
                                @if(!empty($sectionContent->customer_images) && count($sectionContent->customer_images) > 0)
                                    @foreach($sectionContent->customer_images as $image)
                                    <li>
                                        <a href="javascript:void(0);"><img src="{{ $image }}" alt="Img"></a>
                                    </li>
                                    @endforeach
                                @endif
                                <li class="users-text">
                                    <h5>{{ $sectionContent->customer_count ?? 0 }} + {{ __('web.home.customers') }}</h5>
                                    <span>{{ __('web.home.has_used_rental') }} </span>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>	
        <div class="video-btn text-center">
            <a href="https://www.youtube.com/embed/ExJZAegsOis" data-fancybox><span><i class="bx bx-play"></i></span></a>
            <h6>{{ __('web.home.check_our_video') }}</h6>
        </div>
    </div>
</section>
<!-- /Banner -->