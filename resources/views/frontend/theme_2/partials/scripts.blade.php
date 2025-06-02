    <!-- jQuery -->
	<script src="{{ asset('frontend/assets/js/jquery-3.7.1.min.js') }}"></script>
	<!-- jQuery validation -->
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
	<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>
	<!-- Bootstrap Core JS -->
	<script src="{{ asset('frontend/assets/js/bootstrap.bundle.min.js') }}"></script>
	<!-- counterup JS -->
	<script src="{{ asset('frontend/assets/js/jquery.waypoints.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/jquery.counterup.min.js') }}"></script>
	<!-- Select2 JS -->
	<script src="{{ asset('frontend/assets/plugins/select2/js/select2.min.js') }}"></script>
	<!-- Aos -->
	<script src="{{ asset('frontend/assets/plugins/aos/aos.js') }}"></script>
	<!-- Top JS -->
	<script src="{{ asset('frontend/assets/js/backToTop.js') }}"></script>
	<!-- Flatpickr -->
	<script src="{{ asset('frontend/assets/plugins/flatpickr/flatpickr.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/plugins/flatpickr/forms-pickers.js') }}"></script>
	<!-- Datepicker Core JS -->
	<script src="{{ asset('frontend/assets/plugins/moment/moment.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/bootstrap-datetimepicker.min.js') }}"></script>
	<!-- Owl Carousel JS -->
	<script src="{{ asset('frontend/assets/js/owl.carousel.min.js') }}"></script>
	<script src="{{ asset('frontend/assets/js/custom/lang_script.js') }}"></script>
	@stack('scripts')
	@if($isRTL)
		<script src="{{ asset('frontend/assets/js/script-rtl.js') }}"></script>
	@else
		<script src="{{ asset('frontend/assets/js/script.js') }}"></script>
	@endif
	<script src="{{ asset('frontend/assets/js/custom/custom-script.js') }}"></script>