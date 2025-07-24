@extends('frontend.theme_1.app')

@section('content')

<!-- Breadcrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ $data['page_title'] }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item"><a href="javascript:void(0);">{{__('admin.page.pages')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $data['page_title'] }}</li>
                    </ol>
                </nav>							
            </div>
        </div>
    </div>
</div>
<!-- /Breadcrumb Section -->

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
        @case('multiple_section')
            @include('frontend.home.partials.multiple-section')
            @break
    @endswitch
@endforeach

@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/home/home_1.js') }}"></script>
@endpush
