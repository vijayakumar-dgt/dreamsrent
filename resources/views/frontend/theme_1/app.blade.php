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
	<meta name="keywords" content="{{ isset($meta_keywords) ? $meta_keywords : '' }}">
	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ isset($favicon) ? asset($favicon) : asset('frontend/assets/img/favicon.png') }}">
	@php
		$isRTL = isRTL(app()->getLocale());
	@endphp
	@if($isRTL)
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.rtl.min.css') }}">
	@else
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
	@endif
	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">
	@if(request()->routeIs([
		'list',
		'vehicleDetails',
		'booking.checkout',
		'user.dashboard',
		'user.ticket',
		'user.usersettings',
		'user.preference'
	]))
		<!-- Select2 CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/select2/css/select2.min.css') }}">
	@endif
	@if(request()->routeIs([
		'home',
		'list',
		'vehicleDetails',
		'booking.checkout',
	]))
		<!-- Datepicker CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap-datetimepicker.min.css') }}">
	@endif
	@if(request()->routeIs([
		'home',
		'pages',
		'contact-us',
	]))
		<!-- Aos CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/aos/aos.css') }}">
	@endif
	<!-- Fearther CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/feather.css') }}">
	@if(request()->routeIs([
		'home',
		'list',
		'vehicleDetails',
		'pages*',
	]))
		<!-- Owl carousel CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
	@endif
	<!-- Boxicons CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/boxicons/css/boxicons.min.css') }}">
	@stack('styles')
	@if($isRTL)
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/style-rtl.css') }}">
	@else
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
	@endif
	<link rel="stylesheet" href="{{ asset('backend/assets/css/custom/custom-style.css?v=1.0.3') }}">
</head>

<body data-theme={{ $theme ?? 1 }} data-dir="{{ $isRTL ? 'rtl' : 'ltr' }}">

	<div class="main-wrapper">
		<!-- Header -->
		@include('frontend.theme_1.header')
		<!-- /Header -->
		@yield('content')
		<!-- Footer -->
		@include('frontend.theme_1.footer')
		<!-- /Footer -->
		@include('frontend.toast')
		<!-- Cookie Consent -->
		@if(request()->routeIs('home'))
			@include('frontend.home.cookie.consent')
		@endif
	</div>

	<!-- scrollToTop start -->
	<div class="progress-wrap active-progress">
		<svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
		</svg>
	</div>
	<!-- scrollToTop end -->

	<!-- jQuery -->
	<script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
	<!-- jQuery validation -->
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>
	<!-- Bootstrap Core JS -->
	<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
	@if(request()->routeIs(['home', 'pages']))
		<!-- counterup JS -->
		<script src="{{ asset('frontend/assets/js/jquery.waypoints.js') }}"></script>
		<script src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>
	@endif
	@if(request()->routeIs([
		'list',
		'vehicleDetails',
		'booking.checkout',
		'user.dashboard',
		'user.ticket',
		'user.usersettings',
		'user.preference'
	]))
		<!-- Select2 JS -->
		<script src="{{ asset('frontend/assets/plugins/select2/js/select2.min.js') }}"></script>
	@endif
	@if(request()->routeIs([
		'home',
		'pages',
		'contact-us',
	]))
		<!-- Aos -->
		<script src="{{ asset('frontend/assets/plugins/aos/aos.js') }}"></script>
	@endif
	<!-- Top JS -->
	<script src="{{ asset('frontend/assets/js/backToTop.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/moment/moment.min.js') }}"></script>
	@if(request()->routeIs([
		'home',
		'list',
		'vehicleDetails',
		'booking.checkout',
	]))
		<!-- Datepicker Core JS -->
		<script src="{{ asset('frontend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
	@endif
	@if(request()->routeIs([
		'home',
		'list',
		'vehicleDetails',
		'pages*',
	]))
		<!-- Owl Carousel JS -->
		<script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
	@endif
	<script src="{{ asset('frontend/assets/js/custom/lang_script.js') }}"></script>
	@stack('scripts')
	<!-- Custom JS -->
	@if($isRTL)
		<script src="{{ asset('frontend/assets/js/script-rtl.js') }}"></script>
	@else
		<script src="{{ asset('frontend/assets/js/script.js') }}"></script>
	@endif
	<script src="{{ asset('frontend/assets/js/custom/custom-script.js?v=1.0.4') }}"></script>

</body>

</html>
