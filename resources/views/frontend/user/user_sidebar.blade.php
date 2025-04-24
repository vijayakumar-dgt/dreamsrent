<!-- Settings Menu -->
<div class="col-lg-3 theiaStickySidebar">
    <div class="settings-widget">
        <div class="settings-menu">
            <ul>
                <li>
                    <a href="{{ route('user.usersettings') }}"
                        class="{{ request()->routeIs('user.usersettings') ? 'active' : '' }}">
                        <i class="feather-user"></i> {{ __('web.user.profile') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.security') }}"
                    class="{{ request()->routeIs('user.security') ? 'active' : '' }}">
                        <i class="feather-shield"></i> {{ __('web.user.security') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.preference') }}"
                        class="{{ request()->routeIs('user.preference') ? 'active' : '' }}">
                        <i class="feather-star"></i> {{ __('web.user.preferences') }}
                    </a>
                </li>
                <li>
                    <a href="{{ route('user.notification') }}"
                    class="{{ request()->routeIs('user.notification') ? 'active' : '' }}">
                        <i class="feather-bell"></i> {{ __('web.user.notifications') }}
                    </a>
                </li>
               
            </ul>
        </div>
    </div>
</div>
<!-- /Settings Menu -->

@push('scripts')
<!-- Sticky Sidebar JS -->
<script src="{{ asset('frontend/assets/plugins/theia-sticky-sidebar/ResizeSensor.js') }}"></script>
<script src="{{ asset('frontend/assets/plugins/theia-sticky-sidebar/theia-sticky-sidebar.js') }}"></script>
@endpush
