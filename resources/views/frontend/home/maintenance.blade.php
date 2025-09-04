<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<title>{{ isset($title) ? $title : config('app.name') }}</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="{{ asset('frontend/assets/img/favicon.png') }}">

	<!-- Bootstrap CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">

	<!-- Fearther CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/feather.css') }}">

	<!-- Main CSS -->
	<link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
</head>

<body class="error-page">

	<!-- Main Wrapper -->
	<div class="main-wrapper">

		<div class="error-box">
			<img src="{{ $response['image'] ?? asset('frontend/assets/img/maintenance.png') }}" class="img-fluid" alt="Maintenance">
			<h2 class="coming-soon pt-0">{{ __('web.home.maintenence_heading') }}</h2>
			<p>{!! $response['description'] ?? 'Our website is currently undergoing scheduled maintenance, will be right
				back in a few minutes.' !!}</p>
			<h6>{{ __('web.home.maintenence_text') }}</h6>
			<a href="{{ route('home') }}" class="btn-maintance btn btn-primary mt-3">{{ __('web.home.back_to_home') }}</a>
		</div>

	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>

	<!-- Custom JS -->
	<script src="{{ asset('frontend/assets/js/script.js') }}"></script>

</body>

</html>
