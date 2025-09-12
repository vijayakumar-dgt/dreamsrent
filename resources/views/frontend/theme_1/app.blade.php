<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($seo_title) ? $seo_title : $companyName }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ isset($seo_description) ? $seo_description : $companyName }}">
    <meta name="keywords" content="{{ isset($meta_keywords) ? $meta_keywords : '' }}">

    <!-- Open Graph Tags (for social sharing) -->
    <meta property="og:title" content="{{ isset($og_title) ? $og_title : $companyName }}">
    <meta property="og:description" content="{{ isset($og_description) ? $og_description : '' }}">
    <meta property="og:image"
        content="{{ isset($og_image) ? asset($og_image) : asset('frontend/assets/img/logo.svg') }}">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ isset($favicon) ? asset($favicon) : asset('frontend/assets/img/favicon.png') }}">
    @php
    $isRTL = isRTL(app()->getLocale());
    @endphp
    @include('frontend.theme_1.partials.styles')
</head>

<body data-theme="{{ $theme ?? 1 }}" data-dir="{{ $isRTL ? 'rtl' : 'ltr' }}">
    <div class="main-wrapper">
        @include('frontend.theme_1.partials.header')
        @yield('content')
        @include('frontend.theme_1.partials.footer')
        @include('frontend.toast')
        @if(request()->routeIs('home'))
        @include('frontend.home.cookie.consent')
        @endif
    </div>
    @if(!request()->routeIs(['home', 'theme']))
    @include('frontend.preloader')
    @endif
    <div class="progress-wrap active-progress">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"></path>
        </svg>
    </div>
    @include('frontend.theme_1.partials.scripts')
</body>

</html>
