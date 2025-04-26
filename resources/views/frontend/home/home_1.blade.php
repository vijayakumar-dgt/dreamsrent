@extends('theme_1.app')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/custom/theme-2.css') }}.css">
@endpush
@section('content')

@foreach($content_sections as $key => $section)
      @switch($section['section_type'] ?? "")
          @case('banner')
              @include('frontend.home.partials.banner')
              @break
          @case('search_section')
              @include('frontend.home.partials.search')
              @break
          @case('how_it_works')
              @include('frontend.home.partials.how-it-works')
              @break
          @case('popular_vehicle')
              @include('frontend.home.partials.popular_vehicle')
              @break
          @case('car_type')
              @include('frontend.home.partials.car_types')
              @break
          @case('facts_section')
              @include('frontend.home.partials.facts')
              @break
          @case('ad_card_section')
              @include('frontend.home.partials.ad_card')
              @break
          @case('feature_vehicle')
              @include('frontend.home.partials.feature_vehicles')
              @break
          @case('testimonial')
              @include('frontend.home.partials.testimonials')
              @break
          @case('faq')
              @include('frontend.home.partials.faq')
              @break
          @case('why_us_section')
              @include('frontend.home.partials.why_us')
              @break
          @case('blog')
              @include('frontend.home.partials.blog')
              @break
      @endswitch
@endforeach
@endsection
@push('scripts')
   <script src="{{ asset('frontend/assets/js/custom/home/home_1.js') }}"></script>
@endpush
