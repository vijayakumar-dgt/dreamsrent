<div class="col-lg-3">
    <!-- inner sidebar -->
    <div class="settings-sidebar slimscroll">
        <div class="sidebar-menu">
            <ul>
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.account_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.profile-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.profile-settings') }}">
                                <i class="ti ti-user-edit me-2"></i><span>{{ __('admin.general_settings.profile') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        @if (hasPermission($permissions, 'account_settings', 'view'))
                        <li class="{{ request()->routeIs('admin.security-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.security-settings') }}">
                                <i class="ti ti-lock me-2"></i><span>{{ __('admin.general_settings.security') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.notifications-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.notifications-settings') }}">
                                <i class="ti ti-bell me-2"></i><span>{{ __('admin.general_settings.notifications') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>

                @if (hasPermission($permissions, 'website_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.website_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.company-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.company-settings') }}">
                                <i class="ti ti-building me-2"></i><span>{{ __('admin.general_settings.company_settings') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.logo-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.logo-settings') }}">
                                <i class="ti ti-server-cog me-2"></i><span>{{ __('admin.general_settings.logo_favicon_settings') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.localization') ? 'active' : '' }}">
                            <a href="{{ route('admin.localization') }}">
                                <i class="ti ti-settings-2 me-2"></i><span>{{ __('admin.general_settings.localization') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.prefixes-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.prefixes-settings') }}">
                                <i class="ti ti-corner-up-left-double me-2"></i><span>{{ __('admin.general_settings.prefixes') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.seosetup-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.seosetup-settings') }}">
                                <i class="ti ti-seo me-2"></i><span>{{ __('admin.general_settings.seo_setup_settings') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.languages') ? 'active' : '' }}">
                            <a href="{{ route('admin.languages') }}">
                                <i class="ti ti-language me-2"></i><span>{{ __('admin.general_settings.languages') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.maintenance-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.maintenance-settings') }}">
                                <i class="ti ti-color-filter me-2"></i><span>{{ __('admin.general_settings.maintenance_mode') }}</span>
                            </a>
                        </li>
                        {{-- <li class="{{ request()->routeIs('admin.ai-configuration') ? 'active' : '' }}">
                            <a href="{{ route('admin.ai-configuration') }}">
                                <i class="ti ti-grain me-2"></i><span>{{ __('admin.general_settings.ai_configuration') }}</span>
                            </a>
                        </li> --}}
                        <li class="{{ request()->routeIs('admin.addonIndex-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.addonIndex-settings') }}">
                                <i class="ti ti-car-crash me-2"></i><span>{{ __('admin.general_settings.plugin_managers') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.theme-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.theme-settings') }}">
                                <i class="ti ti-template me-2"></i><span>{{ __('admin.general_settings.theme_settings') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.otp-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.otp-settings') }}">
                                <i class="ti ti-grain me-2"></i><span>{{ __('admin.general_settings.otp_settings') }}</span>
                            </a>
                        </li>@if (hasPermission($permissions, 'copyright', 'view'))
                        <li class="{{ request()->routeIs('admin.copyright') ? 'active' : '' }}">
                            <a href="{{ route('admin.copyright') }}">
                                <i class="ti ti-copyright me-2"></i><span>{{ __('admin.cms.copyright') }}</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                @endif

                @if (hasPermission($permissions, 'rental_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.rental_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.rental-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.rental-settings') }}">
                                <i class="ti ti-file-invoice me-2"></i><span>{{ __('admin.general_settings.rental') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('insurance.index') ? 'active' : '' }}">
                            <a href="{{ route('insurance.index') }}">
                                <i class="ti ti-file-delta me-2"></i><span>{{ __('admin.common.insurance') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (hasPermission($permissions, 'app_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.app_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.invoiceSettings-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.invoiceSettings-settings') }}">
                                <i class="ti ti-file-invoice me-2"></i><span>{{ __('admin.general_settings.invoice_settings') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.signature-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.signature-settings') }}">
                                <i class="ti ti-signature me-2"></i><span>{{ __('admin.general_settings.signature') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (hasPermission($permissions, 'system_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.system_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.email-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.email-settings') }}">
                                <i class="ti ti-mail me-2"></i><span>{{ __('admin.general_settings.email_settings') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('email_templates.index') ? 'active' : '' }}">
                            <a href="{{ route('email_templates.index') }}">
                                <i class="ti ti-mail-fast me-2"></i><span>{{ __('admin.general_settings.email_templates') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.smsGateway-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.smsGateway-settings') }}">
                                <i class="ti ti-messages me-2"></i><span>{{ __('admin.general_settings.sms_gateways') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.gdpr-cookies-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.gdpr-cookies-settings') }}">
                                <i class="ti ti-cookie me-2"></i><span>{{ __('admin.general_settings.gdpr_cookies') }}</span><span class="track-icon"></span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (hasPermission($permissions, 'finance_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.finance_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links pb-3 mb-3 border-bottom">
                        <li class="{{ request()->routeIs('admin.paymentIndex-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.paymentIndex-settings') }}">
                                <i class="ti ti-lock me-2"></i><span>{{ __('admin.general_settings.payment_methods') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.bankindex-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.bankindex-settings') }}">
                                <i class="ti ti-lock me-2"></i><span>{{ __('admin.general_settings.bank_accounts') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.tax-rates') ? 'active' : '' }}">
                            <a href="{{ route('admin.tax-rates') }}">
                                <i class="ti ti-file-percent me-2"></i><span>{{ __('admin.general_settings.tax_rates') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.currencies') ? 'active' : '' }}">
                            <a href="{{ route('admin.currencies') }}">
                                <i class="ti ti-world-dollar me-2"></i><span>{{ __('admin.general_settings.currencies') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

                @if (hasPermission($permissions, 'other_settings', 'view'))
                <li class="menu-title"><span>{{ strtoupper(__('admin.general_settings.other_settings')) }}</span></li>
                <li>
                    <ul class="sidebar-links">
                        <li class="{{ request()->routeIs('admin.sitemap') ? 'active' : '' }}">
                            <a href="{{ route('admin.sitemap') }}">
                                <i class="ti ti-map me-2"></i><span>{{ __('admin.general_settings.sitemap') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.clearCache-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.clearCache-settings') }}">
                                <i class="ti ti-database-x me-2"></i><span>{{ __('admin.general_settings.clear_cache') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.storage-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.storage-settings') }}">
                                <i class="ti ti-database me-2"></i><span>{{ __('admin.general_settings.storage') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.system-backup-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.system-backup-settings') }}">
                                <i class="ti ti-file-check me-2"></i><span>{{ __('admin.general_settings.system_backup') }}</span>
                            </a>
                        </li>
                        <li class="{{ request()->routeIs('admin.database-settings') ? 'active' : '' }}">
                            <a href="{{ route('admin.database-settings') }}">
                                <i class="ti ti-file-database me-2"></i><span>{{ __('admin.general_settings.database_backup') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif

            </ul>
        </div>
    </div>
    <!-- /inner sidebar -->
</div>
