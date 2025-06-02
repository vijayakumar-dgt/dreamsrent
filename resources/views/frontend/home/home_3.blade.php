@extends('frontend.theme_3.app')
@section('content')
{{-- @dd($content_sections); --}}
@if(!empty($content_sections) && count($content_sections) > 0)
@foreach ($content_sections as $section)
    @switch($section['section_type'] ?? "")
       @case('banner_three')
           @include('frontend.home.partials_3.banner', ['section' => $section])
           @break
        @case('featured_category')
            @include('frontend.home.partials_3.featured_category', ['section' => $section])
            @break
        @case('popular_vehicle')
            @include('frontend.home.partials_3.top_rated_vehicle', ['section' => $section])
            @break
        @case('ad_card_section')
            @include('frontend.home.partials_3.ad_card', ['section' => $section])
            @break
        @case('how_it_works')
            @include('frontend.home.partials_3.how-it-works', ['section' => $section])
            @break
        @case('brands')
            @include('frontend.home.partials_3.brands', ['section' => $section])
        @break
        @case('feature_vehicle')
            @include('frontend.home.partials_3.feature_vehicles', ['section' => $section])
        @break
        @case('locations')
            @include('frontend.home.partials_3.locations', ['section' => $section])
        @break
        @case('facts_section')
            @include('frontend.home.partials_3.facts', ['section' => $section])
        @break
        @case('testimonial')
            @include('frontend.home.partials_3.testimonials', ['section' => $section])
        @break
        @case('blog')
            @include('frontend.home.partials_3.blogs', ['section' => $section, 'content_sections' => $content_sections])
            @break
        @case('marquee_section')
            @include('frontend.home.partials_3.marquee', ['section' => $section])
        @break
    @endswitch
@endforeach
@endif
@endsection