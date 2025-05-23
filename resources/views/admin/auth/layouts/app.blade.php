<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="Smarthr - Bootstrap Admin Template">
	<meta name="keywords" content="admin, estimates, bootstrap, business, html5, responsive, Projects">
	<meta name="author" content="Dreams technologies - Bootstrap Admin Template">
	<meta name="robots" content="noindex, nofollow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('meta_title', $companyName)</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('backend/assets/img/favicon.png') }}">

	<!-- Apple Touch Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png')}}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/tabler-icons/tabler-icons.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

	<!-- Toastr CSS -->
	<link href="{{ asset('backend/assets/plugins/toastr/toatr.css') }}" rel="stylesheet">
	
	
	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
    @stack('styles')
</head>

<body class="login-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">
			@yield('content')
			@include('admin.partials.toast')
		<div class="login-bg">
			<img src="{{ asset('backend/assets/img/bg/login-bg-01.png') }}" class="login-bg-01">
			<img src="{{ asset('backend/assets/img/bg/login-bg-02.png') }}" class="login-bg-02">
		</div>
	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

	<!-- jQuery validation -->
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

	<!-- Feather Icon JS -->
	<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>

	<!-- Toastr JS -->
	<script src="{{ asset('backend/assets/plugins/toastr/toastr.min.js') }}"></script>

	<!-- Custom JS -->
	<script src="{{ asset('backend/assets/js/script.js') }}"></script>
	
	<script src="{{ asset('backend/assets/js/custom/custom-script.js') }}"></script>

	<!-- language JS -->
	<script src="{{ asset('backend/assets/js/lang/lang-script.js') }}"></script>

    @stack('scripts')
</body>

</html>