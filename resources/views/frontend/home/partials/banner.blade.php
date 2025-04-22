<section class="banner-section banner-slider">		
    <div class="container">
           <div class="home-banner">		
           <div class="row align-items-center">					    
                   <div class="col-lg-6" data-aos="fade-down">
                    <p class="explore-text"> <span><i class="fa-solid fa-thumbs-up me-2"></i></span>{{ $section['section_content'][0]->label ?? "" }} </p>
                    <h1><span>{{ $section['section_content'][0]->line_one ?? "" }}</span> <br>									
                    {{ $section['section_content'][0]->line_two ?? "" }}</h1>
                    <p>{{ $section['section_content'][0]->description ?? "" }}</p>
                    <div class="view-all">
                        <a href="{{ route('list') }}" class="btn btn-view d-inline-flex align-items-center">{{__('web.home.view_all_cars')}} <span><i class="feather-arrow-right ms-2"></i></span></a>
                    </div>
                   </div>
                   <div class="col-lg-6" data-aos="fade-down">
                       <div class="banner-imgs">
                        <img src="{{ $section['section_content'][0]->thumbnail_image }}" class="img-fluid aos" alt="bannerimage">							
                       </div>
                   </div>
               </div>
           </div>	
       </div>
</section>
<!-- /Banner -->