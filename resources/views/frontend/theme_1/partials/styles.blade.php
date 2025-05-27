    @if ($isRTL)
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.rtl.min.css') }}">
    @else
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap.min.css') }}">
    @endif

    <!-- Fontawesome CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/fontawesome/css/all.min.css') }}">

    @if (request()->routeIs([
        'list',
        'vehicleDetails',
        'booking.checkout',
        'user.dashboard',
        'user.ticket',
        'user.usersettings',
        'user.preference',
    ]))
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/select2/css/select2.min.css') }}">
    @endif
    @if (request()->routeIs(['home', 'list', 'vehicleDetails', 'booking.checkout']))
    <!-- Datepicker CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/bootstrap-datetimepicker.min.css') }}">
    @endif

    @if (request()->routeIs(['home', 'pages', 'contact-us']))
    <!-- Aos CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/aos/aos.css') }}">
    @endif

    <!-- Fearther CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/feather.css') }}">

    @if (request()->routeIs(['home', 'list', 'vehicleDetails', 'pages*']))
    <!-- Owl carousel CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/owl.carousel.min.css') }}">
    @endif

    <!-- Boxicons CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/plugins/boxicons/css/boxicons.min.css') }}">

    @stack('styles')

    @if ($isRTL)
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style-rtl.css') }}">
    @else
    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('frontend/assets/css/style.css') }}">
    @endif

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('backend/assets/css/custom/custom-style.css') }}">
