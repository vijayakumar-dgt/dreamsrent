<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! SEOMeta::generate() !!}
    {!! OpenGraph::generate() !!}
    <title>@yield('meta_title', $companyName)</title>

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/x-icon" href="{{ $favicon }}">

    <!-- Apple Touch Icon -->
    <link rel="apple-touch-icon" sizes="180x180" href="{{ $favicon }}">

    <!-- Styles -->
    @include('admin.partials.styles')
</head>

<body data-currency="{{ getDefaultCurrencySymbol() ?? '$' }}" data-user-type="{{ currentUser()->user_type ?? '' }}"
    data-permission_error="{{ session('permission-error') }}">
    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Header -->
        @include('admin.partials.header')

        <!-- Sidebar -->
        @include('admin.partials.sidebar')

        <!-- Main Content -->
        @yield('content')

        <!-- Toast -->
        @include('admin.partials.toast')
    </div>
    <!-- /Main Wrapper -->

    <!-- Scripts -->
    @include('admin.partials.scripts')
</body>

</html>
