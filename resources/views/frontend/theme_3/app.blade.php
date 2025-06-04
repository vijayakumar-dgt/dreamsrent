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