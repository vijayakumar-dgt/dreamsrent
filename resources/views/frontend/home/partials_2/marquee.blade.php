    <section class="support-section">
        <div class="horizontal-slide d-flex" data-direction="left" data-speed="slow">
            <div class="slide-list d-flex">
                @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                @foreach($section['section_content'] as $content)
                <div class="support-item">
                    <h2>{{ ucfirst($content['text'] ?? "") }}</h2>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </section>