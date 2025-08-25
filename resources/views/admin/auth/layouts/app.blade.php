<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="Dreams Rent - Admin Login">
	<meta name="keywords" content="admin, login">
	<meta name="author" content="Dreams technologies">
	<meta name="robots" content="noindex, nofollow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('meta_title', $companyName)</title>
	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('backend/assets/img/favicon.png') }}">
	<!-- Apple Touch Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png')}}">
	@include('admin.auth.partials.styles')
</head>
<body class="login-page">
	<!-- Main Wrapper -->
	<div class="main-wrapper">
		@yield('content')
		@include('admin.partials.toast')
		<div class="login-bg">
			<img src="{{ asset('backend/assets/img/bg/login-bg-01.png') }}" class="login-bg-01" alt="Background 1">
			<img src="{{ asset('backend/assets/img/bg/login-bg-02.png') }}" class="login-bg-02" alt="Background 2">
		</div>
	</div>
	<!-- /Main Wrapper -->
	@include('admin.auth.partials.scripts')
</body>
</html>
