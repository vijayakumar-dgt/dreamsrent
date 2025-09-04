@extends('installer::app')
@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <p>Setup Admin Account</p>
            <div>
                <a class="btn btn-outline-primary @if (!session()->has('step-4-complete')) disabled @endif"
                    href="{{ route('setup.configuration') }}">Next &raquo;</a>
            </div>
        </div>
        <div class="card-body">
            <form id="account_form" method="POST" action="{{ route('setup.account.submit') }}" autocomplete="off" autocomplete="off">
            <div class="mb-3">
                <label for="name">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control"
                    value="{{ old('name', $admin?->name) }}" placeholder="Enter Your Full Name">
            </div>

            <div class="mb-3">
                <label for="email">E-Mail <span class="text-danger">*</span></label>
                <input type="text" name="email" id="email" class="form-control"
                    value="{{ old('email', $admin?->email) }}" placeholder="Enter Your E-Mail Address">
            </div>

            <div class="mb-3">
                <label for="password">Password <span class="text-danger">*</span></label>
                <input autocomplete="new-password" id="password" type="password" name="password"
                    value="{{ old('password') }}" class="form-control" placeholder="Enter Your Password">
            </div>

            <div class="mb-3">
                <label for="confirm_password">Re-Type Password <span class="text-danger">*</span></label>
                <input autocomplete="new-password" id="confirm_password" type="password" name="confirm_password"
                    value="{{ old('password') }}" class="form-control" placeholder="Confirm Your Password">
            </div>

            <button type="submit" id="submit_btn" class="btn btn-primary">Create Account</button>
        </form>

        </div>
       
    </div>
@endsection
@push('scripts')
     <script src="{{ asset('frontend/assets/js/installer/account.js') }}"></script>
@endpush
