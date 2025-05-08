    <section class="faq-section-four pt-0">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="section-heading heading-four" data-aos="fade-down">
                        <h2>{{ ucfirst($section['section_title'] ?? "")}}</h2>
                        <p>{{ ucfirst($section['section_label'] ?? "") }}</p>
                    </div>
                    <div class="accordion faq-accordion" id="faqAccordion">
                        @if (!empty($section['section_content']) && count($section['section_content']) > 0)
                        @foreach ($section['section_content'] as $index => $content)
                            @php
                                $isFirst = $index === 0;
                                $collapseId = 'faqCollapse' . $index;
                            @endphp
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button {{ $isFirst ? '' : 'collapsed' }}"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#{{ $collapseId }}"
                                        aria-expanded="{{ $isFirst ? 'true' : 'false' }}"
                                        aria-controls="{{ $collapseId }}">
                                        {{ ucfirst($content->question ?? '') }}
                                    </button>
                                </h2>
                                <div
                                    id="{{ $collapseId }}"
                                    class="accordion-collapse collapse {{ $isFirst ? 'show' : '' }}"
                                    data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <p>{{ ucfirst($content->answer ?? '') }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
