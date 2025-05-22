@extends('installer::app')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <p>Verify Purchase</p>
            <a class="btn btn-outline-primary @if (!session()->has('step-1-complete')) disabled @endif"
                href="{{ route('setup.requirements') }}">Next &raquo;</a>
        </div>
        <div class="card-body">
            <div class="mb-3 row">
                <div class="col-12">
                    @if (!strtolower(config('app.app_mode')))
                        @php(session()->put('step-1-complete', true))
                        <div class="p-1">
                            <p>You are using demo mode. No purchase code needed. Continue installation.</p>
                        </div>
                        <a href="{{ route('setup.requirements') }}" class="btn btn-success">Continue</a>
                    @else
                        <form id="verify_form" action="{{ route('setup.checkParchase') }}" method="POST">
                            @csrf
                            <label for="purchase_code">Purchase Code</label>
                            <input type="text" id="purchase_code" class="mb-2 form-control" name="purchase_code" />
                            <button id="submit_btn" type="submit" class="btn btn-primary">Check</button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
       
    </div>
@endsection
@push('scripts')
 <script src="{{ asset('frontend/assets/js/installer/verify.js') }}"></script>
    
@endpush
