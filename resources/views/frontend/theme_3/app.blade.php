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
		<!-- Quote Section -->
		{{-- <section class="quote-section">
			<div class="container">
				<div class="quote-list">
					<ul>
						<li>
							<img src="{{ asset('frontend/assets/img/icons/quote-01.svg') }}" class="img-fluid" alt="img">
						</li>
						<li>
							<img src="assets/img/icons/quote-02.svg" class="img-fluid" alt="img">
						</li>
						<li>
							<img src="assets/img/icons/quote-03.svg" class="img-fluid" alt="img">
						</li>
						<li>
							<img src="assets/img/icons/quote-04.svg" class="img-fluid" alt="img">
						</li>
						<li>
							<img src="assets/img/icons/quote-05.svg" class="img-fluid" alt="img">
						</li>
					</ul>
				</div>
			</div>
		</section> --}}
		<!-- /Quote Section -->

		

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

		<!-- FAQ  -->
		{{-- <section class="section faq-section-three">
			<div class="container">	
				<div class="row align-items-center">		
					<div class="col-lg-6">		
						<!-- Heading title-->
						<div class="section-heading heading-three" data-aos="fade-down">
							<h2>Frequently Asked Questions</h2>
							<p>Feel free to customize them further to align with your specific policies</p>
						</div>
						<!-- /Heading title -->
						<div class="faq-info">
							<div class="faq-card" data-aos="fade-down">
								<h4 class="faq-title">
									<a class="collapseds" data-bs-toggle="collapse" href="#faqOne" aria-expanded="true">How old do I need to be to rent a Bike ?</a>
								</h4>
								<div id="faqOne" class="card-collapse collapse show">
									<p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
								</div>
							</div>	
							<div class="faq-card" data-aos="fade-down">
								<h4 class="faq-title">
									<a class="collapsed" data-bs-toggle="collapse" href="#faqTwo" aria-expanded="false">What documents do I need to rent a Bike?</a>
								</h4>
								<div id="faqTwo" class="card-collapse collapse">
									<p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
								</div>
							</div>
							<div class="faq-card" data-aos="fade-down">
								<h4 class="faq-title">
									<a class="collapsed" data-bs-toggle="collapse" href="#faqThree" aria-expanded="false">What types of vehicles are available for rent?</a>
								</h4>
								<div id="faqThree" class="card-collapse collapse">
									<p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
								</div>
							</div>	
							<div class="faq-card" data-aos="fade-down">
								<h4 class="faq-title">
									<a class="collapsed" data-bs-toggle="collapse" href="#faqFour" aria-expanded="false">Can I rent a Bike with a debit card?</a>
								</h4>
								<div id="faqFour" class="card-collapse collapse">
									<p>We offer a diverse fleet of vehicles to suit every need, including compact cars, sedans, SUVs and luxury vehicles. You can browse our selection online or contact us for assistance in choosing the right vehicle for you</p>
								</div>
							</div>													
						</div>	
					</div>
					<div class="col-lg-6">
						<div class="customer-content">
							<p>Overall, car rental counters serve as the primary point of contact for customers to pick up their rental vehicles and complete the necessary paperwork before embarking on their journey. The rental agents are there to assist customers every step of the way and ensure a smooth and seamless rental experience.</p>
							<div class="row">
								<div class="col-md-6">
									<div class="count-box">
										<span class="counts-icon">
											<img src="assets/img/icons/count-01.svg" class="img-fluid" alt="img">
										</span>
										<div class="count-info">
											<h3><span class="counterUp">625</span>+</h3>
											<p>Locations to Pickup</p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="count-box">
										<span class="counts-icon">
											<img src="assets/img/icons/count-02.svg" class="img-fluid" alt="img">
										</span>
										<div class="count-info">
											<h3><span class="counterUp">2547</span>+</h3>
											<p>Count of Bikes</p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="count-box">
										<span class="counts-icon">
											<img src="assets/img/icons/count-03.svg" class="img-fluid" alt="img">
										</span>
										<div class="count-info">
											<h3><span class="counterUp">15000</span></h3>
											<p>Total Kilometers</p>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="count-box">
										<span class="counts-icon">
											<img src="assets/img/icons/count-04.svg" class="img-fluid" alt="img">
										</span>
										<div class="count-info">
											<h3><span class="counterUp">16</span>+</h3>
											<p>Happy Customers</p>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>	
		    </div>	
		    <div class="faq-bg">
		    	<img src="assets/img/bg/ban-bg-01.png" class="img-fluid shape-01" alt="img">
				<img src="assets/img/bg/faq-bg-01.png" class="img-fluid shape-02" alt="img">
				<img src="assets/img/bg/faq-bg-02.png" class="img-fluid shape-03" alt="img">
			</div>	
		</section>	 --}}
		<!-- /FAQ -->

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

		<!-- About us Testimonials -->
		{{-- <section class="section testimonials-three">
			<div class="container">
				<div class="row">
					<div class="col-lg-4">
						<div class="testimonial-feedback">
							<!-- Heading title-->
							<div class="section-heading heading-three" data-aos="fade-down">
								<h2 class="title">What Our <br><span>Customers Say</span> </h2>
							</div>
							<!-- /Heading title -->

							<img src="assets/img/testimonial-img.jpg" alt="img" class="img-fluid">
							<div class="feedback-item">
								<div class="feedback-info">
									<h6>Great</h6>
									<div class="rate-icon">
										<span><i class="bx bxs-star"></i></span>
										<span><i class="bx bxs-star"></i></span>
										<span><i class="bx bxs-star"></i></span>
										<span><i class="bx bxs-star"></i></span>
										<span><i class="bx bxs-star"></i></span>
									</div>
									<p>Based on 5,801 Reviews</p>
								</div>
								<div class="feedback-user">
									<h3><i class="bx bxs-star"></i>Trustpilot</h3>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-8">
						<div class="testimonial-wrapper">
							<div class="testimonial-slider">
	                    		<div class="testimonial-wrap">	
									<div class="users-info">
										<div class="testimonial-name">
											<h6>Marian Hendriques</h6>
											<p>Dubai, Emirates</p>
										</div>
										<div class="users-rating">
											<div class="rate-icon">
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
											</div>
											<p><i class="bx bxs-check-circle"></i>Verified</p>
										</div>
									</div>	
									<div class="testimonial-content">					
										<h3>“ From a Satisfied Business Traveler “</h3>
										<p>As a frequent business traveler, I rely on Dreams Rent for all my transportation needs. Their extensive fleet of vehicles, convenient locations, and competitive pricing make them my go-to choice every time. Plus, their friendly staff always go the extra mile to ensure a seamless rental experience.</p>
									</div>
								</div>
                            	<div class="testimonial-wrap">	
									<div class="users-info">
										<div class="testimonial-name">
											<h6>Lyon Avenue</h6>
											<p>Derby, UK</p>
										</div>
										<div class="users-rating">
											<div class="rate-icon">
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
											</div>
											<p><i class="bx bxs-check-circle"></i>Verified</p>
										</div>
									</div>	
									<div class="testimonial-content">					
										<h3>“ David's Urban Exploration “</h3>
										<p>As a frequent traveler, finding reliable bike rental services is crucial for me. I stumbled upon this website during my recent trip, and I'm glad I did. The process of booking was seamless, and the prices were reasonable. The best part was the quality of the bikes; they were well-maintained and comfortable to ride.</p>
									</div>
								</div>
	                    		<div class="testimonial-wrap">	
									<div class="users-info">
										<div class="testimonial-name">
											<h6>Westfall Avenue</h6>
											<p>New York, USA</p>
										</div>
										<div class="users-rating">
											<div class="rate-icon">
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
											</div>
											<p><i class="bx bxs-check-circle"></i>Verified</p>
										</div>
									</div>	
									<div class="testimonial-content">					
										<h3>“ Sarah's Adventure “</h3>
										<p>Absolutely loved my experience with Dreams Rent! Booking was a breeze; their website is user-friendly and intuitive. The bike I rented was in excellent condition, which made exploring the city a joy. What stood out the most was the exceptional customer service.</p>
									</div>
								</div>
                            	<div class="testimonial-wrap">	
									<div class="users-info">
										<div class="testimonial-name">
											<h6>Saint Clair Street</h6>
											<p>Norwich, UK</p>
										</div>
										<div class="users-rating">
											<div class="rate-icon">
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
												<span><i class="bx bxs-star"></i></span>
											</div>
											<p><i class="bx bxs-check-circle"></i>Verified</p>
										</div>
									</div>	
									<div class="testimonial-content">					
										<h3>“ Edward's Scenic Ride “</h3>
										<p>From start to finish, renting a bike through this website was an absolute pleasure. The website interface was easy to navigate, and I could quickly find the perfect bike for my needs. When I arrived to pick up the bike, I was impressed by its excellent condition. It was evident that they take pride in maintaining their fleet.</p>
									</div>
								</div>
                        	</div>
	                        <div class="slider testimonial-thumbnails">
	                        	<div><img src="assets/img/profiles/avatar-11.jpg" alt="image"></div>
	                            <div><img src="assets/img/profiles/avatar-02.jpg" alt="image"></div>
	                            <div><img src="assets/img/profiles/avatar-03.jpg" alt="image"></div>
	                            <div><img src="assets/img/profiles/avatar-04.jpg" alt="image"></div>
	                        </div>
						</div>
					</div>
				</div>				
			</div>
			<div class="testimonial-bg">
				<img class="img-fluid shape-01" src="assets/img/bg/ban-bg-04.png" alt="Image">
				<img src="assets/img/bg/ban-bg-01.png" class="img-fluid shape-02" alt="img">
			</div>
		</section> --}}
		<!-- About us Testimonials -->

		<!-- Blog Section -->
		{{-- <section class="blog-section bike-news">
			<div class="container">
				<!-- Heading title-->
				<div class="section-heading heading-three mx-auto" data-aos="fade-down">
					<h2>Contemporary news and insights <span>for you</span></h2>
					<p>Here's a hypothetical blog post about car rental services</p>
				</div>
				<!-- /Heading title -->

				<div class="row">
					<div class="col-lg-12">
						<div class="blog-slider nav-center owl-carousel">

							<!-- Blog List -->
							<div class="blog grid-blog">
								<div class="blog-image">
									<a href="blog-details.html"><img class="img-fluid" src="assets/img/blog/blog-bike-01.jpg" alt="Post Image"></a>
								</div>
								<div class="blog-content">
									<h3 class="blog-title"><a href="blog-details.html">Unlocking the Freedom of Travel: The Ultimate Guide.</a></h3>
									<p class="blog-description">Are you planning your next adventure but feeling over whelmed by the logistics of transportation...</p>
									<div class="blog-footer">
										<p><i class="bx bx-calendar"></i>Apr 21, 2024</p>
										<a href="blog-details.html" class="read-more">Read More<i class="bx bx-right-arrow-alt"></i></a>
									</div>
								</div>
							</div>
							<!-- /Blog List -->

							<!-- Blog List -->
							<div class="blog grid-blog">
							  	<div class="blog-image">
								  	<a href="blog-details.html"><img class="img-fluid" src="assets/img/blog/blog-bike-02.jpg" alt="Post Image"></a>
							  	</div>
							  	<div class="blog-content">
								  	<h3 class="blog-title"><a href="blog-details.html">Tips for a Seamless Rental Experience</a></h3>
								  	<p class="blog-description">Book your rental car in advance to secure the best rates and availability, especially during...</p>
								  	<div class="blog-footer">
										<p><i class="bx bx-calendar"></i>Apr 27, 2024</p>
										<a href="blog-details.html" class="read-more">Read More<i class="bx bx-right-arrow-alt"></i></a>
									</div>
							  	</div>
							</div>
							<!-- /Blog List -->

							<!-- Blog List -->
							<div class="blog grid-blog">
							  	<div class="blog-image">
								  	<a href="blog-details.html"><img class="img-fluid" src="assets/img/blog/blog-bike-03.jpg" alt="Post Image"></a>
							  	</div>
							  	<div class="blog-content">
								  	<h3 class="blog-title"><a href="blog-details.html">Embark on Your Next Adventure with Confidence.</a></h3>
								  	<p class="blog-description">With the freedom and flexibility of car rentals, the world is yours to explore!...</p>
								  	<div class="blog-footer">
										<p><i class="bx bx-calendar"></i>May 02, 2024</p>
										<a href="blog-details.html" class="read-more">Read More<i class="bx bx-right-arrow-alt"></i></a>
									</div>
							  	</div>
							</div>
							<!-- /Blog List -->

							<!-- Blog List -->
							<div class="blog grid-blog">
							  	<div class="blog-image">
								  	<a href="blog-details.html"><img class="img-fluid" src="assets/img/blog/blog-bike-04.jpg" alt="Post Image"></a>
							  	</div>
							  	<div class="blog-content">
								  	<h3 class="blog-title"><a href="blog-details.html">Exploring the Outdoors: A Guide to Bike Rentals and Adventure</a></h3>
								  	<p class="blog-description">One of the first steps in planning your biking adventure is selecting the right bike...</p>
								  	<div class="blog-footer">
										<p><i class="bx bx-calendar"></i>May 16, 2024</p>
										<a href="blog-details.html" class="read-more">Read More<i class="bx bx-right-arrow-alt"></i></a>
									</div>
							  	</div>
							</div>
							<!-- /Blog List -->

							<!-- Blog List -->
							<div class="blog grid-blog">
							  	<div class="blog-image">
								  	<a href="blog-details.html"><img class="img-fluid" src="assets/img/blog/blog-bike-05.jpg" alt="Post Image"></a>
							  	</div>
							  	<div class="blog-content">
								  	<h3 class="blog-title"><a href="blog-details.html">Ride into Adventure: Exploring Nature's Beauty with Bikes </a></h3>
								  	<p class="blog-description">From rugged mountain trails and scenic coastal paths to tranquil forested routes, the great...</p>
								  	<div class="blog-footer">
										<p><i class="bx bx-calendar"></i>May 25, 2024</p>
										<a href="blog-details.html" class="read-more">Read More<i class="bx bx-right-arrow-alt"></i></a>
									</div>
							  	</div>
							</div>
							<!-- /Blog List -->

						</div>
					</div>
				</div>
				<div class="view-all-btn text-center aos-init aos-animate" data-aos="fade-down">
					<a href="blog-details.html" class="btn btn-secondary">View all Blog</a>
				</div>

			</div>
		</section> --}}
		<!-- /Blog Section -->

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