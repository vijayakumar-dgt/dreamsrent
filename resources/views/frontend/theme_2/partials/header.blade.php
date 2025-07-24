<header class="header theme-2-header">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <button type="button" class="btn border-0" id="mobile_btn">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </button>
                <a href="{{ route('home') }}" class="navbar-brand logo">
                    <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                </a>
                <a href="{{ route('home') }}" class="navbar-brand logo-small">
                    <img src="{{ $smallLogo ?? asset('frontend/assets/img/logo-small.png') }}" class="img-fluid" alt="Logo">
                </a>
                @if(!empty($language_switcher) && $language_switcher == 1)
                <div class="navbar-brand dropdown has-arrow flag-nav flag-nav1 nav-item-box flag-resposnive">
                    <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                        role="button">
                        <img src="{{ asset('/backend/assets/img/flags/' . app()->getLocale() . '.svg') }}"
                            alt="Language" class="img-fluid">
                    </a>
                    <ul class="dropdown-menu flag-menu p-2">
                        @if ($allLanguages)
                        @foreach ($allLanguages as $language)
                        <li>
                            <button type="button" class="dropdown-item change-user-language"
                                data-id="{{ $language->id }}" data-language_code="{{ $language->code }}">
                                <img src="{{ asset('/backend/assets/img/flags/' . $language->code . '.svg') }}"
                                    alt="" height="16">
                                {{ $language->name }}
                            </button>
                        </li>
                        @endforeach
                        @endif
                    </ul>
                </div>
                @endif
            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="{{ route('home') }}" class="menu-logo">
                        <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </a>
                    <button type="button" id="menu_close" class="menu-close btn border-0"><i class="fas fa-times"></i></button>
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
                                            $currentUrl === $menuLink ||
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
                @if(!empty($language_switcher) && $language_switcher == 1)
                <li class="nav-item">
                    <div class="nav-item dropdown has-arrow flag-nav flag-nav1 nav-item-box">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                            <img src="{{ asset('/backend/assets/img/flags/' . app()->getLocale() . '.svg') }}" alt="Language" class="img-fluid">
                        </a>
                        <ul class="dropdown-menu flag-menu p-2">
                            @if ($allLanguages)
                                @foreach ($allLanguages as $language)
                                    <li>
                                        <button class="dropdown-item change-user-language"
                                            data-id="{{ $language->id }}" data-language_code="{{ $language->code }}">
                                            <img src="{{ asset('/backend/assets/img/flags/' . $language->code . '.svg') }}"
                                                alt="" height="16">
                                            {{ $language->name }}
                                        </button>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </li>
                @endif
                @if (Auth::guard('web')->check())
                    <!-- Show this if user is logged in -->
                    <!-- Notifications -->
                    <li class="nav-item dropdown logged-item noti-nav noti-wrapper">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <span class="bell-icon">
                                <img src="{{ asset('frontend/assets/img/icons/bell-icon.svg') }}" alt="Bell">
                            </span>
                            <span class="badge badge-pill d-none" id="newNotificationBadge"></span>
                        </a>
                        <div class="dropdown-menu notifications">
                            <div class="topnav-dropdown-header">
                                <span class="notification-title">{{ __('web.user.notifications') }}</span>
                                <button type="button" class="clear-noti has-notification d-none btn border-0" id="markAllAsRead">
                                    {{ __('web.user.clear_all') }}
                                </button>
                            </div>
                            <div class="noti-content">
                                <ul class="notification-list">
                                    <!-- Add more notifications as needed -->
                                </ul>
                            </div>
                            <div class="topnav-dropdown-footer has-notification">
                                <a href="/user/notifications">{{ __('web.user.view_all_notifications') }}</a>
                            </div>
                        </div>
                    </li>
                    <!-- /Notifications -->

                    <!-- User Menu -->
                    <li class="nav-item dropdown has-arrow logged-item">
                        <a href="#" class="dropdown-toggle nav-link" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle header_profile_image" src="{{ getProfileImage() }}" alt="Profile">
                            </span>
                            <span class="user-text">{{ getCurrentUserFullname() }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                <i class="feather-user-check"></i> {{ __('web.user.dashboard') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.bookings') }}">
                                <i class="feather-calendar"></i> {{ __('web.user.my_bookings') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.reviews') }}">
                                <i class="feather-star"></i> {{ __('web.common.reviews') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.wishlists') }}">
                                <i class="feather-heart"></i> {{ __('web.user.wishlist') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.messages') }}">
                                <i class="feather-message-square"></i> {{ __('web.user.messages') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.wallet') }}">
                                <i class="feather-dollar-sign"></i> {{ __('web.user.my_wallet') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.ticket') }}">
                                <i class="feather-life-buoy"></i> {{ __('web.user.tickets') }}
                            </a>
                             <a class="dropdown-item" href="{{ route('user.payments') }}">
                                <i class="feather-credit-card"></i> {{ __('web.user.payment') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.usersettings') }}">
                                <i class="feather-settings"></i> {{ __('web.common.settings') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.logout') }}">
                                <i class="feather-power"></i> {{ __('web.common.logout') }}
                            </a>
                        </div>
                    </li>
                    <!-- /User Menu -->
                @else
                    <!-- Show this if user is NOT logged in -->
                    <li class="nav-item">
                        <a class="nav-link header-login" href="{{ route('user-login') }}">
                            <span><i class="fa-regular fa-user"></i></span> {{ __('web.home.signin') }}
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link header-reg" href="{{ route('user-register') }}">
                            <span><i class="fa-solid fa-lock"></i></span> {{ __('web.home.signup') }}
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
    </div>
</header>
