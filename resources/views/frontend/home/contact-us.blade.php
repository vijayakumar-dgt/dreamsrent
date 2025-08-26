    @extends($layout)
    @push('styles')
    <link rel="stylesheet" href="{{ asset('/backend/assets/plugins/intltelinput/css/intlTelInput.css') }}">
    @endpush
    @section('content')
        <section class="contact-section">
            <div class="container">
                <div class="contact-info-area">
                    <div class="row">
                        <div class="col-lg-3 col-md-6 col-12 d-flex aos" data-aos="fade-down" data-aos-duration="1200" data-aos-delay="0.1">
                            <div class="single-contact-info flex-fill">
                                <span><i class="feather-phone-call"></i></span>
                                <h3>{{ __('web.user.phone_number') }}</h3>
                                <a href="javascript:void(0);">{{ $companyPhoneNumber ?? "-" }}</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down" data-aos-duration="1200" data-aos-delay="0.2">
                            <div class="single-contact-info flex-fill">
                                <span><i class="feather-mail"></i></span>
                                <h3>{{ __('web.home.email_address') }}</h3>
                                <a href="mailto:{{ $companyEmail }}">{{ $companyEmail ?? "-" }}</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down" data-aos-duration="1200" data-aos-delay="0.3">
                            <div class="single-contact-info flex-fill">
                                <span><i class="feather-map-pin"></i></span>
                                <h3>{{ __('web.user.location') }}</h3>
                                <a href="javascript:void(0);">{{ $companyAddress ?? "-" }}</a>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 col-12 d-flex" data-aos="fade-down" data-aos-duration="1200" data-aos-delay="0.4">
                            <div class="single-contact-info flex-fill">
                                <span><i class="feather-clock"></i></span>
                                <h3>{{ __('web.user.opening_hours') }}</h3>
                                <a href="javascript:void(0);">Mon - Sat (10.00AM - 05.30PM)</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-info-area" data-aos="fade-down" data-aos-duration="1200" data-aos-delay="0.5">
                    <div class="row">
                        <div class="col-lg-6 d-flex">
                            <img src="{{ asset('/frontend/assets/img/contact-info.jpg') }}" class="img-fluid" alt="Contact">
                        </div>
                        <div class="col-lg-6">
                            <form id="contactForm">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">
                                    <h1>{{ __('web.home.get_in_touch') }}</h1>
                                    <div class="col-md-12">
                                        <div class="input-block">
                                            <label for="contact_name">{{ __('web.home.name') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact_name" id="contact_name" placeholder="{{ __('web.home.name_placeholder') }}">
                                            <span class="error-text text-danger" id="contact_name_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-block">
                                            <label for="contact_email">{{ __('web.home.email_address') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="contact_email" id="contact_email" placeholder="{{ __('web.home.email_placeholder') }}">
                                            <span class="error-text text-danger" id="contact_email_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-block">
                                            <label for="contact_phone">{{ __('web.home.phone_number') }} <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="contact_phone" name="contact_phone" placeholder="{{ __('web.home.phone_number_placeholder') }}">
                                            <input type="hidden" name="phone_number" id="international_phone_number">
                                            <span class="error-text text-danger" id="contact_phone_error"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-block">
                                            <label for="contact_comments">{{ __('web.home.comments') }} <span class="text-danger">*</span></label>
                                            <textarea class="form-control" rows="4" name="contact_comments" id="contact_comments" required cols="50" placeholder="{{ __('web.home.comments_placeholder') }}"></textarea>
                                            <span class="error-text text-danger" id="contact_comments_error"></span>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn contact-btn submitbtn">{{ __('web.home.send_enquiry') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    @endsection
    @push('scripts')
    <script src="{{ asset('/backend/assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>
    <script src="{{ asset('frontend/assets/js/custom/home/contact-us.js') }}"></script>
    @endpush
