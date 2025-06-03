<!-- Footer -->
<footer class="footer-two">
    <div class="sec-bg">
        <img src="/frontend/assets/img/bg/sec-bg-wave.png" alt="Img">
        <img src="/frontend/assets/img/bg/anchor-img-02.png" alt="Img">
    </div>
    <div class="container">
        <div class="footer-top">
            <div class="row">
                @if (!empty($footers))
                @foreach ($footers as $footer)
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h4>{{ ucfirst($footer->name) }}</h4>
                            <ul class="footer-links">
                                @if ($footer->menus)
                                    @foreach ($footer->parsed_menus as $menu)
                                        @php
                                            $rawLink = trim($menu['link']);
                                            $isFullUrl = filter_var($rawLink, FILTER_VALIDATE_URL);
                                            $menuLink = $isFullUrl ? rtrim($rawLink, '/') : rtrim(url($rawLink), '/');
                                            $currentUrl = rtrim(Request::url(), '/');
                                        @endphp
                                <li><a href="{{ $menuLink }}"><i class="fas fa-chevron-right"></i>{{ $menu['label'] }}</a></li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                    <div class="footer-widget">
                        <div class="widget-title">
                            <h4>24/7 Live Support</h4>
                            <ul class="footer-address">
                                <li>Want to book a Yacht instantly Contact Us !!!</li>
                                <li>Contact  : {{ $companyPhoneNumber }}</li>
                                <li>Email : {{ $companyEmail }}</li>
                                <li class="social-link">
                                    <ul>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-facebook-f"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-instagram"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-behance"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-twitter"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-pinterest-p"></i></a></li>
                                        <li><a href="javascript:void(0);"><i class="fa-brands fa-linkedin"></i></a></li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <div class="copy-right">
                <p>{!! $copyright ?? 'Copyright © '.date('Y').' '.config('app.name').'. All Rights Reserved.' !!}</p>
            </div>
            <div class="app-store-links d-flex align-items-center">
                <span class="me-2"><a href="javascript:void(0);"><img src="{{ asset('frontend/assets/img/icons/google-play.svg') }}" alt="Img"></a></span>
                <span><a href="javascript:void(0);"><img src="{{ asset('frontend/assets/img/icons/app-store.svg') }}" alt="Img"></a></span>
            </div>
        </div>
    </div>
</footer>
<!-- Footer -->