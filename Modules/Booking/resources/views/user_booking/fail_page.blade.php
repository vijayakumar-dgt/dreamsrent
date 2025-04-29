@extends($layout)
@section('content')
<div class="error-box">
    <img src="/assets/img/warning-sign.png" class="img-fluid paymentFailImg" alt="Payment Failed">
    <h3>Oops! Payment Failed!</h3>
    <p>The payment could not be processed. Please try again or contact support if the issue persists.</p>
    <div class="back-button">
        <!-- <a href="javascript:void(0);" class="btn btn-primary" onclick="window.history.back();">Try Again</a> -->
        <a href="/" class="btn btn-secondary">Back to Home</a>
    </div>
</div>
@endsection
