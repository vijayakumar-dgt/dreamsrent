@if(!empty($notifications) && count($notifications) > 0)
@foreach($notifications as $notification)
@php
    $notificationContent = $notification->subject;
    if(strlen($notificationContent) > 75) {
        $notificationContent = substr($notificationContent, 0, 80) . '...';
    }
@endphp
<li class="notification-message">
    <a href="#">
        <div class="media d-flex">
            {{-- <span class="avatar avatar-lg flex-shrink-0">
                <img class="avatar-img rounded-circle" alt="User Image" src="{{ asset('assets/img/profiles/avatar-01.jpg') }}">
            </span> --}}
            <div class="media-body flex-grow-1">
                <p class="noti-details"><span class="noti-title">{{ $notificationContent }}</span></p>
                <p class="noti-time"><span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span></p>
            </div>
        </div>
    </a>
</li>
@endforeach
@else
<li class="notification-message">
    <a href="#">
        <div class="media d-flex">
            <div class="media-body flex-grow-1">
                <p class="noti-details text-center"><span class="noti-title">{{ __('web.user.no_new_notifications') }}</span></p>
            </div>
        </div>
    </a>
</li>
@endif