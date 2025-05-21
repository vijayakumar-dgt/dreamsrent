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
	<link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">

	<!-- Apple Touch Icon -->
	<link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon }}">

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

	@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details']))
	<!-- Datatable CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/plugins/datatables/dataTables.bootstrap5.min.css') }}">
	@endif

	@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details', 'admin.newsletters']))
	<!-- Daterangepikcer CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/daterangepicker/daterangepicker.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/assets/css/bootstrap-datetimepicker.min.css') }}">
	@endif

	@if (Route::is(['admin.addPage', 'admin.editPage']))
	<!-- Dragula CSS -->
	<link rel="stylesheet" href="{{asset('backend/assets/plugins/dragula/css/dragula.min.css')}}">
	@endif

	<!-- summernote CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.css') }}">

	<!-- Fontawesome CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/fontawesome/css/all.min.css') }}">

	<!-- Select2 CSS -->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/select2/css/select2.min.css') }}">

	<!-- Mobile CSS-->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">

	@if (Route::is(['admin.seosetup-settings']))
	<!-- TagsInput CSS-->
	<link rel="stylesheet" href="{{ asset('backend/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css') }}">
	@endif

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

	@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details']))
	<!-- jQuery validation -->
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>
	@endif

	<!-- Feather Icon JS -->
	<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>

	<!-- Bootstrap Core JS -->
	<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

	<!-- Slimscroll JS -->
	<script src="{{ asset('backend/assets/js/jquery.slimscroll.min.js') }}"></script>

	<!-- Sticky Sidebar JS -->
	<script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>

	@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details', 'admin.newsletters']))
	<!-- Daterangepikcer JS -->
	<script src="{{ asset('backend/assets/js/moment.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/daterangepicker/daterangepicker.js') }}"></script>
	<script src="{{ asset('backend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
	@endif

	@if (Route::is(['admin.addPage', 'admin.editPage']))
	<!-- Dragula JS -->
	<script src="{{asset('backend/assets/plugins/dragula/js/dragula.min.js') }}"></script>
	<script src="{{asset('backend/assets/plugins/dragula/js/drag-drop.min.js') }}"></script>
	<script src="{{asset('backend/assets/plugins/dragula/js/draggable-cards.js') }}"></script>
	@endif

	@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details']))
	<!-- Datatable JS -->
	<script src="{{ asset('backend/assets/plugins/datatables/jquery.dataTables.min.js') }}"></script>
	<script src="{{ asset('backend/assets/plugins/datatables/dataTables.bootstrap5.min.js') }}"></script>
	@endif

	<!-- Select2 JS -->
	<script src="{{ asset('backend/assets/plugins/select2/js/select2.min.js') }}"></script>

	<!-- summernote JS -->
	<script src="{{ asset('backend/assets/plugins/summernote/summernote-bs5.min.js') }}"></script>

	<!-- Mobile Input -->
	<script src="{{ asset('backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>

	<!-- Toastr JS -->
	<script src="{{ asset('backend/assets/plugins/toastr/toastr.min.js') }}"></script>

	@if (Route::is(['admin.seosetup-settings']))
	<!-- Bootstrap Tagsinput JS -->
	<script src="{{ asset('backend/assets/plugins/bootstrap-tagsinput/bootstrap-tagsinput.js') }}"></script>
	@endif

	<!-- Custom JS -->
	<script src="{{ asset('backend/assets/js/script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/lang/lang-script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/permission/permission-script.js') }}"></script>

	<script src="{{ asset('backend/assets/js/custom/custom-script.js') }}"></script>

	@if (Route::is(['dashboard', 'admin.income-report', 'admin.earning-report']))
	<script src="{{ asset('backend/assets/js/admin/apexcharts.js') }}"></script>
	@endif

	@stack('scripts')
</body>

</html>
