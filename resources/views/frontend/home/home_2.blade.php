@extends('theme_2.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/theme-2-style.css') }}">
@endpush
@section('content')
@php

@endphp
@foreach($content_sections as $key => $section)
    @switch($section['section_type'] ?? "")
       @case('banner_two')
           @include('frontend.home.partials_2.banner', ['section' => $section, 'content_sections' => $content_sections])
           @break
       @case('featured_category')
           @include('frontend.home.partials_2.featured_category', ['section' => $section])
           @break
       @case('popular_vehicle')
           @include('frontend.home.partials_2.popular_vehicle', ['section' => $section])
           @break
       @case('brands')
           @include('frontend.home.partials_2.brands', ['section' => $section])
           @break
       @case('facts_section')
           @include('frontend.home.partials_2.facts', ['section' => $section])
           @break
       @case('feature_vehicle')
           @include('frontend.home.partials_2.feature_vehicles', ['section' => $section])
           @break
       @case('testimonial')
           @include('frontend.home.partials_2.testimonials', ['section' => $section])
           @break
        @case('blog')
            @include('frontend.home.partials_2.blogs', ['section' => $section, 'content_sections' => $content_sections])
            @break
        @case('faq')
            @include('frontend.home.partials_2.faq', ['section' => $section])
            @break
        @case('marquee_section')
            @include('frontend.home.partials_2.marquee', ['section' => $section])
            @break
        @case('all_category')
            @include('frontend.home.partials_2.all_category', ['section' => $section])
            @break
        @case('best_vehcile')
            @include('frontend.home.partials_2.best_vehicle', ['section' => $section])
            @break
     @endswitch
@endforeach
@endsection
@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/home/home_2.js?v=1.1') }}"></script>
@endpush
