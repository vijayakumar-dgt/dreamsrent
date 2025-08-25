<!-- jQuery -->
<script src="{{ asset('backend/assets/js/jquery-3.7.1.min.js') }}"></script>

@if (!Route::is(['admin.permissions', 'admin.customer-recent-rents', 'admin.customer-details']))
<!-- jQuery validation -->
<script src="{{ asset('backend/assets/js/jquery/jquery-validation.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/jquery/jquery-validation-additional-methods.min.js') }}"></script>
@endif

<!-- Feather Icon JS -->
<script src="{{ asset('backend/assets/js/feather.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/purify.min.js') }}"></script>

<!-- Bootstrap Core JS -->
<script src="{{ asset('backend/assets/js/bootstrap.bundle.min.js') }}"></script>

<!-- Slimscroll JS -->
<script src="{{ asset('backend/assets/js/jquery.slimscroll.min.js') }}"></script>

<!-- Sticky Sidebar JS -->
<script src="{{ asset('frontend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
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
<script src="{{ asset('frontend/assets/plugins/select2/js/select2.min.js') }}"></script>

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