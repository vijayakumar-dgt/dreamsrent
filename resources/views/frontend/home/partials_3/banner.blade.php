@php 
    $section_content = $section['section_content'];
    $titleRaw = $section['section_title'];
    $titleWords = explode(' ', $titleRaw);
    $wordCount = count($titleWords);

    $lastPartCount = min(2, $wordCount);

    $titleMain = implode(' ', array_slice($titleWords, 0, -$lastPartCount));
    $titleLastPart = implode(' ', array_slice($titleWords, -$lastPartCount));
    $vehicle_types = $data['vehicle_types'] ?? [];
@endphp
<!-- Banner -->
<section class="banner-section banner-slider">		
    <div class="container">
        <div class="home-banner">		
            <div class="row align-items-center">					    
                <div class="col-lg-7" data-aos="fade-down">
                    <h1>{{ $titleMain ?? "" }} <span>{{ $titleLastPart ?? "" }} </span></h1>
                    <h4>{{ optional($section_content[0])->description }}</h4>
                    <div class="banner-search">
                        <form action="{{ route('list') }}" class="form-block d-flex align-items-center">
                            <div class="search-input">
                                <div class="input-block">
                                    <label>Any type</label>
                                    <select class="select" name="vehicle_type_id">
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
                                    <label>Model</label>
                                    <select class="select">
                                        <option>KTM 300</option>
                                        <option>KTM RC 390</option>
                                    </select>
                                </div>
                            </div>
                            <div class="search-input">
                                <div class="input-block">
                                    <label>Location</label>
                                    <select class="select">
                                        <option>Newyork</option>
                                        <option>Los Angeles</option>
                                    </select>
                                </div>
                            </div>
                            <div class="search-btn">
                                <button class="btn btn-primary" type="submit"><i class="bx bx-search-alt"></i>Search</button>
                            </div>
                        </form>
                    </div>
                    <p>Experience the ultimate freedown of Dreamsrental - tailor adventure by choosing from Premium  bikes</p>
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
                                <h4>{{ optional($section_content[0])->customer_count }} + Customers</h4>
                                <p>has used our renting services </p>
                            </div>
                        </div>
                        <div class="view-all">
                            <a href="{{ route('list') }}" class="btn btn-view d-inline-flex align-items-center">Rent a Bike</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>	
    </div>
    <div class="banner-image">
        <div class="banner-bg-img"   data-aos="fade-left">
            <img src="{{ optional($section_content[0])->thumbnail_image }}" class="img-fluid" alt="img">
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