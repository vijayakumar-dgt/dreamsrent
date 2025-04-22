@if(!empty($notifications) && count($notifications) > 0)
@foreach($notifications as $notification)
@php
    $notificationContent = $notification->subject;
    if(strlen($notificationContent) > 75) {
        $notificationContent = substr($notificationContent, 0, 80) . '...';
    }
@endphp
<div class="notification-list">
    <!-- Notification item -->
    <div class="d-flex">
        <div class="flex-grow-1">
            <p class="mb-1"><a href="javascript:void(0);">{{ $notificationContent }}</a></p>
            <span class="fs-12 noti-time"><i class="ti ti-clock me-1"></i>{{$notification->created_at->diffForHumans()}}</span>
        </div>
    </div>
</div>
@endforeach
@else
<div class="notification-list">
    <div class="text-center">
            <p class="mb-0">No Notifications</p>
    </div>
</div>
@endif