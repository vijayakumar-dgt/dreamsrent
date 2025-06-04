<!-- Seasonal Special -->
<section class="seasonal-special-sec">
    <div class="container">
        <div class="sec-title">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
            <div class="sec-btns">
                <a href="{{ route('user-login') }}" class="btn btn-dark-blue">Get Started</a>
                <a href="{{ route('list') }}" class="btn btn-primary d-flex align-items-center"><i class="bx bx-bar-chart me-2"></i>Learn More</a>
            </div>
        </div>
    </div>
</section>
<!-- /Seasonal Special -->