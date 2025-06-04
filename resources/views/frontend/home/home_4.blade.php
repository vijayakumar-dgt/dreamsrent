@extends('frontend.theme_4.app')
@section('content')
{{-- @dd($content_sections); --}}
@if(!empty($content_sections) && count($content_sections) > 0)
@foreach ($content_sections as $section)
        @switch($section['section_type'] ?? "")
            @case('banner_four')
                @include('frontend.home.partials_4.banner', ['section' => $section])
                @break
            @case('featured_category')
                @include('frontend.home.partials_4.featured_category', ['section' => $section])
                @break
            @case('yacht_benefits')
                @include('frontend.home.partials_4.yacht_benefits', ['section' => $section])
                @break
            @case('popular_vehicle')
                @include('frontend.home.partials_4.popular_vehicles', ['section' => $section])
            @break
            @case('how_it_works')
                @include('frontend.home.partials_4.how-it-works', ['section' => $section])
                @break
            @case('locations')
                @include('frontend.home.partials_4.locations', ['section' => $section])
                @break
            @case('testimonial')
                @include('frontend.home.partials_4.testimonials', ['section' => $section])
                @break
            @case('ad_card_section')
                @include('frontend.home.partials_4.ad_card', ['section' => $section])
                @break
            @case('blog')
                @include('frontend.home.partials_4.blogs', ['section' => $section])
                @break
            @case('faq')
                @include('frontend.home.partials_4.faq', ['section' => $section])
                @break
        @endswitch
@endforeach
@endif
@endsection