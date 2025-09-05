@extends($layout)

@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_settings') }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('home') }}">{{__('web.home.home')}}</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ __('web.user.user_settings') }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>
<!-- /Breadscrumb Section -->

@include('frontend.user.nav_menu')

<!-- Page Content -->
<div class="content settings-profile-content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header content-settings-header">
            <h4>{{__('web.common.settings')}}</h4>
        </div>
        <!-- /Content Header -->

        <div class="row">
            @include('frontend.user.user_sidebar')

            <!-- Settings Details -->
            <div class="col-lg-9">
                <div class="settings-info">
                    <div class="settings-sub-heading">
                        <h4>{{__('web.user.notifications')}}</h4>
                    </div>
                    <div class="notification-grid">
                        <div class="notification-checkbox">
                            <h5>{{__('web.user.notify_me_when')}}</h5>
                            <ul class="nav">
                                <li>
                                    <label class="custom_check">
                                        <input type="checkbox" name="booking" id="booking" @if($user &&
                                            $user->booking_confirmation == 1) checked @endif>
                                        <span class="checkmark"></span>
                                        {{__('web.user.booking_confirmation')}}
                                    </label>
                                </li>
                            </ul>
                        </div>
                        <div class="notification-status">
                            <div class="notification-status-content">
                                <h5>{{__('web.user.desktop_notifications')}}</h5>
                                <p>{{__('web.user.receive_desktop')}}</p>
                            </div>
                            <div class="status-toggle">
                                <input id="desktop_notifications" class="check" type="checkbox" @if($user &&
                                    $user->desktop_notifications == 1) checked @endif>
                                <label for="desktop_notifications" class="checktoggle">checkbox</label>
                            </div>
                        </div>
                        <div class="notification-status">
                            <div class="notification-status-content">
                                <h5>{{__('web.user.email_notifications')}}</h5>
                                <p>{{__('web.user.receive_email')}}</p>
                            </div>
                            <div class="status-toggle">
                                <input id="email_notifications" class="check" type="checkbox" @if($user &&
                                    $user->email_notifications == 1) checked @endif>
                                <label for="email_notifications" class="checktoggle">checkbox</label>
                            </div>
                        </div>
                        <div class="profile-submit-btn">
                            <button type="submit"
                                class="btn btn-primary submitbtn">{{ __('web.user.save_changes') }}</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Details -->
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection
@push('scripts')
<script src="{{ asset('frontend/assets/js/user/notifications.js') }}"></script>
@endpush
