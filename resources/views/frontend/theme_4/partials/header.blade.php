<!-- Header -->
<header class="header header-two">
    <div class="header-two-top">
        <div class="container">
            <div class="header-top-items">
                <ul class="header-address">
                    <li><span><i class="bx bxs-phone"></i></span>{{ $companyPhoneNumber }}</li>
                    <li><span><i class="bx bx-map"></i></span>{{ $company_address_line }}</li>
                </ul>
                <div class="header-top-right d-flex align-items-center">
                    <div class="header-top-flag-drops d-flex align-items-center">
                        @if(!empty($language_switcher) && $language_switcher == 1)
                        <div class="header-top-drpodowns me-3">
                            <div class="dropdown header-dropdown country-flag">
                                <a class="dropdown-toggle nav-tog" data-bs-toggle="dropdown" href="javascript:void(0);">
                                    <img src="{{ asset('/backend/assets/img/flags/' . app()->getLocale() . '.svg') }}" alt="Img">
                                    {{ getLanguageName(app()->getLocale()) }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end">
                                    @if ($allLanguages)
                                    @foreach ($allLanguages as $language)
                                    <a href="javascript:void(0);" type="button" class="dropdown-item change-user-language"
                                        data-id="{{ $language->id }}" data-language_code="{{ $language->code }}">
                                        <img src="{{ asset('/backend/assets/img/flags/' . $language->code . '.svg') }}" alt="Img">
                                         {{ $language->name }}
                                    </a>
                                    @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="header-top-social-links">
                        <ul>
                            <li>
                                <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa-brands fa-behance"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa-brands fa-twitter"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa-brands fa-pinterest-p"></i></a>
                            </li>
                            <li>
                                <a href="#"><i class="fa-brands fa-linkedin"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="{{ route('home') }}" class="navbar-brand logo">
                    <img src="{{ $logo }}" class="img-fluid" alt="Logo">
                </a>
                <a href="{{ route('home') }}" class="navbar-brand logo-small">
                    <img src="{{ $smallLogo }}" class="img-fluid" alt="Logo">
                </a>
            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ route('home') }}" class="menu-logo">
                        <img src="{{ $logo }}" class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);"> <i class="fas fa-times"></i></a>
                </div>
                <ul class="main-nav">
                    @if ($headers)
                        @foreach ($headers as $header)
                            @if ($header->menus_array)
                                @foreach ($header->menus_array as $menu)
                                    @php
                                        $rawLink = trim($menu['link']);
                                        $isFullUrl = filter_var($rawLink, FILTER_VALIDATE_URL);
                                        $menuLink = $isFullUrl ? rtrim($rawLink, '/') : rtrim(url($rawLink), '/');
                                        $currentUrl = rtrim(Request::url(), '/');
                                        $isHome = $rawLink === '/';

                                        $active = '';
                                        if (
                                            $currentUrl == $menuLink ||
                                            (Str::contains($menuLink, 'vehicles') && Str::contains($currentUrl, 'vehicle-details')) ||
                                            (Str::contains($menuLink, 'blogs') && Str::contains($currentUrl, 'blog-details'))
                                        ) {
                                            $active = 'active';
                                        }

                                        if ($isHome && request()->routeIs(['home', 'theme'])) {
                                            $active = 'active';
                                        }
                                    @endphp

                                    <li class="{{ $isHome ? 'has-submenu' : '' }} {{ $active }}">
                                        @if ($isHome)
                                            <a href="javascript:void(0);">{{ __('web.home.home') }} <i class="fas fa-chevron-down"></i></a>
                                            <ul class="submenu">
                                                <li><a href="{{ url('/theme/home-01') }}">{{ __('web.home.car_theme') }} 1</a></li>
                                                <li><a href="{{ url('/theme/home-02') }}">{{ __('web.home.car_theme') }} 2</a></li>
                                                <li><a href="{{ url('/theme/home-03') }}">{{ __('web.home.bike') }}</a></li>
                                                <li><a href="{{ url('/theme/home-04') }}">{{ __('web.home.yacht') }}</a></li>
                                            </ul>
                                        @else
                                            <a href="{{ $menuLink }}">{{ $menu['label'] }}</a>
                                        @endif
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if (Auth::guard('web')->check())
                        <li class="login-link">
                            <a href="{{ route('user.dashboard') }}">{{ __('web.user.dashboard') }}</a>
                        </li>
                        <li class="login-link">
                            <a href="{{ route('user.logout') }}">{{ __('web.common.logout') }}</a>
                        </li>
                    @else
                        <li class="login-link">
                            <a href="{{ route('user-register') }}">{{ __('web.home.signup') }}</a>
                        </li>
                        <li class="login-link">
                            <a href="{{ route('user-login') }}">{{ __('web.home.signin') }}</a>
                        </li>
                    @endif
                </ul>
            </div>
            <ul class="nav header-navbar-rht">
                <li class="nav-item">
                    @if (Auth::guard('web')->check())
                    <a class="nav-link login-link ms-1" href="{{ route('user.dashboard') }}">{{ __('web.user.dashboard') }} / </a>
                    <a class="nav-link login-link ms-1" href="{{ route('user.logout') }}">{{ __('web.common.logout') }} </a>
                    @else
                    <a class="nav-link login-link" href="{{ route('user-login') }}"><span><i class="bx bx-user me-2"></i></span>{{ __('web.home.signin') }} / </a>
                    <a class="nav-link login-link ms-1" href="{{ route('user-register') }}">{{ __('web.home.signup') }} </a>
                    @endif
                </li>
            </ul>
        </nav>
    </div>
</header>
<!-- /Header -->
