<form id="auto-booking-form" action="{{ route('booking.checkout', $slug) }}" method="POST">
    @csrf
    @foreach ($data as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endforeach
</form>
<script src="{{ asset('frontend/assets/js/custom/redirect-to-booking.js') }}"></script>
