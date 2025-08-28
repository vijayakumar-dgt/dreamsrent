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
