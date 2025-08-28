@extends('installer::app')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <p>Configure Your Website</p>
            <div>
                <a class="btn btn-outline-primary" href="{{ route('setup.account') }}">&laquo; Back</a>
                <a class="btn btn-outline-primary @if (!session()->has('step-5-complete')) disabled @endif"
                    href="{{ route('setup.smtp') }}">Next &raquo;</a>
            </div>
        </div>
        <div class="card-body">
            <form id="config_form" autocomplete="off">
                <div class="mb-3">
                    <label for="config_app_name">App Name <span class="text-danger">*</span></label>
                    <input type="text" id="config_app_name" name="config_app_name" class="form-control"
                        value="{{ old('config_app_name', $app_name ?? null) }}" placeholder="Enter Your App Name">
                </div>
                <button type="submit" id="submit_btn" class="btn btn-primary">Save Config</button>
            </form>
        </div>
       
    </div>
@endsection
@push('scripts')
     <script src="{{ asset('frontend/assets/js/installer/config.js') }}"></script>
@endpush
