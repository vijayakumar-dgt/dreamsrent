    <footer class="footer">
        <!-- Footer Top -->
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-7">
                        <div class="row">
                            @if (!empty($footers))
                                @foreach ($footers as $footer)
                                    <div class="col-lg-4 col-md-6">
                                        <!-- Footer Widget -->
                                        <div class="footer-widget footer-menu">
                                            <h5 class="footer-title">{{ ucfirst($footer->name) }}</h5>
                                            <ul>
                                                @if ($footer->menus)
                                                    @foreach ($footer->parsed_menus as $menu)
                                                        @php
                                                            $rawLink = trim($menu['link']);
                                                            $isFullUrl = filter_var($rawLink, FILTER_VALIDATE_URL);
                                                            $menuLink = $isFullUrl ? rtrim($rawLink, '/') : rtrim(url($rawLink), '/');
                                                            $currentUrl = rtrim(Request::url(), '/');
                                                        @endphp
                                                        <li>
                                                            <a href="{{ $menuLink }}">{{ $menu['label'] }}</a>
                                                        </li>
                                                    @endforeach
                                                @endif
                                            </ul>
                                        </div>
                                        <!-- /Footer Widget -->
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-5">
                        <div class="footer-contact footer-widget">
                            <h5 class="footer-title">{{ __('web.home.contact_info') }}</h5>
                            <div class="footer-contact-info">
                                @if ($companyPhoneNumber)
                                    <div class="footer-address">
                                        <span><i class="feather-phone-call"></i></span>
                                        <div class="addr-info">
                                            <a href="tel:{{ $companyPhoneNumber }}">{{ $companyPhoneNumber }}</a>
                                        </div>
                                    </div>
                                @endif
                                @if ($companyEmail)
                                    <div class="footer-address">
                                        <span><i class="feather-mail"></i></span>
                                        <div class="addr-info">
                                            <a href="mailto:{{ $companyEmail }}">{{ $companyEmail }}</a>
                                        </div>
                                    </div>
                                @endif
                                <div class="update-form">
                                    <form id="newsletterForm" autocomplete="off">
                                        <span><i class="feather-mail"></i></span>
                                        <input type="email" name="subscriber_email" id="subscriber_email" class="form-control" placeholder="{{ __('web.home.enter_your_email') }}">
                                        <button type="submit" class="btn btn-subscribe submitbtn">
                                            <span><i class="feather-send"></i></span>
                                        </button>
                                    </form>
                                </div>
                                <span class="text-danger error-text" id="subscriber_email_error"></span>
                            </div>
                            <div class="footer-social-widget d-none">
                                <ul class="nav-social">
                                    <li><a href="javascript:void(0)"><i class="fa-brands fa-facebook-f fa-facebook fi-icon"></i></a></li>
                                    <li><a href="javascript:void(0)"><i class="fab fa-instagram fi-icon"></i></a></li>
                                    <li><a href="javascript:void(0)"><i class="fab fa-behance fi-icon"></i></a></li>
                                    <li><a href="javascript:void(0)"><i class="fab fa-twitter fi-icon"></i></a></li>
                                    <li><a href="javascript:void(0)"><i class="fab fa-linkedin fi-icon"></i></a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Footer Top -->
        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <!-- Copyright -->
                <div class="copyright">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="copyright-text">
                                <p>{!! $copyright ?? 'Copyright © '.date('Y').' '.config('app.name').'. All Rights Reserved.' !!}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <!-- Copyright Menu -->
                            <div class="copyright-menu">
                                <div class="vistors-details">
                                    <ul class="d-flex">
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{ asset('frontend/assets/img/icons/paypal.svg') }}" alt="Paypal"></a></li>
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{ asset('frontend/assets/img/icons/visa.svg') }}" alt="Visa"></a></li>
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{ asset('frontend/assets/img/icons/master.svg') }}" alt="Master"></a></li>
                                        <li><a href="javascript:void(0)"><img class="img-fluid" src="{{ asset('frontend/assets/img/icons/applegpay.svg') }}" alt="applegpay"></a></li>
                                    </ul>
                                </div>
                            </div>
                            <!-- /Copyright Menu -->
                        </div>
                    </div>
                </div>
                <!-- /Copyright -->
            </div>
        </div>
        <!-- /Footer Bottom -->
    </footer>
