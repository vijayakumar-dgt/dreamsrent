@if($isRTL)
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.rtl.min.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
@endif
<!-- Fontawesome CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">

<!-- Select2 CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/select2/css/select2.min.css') }}">

<!-- Datepicker CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap-datetimepicker.min.css') }}">

<!-- Aos CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/aos/aos.css') }}">

<!-- Fearther CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/feather.css') }}">

<!-- Owl carousel CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">

<!-- Slick CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/slick/slick.css') }}">

<!-- Boxicons CSS -->
<link rel="stylesheet" href="{{ asset('frontend/assets/plugins/boxicons/css/boxicons.min.css') }}">
@stack('styles')
@if($isRTL)
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style-rtl.css') }}">
@else
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
@endif
<link rel="stylesheet" href="{{ asset('backend/assets/css/custom/custom-style.css') }}">
