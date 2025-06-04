<!-- Renting Yacht  -->
<section class="renting-yacht-sec">
    <div class="sec-round-colors">
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-orange round-small"></span>
        <span class="bg-dark-blue round-small"></span>
        <span class="bg-dark-blue round-big"></span>
        <span class="bg-orange round-big"></span>
    </div>
    <div class="sec-bg">
        <img src="/frontend/assets/img/bg/ship-part-bg-01.png" alt="Bg">
    </div>
    <div class="container">
        <div class="section-header-two">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        @php 
            $firstHalfPoints = !empty($section['section_content']['items']) && count($section['section_content']['items']) > 0 ? array_slice($section['section_content']['items'], 0, 3) : [];//section_content first 3 points
            $lastHalfPoints = !empty($section['section_content']['items']) && count($section['section_content']['items']) > 0 ? array_slice($section['section_content']['items'], 3) : [];//section_content last 3 points
        @endphp
        <div class="renting-yacht-benifits d-flex align-items-center justify-content-between">
            <ul>
                @if(!empty($firstHalfPoints) && count($firstHalfPoints) > 0)
                @foreach($firstHalfPoints as $point)
                <li>
                    <span class="benifit-icon"><img src="{{ $point['image'] ?? "" }}" alt="icon"></span>
                    <div class="benifit-contents">
                        <h5>{{ $point['label'] ?? "" }}</h5>
                        <p>{{ $point['description'] }}</p>
                    </div>
                </li>
                @endforeach
                @endif
            </ul>
            <div class="yatcht-center-img">
                <span><img src="/frontend/assets/img/bg/benifits-sec-bg-01.png" class="img-fluid" alt="Img"></span>
                <span class="roung-img-bg"></span>
            </div>
            <ul>
                @if(!empty($lastHalfPoints) && count($lastHalfPoints) > 0)
                @foreach($lastHalfPoints as $point)
                <li>
                    <span class="benifit-icon"><img src="{{ $point['image'] ?? "" }}" alt="icon"></span>
                    <div class="benifit-contents">
                        <h5>{{ $point['label'] ?? "" }}</h5>
                        <p>{{ $point['description'] }}</p>
                    </div>
                </li>
                @endforeach
                @endif
            </ul>
        </div>
    </div>
</section>
<!-- /Renting Yacht -->