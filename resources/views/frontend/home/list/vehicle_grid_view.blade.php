<!-- Car Grid View -->
<section class="section car-listing pt-0">
    <div class="container">
        <div class="row">
            @include('frontend.home.list.filter')
            <div class="col-lg-9">
                <div class="row">
                    <!-- col -->
                    <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">										
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-01.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-01-slide1.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-01-slide2.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-01-slide3.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>04</span>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Toyota</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-04.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Toyota Camry SE 350</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 138 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.2m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="10 KM"></span>
                                            <p>10 KM</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2018"></span>
                                            <p>2018</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Washington
                                    </div>
                                    <div class="listing-price">
                                        <h6>$160 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                            <div class="feature-text">
                                <span class="bg-danger">Featured</span>
                            </div>
                        </div>			 
                    </div>
                    <!-- /col -->

                    <!-- col -->
                    <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-02.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-02-slide1.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-02-slide2.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-02-slide3.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>04</span>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">KIA</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="assets/img/profiles/avatar-02.jpg" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Kia Soul 2016</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 170 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="assets/img/icons/map-pin.svg" alt="author">4.0m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Belgium
                                    </div>
                                    <div class="listing-price">
                                        <h6>$80 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                        </div>				 
                    </div>
                    <!-- /col -->

                    <!-- col -->
                    <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <a href="listing-details.html">
                                    <img src="{{ asset('frontend/assets/img/cars/car-03.jpg') }}" class="img-fluid" alt="Audi">
                                </a>
                                <div class="fav-item justify-content-end">
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Audi</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-03.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Audi A3 2019 new</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 150 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.5m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Newyork, USA
                                    </div>
                                    <div class="listing-price">
                                        <h6>$45 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                        </div>			 
                    </div>
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <a href="listing-details.html">
                                    <img src="{{ asset('frontend/assets/img/cars/car-04.jpg') }}" class="img-fluid" alt="Audi">
                                </a>
                                <div class="fav-item justify-content-end">
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Ferrai</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-04.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Ferrari 458 MM Speciale</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 160 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.5m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Newyork, USA
                                    </div>
                                    <div class="listing-price">
                                        <h6>$160 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>	
                            <div class="feature-text">
                                <span class="bg-danger">Featured</span>
                            </div>	 
                        </div>			 
                    </div> --}}
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <a href="listing-details.html">
                                    <img src="{{ asset('frontend/assets/img/cars/car-05.jpg') }}" class="img-fluid" alt="Audi">
                                </a>
                                <div class="fav-item justify-content-end">
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>
                                <span class="featured-text">Chevrolet</span>	
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-05.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">2018 Chevrolet Camaro</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <span>(5.0) 200 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">4.5m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Germany
                                    </div>
                                    <div class="listing-price">
                                        <h6>$36 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                            <div class="feature-text">
                                <span class="bg-warning">Top Rated</span>
                            </div>	
                        </div>			 
                    </div> --}}
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <a href="listing-details.html">
                                    <img src="{{ asset('frontend/assets/img/cars/car-06.jpg') }}" class="img-fluid" alt="Audi">
                                </a>
                                <div class="fav-item justify-content-end">
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Acura</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-06.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Acura Sport Version</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 125 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.2m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Newyork, USA
                                    </div>
                                    <div class="listing-price">
                                        <h6>$30 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                        </div>				 
                    </div> --}}
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-07.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-07-slide1.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-07-slide2.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-07-slide3.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>04</span>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Chevrolet</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-07.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Chevrolet Pick Truck 3.5L</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 165 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">3.6m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Spain
                                    </div>
                                    <div class="listing-price">
                                        <h6>$77 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                        </div>			 
                    </div> --}}
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-10.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-10-slide1.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-10-slide2.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-10-slide3.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>04</span>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Ford</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-10.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Ford Mustang 4.0 AT</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 170 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">4.1m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>42 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Petrol"></span>
                                            <p>Petrol</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2021</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Dallas, USA
                                    </div>
                                    <div class="listing-price">
                                        <h6>$80 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                            <div class="feature-text">
                                <span class="bg-danger">Featured</span>
                            </div>
                        </div>				 
                    </div> --}}
                    <!-- /col -->

                    <!-- col -->	
                    {{-- <div class="col-xxl-4 col-lg-6 col-md-6 col-12">
                        <div class="listing-item">											
                            <div class="listing-img">
                                <div class="img-slider owl-carousel">
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-08.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-08-slide1.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-08-slide2.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                    <div class="slide-images">
                                        <a href="listing-details.html">
                                            <img src="{{ asset('frontend/assets/img/cars/car-08-slide3.jpg') }}" class="img-fluid" alt="Toyota">
                                        </a>
                                    </div>
                                </div>
                                <div class="fav-item justify-content-end">
                                    <span class="img-count"><i class="feather-image"></i>04</span>
                                    <a href="javascript:void(0)" class="fav-icon">
                                        <i class="feather-heart"></i>
                                    </a>										
                                </div>	
                                <span class="featured-text">Toyota</span>
                            </div>										
                            <div class="listing-content">
                                <div class="listing-features d-flex align-items-end justify-content-between">
                                    <div class="list-rating">
                                        <a href="javascript:void(0)" class="author-img">
                                            <img src="{{ asset('frontend/assets/img/profiles/avatar-08.jpg') }}" alt="author">
                                        </a>
                                        <h3 class="listing-title">
                                            <a href="listing-details.html">Toyota Tacoma 4WD</a>
                                        </h3>																	  
                                        <div class="list-rating">							
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star filled"></i>
                                            <i class="fas fa-star"></i>
                                            <span>(4.0) 138 Reviews</span>
                                        </div>
                                    </div>
                                    <div class="list-km">
                                        <span class="km-count"><img src="{{ asset('frontend/assets/img/icons/map-pin.svg') }}" alt="author">4.1m</span>
                                    </div>
                                </div> 
                                <div class="listing-details-group">
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-01.svg') }}" alt="Auto"></span>
                                            <p>Auto</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-02.svg') }}" alt="22 miles"></span>
                                            <p>22 miles</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-03.svg') }}" alt="Diesel"></span>
                                            <p>Diesel</p>
                                        </li>
                                    </ul>	
                                    <ul>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-04.svg') }}" alt="Power"></span>
                                            <p>Power</p>
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-05.svg') }}" alt="2019"></span>
                                            <p>2019</p>	
                                        </li>
                                        <li>
                                            <span><img src="{{ asset('frontend/assets/img/icons/car-parts-06.svg') }}" alt="Persons"></span>
                                            <p>5 Persons</p>
                                        </li>
                                    </ul>
                                </div>																 
                                <div class="listing-location-details">
                                    <div class="listing-price">
                                        <span><i class="feather-map-pin"></i></span>Dallas, USA
                                    </div>
                                    <div class="listing-price">
                                        <h6>$30 <span>/ Day</span></h6>
                                    </div>
                                </div>
                                <div class="listing-button">
                                    <a href="listing-details.html" class="btn btn-order"><span><i class="feather-calendar me-2"></i></span>Rent Now</a>
                                </div>	
                            </div>
                        </div>			 
                    </div> --}}
                    <!-- /col -->

                </div>	
                <!--Pagination--> 
                <div class="blog-pagination">
                    <nav>
                        <ul class="pagination page-item justify-content-center">
                            <li class="previtem">
                                <a class="page-link" href="#"><i class="fas fa-regular fa-arrow-left me-2"></i> Prev</a>
                            </li>
                            <li class="justify-content-center pagination-center"> 
                                <div class="page-group">
                                    <ul>
                                        <li class="page-item">
                                            <a class="page-link" href="#">1</a>
                                        </li>
                                        <li class="page-item">
                                            <a class="active page-link" href="#">2 <span class="visually-hidden">(current)</span></a>
                                        </li>
                                        <li class="page-item">
                                            <a class="page-link" href="#">3</a>
                                        </li>
                                    </ul>
                                </div>													
                            </li>													
                            <li class="nextlink">
                                <a class="page-link" href="#">Next <i class="fas fa-regular fa-arrow-right ms-2"></i></a>
                            </li>
                        </ul>
                    </nav>
                </div>
                <!--/Pagination-->

            </div>
        </div>		
      </div>	
</section>	
<!-- /Car Grid View -->	