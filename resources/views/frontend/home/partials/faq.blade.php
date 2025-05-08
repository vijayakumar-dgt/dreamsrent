<section class="section faq-section bg-light-primary">
    <div class="container">				
        <!-- Heading title-->
        <div class="section-heading" data-aos="fade-down">
            <h2>{{ $section['section_title'] ?? "" }}</h2>
            <p>{{ $section['section_label'] ?? "" }}</p>
        </div>
        <!-- /Heading title -->
        <div class="faq-info">
            @foreach($section['section_content'] as $faq)
                <div class="faq-card bg-white" data-aos="fade-down">
                    <h4 class="faq-title">
                        <a class="@if($loop->first == false) collapsed @endif" data-bs-toggle="collapse" href="#faq-{{ $faq->id ?? $loop->index }}" aria-expanded="{{ $loop->first ? 'true' : 'false' }}">
                            {{ ucfirst($faq->question ?? "") }}
                        </a>
                    </h4>
                    <div id="faq-{{ $faq->id ?? $loop->index }}" class="card-collapse collapse @if($loop->first) show @endif">
                        <p>{{ ucfirst($faq->answer ?? "") }}</p>
                    </div>
                </div>	
            @endforeach									
        </div>		
    </div>	
</section>