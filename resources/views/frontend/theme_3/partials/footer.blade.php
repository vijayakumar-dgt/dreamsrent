<!-- Footer -->
<footer class="footer footer-three">
    <!-- Footer Top -->
    <div class="footer-top aos" data-aos="fade-up">
        <div class="container">
            <div class="row">
                <div class="col-lg-3">
                    <div class="footer-contact footer-widget">
                        <div class="footer-logo">
                            <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" class="img-fluid aos" alt="logo">
                        </div>
                        <div class="footer-contact-info">
                            <h6>Want to book a bike instantly Contact Us !!!</h6>
                            <div class="footer-address">
                                <div class="addr-info">
                                    <a href="tel:{{ $companyPhoneNumber ?? "" }}"><i class="bx bxs-phone"></i>{{ $companyPhoneNumber ?? "" }}</a>
                                </div>
                            </div>
                            <div class="footer-address">
                                <div class="addr-info">
                                    <a href="mailto:{{ $companyEmail ?? "" }}"><i class="bx bxs-envelope"></i>{{ $companyEmail ?? "" }}</a>
                                </div>
                            </div>
                        </div>
                        <ul class="store-icon">
                            <li>
                                <a href="javascript:void(0);">
                                    <img src="{{ asset('frontend/assets/img/icons/play-icon.svg') }}" class="img-fluid" alt="logo">
                                </a>
                            </li>
                            <li>
                                <a href="javascript:void(0);">
                                    <img src="{{ asset('frontend/assets/img/icons/app-icon.svg') }}" class="img-fluid" alt="logo">
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                @if (!empty($footers))
                    @foreach ($footers as $footer)
                        <div class="col-lg-3 col-md-4 col-12">
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
    <!-- /Footer Top -->

    <!-- Footer Bottom -->
    <div class="footer-bottom">
        <div class="container">
            <!-- Copyright -->
            <div class="copyright">
                <div class="row align-items-center">
                    <div class="col-lg-6">
                        <div class="copyright-text">
                            <p>{!! $copyright ?? 'Copyright © '.date('Y').' '.config('app.name').'. All Rights Reserved.' !!}</p>
                        </div>
                    </div>
                    <div class="col-lg-6">

                        <div class="footer-list">
                            <ul>
                                <li>
                                    <ul class="social-icon">
                                        <li>
                                            <a href="javascript:void(0)"><i class="fa-brands fa-facebook-f fa-facebook"></i></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"><i class="fab fa-instagram"></i></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"><i class="fab fa-behance"></i></a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"><i class="fab fa-twitter"></i> </a>
                                        </li>
                                        <li>
                                            <a href="javascript:void(0)"><i class="fab fa-linkedin"></i></a>
                                        </li>
                                    </ul>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Copyright -->
        </div>
    </div>
    <!-- /Footer Bottom -->
</footer>
<!-- /Footer -->
