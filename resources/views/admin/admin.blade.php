<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
	<meta name="description" content="Dreamsrent Admin">
	<meta name="keywords" content="admin">
	<meta name="author" content="Dreams technologies - Dreamsrent">
	<meta name="robots" content="noindex, nofollow">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<title>@yield('meta_title', $companyName)</title>

	<!-- Favicon -->
	<link rel="shortcut icon" type="image/x-icon" href="{{ asset('backend/assets/img/favicon.png') }}">

	<!-- Apple Touch Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ asset('backend/assets/img/apple-touch-icon.png') }}">

	<!-- Bootstrap CSS -->
	@php
		$isRTL = isRTL(app()->getLocale());
	@endphp
	@if ($isRTL)
	<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.rtl.min.css') }}">
	@else
	<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap.min.css') }}">
	@endif

	<!-- Tabler Icon CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/tabler-icons/tabler-icons.min.css') }}">

	<!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">

	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/daterangepicker/daterangepicker.css') }}">

	<!-- Dragula CSS -->
	<link rel="stylesheet" href="{{asset('backend/assets/plugins/dragula/css/dragula.min.css')}}">

	<!-- summernote CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.css') }}">

	<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-datetimepicker.min.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/select2/css/select2.min.css') }}">

	<!-- Mobile CSS-->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">

	<!-- TagsInput CSS-->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">

	<!-- Toastr CSS -->
	<link href="{{ asset('backend/assets/plugins/toastr/toatr.css') }}" rel="stylesheet">

	<!-- Main CSS -->
	@if ($isRTL)
	<link rel="stylesheet" href="{{ asset('backend/assets/css/style-rtl.css') }}">
	@else
	<link rel="stylesheet" href="{{ asset('backend/assets/css/style.css') }}">
	@endif
	@stack('style')
	<link rel="stylesheet" href="{{ asset('backend/assets/css/custom/custom-style.css') }}">
</head>

<body data-page="{{ Route::currentRouteName() }}" data-user-type="{{ current_user()->user_type ?? '' }}" data-currency="{{ getDefaultCurrencySymbol() ?? '$' }}" data-permission_error="{{ session('permission-error') }}">

	<!-- Main Wrapper -->
	<div class="main-wrapper">
		@include('admin.partials.header')
		@include('admin.partials.sidebar')
		@yield('content')
		@include('admin.partials.toast')
	</div>
	<!-- /Main Wrapper -->

	<!-- jQuery -->
	<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

	<!-- jQuery validation -->
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>

	<!-- Feather Icon JS -->
	<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

	<!-- Slimscroll JS -->
	<script src="{{ asset('backend/assets/js/jquery.slimscroll.min.js') }}"></script>

	<!-- Sticky Sidebar JS -->
	<script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

	<!-- Daterangepikcer JS -->
	<script src="{{ asset('backend/assets/js/moment.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ asset('backend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>

	<!-- Dragula JS -->
	<script src="{{asset('backend/assets/plugins/dragula/js/dragula.min.js') }}"></script>
	<script src="{{asset('backend/assets/plugins/dragula/js/drag-drop.min.js') }}"></script>
	<script src="{{asset('backend/assets/plugins/dragula/js/draggable-cards.js') }}"></script>

	<!-- Datatable JS -->
	<script src="{{ asset('backend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>

	<!-- Select2 JS -->
	<script src="{{ asset('backend/assets/plugins/select2/js/select2.min.js') }}"></script>

	<!-- summernote JS -->
	<script src="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>

	<!-- Mobile Input -->
	<script src="{{ asset('backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>

	<!-- Toastr JS -->
	<script src="{{ asset('backend/assets/plugins/toastr/toastr.min.js') }}"></script>

	<!-- Bootstrap Tagsinput JS -->
	<script src="{{ asset('backend/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>

	<!-- Custom JS -->
	<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/lang/lang-script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/permission/permission-script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/custom/custom-script.js?v=1.1') }}"></script>

	<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

	@stack('scripts')
</body>

</html>
