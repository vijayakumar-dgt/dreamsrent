<header class="header theme-2-header">
    <div class="container-fluid">
        <nav class="navbar navbar-expand-lg header-nav">
            <div class="navbar-header">
                <a id="mobile_btn" href="javascript:void(0);">
                    <span class="bar-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                </a>
                <a href="/" class="navbar-brand logo">
                    <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                </a>
                <a href="/" class="navbar-brand logo-small">
                    <img src="{{ $smallLogo ?? asset('frontend/assets/img/logo-small.png') }}" class="img-fluid" alt="Logo">
                </a>
            </div>
            <div class="main-menu-wrapper">
                <div class="menu-header">
                    <a href="/" class="menu-logo">
                        <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" class="img-fluid" alt="Logo">
                    </a>
                    <a id="menu_close" class="menu-close" href="javascript:void(0);">
                        <i class="fas fa-times"></i>
                    </a>
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
                                        $active = '';

                                        if (
                                            $currentUrl === $menuLink ||
                                            (Str::contains($menuLink, 'vehicles') && Str::contains($currentUrl, 'vehicle-details')) ||
                                            (Str::contains($menuLink, 'blogs') && Str::contains($currentUrl, 'blog-details'))
                                        ) {
                                            $active = 'active';
                                        }
                                    @endphp
                                    <li class="{{ $active }}">
                                        <a href="{{ $menuLink }}">{{ $menu['label'] }}</a>
                                    </li>
                                @endforeach
                            @endif
                        @endforeach
                    @endif

                    @if (Auth::guard('web')->check())
                        <li class="login-link">
                            <a href="{{ route('user.logout') }}">Logout</a>
                        </li>
                    @else
                        <li class="login-link">
                            <a href="#">Sign Up</a>
                        </li>
                        <li class="login-link">
                            <a href="#">Sign In</a>
                        </li>
                    @endif
                </ul>
            </div>
            <ul class="nav header-navbar-rht">
                <li class="nav-item">
                    <div class="nav-item dropdown has-arrow flag-nav flag-nav1 nav-item-box">
<<<<<<< HEAD
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);" role="button">
                            <img src="{{ asset('/assets/img/flags/' . app()->getLocale() . '.svg') }}" alt="Language" class="img-fluid">
=======
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);"
                            role="button">
                            <img src="{{ asset('/backend/assets/img/flags/'. app()->getLocale() .'.svg') }}" alt="Language" class="img-fluid">
>>>>>>> development
                        </a>
                        <ul class="dropdown-menu flag-menu p-2">
                            @if ($allLanguages)
                                @foreach ($allLanguages as $language)
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item change-user-language" data-id="{{ $language->id }}" data-language_code="{{ $language->code }}">
<<<<<<< HEAD
                                            <img src="{{ asset('/assets/img/flags/' . $language->code . '.svg') }}" alt="" height="16">
=======
                                            <img src="{{ asset('/backend/assets/img/flags/'. $language->code.'.svg') }}" alt="" height="16">
>>>>>>> development
                                            {{ $language->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                </li>
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
                                <a href="javascript:void(0)" class="clear-noti has-notification d-none" id="markAllAsRead">
                                    {{ __('web.user.clear_all') }}
                                </a>
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
                        <a href="#" class="dropdown-toggle user-drop-down nav-link" data-bs-toggle="dropdown">
                            <span class="user-img">
                                <img class="rounded-circle header_profile_image" src="{{ getProfileImage() }}" alt="Profile">
                            </span>
                            <span class="user-text">{{ Auth::guard('web')->user()->username }}</span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="{{ route('user.dashboard') }}">
                                <i class="feather-user-check"></i> {{ __('web.user.dashboard') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.usersettings') }}">
                                <i class="feather-settings"></i> {{ __('web.common.settings') }}
                            </a>
                            <a class="dropdown-item" href="{{ route('user.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="feather-power"></i> {{ __('web.common.logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('user.logout') }}" method="GET" class="d-none">
                                @csrf
                            </form>
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
