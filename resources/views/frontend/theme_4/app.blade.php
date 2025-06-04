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
    @include('frontend.theme_4.partials.styles')
</head>
<body class="home-two">
	
	<div class="main-wrapper">

		<!-- Hero Sec Main -->
		<div class="hero-sec-main">
			@if(request()->routeIs('home'))
		    @include('frontend.theme_4.partials.header')
			@else
			@include('frontend.theme_1.partials.header')
			@endif
			@yield('content')
		</div>
		<!-- /Hero Sec Main -->

		

		

		

		

		

		<!-- Yacht Offer -->
		<section class="yacht-offer-sec">
			<div class="sec-bg">
				<img src="/frontend/assets/img/bg/sec-bg-wave.png" class="wave-bottom" alt="Bg">
			</div>
			<div class="container">
				<div class="section-header-two">
					<h2>Yacht - Last Minute Offers!</h2>
					<p>Most popular worldwide Category due to their 
						reliability, affordability, and features.
					</p>
				</div>
				<div class="yacht-list-cards">
					<div class="row">
						<div class="col-xl-6">
							<div class="top-rated-card">
								<div class="rated-yacht-img slide-card-images">
									<div class="yacht-image-slider owl-carousel">
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-01.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-02.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-03.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-04.jpg" class="img-fluid" alt="img">
											</a>
										</div>
									</div>
								</div>
								<div class="rated-yacht-content">
									<div class="yacht-content-head">
										<div class="head-items-left">
											<h4><a href="listing-details.html">Yacht Sun Odyssey 419</a></h4>
											<span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>Chicago, IL</span>
										</div>
										<div class="head-items-right">
											<div class="rated-star">
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star"></i>
											</div>
											<span class="km-badge"><i class="bx bx-map-pin me-2"></i>3.2m</span>
										</div>
									</div>
									<div class="yacht-content-body">
										<ul class="yacht-features-info">
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-01.svg" alt="Img"></span>
												<h6>People <span> : 8</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
												<h6>Length <span> : 4.6m</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
												<h6>Build <span> : 2024</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
												<h6>Cabins <span> : 4</span></h6>
											</li>
										</ul>
									</div>
									<div class="yacht-content-footer">
										<p>From <span>$180 </span> /day</p>
										<div class="yacht-book-btn">
											<a href="javascript:void(0);" class="yacht-user-img"><img src="/frontend/assets/img/profiles/avatar-14.jpg" alt="Img"></a>
											<a href="listing-details.html" class="btn btn-secondary">Book Now</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6">
							<div class="top-rated-card">
								<div class="rated-yacht-img slide-card-images">
									<div class="yacht-image-slider owl-carousel">
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-05.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-06.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-07.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-08.jpg" class="img-fluid" alt="img">
											</a>
										</div>
									</div>
								</div>
								<div class="rated-yacht-content">
									<div class="yacht-content-head">
										<div class="head-items-left">
											<h4><a href="listing-details.html">Bavaria Cruiser | Oceanos</a></h4>
											<span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>Miami beach, Miami</span>
										</div>
										<div class="head-items-right">
											<div class="rated-star">
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star"></i>
											</div>
											<span class="km-badge"><i class="bx bx-map-pin me-2"></i>3.2m</span>
										</div>
									</div>
									<div class="yacht-content-body">
										<ul class="yacht-features-info">
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-01.svg" alt="Img"></span>
												<h6>People <span> : 4</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
												<h6>Length <span> : 2.6m</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
												<h6>Build <span> : 2023</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
												<h6>Cabins <span> : 8</span></h6>
											</li>
										</ul>
									</div>
									<div class="yacht-content-footer">
										<p>From <span>$150 </span> /day</p>
										<div class="yacht-book-btn">
											<a href="javascript:void(0);" class="yacht-user-img"><img src="/frontend/assets/img/profiles/avatar-15.jpg" alt="Img"></a>
											<a href="listing-details.html" class="btn btn-secondary">Book Now</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6">
							<div class="top-rated-card">
								<div class="rated-yacht-img slide-card-images">
									<div class="yacht-image-slider owl-carousel">
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-09.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-10.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-12.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-13.jpg" class="img-fluid" alt="img">
											</a>
										</div>
									</div>
								</div>
								<div class="rated-yacht-content">
									<div class="yacht-content-head">
										<div class="head-items-left">
											<h4><a href="listing-details.html">Sailing yacht Dufour</a></h4>
											<span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>Key West, harbour</span>
										</div>
										<div class="head-items-right">
											<div class="rated-star">
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star"></i>
											</div>
											<span class="km-badge"><i class="bx bx-map-pin me-2"></i>7.1m</span>
										</div>
									</div>
									<div class="yacht-content-body">
										<ul class="yacht-features-info">
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-01.svg" alt="Img"></span>
												<h6>People <span> : 2</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
												<h6>Length <span> : 4.6m</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
												<h6>Build <span> : 2023</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
												<h6>Cabins <span> : 3</span></h6>
											</li>
										</ul>
									</div>
									<div class="yacht-content-footer">
										<p>From <span>$410 </span> /day</p>
										<div class="yacht-book-btn">
											<a href="javascript:void(0);" class="yacht-user-img"><img src="/frontend/assets/img/profiles/avatar-13.jpg" alt="Img"></a>
											<a href="listing-details.html" class="btn btn-secondary">Book Now</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-xl-6">
							<div class="top-rated-card">
								<div class="rated-yacht-img slide-card-images">
									<div class="yacht-image-slider owl-carousel">
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-11.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-14.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-15.jpg" class="img-fluid" alt="img">
											</a>
										</div>
										<div class="slide-images">
											<a href="listing-details.html">
												<img src="/frontend/assets/img/yacht/boat-16.jpg" class="img-fluid" alt="img">
											</a>
										</div>
									</div>
								</div>
								<div class="rated-yacht-content">
									<div class="yacht-content-head">
										<div class="head-items-left">
											<h4><a href="listing-details.html">Beneteau Oceanis 41.1</a></h4>
											<span class="d-flex align-items-center"><i class="bx bx-map me-2"></i>Annopolis, Mariana</span>
										</div>
										<div class="head-items-right">
											<div class="rated-star">
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star filled"></i>
												<i class="bx bxs-star"></i>
											</div>
											<span class="km-badge"><i class="bx bx-map-pin me-2"></i>7.1m</span>
										</div>
									</div>
									<div class="yacht-content-body">
										<ul class="yacht-features-info">
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-01.svg" alt="Img"></span>
												<h6>People <span> : 3</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-02.svg" alt="Img"></span>
												<h6>Length <span> : 5.6m</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-04.svg" alt="Img"></span>
												<h6>Build <span> : 2024</span></h6>
											</li>
											<li>
												<span class="yacht-feature-icon"><img src="/frontend/assets/img/icons/yacht-feature-icon-06.svg" alt="Img"></span>
												<h6>Cabins <span> : 4</span></h6>
											</li>
										</ul>
									</div>
									<div class="yacht-content-footer">
										<p>From <span>$680 </span> /day</p>
										<div class="yacht-book-btn">
											<a href="javascript:void(0);" class="yacht-user-img"><img src="/frontend/assets/img/profiles/avatar-13.jpg" alt="Img"></a>
											<a href="listing-details.html" class="btn btn-secondary">Book Now</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-12">
							<div class="view-more-btn text-center">
								<a href="listing-grid.html" class="btn btn-secondary">View all Yachts</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /Yacht Offer -->

		<!-- More Boats Info -->
		<section class="more-boats-info-sec">
			<div class="container-fluid">
				<div class="sec-bottom-info">
					<div class="row bottom-text-row">
						<div class="col-xl-3 col-lg-6">
							<div class="bottom-ship-info-card">
								<div class="hover-ship-info w-100">
									<h4>Classic Dancer premium A/C Boat rental</h4>
									<div class="address-info">
										<span><i class="bx bx-map"></i>Hulbert, MI</span>
										<div class="rated-star">
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star"></i>
											<span>55 Reviews</span>
										</div>
									</div>
									<ul class="ship-features">
										<li>Cabins : 4</li>
										<li>People : 8</li>
										<li>Length : 4.6</li>
									</ul>
									<div class="ship-pricing">
										<h5>From <span> $180 </span> /day</h5>
										<a href="listing-details.html" class="btn btn-primary btn-buy">Book Now</a>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-xl-3 col-lg-6">
							<div class="bottom-ship-info-card">
								<div class="hover-ship-info w-100">
									<h4>Exclusive "Classic Paradise" Premium Vessel</h4>
									<div class="address-info">
										<span><i class="bx bx-map"></i>Miami, FL</span>
										<div class="rated-star">
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star"></i>
											<span>55 Reviews</span>
										</div>
									</div>
									<ul class="ship-features">
										<li>Cabins : 4</li>
										<li>People : 8</li>
										<li>Length : 4.6</li>
									</ul>
									<div class="ship-pricing">
										<h5>From <span> $180 </span> /day</h5>
										<a href="listing-details.html" class="btn btn-primary btn-buy">Book Now</a>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-xl-3 col-lg-6">
							<div class="bottom-ship-info-card">
								<div class="hover-ship-info w-100">
									<h4>Explore the water in Style: Exclusive Houseboat</h4>
									<div class="address-info">
										<span><i class="bx bx-map"></i>Poug, NY</span>
										<div class="rated-star">
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star"></i>
											<span>55 Reviews</span>
										</div>
									</div>
									<ul class="ship-features">
										<li>Cabins : 4</li>
										<li>People : 8</li>
										<li>Length : 4.6</li>
									</ul>
									<div class="ship-pricing">
										<h5>From <span> $180 </span> /day</h5>
										<a href="listing-details.html" class="btn btn-primary btn-buy">Book Now</a>
									</div>
								</div>
								
							</div>
						</div>
						<div class="col-xl-3 col-lg-6">
							<div class="bottom-ship-info-card">
								<div class="hover-ship-info w-100">
									<h4>2022 Sea Doo Pontoon for Exciting Adventure</h4>
									<div class="address-info">
										<span><i class="bx bx-map"></i>Heflin, AL</span>
										<div class="rated-star">
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star filled"></i>
											<i class="bx bxs-star"></i>
											<span>55 Reviews</span>
										</div>
									</div>
									<ul class="ship-features">
										<li>Cabins : 4</li>
										<li>People : 8</li>
										<li>Length : 4.6</li>
									</ul>
									<div class="ship-pricing">
										<h5>From <span> $180 </span> /day</h5>
										<a href="listing-details.html" class="btn btn-primary btn-buy">Book Now</a>
									</div>
								</div>
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /More Boats Info -->

		<!-- Boat marketplace -->
		<section class="boats-marketplace-sec">
			<div class="sec-round-colors">
				<span class="bg-orange round-small"></span>
				<span class="bg-dark-blue round-small"></span>
				<span class="bg-dark-blue round-small"></span>
				<span class="bg-dark-blue round-big"></span>
			</div>
			<div class="sec-bg">
				<img src="/frontend/assets/img/bg/dotted-round-bg.png" alt="Bg">
				<img src="/frontend/assets/img/bg/anchor-img.png" alt="Bg">
			</div>
			<div class="container">
				<div class="row align-items-center">
					<div class="col-lg-6">
						<div class="sec-col-left-imgs">
							<span class="sec-left-one"><img src="/frontend/assets/img/bg/sec-modal-img-01.jpg" class="img-fluid" alt="Img"></span>
							<span class="sec-left-two"><img src="/frontend/assets/img/bg/sec-modal-img-02.jpg" class="img-fluid" alt="Img"></span>
							<div class="experience-info">
								<h5>15+ <span>Years of <br> Experience</span></h5>
							</div>
						</div>
					</div>
					<div class="col-lg-6">
						<div class="section-header-two">
							<h2>Providing a large fleet of Boats marketplace for a perfect and dreamy experience</h2>
							<h4>Know what you looking for? Browse our extensive 
								select of charter yachts from around the world.
							</h4>
							<p>Find and book your dream yacht through Floaty, the world’s 
								leading luxury yacht charter comparison site. View all superyachts available to rent,
								 get expert advice from our comprehensive 
								destination guides and be inspired by our bespoke superyacht itineraries. 
							</p>
							<a href="listing-details.html" class="btn btn-primary d-flex align-items-center"><i class="bx bx-bar-chart me-2"></i>Learn More</a>
						</div>
					</div>
				</div>
				<div class="yacht-owner-card">
					<div class="sec-bg">
						<img src="/frontend/assets/img/bg/sec-bg-wave.png" alt="Img">
					</div>
					<div class="yacht-owner-title">
						<h3>Are you a Yacht Owner ?</h3>
						<p>List your boat on Boataround and earn money.</p>
						<a href="javascript:void(0);" class="btn btn-primary">Get Started</a>
					</div>
					<div class="yacht-owner-img">
						<img src="/frontend/assets/img/bg/yacht-owner-bg-01.png" alt="Img">
					</div>
				</div>
			</div>
		</section>
		<!-- /Boat marketplace -->

		<!-- Client Review -->
		<section class="our-client-review-sec">
			<div class="sec-bg">
				<img src="/frontend/assets/img/bg/ship-part-bg-01.png" alt="Bg">
			</div>
			<div class="sec-round-colors">
				<span class="bg-orange round-small"></span>
			</div>
			<div class="container">
				<div class="section-header-two">
					<h2>What Our Clients Speak’s</h2>
					<p>Discover what our customers have think about us
					</p>
				</div>
				<div class="row">
					<div class="col-lg-4 col-md-6 d-flex">
						<div class="client-review-card flex-fill">
							<div class="client-review-content">
								<div class="client-img">
									<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-16.jpg" alt="Img"></a>
									<p>The crew went above and beyond to ensure that every detail was perfect, from the 
										gourmet dining experience to the
										sunset cruise along the coast. It was truly a dream come true, and we can't wait to do it again!
									</p>
									<h5><a href="javascript:void(0);">Rabien Ustoc</a></h5>
									<span>Newyork, USA</span>
									<div class="quataion-mark">
										<img src="/frontend/assets/img/icons/quatation-mark.svg" class="img-fluid" alt="Img">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 d-flex">
						<div class="client-review-card flex-fill">
							<div class="client-review-content">
								<div class="client-img">
									<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-17.jpg" alt="Img"></a>
									<p> 
										Our family vacation with Seaside Yacht Rentals was simply spectacular! Renting a yacht was a 
										new experience for us, 
										and it exceeded all our expectations. The kids had a blast exploring the coastline.
									</p>
									<h5><a href="javascript:void(0);">Adrian Tres</a></h5>
									<span>Newyork, USA</span>
									<div class="quataion-mark">
										<img src="/frontend/assets/img/icons/quatation-mark.svg" class="img-fluid" alt="Img">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6 d-flex">
						<div class="client-review-card flex-fill">
							<div class="client-review-content">
								<div class="client-img">
									<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-18.jpg" alt="Img"></a>
									<p> 
										Our weekend yacht rental with Ocean Escape Charters was an absolute blast! We wanted to plan 
										a memorable getaway 
										with our friends, and chartering a yacht seemed like the perfect idea.
									</p>
									<h5><a href="javascript:void(0);">Mariana Fauzel</a></h5>
									<span>Newyork, USA</span>
									<div class="quataion-mark">
										<img src="/frontend/assets/img/icons/quatation-mark.svg" class="img-fluid" alt="Img">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="view-all text-center">
							<a href="testimonial.html" class="btn btn-secondary">View all Testimonails</a>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /Client Review -->

		<!-- FAQ -->
		<section class="faq-sec-two">
			<div class="sec-round-colors">
				<span class="bg-orange round-small"></span>
				<span class="bg-orange round-small"></span>
				<span class="bg-dark-blue round-small"></span>
				<span class="bg-dark-blue round-big"></span>
			</div>
			<div class="sec-bg">
				<img src="/frontend/assets/img/bg/anchor-img.png" alt="Bg">
				<img src="/frontend/assets/img/bg/ship-part-bg-02.png" alt="Bg">
				<img src="/frontend/assets/img/bg/ship-part-bg-03.png" alt="Bg">
				<img src="/frontend/assets/img/bg/sec-bg-wave.png" alt="Bg">
			</div>
			<div class="container">
				<div class="counter-group counter-group-two">
			        <div class="row">
						<div class="col-lg-3 col-md-6 col-12 d-flex">
							<div class="count-group flex-fill">
								<div class="customer-count d-flex align-items-center">
									<div class="count-img">
										<img src="/frontend/assets/img/icons/counter-icon-01.svg" alt="Icon">
									</div>
									<div class="count-content">
										<h4><span class="counterUp">2547</span>+</h4>
										<p>Count of Yachts</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-12 d-flex">
							<div class="count-group flex-fill">
								<div class="customer-count d-flex align-items-center">
									<div class="count-img">
										<img src="/frontend/assets/img/icons/counter-icon-02.svg" alt="Icon">
									</div>
									<div class="count-content">
										<h4><span class="counterUp">16</span>k</h4>
										<p>Happy Customers</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-12 d-flex">
							<div class="count-group flex-fill">
								<div class="customer-count d-flex align-items-center">
									<div class="count-img">
										<img src="/frontend/assets/img/icons/counter-icon-03.svg" alt="Icon">
									</div>
									<div class="count-content">
										<h4><span class="counterUp">15000</span></h4>
										<p>Total Nauticles</p>
									</div>
								</div>
							</div>
						</div>
						<div class="col-lg-3 col-md-6 col-12 d-flex">
							<div class="count-group flex-fill">
								<div class="customer-count d-flex align-items-center">
									<div class="count-img">
										<img src="/frontend/assets/img/icons/counter-icon-04.svg" alt="Icon">
									</div>
									<div class="count-content">
										<h4><span class="counterUp">5000</span>+</h4>
										<p>Booking Completed</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="faq-items">
					<div class="section-header-two">
						<h2>Frequently Asked Questions</h2>
						<p>Feel free to customize them further to align with your specific policies</p>
					</div>
					<div class="row justify-content-center">
						<div class="col-lg-8">
							<div class="faq-main-items" id="faq-details">
								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingOne">
										<a href="javascript:void(0);" class="accordion-button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
											What documents do I need to rent a Yacht?
										</a>
									</h2>
									<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faq-details">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>We offer a diverse fleet of vehicles to suit every 
													need, including compact yachts. You can browse our selection online or contact us for 
													assistance in choosing the right vehicle for you
												</p>
											</div> 
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->
								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingTwo">
										<a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
											What types of Yacht’s are available for rent?
										</a>
									</h2>
									<div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faq-details">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>We offer a diverse fleet of vehicles to suit every 
													need, including compact yachts. You can browse our selection online or contact us for 
													assistance in choosing the right vehicle for you
												</p>
											</div> 
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->
								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingThree">
										<a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
											Can I rent a yacht for more than one week?
										</a>
									</h2>
									<div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faq-details">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>We offer a diverse fleet of vehicles to suit every 
													need, including compact yachts. You can browse our selection online or contact us for 
													assistance in choosing the right vehicle for you
												</p>
											</div> 
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->
								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingFour">
										<a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
											How old do I need to be to rent a Yacht ?
										</a>
									</h2>
									<div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faq-details">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>We offer a diverse fleet of vehicles to suit every 
													need, including compact yachts. You can browse our selection online or contact us for 
													assistance in choosing the right vehicle for you
												</p>
											</div> 
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->
								<!-- FAQ Item -->
								<div class="accordion-item">
									<h2 class="accordion-header" id="headingFive">
										<a href="javascript:void(0);" class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
											Can I rent a Yacht with a debit card?
										</a>
									</h2>
									<div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faq-details">
										<div class="accordion-body">
											<div class="accordion-content">
												<p>We offer a diverse fleet of vehicles to suit every 
													need, including compact yachts. You can browse our selection online or contact us for 
													assistance in choosing the right vehicle for you
												</p>
											</div> 
										</div>
									</div>
								</div>
								<!-- /FAQ Item -->
							</div>
						</div>
					</div>
				</div>
			</div>
		</section>
		<!-- /FAQ -->

		<!-- Seasonal Special -->
		<section class="seasonal-special-sec">
			<div class="container">
				<div class="sec-title">
					<h2>Yacht Charter Seasonal Specials</h2>
					<p>Experience the ultimate in luxury and adventure with our yacht rental services.</p>
					<div class="sec-btns">
						<a href="login.html" class="btn btn-dark-blue">Get Started</a>
						<a href="listing-details.html" class="btn btn-primary d-flex align-items-center"><i class="bx bx-bar-chart me-2"></i>Learn More</a>
					</div>
				</div>
			</div>
		</section>
		<!-- /Seasonal Special -->

		<!-- News & Insights -->
		<section class="news-insights-sec">
			<div class="sec-round-colors">
				<span class="bg-orange round-small"></span>
				<span class="bg-orange round-small"></span>
				<span class="bg-orange round-small"></span>
				<span class="bg-dark-blue round-small"></span>
				<span class="bg-dark-blue round-big"></span>
				<span class="bg-dark-blue round-big"></span>
			</div>
			<div class="sec-bg">
				<img src="/frontend/assets/img/bg/ship-part-bg-01.png" alt="Img">
			</div>
			<div class="container">
				<div class="section-header-two">
					<h2>News & Insights For You</h2>
					<p>This blog post provides valuable insights into the benefits of yacht rentals 
						and offers practical tips for planning the perfect charter.
					</p>
				</div>
				<div class="row">
					<div class="col-lg-4 col-md-6">
						<div class="article-grid-card">
							<div class="article-img">
								<a href="blog-grid.html"><img src="/frontend/assets/img/blog/article-01.jpg" class="img-fluid" alt="Img"></a>
								<span class="date-info"><i class="bx bx-calendar me-2"></i>Apr 21, 2024</span>
							</div>
							<div class="user-head">
								<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-14.jpg" alt="Img">By Sanax</a>
								<div class="user-head-right">
									<span class="me-3"><i class="bx bx-comment-detail me-2"></i>25 Comments</span>
									<span><i class="bx bx-like me-2"></i>25 Likes</span>
								</div>
							</div>
							<div class="article-title">
								<h4><a href="blog-grid.html">The Ultimate Guide to Yacht Rentals: Experience Luxury</a></h4>
								<p>Are you dreaming of a vacation where luxury meets adventure? Look no further</p>
								<a href="blog-details.html" class="read-more">Read More <i class="bx bx-right-arrow-alt ms-2"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6">
						<div class="article-grid-card">
							<div class="article-img">
								<a href="blog-grid.html"><img src="/frontend/assets/img/blog/article-02.jpg" class="img-fluid" alt="Img"></a>
								<span class="date-info"><i class="bx bx-calendar me-2"></i>Apr 25, 2024</span>
							</div>
							<div class="user-head">
								<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-15.jpg" alt="Img">By Adrian</a>
								<div class="user-head-right">
									<span class="me-3"><i class="bx bx-comment-detail me-2"></i>25 Comments</span>
									<span><i class="bx bx-like me-2"></i>25 Likes</span>
								</div>
							</div>
							<div class="article-title">
								<h4><a href="blog-grid.html">Planning Your Yacht Charter this Summer </a></h4>
								<p>Start planning your yacht charter today and experience the magic of luxury travel....</p>
								<a href="blog-details.html" class="read-more">Read More <i class="bx bx-right-arrow-alt ms-2"></i></a>
							</div>
						</div>
					</div>
					<div class="col-lg-4 col-md-6">
						<div class="article-grid-card">
							<div class="article-img">
								<a href="blog-grid.html"><img src="/frontend/assets/img/blog/article-03.jpg" class="img-fluid" alt="Img"></a>
								<span class="date-info"><i class="bx bx-calendar me-2"></i>Apr 30, 2024</span>
							</div>
							<div class="user-head">
								<a href="javascript:void(0);" class="img-avatar"><img src="/frontend/assets/img/profiles/avatar-16.jpg" alt="Img">By Hendrita</a>
								<div class="user-head-right">
									<span class="me-3"><i class="bx bx-comment-detail me-2"></i>25 Comments</span>
									<span><i class="bx bx-like me-2"></i>25 Likes</span>
								</div>
							</div>
							<div class="article-title">
								<h4><a href="blog-grid.html">Experience the Magic of Yacht Rentals in your Location</a></h4>
								<p>Whether you're cruising the Caribbean, exploring the Mediterranean, or discovering the...</p>
								<a href="blog-details.html" class="read-more">Read More <i class="bx bx-right-arrow-alt ms-2"></i></a>
							</div>
						</div>
					</div>
					<div class="col-md-12">
						<div class="view-more text-center">
							<a href="blog-grid.html" class="btn btn-secondary">View all Articles</a>
						</div>
					</div>
				</div>
				<div class="yacht-owner-card party-rental-yacht">
					<div class="sec-bg">
						<img src="/frontend/assets/img/bg/sec-bg-wave.png" alt="Img">
					</div>
					<div class="row">
						<div class="col-md-4"></div>
						<div class="col-md-8">
							<div class="yacht-owner-title">
								<h3>Special Offers on Party rental Yachts!!!</h3>
								<p>View all the yachts under the Offer</p>
								<a href="listing-details.html" class="btn btn-primary">View Yachts</a>
							</div>
						</div>
					</div>
					<div class="yacht-owner-img">
						<img src="/frontend/assets/img/bg/yacht-owner-bg-02.png" alt="Img">
					</div>
				</div>
			</div>
		</section>
		<!-- /News & Insights -->
		@include('frontend.toast')
		@include('frontend.theme_4.partials.footer')

	</div>

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
		<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919px, 307.919px; stroke-dashoffset: 228.265px;"></path>
		</svg>
	</div>
	<!-- scrollToTop end -->
    @include('frontend.theme_4.partials.scripts')
</body>
</html>