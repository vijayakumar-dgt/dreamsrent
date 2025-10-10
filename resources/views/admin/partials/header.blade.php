<div class="header">
    <div class="main-header">
        <div class="header-left">
            <a href="#" class="logo">
                <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" alt="Logo">
            </a>
            <a href="#" class="dark-logo">
                <img src="{{ $logo ?? asset('frontend/assets/img/logo.svg') }}" alt="Logo">
            </a>
        </div>
        <a id="mobile_btn" class="mobile_btn" href="#sidebar">
            <span class="bar-icon">
                <span></span>
                <span></span>
                <span></span>
            </span>
        </a>
        <div class="header-user">
            <div class="nav user-menu nav-list">
                <div class="me-auto d-flex align-items-center" id="header-search">
                    <a id="toggle_btn" href="javascript:void(0);">
                        <i class="ti ti-menu-deep"></i>
                    </a>
                    @if (isAccessMenu('reservation') && haspermission($permissions, 'reservations', 'create'))
                    <div class="add-dropdown">
                        <a href="{{ route('reservation.create') }}" class="btn btn-dark d-inline-flex align-items-center">
                            <i class="ti ti-plus me-1"></i>{{ __('admin.bookings.new_reservation') }}
                        </a>
                    </div>
                    @endif
                </div>
                <div class="d-flex align-items-center header-icons">
                    <!-- Flag -->
                    @if(!empty($language_switcher) && $language_switcher == 1)
                    <div class="nav-item dropdown has-arrow flag-nav nav-item-box">
                        <a class="nav-link dropdown-toggle" data-bs-toggle="dropdown" href="javascript:void(0);">
                            <img src="{{ asset('/backend/assets/img/flags/'. app()->getLocale() .'.svg') }}" class="img-fluid" alt="{{ strtoupper(app()->getLocale()) }} Flag">
                        </a>
                        <ul class="dropdown-menu p-2">
                            @if ($allLanguages)
                                @foreach ($allLanguages as $language)
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item change-language" data-id="{{ $language->id }}" data-language_code="{{ $language->code }}">
                                            <img src="{{ asset('/backend/assets/img/flags/'. $language->code.'.svg') }}"  height="16" alt="{{ strtoupper($language->code) }} Flag">
                                            {{ $language->name }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        </ul>
                    </div>
                    @endif
                    <!-- /Flag -->
                    <div class="notification_item">
                        <a href="#" class="btn btn-menubar position-relative" id="notification_popup" data-bs-toggle="dropdown" data-bs-auto-close="outside">
                            <i class="ti ti-bell"></i>
                            <span class="badge bg-violet rounded-pill d-none" id="newNotificationBadge"></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <div class="topnav-dropdown-header pb-0">
                                <h5 class="notification-title">{{ __('web.user.notifications') }}</h5>
                            </div>
                            <div class="noti-content">
                            </div>
                            <div class="d-flex align-items-center justify-content-between topnav-dropdown-footer">
                                <div class="d-flex align-items-center">
                                    <a href="javascript:void(0);" class="link-primary text-decoration-underline me-3 d-none has-notification" id="markAllAsRead">{{__('web.user.mark_all_as_read')}}</a>
                                </div>
                                <a href="{{ route('admin.notifications') }}" class="btn btn-primary btn-sm d-inline-flex align-items-center">{{ __('web.user.view_all_notifications') }}<i class="ti ti-chevron-right ms-1"></i></a>
                            </div>
                        </div>
                    </div>
                    <div>
                        <a href="{{ route('admin.messages') }}" class="btn btn-menubar position-relative">
                            <i class="ti ti-message"></i>
                        </a>
                    </div>
                    <div>
                        <a href="{{ route('admin.income-report') }}" class="btn btn-menubar">
                            <i class="ti ti-chart-bar"></i>
                        </a>
                    </div>
                    <div class="dropdown profile-dropdown">
                        <a href="javascript:void(0);" class="d-flex align-items-center" data-bs-toggle="dropdown"  data-bs-auto-close="outside">
                            <span class="avatar avatar-sm">
                                <img src="{{ $userDetails && $userDetails->profile_image ? $userDetails->profile_image : uploadedAsset('','profile') }}" class="img-fluid rounded-circle" alt="Profile">
                            </span>
                        </a>
                        <div class="dropdown-menu">
                            <div class="profileset d-flex align-items-center">
                                <span class="user-img me-2">
                                    <img src="{{ $userDetails && $userDetails->profile_image ? $userDetails->profile_image : uploadedAsset('','profile') }}" alt="Profile">
                                </span>
                                <div>
                                    <h6 class="fw-semibold mb-1"> {{ getCurrentUserFullname() }} </h6>
                                    <p class="fs-13"> {{ $userDetails->email ?? '-' }} </p>
                                </div>
                            </div>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.profile-settings') }}">
                                <i class="ti ti-user-edit"></i>{{ __('admin.common.edit_profile') }}
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('payment.payment') }}">
                                <i class="ti ti-credit-card"></i>{{ __('admin.finance_accounts.payments') }}
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.notifications') }}">
                                <i class="ti ti-bell"></i>{{ __('web.user.notifications') }}
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.security-settings') }}">
                                <i class="ti ti-exchange"></i>{{ __('admin.general_settings.change_password') }}
                            </a>
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.company-settings') }}">
                                <i class="ti ti-settings"></i>{{ __('admin.general_settings.settings') }}
                            </a>
                            <a class="dropdown-item logout d-flex align-items-center" href="{{ route('admin.logout') }}">
                                <i class="ti ti-logout"></i>{{ __('admin.common.logout') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div class="dropdown mobile-user-menu">
            <a href="javascript:void(0);" class="nav-link dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-ellipsis-v"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="{{ route('admin.profile-settings') }}">{{ __('admin.general_settings.profile') }}</a>
                <a class="dropdown-item" href="{{ route('admin.company-settings') }}">{{ __('admin.general_settings.settings') }}</a>
                <a class="dropdown-item" href="{{ route('admin.logout') }}">{{ __('admin.common.logout') }}</a>
            </div>
        </div>
        <!-- /Mobile Menu -->

        <div class="datatable-language-data d-none"
            data-empty_table="{{ __('admin.common.empty_table') }}"
            data-info="{{ __('admin.common.showing') }} _START_ {{ __('admin.common.to') }} _END_ {{ __('admin.common.of') }} _TOTAL_ {{ __('admin.common.entries') }}"
            data-info_empty="{{ __('admin.common.showing') }} 0 {{ __('admin.common.to') }} 0 {{ __('admin.common.of') }} 0 {{ __('admin.common.entries') }}"
            data-info_filtered="({{ __('admin.common.filtered_from') }} _MAX_ {{ __('admin.common.total_entries') }})"
            data-length_menu="{{ __('admin.common.show') }} _MENU_ {{ __('admin.common.entries') }}"
            data-search="{{ __('admin.common.search') }}:"
            data-zero_records="{{ __('admin.common.empty_table') }}"
            data-paginate_first="{{ __('admin.common.first') }}"
            data-paginate_last="{{ __('admin.common.last') }}"
            data-paginate_next="{{ __('admin.common.next') }}"
            data-paginate_previous="{{ __('admin.common.previous') }}">
        </div>
    </div>
</div>
