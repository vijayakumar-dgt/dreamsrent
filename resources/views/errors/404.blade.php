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
		
		<!-- Main CSS -->
		<link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
	</head>
    <body class="error-page">
	
		<!-- Main Wrapper -->
        <div class="main-wrapper">
			
			<div class="error-box">
				<img src="/frontend/assets/img/404.png" class="img-fluid" alt="Page not found">
				<h3>Oops! Page not found!</h3>
				<p>The page you requested was not found.</p>
				<div class="back-button">					
					<a href="{{ route('home') }}" class="btn btn-primary">Back to Home</a>
				</div>
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