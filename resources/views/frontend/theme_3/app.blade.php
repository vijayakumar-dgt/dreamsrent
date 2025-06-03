<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>{{ isset($seo_title) ? $seo_title : config('app.name') }}</title>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="description" content="{{ isset($seo_description) ? $seo_description : config('app.name') }}">
	<meta name="keywords" content="{{ isset($meta_keywords) ? $meta_keywords : '' }}">

	<!-- Open Graph Tags (for social sharing) -->
	<meta property="og:title" content="{{ isset($og_title) ? $og_title : config('app.name') }}">
	<meta property="og:description" content="{{ isset($og_description) ? $og_description : '' }}">
	<meta property="og:image" content="{{ isset($og_image) ? asset($og_image) : asset('frontend/assets/img/logo.svg') }}">
	<meta property="og:url" content="{{ url()->current() }}">

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ isset($favicon) ? asset($favicon) : asset('frontend/assets/img/favicon.png') }}">
	@php
		$isRTL = isRTL(app()->getLocale());
	@endphp
    @include('frontend.theme_3.partials.styles')
</head>
<body>
	<div class="main-wrapper home-three">
		@include('frontend.theme_3.partials.header')
        @yield('content')
		

		

		<!-- Recommended Section -->
		{{-- <section class="section recommend-section">
			<div class="container-fluid">	
				<!-- Heading title-->
				<div class="row">
					<div class="col-lg-12 mx-auto">
						<div class="section-heading heading-three mx-auto" data-aos="fade-down">
							<h2>Highly <span>Recommended</span></h2>
							<p>Here's a list of some of the most popular Bikes globally, based on sales and customer preferences</p>
						</div>
					</div>
				</div>
				<!-- /Heading title -->

				<div class="row">
					<div class="col-md-12">
						<div class="recommend-slider owl-carousel">

							<div class="listing-item bike-list">											
								<div class="listing-img">
									<a href="listing-details.html">
										<img src="assets/img/bike/bike-01.png" class="img-fluid" alt="img">
									</a>
								</div>										
								<div class="listing-content">
									<div class="listing-features d-flex justify-content-between">
										<div class="list-rating">												  
											<div class="list-ratings">							
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star"></i>
												<span>138 Reviews</span>
											</div>
											<h3 class="listing-title">
												<a href="listing-details.html">KTM 1290 Super Duke R EVO</a>
											</h3>					
										</div>
										<div class="list-km">
											<span class="km-count"><img src="assets/img/icons/map-pin.svg" alt="author">3.2m</span>
										</div>
									</div> 
									<div class="listing-details-group">
										<ul>
											<li>
												<span><img src="assets/img/icons/bike-icon-01.svg" alt="img"></span>
												<p>Drum</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-02.svg" alt="img"></span>
												<p>41 Km/L</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-03.svg" alt="img"></span>
												<p>Diesel</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-04.svg" alt="img"></span>
												<p>Tubeless</p>
											</li>
										</ul>
									</div>	
									<div class="listing-button">
										<div class="listing-price">
											<h6>$160 <span>/ Day</span></h6>
										</div>
										<div class="d-flex align-items-center">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>		
											<a href="listing-details.html" class="btn btn-order">Book Now</a>
										</div>
									</div>	
								</div>
							</div>
							<div class="listing-item bike-list">											
								<div class="listing-img">
									<a href="listing-details.html">
										<img src="assets/img/bike/bike-03.png" class="img-fluid" alt="img">
									</a>
								</div>										
								<div class="listing-content">
									<div class="listing-features d-flex justify-content-between">
										<div class="list-rating">												  
											<div class="list-ratings">							
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star"></i>
												<span>112 Reviews</span>
											</div>
											<h3 class="listing-title">
												<a href="listing-details.html">Honda Rebel 300</a>
											</h3>					
										</div>
										<div class="list-km">
											<span class="km-count"><img src="assets/img/icons/map-pin.svg" alt="author">3.5m</span>
										</div>
									</div> 
									<div class="listing-details-group">
										<ul>
											<li>
												<span><img src="assets/img/icons/bike-icon-01.svg" alt="img"></span>
												<p>Drum</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-02.svg" alt="img"></span>
												<p>20 Km/L</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-03.svg" alt="img"></span>
												<p>Diesel</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-04.svg" alt="img"></span>
												<p>Tubeless</p>
											</li>
										</ul>
									</div>	
									<div class="listing-button">
										<div class="listing-price">
											<h6>$150 <span>/ Day</span></h6>
										</div>
										<div class="d-flex align-items-center">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>		
											<a href="listing-details.html" class="btn btn-order">Book Now</a>
										</div>
									</div>	
								</div>
							</div>

							<div class="listing-item bike-list">											
								<div class="listing-img">
									<a href="listing-details.html">
										<img src="assets/img/bike/bike-02.png" class="img-fluid" alt="img">
									</a>
								</div>										
								<div class="listing-content">
									<div class="listing-features d-flex justify-content-between">
										<div class="list-rating">												  
											<div class="list-ratings">							
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star filled"></i>
												<i class="fas fa-star"></i>
												<span>125 Reviews</span>
											</div>
											<h3 class="listing-title">
												<a href="listing-details.html">Kawasaki z900</a>
											</h3>					
										</div>
										<div class="list-km">
											<span class="km-count"><img src="assets/img/icons/map-pin.svg" alt="author">3.0m</span>
										</div>
									</div> 
									<div class="listing-details-group">
										<ul>
											<li>
												<span><img src="assets/img/icons/bike-icon-01.svg" alt="img"></span>
												<p>Drum</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-02.svg" alt="img"></span>
												<p>17 Km/L</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-03.svg" alt="img"></span>
												<p>Diesel</p>
											</li>
											<li>
												<span><img src="assets/img/icons/bike-icon-04.svg" alt="img"></span>
												<p>Tubeless</p>
											</li>
										</ul>
									</div>	
									<div class="listing-button">
										<div class="listing-price">
											<h6>$180 <span>/ Day</span></h6>
										</div>
										<div class="d-flex align-items-center">
											<a href="javascript:void(0)" class="fav-icon">
												<i class="feather-heart"></i>
											</a>		
											<a href="listing-details.html" class="btn btn-order">Book Now</a>
										</div>
									</div>	
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</section> --}}
		<!-- /Recommended Section -->

		

		<!-- Bike Section -->
		{{-- <section class="section bike-section">
			<div class="container">
				<div class="bike-wrap">
					<div class="bike-content">
						<h2>Want to add your Bike for rent in Dreams rent</h2>
					</div>
					<div class="bike-btn">
						<a href="register.html" class="btn btn-theme">Add your Listing</a>
					</div>
				</div>
			</div>			
			<div class="bike-bg">
				<img class="img-fluid shape-01" src="assets/img/bg/ban-bg-05.png" alt="Image">
				<img class="img-fluid shape-02" src="assets/img/bg/ban-bg-06.png" alt="Image">
				<img class="img-fluid shape-03" src="assets/img/bg/shape-bg.png" alt="Image">
				<img class="img-fluid shape-04" src="assets/img/bg/ban-bg-06.png" alt="Image">
			</div>
		</section> --}}
		<!-- /Bike Section -->

		<!-- Pricing Plan -->
		{{-- <section class="price-section">
			<div class="container">
				<!-- Heading title-->
				<div class="section-heading heading-three mx-auto" data-aos="fade-down">
					<h2 class="mx-auto">Scrutinize the Optimum Pricing Scheme <span>to Begin</span>.</h2>
					<p>Pricing plans for businesses at every stage of growth. Try our risk-free for 14 days. No credit card required.</p>
				</div>
				<!-- /Heading title -->
									
				<!-- Plan Selected -->
				<div class="price-tab">
					<ul class="nav">
						<li>
							<a href="#" data-bs-toggle="tab" data-bs-target="#month" class="active">Monthly</a>
						</li>
						<li>
							<a href="#" data-bs-toggle="tab" data-bs-target="#year">Yearly<span>Save 20%</span></a>
						</li>
					</ul>
				</div>
				<!-- /Plan Selected -->
				<div class="tab-content">
					<div class="tab-pane show active" id="month">
						<div class="row align-items-center justify-content-center">
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap">
									<div class="price-head">
										<span class="level-badge">Basic </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$15</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal <span class="coming-soon">Coming Soon</span></li>
										</ul>					
									</div>							
								</div>
						   	</div>
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap active">
									<div class="price-head">
										<span class="level-badge bg-green">Commercial </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$55</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day bg-primary">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal <span class="coming-soon">Coming Soon</span></li>
										</ul>					
									</div>	
									<div class="feature-text">
										<span class="bg-danger">Best Value</span>
									</div>						
								</div>
						   	</div>
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap">
									<div class="price-head">
										<span class="level-badge bg-purple">Premium </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$105</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal</li>
										</ul>					
									</div>							
								</div>
						   	</div>
						</div>
					</div>
					<div class="tab-pane fade" id="year">
						<div class="row align-items-center justify-content-center">
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap">
									<div class="price-head">
										<span class="level-badge">Basic </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$55</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal <span class="coming-soon">Coming Soon</span></li>
										</ul>					
									</div>							
								</div>
						   	</div>
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap active">
									<div class="price-head">
										<span class="level-badge bg-green">Commercial </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$85</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day bg-primary">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal <span class="coming-soon">Coming Soon</span></li>
										</ul>					
									</div>	
									<div class="feature-text">
										<span class="bg-danger">Best Value</span>
									</div>						
								</div>
						   	</div>
							<div class="col-lg-4 col-md-6 col-12" data-aos="fade-down">
								<div class="price-wrap">
									<div class="price-head">
										<span class="level-badge bg-purple">Premium </span>
										<p>For all individuals and starters who want to start with domaining.</p>
									</div>
									<div class="price-level">
										<h3>$105</h3>	
										<p>Per member, per Month</p>	
										<span class="trial-day">Start free 14-day Trial</span>						
									</div>	
									<div class="price-details">
										<ul>
										 	<li><i class="bx bxs-check-circle"></i>Access to All Features</li>
										 	<li><i class="bx bxs-check-circle"></i>1k lookups / per month</li>
										 	<li><i class="bx bxs-check-circle"></i>10 Monitoring Quota</li>
										 	<li><i class="bx bxs-check-circle"></i>30K API Credits / month</li>
										 	<li><i class="bx bxs-check-circle"></i>60 minutes Monitoring intervel</li>
										 	<li><i class="bx bxs-check-circle"></i>20% discount on backorders</li>
										 	<li><i class="bx bxs-check-circle"></i>Domain Name Appraisal</li>
										</ul>					
									</div>							
								</div>
						   	</div>
						</div>
					</div>
				</div>
				<div class="price-bg">
					<img class="img-fluid shape-01" src="assets/img/bg/price-bg.png" alt="Image">
					<img src="assets/img/bg/destination-bg-01.png" class="img-fluid shape-02" alt="img">
					<img src="assets/img/bg/price-bg-01.png" class="img-fluid shape-03" alt="img">
				</div>
			</div>
		</section> --}}
		<!-- /Pricing Plan -->

		

		

		<!-- Best Section -->
		{{-- <section class="section rental-section">
			<div class="container">
				<div class="rental-wrap">
					<div class="rental-content">
						<h2>We Make Finding The Right Bike Simple</h2>
						<div class="btn-item">
							<a href="listing-grid.html" class="btn btn-theme">View all Bikes</a>
						</div>
					</div>
				</div>
			</div>
			<div class="rental-bg">
				<img class="img-fluid ban-bg" src="assets/img/bg/bike-bg.jpg" alt="Image">
				<img class="img-fluid shape-01" src="assets/img/bg/ban-bg-05.png" alt="Image">
				<img class="img-fluid shape-02" src="assets/img/bg/ban-bg-06.png" alt="Image">
				<img class="img-fluid shape-03" src="assets/img/bg/shape-bg.png" alt="Image">
				<img class="img-fluid shape-04" src="assets/img/bg/ban-bg-04.png" alt="Image">
			</div>
		</section> --}}
		<!-- /Best Section -->
		@include('frontend.toast')
		@include('frontend.theme_3.partials.footer')
	</div>

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
		<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
		</svg>
	</div>
	<!-- scrollToTop end -->
	@include('frontend.theme_3.partials.scripts')
</body>
</html>