<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-logo">
        <a href="{{ route('dashboard') }}" class="logo logo-normal">
            <img src="{{ $logo }}" alt="Logo">
        </a>
        <a href="{{ route('dashboard') }}" class="logo-small">
            <img src="{{ $smallLogo }}" alt="Logo">
        </a>
        <a href="{{ route('dashboard') }}" class="dark-logo">
            <img src="{{ $logo }}" alt="Logo">
        </a>
    </div>
    <!-- /Logo -->
    <div class="sidebar-inner slimscroll">
        <div id="sidebar-menu" class="sidebar-menu">
            <ul>
                @if (haspermission($permissions, 'dashboard', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.main.main')) }}</span></li>
                <li>
                    <ul>
                        <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                            <a href="{{ route('dashboard') }}">
                                <i class="ti ti-layout-grid-add"></i><span>{{ __('admin.main.dashboard') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
                @if (haspermission($permissions, ['reservations', 'calendar', 'enquiries', 'quotations'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.bookings.bookings')) }}</span></li>
                <li>
                    <ul>
                        @if (haspermission($permissions, 'reservations', 'view') && isAccessMenu('reservation'))
                        <li class="{{ request()->routeIs(['reservation.index', 'reservation.create', 'reservation.edit', 'reservation.details']) ? 'active' : '' }}">
                            <a href="{{ route('reservation.index') }}">
                                <i class="ti ti-files"></i><span>{{ __('admin.common.reservations') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'calendar', 'view'))
                        <li class="{{ request()->routeIs('calendar.index') ? 'active' : '' }}">
                            <a href="{{ route('calendar.index') }}">
                                <i class="ti ti-calendar-bolt"></i><span>{{ __('admin.bookings.calendar') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'quotations', 'view'))
                        <li class="{{ request()->routeIs(['quotations.index', 'quotations.create', 'quotations.edit', 'quotations.details']) ? 'active' : '' }}">
                            <a href="{{ route('quotations.index') }}">
                                <i class="ti ti-file-symlink"></i><span>{{ __('admin.common.quotations') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'enquiries', 'view'))
                        <li class="{{ request()->routeIs('enquiry.index') ? 'active' : '' }}">
                            <a href="{{ route('enquiry.index') }}">
                                <i class="ti ti-mail"></i><span>{{ __('admin.common.enquiries') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (haspermission($permissions, ['customers', 'drivers', 'locations'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.manage.manage')) }}</span></li>
                <li>
                    <ul>
                        @if (haspermission($permissions, 'customers', 'view'))
                        <li class="{{ request()->routeIs(['admin.customers', 'admin.customer-details', 'admin.customer-recent-rents']) ? 'active' : ''}}">
                            <a href="{{ route('admin.customers') }}">
                                <i class="ti ti-users-group"></i><span>{{ __('admin.common.customers') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'drivers', 'view'))
                        <li class="{{ request()->routeIs('driver.index') ? 'active' : ''}}">
                            <a href="{{ route('driver.index') }}">
                                <i class="ti ti-user-bolt"></i><span>{{ __('admin.manage.drivers') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'locations', 'view'))
                        <li class="{{ request()->routeIs('locations') ? 'active' : ''}}">
                            <a href="{{ route('locations') }}">
                                <i class="ti ti-map-pin-bolt"></i><span>{{ __('admin.manage.locations') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (haspermission($permissions, ['vehicles', 'vehicle_attributes', 'extra_service', 'inspections', 'maintenance'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.rentals.rentals')) }}</span></li>
                <li>
                    <ul>
                        @if (haspermission($permissions, 'vehicles', 'view'))
                        <li class="{{ request()->routeIs(['vehicle.list', 'vehicle.vehicleadd', 'edit.car']) ? 'active' : '' }}">
                            <a href="{{ route('vehicle.list') }}">
                                <i class="ti ti-car"></i><span>{{ __('admin.common.vehicles') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'vehicle_attributes', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);"
                                class="{{ request()->routeIs(['cartypes', 'doorType.index', 'damage-types', 'tags', 'brand.index', 'carModel.index', 'carSeat.index', 'carColor.index', 'carTrasmission.index', 'fuelType.index', 'steeringType.index', 'category.index', 'seasons', 'cylinders', 'safetyFeature.index']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-device-camera-phone"></i><span>{{ __('admin.rentals.vehicle_attributes') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ request()->routeIs('cartypes') ? 'active' : '' }}" href="{{ route('cartypes') }}">{{ __('admin.rentals.vehicle_types') }}</a></li>
                                <li><a class="{{ request()->routeIs('doorType.index') ? 'active' : '' }}" href="{{ route('doorType.index') }}">{{ __('admin.rentals.door_types') }}</a></li>
                                <li><a class="{{ request()->routeIs('damage-types') ? 'active' : '' }}" href="{{ route('damage-types') }}">{{ __('admin.rentals.damage_types') }}</a></li>
                                <li><a class="{{ request()->routeIs('tags') ? 'active' : '' }}" href="{{ route('tags') }}">{{ __('admin.common.tags') }}</a></li>
                                <li><a class="{{ request()->routeIs('brand.index') ? 'active' : '' }}" href="{{ route('brand.index') }}">{{ __('admin.rentals.brands') }}</a></li>
                                <li><a class="{{ request()->routeIs('carModel.index') ? 'active' : '' }}" href="{{ route('carModel.index') }}">{{ __('admin.rentals.vehicle_models') }}</a></li>
                                <li><a class="{{ request()->routeIs('carSeat.index') ? 'active' : '' }}" href="{{ route('carSeat.index') }}">{{ __('admin.rentals.seat_types') }}</a></li>
                                <li><a class="{{ request()->routeIs('carColor.index') ? 'active' : '' }}" href="{{ route('carColor.index') }}">{{ __('admin.rentals.vehicle_colors') }}</a></li>
                                <li><a class="{{ request()->routeIs('carTrasmission.index') ? 'active' : '' }}" href="{{ route('carTrasmission.index') }}">{{ __('admin.rentals.vehicle_transmission') }}</a></li>
                                <li><a class="{{ request()->routeIs('fuelType.index') ? 'active' : '' }}" href="{{ route('fuelType.index') }}">{{ __('admin.rentals.fuel_type') }}</a></li>
                                <li><a class="{{ request()->routeIs('steeringType.index') ? 'active' : '' }}" href="{{ route('steeringType.index') }}">{{ __('admin.rentals.steering_type') }}</a></li>
                                <li><a class="{{ request()->routeIs('category.index') ? 'active' : '' }}" href="{{ route('category.index') }}">{{ __('admin.common.category') }}</a></li>
                                <li><a class="{{ request()->routeIs('seasons') ? 'active' : '' }}" href="{{ route('seasons') }}">{{ __('admin.common.seasons') }}</a></li>
                                <li><a class="{{ request()->routeIs('cylinders') ? 'active' : '' }}" href="{{ route('cylinders') }}">{{ __('admin.rentals.cylinders') }}</a></li>
                                <li><a class="{{ request()->routeIs('safetyFeature.index') ? 'active' : '' }}" href="{{ route('safetyFeature.index') }}">{{ __('admin.rentals.safety_features') }}</a></li>
                            </ul>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'extra_service', 'view'))
                        <li class="{{ request()->routeIs('extra_services') ? 'active' : '' }}">
                            <a href="{{ route('extra_services') }}">
                                <i class="ti ti-script-plus"></i><span>{{ __('admin.common.extra_services') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'inspections', 'view'))
                        <li class="{{ request()->routeIs('inspection.index') ? 'active' : '' }}">
                            <a href="{{ route('inspection.index') }}">
                                <i class="ti ti-dice-6"></i><span>{{ __('admin.common.inspections') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'maintenance', 'view'))
                        <li class="{{ request()->routeIs('maintenance.index') ? 'active' : '' }}">
                            <a href="{{ route('maintenance.index') }}">
                                <i class="ti ti-color-filter"></i><span>{{ __('admin.rentals.maintenance') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (haspermission($permissions, 'reviews', 'view'))
                        <li class="{{ request()->routeIs('admin.reviews') ? 'active' : '' }}">
                            <a href="{{ route('admin.reviews') }}">
                                <i class="ti ti-star"></i><span>{{ __('admin.rentals.reviews') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['invoices', 'payments'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.finance_accounts.finance_accounts')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'invoices', 'view'))
                        <li class="{{ request()->routeIs('admin.invoice') ? 'active' : '' }}">
                            <a href="{{ route('admin.invoice') }}">
                                <i class="ti ti-file-invoice"></i><span>{{ __('admin.finance_accounts.invoices') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'payments', 'view'))
                        <li class="{{ request()->routeIs('payment.payment') ? 'active' : '' }}">
                            <a href="{{ route('payment.payment') }}">
                                <i class="ti ti-speakerphone"></i><span>{{ __('admin.finance_accounts.payments') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['messages', 'newsletters'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.others.others')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'messages', 'view'))
                        <li class="{{ request()->routeIs('admin.messages') ? 'active' : '' }}">
                            <a href="{{ route('admin.messages') }}">
                                <i class="ti ti-message"></i><span>{{ __('admin.others.messages') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'newsletters', 'view'))
                        <li class="{{ request()->routeIs('admin.newsletters') ? 'active' : '' }}">
                            <a href="{{ route('admin.newsletters') }}">
                                <i class="ti ti-file-horizontal"></i><span>{{ __('admin.others.newsletters') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['page', 'section', 'menu_management', 'testimonials', 'faq', 'blogs', 'cms_locations', 'how_it_works', 'copyright'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.cms.cms')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'page', 'view'))
                        <li class="{{ request()->routeIs(['admin.pageIndex', 'admin.addPage', 'admin.editPage']) ? 'active' : '' }}">
                            <a href="{{ route('admin.pageIndex') }}">
                                <i class="ti ti-file-invoice"></i><span>{{ __('admin.cms.pages') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'section', 'view'))
                        <li class="{{ request()->routeIs('admin.indexSection') ? 'active' : '' }}">
                            <a href="{{ route('admin.indexSection') }}">
                                <i class="ti ti-file-symlink"></i><span>{{ __('admin.cms.section') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'menu_management', 'view'))
                        <li class="{{ request()->routeIs('admin.menu','admin.menuManagement') ? 'active' : '' }}">
                            <a href="{{ route('admin.menu') }}">
                                <i class="ti ti-menu-2"></i><span>{{ __('admin.cms.menu_management') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'blogs', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->is('admin/content/blog-category') || request()->is('admin/content/blogs') || request()->is('admin/content/blog-comments') || request()->is('admin/content/blog-tags') || request()->is('admin/content/add-blog') || request()->is('admin/content/blogs/*') ? 'subdrop active' : '' }}">
                                <i class="ti ti-brand-blogger"></i><span>{{ __('admin.cms.blogs') }}</span><span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a href="{{ route('admin.blogs') }}" class="{{ request()->is('admin/content/blogs') || request()->is('admin/content/add-blog') || request()->is('admin/content/blogs/*') ? 'active' : '' }}">{{ __('admin.cms.all_blogs') }}</a></li>
                                <li><a href="{{ route('admin.blog-category') }}" class="{{ request()->is('admin/content/blog-category') ? 'active' : '' }}">{{ __('admin.common.categories') }}</a></li>
                                <li><a href="{{ route('admin.blog-comments') }}" class="{{ request()->is('admin/content/blog-comments') ? 'active' : '' }}">{{ __('admin.common.comments') }}</a></li>
                                <li><a href="{{ route('admin.blog-tags') }}" class="{{ request()->is('admin/content/blog-tags') ? 'active' : '' }}">{{ __('admin.cms.blog_tags') }}</a></li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'cms_locations', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['country.index', 'state.index', 'city.index']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-device-camera-phone"></i><span>{{ __('admin.cms.locations') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li><a class="{{ request()->routeIs('country.index') ? 'active' : '' }}" href="{{ route('country.index') }}">{{ __('admin.common.countries') }}</a></li>
                                <li><a class="{{ request()->routeIs('state.index') ? 'active' : '' }}" href="{{ route('state.index') }}">{{ __('admin.common.states') }}</a></li>
                                <li><a class="{{ request()->routeIs('city.index') ? 'active' : '' }}" href="{{ route('city.index') }}">{{ __('admin.common.cities') }}</a></li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'testimonials', 'view'))
                        <li class="{{ request()->routeIs('admin.testimoials') ? 'active' : '' }}">
                            <a href="{{ route('admin.testimoials') }}">
                                <i class="ti ti-brand-hipchat"></i><span>{{ __('admin.cms.testimonials') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'faq', 'view'))
                        <li class="{{ request()->routeIs('admin.faq') ? 'active' : '' }}">
                            <a href="{{ route('admin.faq') }}">
                                <i class="ti ti-question-mark"></i><span>{{ __('admin.cms.faq') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'how_it_works', 'view'))
                        <li class="{{ request()->routeIs('admin.howItWorks') ? 'active' : '' }}">
                            <a href="{{ route('admin.howItWorks') }}">
                                <i class="ti ti-messages"></i><span>{{ __('admin.cms.how_it_works') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['income_vs_expense', 'earnings'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.common.reports')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'income_vs_expense', 'view'))
                        <li class="{{ request()->routeIs('admin.income-report') ? 'active' : '' }}">
                            <a href="{{ route('admin.income-report') }}">
                                <i class="ti ti-chart-histogram"></i><span>{{ __('admin.reports.income') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'earnings', 'view'))
                        <li class="{{ request()->routeIs('admin.earning-report') ? 'active' : '' }}">
                            <a href="{{ route('admin.earning-report') }}">
                                <i class="ti ti-chart-line"></i><span>{{ __('admin.reports.earning_report') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['contact_messages', 'announcements', 'tickets'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.support.support')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'contact_messages', 'view'))
                        <li class="{{ request()->routeIs('communication.contact-message') ? 'active' : '' }}">
                            <a href="{{ route('communication.contact-message') }}" >
                                <i class="ti ti-messages"></i><span>{{ __('admin.support.contact_messages') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'announcements', 'view'))
                        <li class="{{ request()->routeIs('communication.announcement') ? 'active' : '' }}">
                            <a href="{{ route('communication.announcement') }}">
                                <i class="ti ti-speakerphone"></i><span>{{ __('admin.support.announcements') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'tickets', 'view'))
                       <li class="{{ request()->routeIs('communication.ticket', 'communication.ticket-details') ? 'active' : '' }}">
                            <a href="{{ route('communication.ticket') }}">
                                <i class="ti ti-ticket"></i><span>{{ __('admin.support.tickets') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['users', 'roles_permissions'], 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.user_management.user_management')) }}</span></li>
                <li>
                    <ul>
                        @if (hasPermission($permissions, 'users', 'view'))
                        <li class="{{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <a href="{{ route('admin.users') }}" >
                                <i class="ti ti-user-circle"></i><span>{{ __('admin.common.users') }}</span>
                            </a>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'roles_permissions', 'view'))
                        <li class="{{ request()->routeIs(['admin.roles-permisions', 'admin.permissions']) ? 'active' : '' }}">
                            <a href="{{ route('admin.roles-permisions') }}">
                                <i class="ti ti-user-shield"></i><span>{{ __('admin.user_management.roles_permissions') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
                @if (hasPermission($permissions, ['account_settings', 'website_settings', 'rental_settings', 'app_settings', 'system_settings', 'finance_settings', 'other_settings'], 'view') || request()->routeIs(['admin.profile-settings']))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.settings_configuration')) }}</span></li>
                <li>
                    <ul>
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['admin.profile-settings','admin.security-settings', 'admin.notifications-settings']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-user-cog"></i><span>{{ __('admin.general_settings.account_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.profile-settings') }}" class="{{ request()->routeIs('admin.profile-settings') ? 'active' : '' }}">{{ __('admin.general_settings.profile') }}</a>
                                </li>
                                @if (hasPermission($permissions, 'account_settings', 'view'))
                                <li>
                                    <a href="{{ route('admin.security-settings') }}" class="{{ request()->routeIs('admin.security-settings') ? 'active' : '' }}">{{ __('admin.general_settings.security') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.notifications-settings') }}" class="{{ request()->routeIs('admin.notifications-settings') ? 'active' : '' }}" >{{ __('admin.general_settings.notifications') }}</a>
                                </li>
                                @endif
                            </ul>
                        </li>
                        @if (hasPermission($permissions, 'website_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['admin.company-settings','admin.localization','admin.prefixes-settings','admin.seosetup-settings','admin.languages','admin.copyright','admin.maintenance-settings','admin.ai-configuration', 'admin.theme-settings', 'admin.otp-settings']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-world-cog"></i><span>{{ __('admin.general_settings.website_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.company-settings') }}" class="{{ request()->routeIs('admin.company-settings') ? 'active' : '' }}">{{ __('admin.general_settings.company_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.logo-settings') }}" class="{{ request()->routeIs('admin.logo-settings') ? 'active' : '' }}">{{ __('admin.general_settings.logo_favicon_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.localization') }}" class="{{ request()->routeIs('admin.localization') ? 'active' : '' }}">{{ __('admin.general_settings.localization') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.prefixes-settings') }}" class="{{ request()->routeIs('admin.prefixes-settings') ? 'active' : '' }}">{{ __('admin.general_settings.prefixes') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.seosetup-settings') }}" class="{{ request()->routeIs('admin.seosetup-settings') ? 'active' : '' }}">{{ __('admin.general_settings.seo_setup_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.languages') }}" class="{{ request()->routeIs('admin.languages') ? 'active' : '' }}">{{ __('admin.general_settings.languages') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.maintenance-settings') }}" class="{{ request()->routeIs('admin.maintenance-settings') ? 'active' : '' }}">{{ __('admin.general_settings.maintenance_mode') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.theme-settings') }}" class="{{ request()->routeIs('admin.theme-settings') ? 'active' : '' }}">{{ __('admin.general_settings.theme_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.otp-settings') }}" class="{{ request()->routeIs('admin.otp-settings') ? 'active' : '' }}">{{ __('admin.general_settings.otp_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.copyright') }}" class="{{ request()->routeIs('admin.copyright') ? 'active' : '' }}">{{ __('admin.cms.copyright') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'rental_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['admin.rental-settings', 'insurance.index']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-clock-cog"></i><span>{{ __('admin.general_settings.rental_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.rental-settings') }}" class="{{ request()->routeIs('admin.rental-settings') ? 'active' : '' }}">{{ __('admin.general_settings.rental') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('insurance.index') }}" class="{{ request()->routeIs('insurance.index') ? 'active' : '' }}">{{ __('admin.common.insurance') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'app_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['admin.signature-setting', 'admin.invoiceSettings-settings']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-device-mobile-cog"></i><span>{{ __('admin.general_settings.app_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.invoiceSettings-settings') }}" class="{{ request()->routeIs('admin.invoiceSettings-settings') ? 'active' : '' }}">{{ __('admin.general_settings.invoice_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.signature-settings') }}" class="{{ request()->routeIs('admin.signature-setting') ? 'active' : '' }}">{{ __('admin.general_settings.signature') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'system_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['email_templates.index', 'admin.smsGateway-settings', 'admin.gdpr-cookies-settings', 'admin.email-settings']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-device-desktop-cog"></i><span>{{ __('admin.general_settings.system_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.email-settings') }}" class="{{ request()->routeIs('admin.email-settings') ? 'active' : '' }}">{{ __('admin.general_settings.email_settings') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('email_templates.index') }}" class="{{ request()->routeIs('email_templates.index') ? 'active' : '' }}">{{ __('admin.general_settings.email_templates') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.smsGateway-settings') }}" class="{{ request()->routeIs('admin.smsGateway-settings') ? 'active' : '' }}">{{ __('admin.general_settings.sms_gateways') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.gdpr-cookies-settings') }}" class="{{ request()->routeIs('admin.gdpr-cookies-settings') ? 'active' : '' }}">{{ __('admin.general_settings.gdpr_cookies') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'finance_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIs(['admin.currencies', 'admin.tax-rates']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-settings-dollar"></i><span>{{ __('admin.general_settings.finance_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.paymentIndex-settings') }}" class="{{ request()->routeIs('admin.paymentIndex-settings') ? 'active' : '' }}">{{ __('admin.general_settings.payment_methods') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.bankindex-settings') }}" class="{{ request()->routeIs('admin.bankindex-settings') ? 'active' : '' }}">{{ __('admin.general_settings.bank_accounts') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.tax-rates') }}" class="{{ request()->routeIs('admin.tax-rates') ? 'active' : '' }}">{{ __('admin.general_settings.tax_rates') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.currencies') }}" class="{{ request()->routeIs('admin.currencies') ? 'active' : '' }}">{{ __('admin.general_settings.currencies') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                        @if (hasPermission($permissions, 'other_settings', 'view'))
                        <li class="submenu">
                            <a href="javascript:void(0);" class="{{ request()->routeIS(['admin.sitemap','admin.storage-settings', 'admin.database-settings', 'admin.system-backup-settings']) ? 'subdrop active' : '' }}">
                                <i class="ti ti-settings-2"></i><span>{{ __('admin.general_settings.other_settings') }}</span>
                                <span class="menu-arrow"></span>
                            </a>
                            <ul>
                                <li>
                                    <a href="{{ route('admin.sitemap') }}" class="{{ request()->routeIs('admin.sitemap') ? 'active' : '' }}">{{ __('admin.general_settings.sitemap') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.clearCache-settings') }}" class="{{ request()->routeIs('admin.clearCache-settings') ? 'active' : '' }}">{{ __('admin.general_settings.clear_cache') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.storage-settings') }}" class="{{ request()->routeIs('admin.storage-settings') ? 'active' : '' }}">{{ __('admin.general_settings.storage') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.system-backup-settings') }}" class="{{ request()->routeIs('admin.system-backup-settings') ? 'active' : '' }}">{{ __('admin.general_settings.system_backup') }}</a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.database-settings') }}" class="{{ request()->routeIs('admin.database-settings') ? 'active' : '' }}">{{ __('admin.general_settings.database_backup') }}</a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif
            </ul>
        </div>
    </div>
</div>
<!-- /Sidebar -->
