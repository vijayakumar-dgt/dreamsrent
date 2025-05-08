    <section class="category-section-four">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <!-- Heading title-->
                    <div class="section-heading heading-four" data-aos="fade-down">
                        <h2>{{ $section['section_title'] }}</h2>
                        <p>{{ $section['section_label'] }}</p>
                    </div>
                    <!-- /Heading title -->
                    <div class="row row-gap-4">
                        @if(!empty($section['section_content']) && count($section['section_content']) > 0)
                        @foreach($section['section_content'] as $content)
                        <!-- Category Item -->
                        <div class="col-xl-2 col-md-4 col-sm-6 d-flex">
                            <div class="category-item flex-fill">
                                <div class="category-info d-flex align-items-center justify-content-between">
                                    <div>
                                        <h6 class="title"><a
                                                href="{{ route('list', ['category' => $content->id ?? '' ]) }}">{{ ucfirst($content->name ?? "") }}</a>
                                        </h6>
                                        <p>{{ $content->car_count ?? "" }} {{ __('web.common.vehicles')  }}</p>
                                    </div>
                                    <a href="{{ route('list', ['category' => $content->id ?? '' ]) }}" class="link-icon"><i
                                            class="bx bx-right-arrow-alt"></i></a>
                                </div>
                                <div class="category-img">
                                    <img src="{{ $content->image_url ?? '' }}" alt="img" class="img-fluid">
                                </div>
                            </div>
                        </div>
                        <!-- /Category Item -->
                        @endforeach
                        @endif
                    </div>
                    <div class="view-all-btn text-center aos" data-aos="fade-down">
                        <a href="{{ route('list') }}" class="btn btn-secondary">{{ __('web.home.view_all') }}<i
                                class="bx bx-right-arrow-alt ms-1"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </section>