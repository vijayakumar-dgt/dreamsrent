@php
  $vehicleTypes = $section['section_content'];
  $title = $section['section_title'];
  //split last word
  $title = explode(' ', $title);
  $titlelastpart = end($title);
  $title = implode(' ', array_slice($title, 0, -1));

@endphp
<!-- Category  Section -->
<section class="section category-section">
    <div class="category-bg">
        <img src="{{ asset('frontend/assets/img/bg/category-bg.png') }}" class="img-fluid shape-01" alt="image">
        <img src="{{ asset('frontend/assets/img/bg/ban-bg-05.png') }}" class="img-fluid shape-02" alt="image">
    </div>		
    <div class="container">	
        <div class="row">	
            <div class="col-md-12">	
                
                <!-- Heading title-->
                <div class="section-heading heading-three" data-aos="fade-down">
                    <h2>{{ $title }} <span>{{ $titlelastpart }}</span></h2>
                    <p>{{ $section['section_label'] ?? "" }}</p>
                </div>
                <!-- /Heading title -->

                <!-- Category List -->
                <div class="bike-category-slider nav-center owl-carousel">
                    @if(!empty($vehicleTypes) && count($vehicleTypes) > 0)
                    @foreach($vehicleTypes as $vehicleType)
                    <div class="item">
                        <a href="javascript:void(0);" class="category-wrap">
                            <span class="category-img">
                                <img src="{{ $vehicleType->image_url }}" class="img-fluid" alt="image">
                            </span>
                            <h6>{{ $vehicleType->name }} </h6>
                            <p>{{ $vehicleType->car_count }} {{ __('web.home.bikes') }}</p>
                        </a>
                    </div>
                    @endforeach
                    @endif
                </div>
                <!-- /Category List -->

                <div class="view-all-btn text-center aos" data-aos="fade-down">
                    <a href="{{ route('list') }}" class="btn btn-secondary">{{ __('web.home.view_all_categories') }}</a>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- /Category  Section -->