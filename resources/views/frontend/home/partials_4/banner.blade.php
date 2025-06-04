@php 
    $sectionContent = $section['section_content'];
    $sectionContent = isset($section['section_content'][0]) ? $section['section_content'][0] : [];
@endphp
<!-- Banner -->
<section class="banner-section banner-sec-two banner-slider">	
    <div class="banner-img-slider owl-carousel">
        <div class="slider-img">
            <img src="/frontend/assets/img/bg/home-banner-img.png" alt="Img">
        </div>
        <div class="slider-img">
            <img src="/frontend/assets/img/bg/home-banner-img-02.png" alt="Img">
        </div>
        <div class="slider-img">
            <img src="/frontend/assets/img/bg/home-banner-img-03.png" alt="Img">
        </div>
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
                            <form action="listing-grid.html">
                                <div class="banner-search-list">
                                    <div class="input-block">
                                        <label><i class="bx bx-map"></i>Location</label>
                                        <select class="select">
                                            <option>Choose Location</option>
                                            <option>Newyork</option>
                                        </select>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bx-calendar"></i>Pickup Date</label>
                                        <div class="date-widget">												
                                            <div class="group-img">
                                            <input type="text" class="form-control datetimepicker" placeholder="04/11/2023">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bx-calendar"></i>Pickup Date</label>
                                        <div class="date-widget">												
                                            <div class="group-img">
                                            <input type="text" class="form-control datetimepicker" placeholder="04/11/2023">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-block">
                                        <label><i class="bx bxs-ship"></i>Yacht Type</label>
                                        <select class="select">
                                            <option>Catamaran</option>
                                            <option>Motor yachts</option>
                                            <option>Sailing yachts</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="input-block-btn">
                                    <button class="btn btn-primary" type="submit">
                                        <i class="bx bx-search-alt me-2"></i> Search
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
                                    <h5>{{ $sectionContent->customer_count ?? 0 }} + Customers</h5>
                                    <span>has used our renting services </span>
                                </li>
                            </ul>
                        </div>
                        
                    </div>
                    
                </div>
            </div>
        </div>	
        <div class="video-btn text-center">
            <a href="https://www.youtube.com/embed/ExJZAegsOis" data-fancybox><span><i class="bx bx-play"></i></span></a>
            <h6>Check Our Video</h6>
        </div>
    </div>
</section>
<!-- /Banner -->