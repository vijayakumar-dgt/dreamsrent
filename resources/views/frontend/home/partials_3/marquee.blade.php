@php
    $sectionContent = $section['section_content'];
    $tags = array_column($sectionContent, 'text');
@endphp
<!-- Quote Section -->
<section class="quote-section">
    <div class="container">
        <div class="quote-list">
            <ul>
                @if(!empty($tags) && count($tags) > 0)
                @foreach($tags as $tag)
                <li>
                    <h4 class="text-white">{{ $tag }}</h4>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
    </div>
</section>
<!-- /Quote Section -->
