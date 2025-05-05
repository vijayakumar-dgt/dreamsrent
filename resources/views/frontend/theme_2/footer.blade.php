<footer class="footer footer-four">
    <!-- Footer Top -->
    <div class="footer-top aos" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-5">
                    <div class="footer-contact footer-widget">
                        <div class="footer-logo">
                            <img src="{{ $logo ?? asset('frontend/assets/img/logo-white.svg') }}" class="img-fluid aos" alt="logo">
                        </div>
                        <div class="footer-contact-info">
                            <p>{{ __('web.home.theme_2_footer_content') }}</p>
                        </div>
                        <div class="d-flex align-items-center gap-1 app-icon">
                            <a href="javascript:void(0);">
                                <img src="/frontend/assets/img/icons/gpay.svg" class="img-fluid" alt="logo">
                            </a>
                            <a href="javascript:void(0);">
                                <img src="/frontend/assets/img/icons/app.svg" class="img-fluid" alt="logo">
                            </a>
                        </div>
                        <ul class="social-icon">
                            <li>
                                <a href="javascript:void(0)"><i class="fa-brands fa-facebook-f"></i></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fab fa-behance"></i></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fab fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="javascript:void(0)"><i class="fab fa-linkedin"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
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
            </div>
        </div>
    </div>
    <!-- /Footer Top -->
    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <!-- Copyright -->
            <div class="copyright">
                <div class="row align-items-center row-gap-3">
                    <div class="col-lg-4">
                        <div class="copyright-text">
                            <p>{!! $copyright ?? 'Copyright © '.date('Y').' '.config('app.name').'. All Rights Reserved.' !!}</p>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="payment-list">
                            <a href="javascript:void(0);">
                                <img src="/frontend/assets/img/icons/payment-01.svg" alt="img">
                            </a>
                            <a href="javascript:void(0);">
                                <img src="/frontend/assets/img/icons/payment-02.svg" alt="img">
                            </a>
                            <a href="javascript:void(0);">
                                <img src="/frontend/assets/img/icons/payment-03.svg" alt="img">
                            </a>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <ul class="privacy-link">
                            <li>
                                <a href="/pages/privacy-policy">{{ __('web.home.privacy_policy') }}</a>
                            </li>
                            <li>
                                <a href="/pages/terms-conditions">{{ __('web.home.terms_and_conditions') }}</a>
                            </li>
                            <li>
                                <a href="/pages/refund">{{ __('web.home.refund_policy') }}</a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /Copyright -->
        </div>
    </div>
    <!-- /Footer Bottom -->
</footer>
