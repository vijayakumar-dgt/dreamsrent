<!-- Seasonal Special -->
<section class="seasonal-special-sec">
    <div class="container">
        <div class="sec-title">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
            <div class="sec-btns">
                <a href="{{ route('user-login') }}" class="btn btn-dark-blue">{{ __('web.home.get_started') }}</a>
                <a href="{{ route('list') }}" class="btn btn-primary d-flex align-items-center"><i class="bx bx-bar-chart me-2"></i>{{ __('web.common.learn_more') }}</a>
            </div>
        </div>
    </div>
</section>
<!-- /Seasonal Special -->