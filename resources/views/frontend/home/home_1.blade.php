    @extends('frontend.theme_1.app')
    @section('content')
    @php
        $partials = [
                'banner' => 'frontend.home.partials.banner',
                'search_section' => 'frontend.home.partials.search',
                'how_it_works' => 'frontend.home.partials.how-it-works',
                'popular_vehicle' => 'frontend.home.partials.popular_vehicle',
                'car_type' => 'frontend.home.partials.car_types',
                'facts_section' => 'frontend.home.partials.facts',
                'ad_card_section' => 'frontend.home.partials.ad_card',
                'feature_vehicle' => 'frontend.home.partials.feature_vehicles',
                'testimonial' => 'frontend.home.partials.testimonials',
                'faq' => 'frontend.home.partials.faq',
                'why_us_section' => 'frontend.home.partials.why_us',
                'blog' => 'frontend.home.partials.blog',
        ];
    @endphp
    @foreach($content_sections as $section)
        @includeIf($partials[$section['section_type']] ?? null)
    @endforeach
   
    @endsection
    @push('scripts')
    <script src="{{ asset('frontend/assets/js/custom/home/home_1.js') }}"></script>
    @endpush
