@php 
    $sectionContent = $section['section_content'];
    $howItWorks = isset($section['section_content'][0]) ? $section['section_content'][0]->value : '';
@endphp
{!! $howItWorks !!}