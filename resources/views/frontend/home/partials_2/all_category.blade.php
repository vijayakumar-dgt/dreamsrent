    <section class="categories-section">
        <div class="container">
            <div class="accordion custom-accordion" id="faqAcordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqOne" aria-expanded="true" aria-controls="faqOne">
                            {{ $section['section_label'] ?? 'Categories' }}
                        </button>
                    </h2>
                    <div id="faqOne" class="accordion-collapse collapse show" data-bs-parent="#faqAcordion">
                        <div class="accordion-body">
                            <div class="row row-gap-3">
                                @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                                @foreach($section['section_content']->chunk(3) as $chunk)
                                    <div class="col-lg-3 col-md-4 col-sm-6">
                                        <ul class="category-list">
                                            @foreach($chunk as $item)
                                                <li><a href="{{ route('list', ['category' => $item['id'] ?? '' ]) }}">{{ $item['name'] ?? '' }}</a></li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endforeach
                                @else
                                <div class="col-12">
                                    <p class="text-center">{{ __('web.common.empty_table') }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
