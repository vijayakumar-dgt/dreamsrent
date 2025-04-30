<div class="dashboard-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="dashboard-menu">
                    <ul>
                        <li>
                            <a href="{{ route('user.dashboard') }}" class="{{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/dashboard-icon.svg" alt="Icon">
                                <span>{{ __('web.user.dashboard') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.bookings') }}" class="{{ request()->routeIs('user.bookings') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/booking-icon.svg" alt="Icon">
                                <span>{{ __('web.user.my_bookings') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.reviews') }}" class="{{ request()->routeIs('user.reviews') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/review-icon.svg" alt="Icon">
                                <span>{{ __('web.common.reviews') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.wishlists') }}" class="{{ request()->routeIs('user.wishlists') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/wishlist-icon.svg" alt="Icon">
                                <span>{{ __('web.user.wishlist') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.messages') }}" class="{{ request()->routeIs('user.messages') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/message-icon.svg" alt="Icon">
                                <span>{{ __('web.user.messages') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.wallet') }}" class="{{ request()->routeIs('user.wallet') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/wallet-icon.svg" alt="Icon">
                                <span>{{ __('web.user.my_wallet') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.ticket') }}" class="{{ request()->routeIs('user.ticket') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/message-icon.svg" alt="Icon">
                                <span>{{ __('web.user.tickets') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.payments') }}" class="{{ request()->routeIs('user.payments') ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/payment-icon.svg" alt="Icon">
                                <span>{{ __('web.user.payment') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('user.usersettings') }}" class="{{ request()->routeIs(['user.usersettings', 'user.security', 'user.preference', 'user.notification', 'user.integration']) ? 'active' : '' }}">
                                <img src="/frontend/assets/img/icons/settings-icon.svg" alt="Icon">
                                <span>{{ __('web.common.settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
