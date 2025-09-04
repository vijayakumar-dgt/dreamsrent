@extends($layout)
@section('content')
<div class="error-box">
    <img src="/backend/assets/img/warning-sign.png" class="img-fluid paymentFailImg" alt="Payment Failed">
    <h3>{{ __('web.common.payment_failed_message') }}</h3>
    <p>{{ __('web.common.payment_fail_description') }}</p>
    <div class="back-button">
        <a href="{{ route('home') }}" class="btn btn-secondary">{{ __('web.common.back_to_home') }}</a>
    </div>
</div>
@endsection
