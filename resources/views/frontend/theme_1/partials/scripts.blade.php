    <!-- jQuery -->
    <script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>

    <!-- jQuery validation -->
    <script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
    <script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>

    <!-- Bootstrap JS -->
    <script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>

    @if (request()->routeIs('home', 'pages','theme'))
    <!-- Counterup JS -->
    <script src="{{ asset('frontend/assets/js/jquery.waypoints.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>
    @endif

    @if (request()->routeIs([
        'list',
        'vehicleDetails',
        'booking.checkout',
        'user.dashboard',
        'user.ticket',
        'user.usersettings',
        'user.preference',
    ]))
    <!-- Select2 JS -->
    <script src="{{ asset('frontend/assets/plugins/select2/js/select2.min.js') }}"></script>
    @endif

    @if (request()->routeIs(['home', 'pages', 'contact-us','theme']))
    <!-- Aos JS -->
    <script src="{{ asset('frontend/assets/plugins/aos/aos.js') }}"></script>
    @endif

    <!-- Back to top JS -->
    <script src="{{ asset('frontend/assets/js/backToTop.js') }}"></script>

    <!-- Moment JS -->
    <script src="{{ asset('frontend/assets/plugins/moment/moment.min.js') }}"></script>

    @if (request()->routeIs(['home', 'list', 'vehicleDetails', 'booking.checkout','theme']))
    <!-- Datetimepicker JS -->
    <script src="{{ asset('frontend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
    @endif

    @if (request()->routeIs(['home', 'list', 'vehicleDetails', 'pages*','theme']))
    <!-- Owl carousel JS -->
    <script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
    @endif

    <!-- Language JS -->
    <script src="{{ asset('frontend/assets/js/custom/lang_script.js') }}"></script>
    @stack('scripts')

    <!-- Main JS -->
    @if ($isRTL)
    <script src="{{ asset('frontend/assets/js/script-rtl.js') }}"></script>
    @else
    <script src="{{ asset('frontend/assets/js/script.js') }}"></script>
    @endif

    <!-- Custom JS -->
    <script src="{{ asset('frontend/assets/js/custom/custom-script.js') }}"></script>
