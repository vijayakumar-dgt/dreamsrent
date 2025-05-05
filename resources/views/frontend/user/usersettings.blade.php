@extends($layout)
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/plugins/intltelinput/css/intlTelInput.css') }}">
@endpush
@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_settings') }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{ __('web.home.home') }}</a></li>
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
<div class="content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header content-settings-header">
            <h4>{{ __('web.user.settings') }}</h4>
        </div>
        <!-- /Content Header -->
        <div class="row">
            @include('frontend.user.user_sidebar')
            <!-- Settings Details -->
            <div class="col-lg-9">
                <div class="settings-info">
                    <div class="settings-sub-heading">
                        <h4>{{ __('web.user.settings') }}</h4>
                    </div>
                    <form id="userProfileForm" enctype="multipart/form-data">
                        @csrf
                        <!-- Basic Info -->
                        <div class="profile-info-grid">
                            <div class="profile-info-header">
                                <h5>{{ __('web.user.basic_information') }}</h5>
                                <p>{{ __('web.user.user_information') }}</p>
                            </div>
                            <div class="profile-inner">
                                <div class="profile-info-pic">
                                    <div class="profile-info-img position-relative">
                                        <img id="profile_photo_preview" src="{{ $user->userDetail ? uploadedAsset($user->userDetail->profile_image, 'profile') : '' }}" alt="Profile" id="profile-preview">
                                        <div class="profile-edit-info">
                                            <a href="javascript:void(0)" onclick="document.getElementById('profile_photo').click();">
                                                <input type="file" class="form-control image-sign d-none" id="profile_photo" name="profile_photo">
                                                <i class="feather-edit"></i>
                                            </a>
                                            <a href="javascript:void(0)" onclick="removeImage()">
                                                <i class="feather-trash-2"></i>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="profile-info-content">
                                        <h6>{{ __('web.user.profile_picture') }}</h6>
                                        <p>{{ __('web.user.picture_format') }}</p>
                                    </div>
                                </div>
                                <span id="profile_photo_error" class="text-danger error-text"></span>
                                <input type="hidden" name="id" value="{{ auth()->id() }}">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.first_name') }}<span class="text-danger">*</span></label>
                                            <input type="text" id="first_name" name="first_name" class="form-control" placeholder="{{ __('web.user.enter_first_name') }}" value="{{ $user->userDetail ? $user->userDetail->first_name : '' }}">
                                            <span id="first_name_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.last_name') }}<span class="text-danger">*</span></label>
                                            <input type="text" id="last_name" name="last_name" class="form-control" value="{{ $user->userDetail ? $user->userDetail->last_name : '' }}" placeholder="{{ __('web.user.enter_last_name') }}">
                                            <span id="last_name_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.phone_number') }} <span class="text-danger">*</span></label>
                                            <input type="text" id="user_phone" name="user_phone" value="{{ $user->phone_number ? $user->phone_number : '' }}" class="form-control user_phone" placeholder="{{ __('web.user.enter_mobile_number') }}">
                                            <input type="hidden" id="international_phone_number" name="international_phone_number" value="{{ $user->phone_number ? $user->phone_number : '' }}">
                                            <span id="user_phone_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.email') }}<span class="text-danger">*</span></label>
                                            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email ? $user->email : '' }}" placeholder="{{ __('web.user.enter_email') }}">
                                            <span id="email_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Basic Info -->
                        <!-- Address Info -->
                        <div class="profile-info-grid">
                            <div class="profile-info-header">
                                <h5>{{ __('web.user.address_information') }}</h5>
                                <p>{{ __('web.user.user_address_information') }}</p>
                            </div>
                            <div class="profile-inner">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.address') }}</label>
                                            <textarea class="form-control" id="address_line" name="address_line" placeholder="{{ __('web.user.enter_address') }}">{{ $user->userDetail ? $user->userDetail->address : '' }}</textarea>
                                            <span id="address_line_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.country') }} <span class="text-danger">*</span></label>
                                            <select name="country" class="form-control custom-select2" id="country" data-placeholder="{{__('web.common.select')}}">
                                                @if(!empty($countries) && count($countries) > 0)
                                                    @foreach($countries as $country)
                                                        <option value="{{ $country->id }}" @if($user->userDetail && $user->userDetail->country_id == $country->id) selected @endif>{{ $country->name }}</option>
                                                    @endforeach
                                                @endif
                                            </select>
                                            <span id="country_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.state') }} <span class="text-danger">*</span></label>
                                            <select name="state" class="form-control custom-select2" id="state" data-default-id="{{ $user->userDetail ? $user->userDetail->state_id : '' }}" data-placeholder="{{__('web.common.select')}}">
                                            </select>
                                            <span id="state_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.city') }} <span class="text-danger">*</span></label>
                                            <select name="city" id="city" class="form-control custom-select2" data-default-id="{{ $user->userDetail ? $user->userDetail->city_id : '' }}" data-placeholder="{{__('web.common.select')}}">
                                            </select>
                                            <span id="city_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="profile-form-group">
                                            <label>{{ __('web.user.pincode') }}<span class="text-danger">*</span></label>
                                            <input type="text" id="postal_code" name="postal_code" value="{{ $user->userDetail ? $user->userDetail->postal_code : '' }}" class="form-control" placeholder="Enter Pincode" maxlength="6">
                                            <span id="postal_code_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /Address Info -->
                        <!-- Profile Submit -->
                        <div class="profile-submit-btn">
                            <button type="reset" class="btn btn-secondary">{{ __('web.user.cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('web.user.save_changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Settings Details -->
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection
@push('scripts')
<script src="{{ asset('assets/plugins/intltelinput/js/intlTelInput.js') }}"></script>
<script src="{{ asset('frontend/assets/js/user/userprofile.js') }}"></script>
@endpush
