@foreach ($notifications as $notification)
<div class="border rounded p-3 mb-3 bg-white d-flex justify-content-between align-items-start cursor-pointer ">
    <div class="d-flex align-items-start @if(!$notification->readed ?? false) notificationitem @endif" data-id="{{ $notification->id ?? '' }}" >
        <div class="position-relative me-3">
            <img src="{{ getProfileImage() }}" class="rounded-circle" width="50" height="50" alt="Profile">
            @if(!$notification->readed ?? false)
                <span class="position-absolute top-0 start-100 translate-middle p-1 bg-primary border border-white rounded-circle">
                    <span class="visually-hidden">{{ __('admin.common.new') }}</span>
                </span>
            @endif
        </div>
        <div>
            <p class="mb-1"><span class="fw-bold text-dark">{{ __('admin.common.notice') }}: </span>{{ $notification->subject ?? '' }}</p>
            <small class="text-dark">{!! $notification->content ?? '' !!}</small>
        </div>
    </div>
    <div class="text-end ms-3">
        <button class="btn btn-sm btn-outline-danger mb-2 del_notification" data-id="{{ $notification->id ?? '' }}">
            <i class="fa fa-trash"></i>
        </button>
        <p class="small mb-0">{{ \Carbon\Carbon::parse($notification->created_at)->diffForHumans() }}</p>
    </div>
</div>
@endforeach
