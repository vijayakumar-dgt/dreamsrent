@extends($layout)

@section('content')
<!-- Breadscrumb Section -->
<div class="breadcrumb-bar">
    <div class="container">
        <div class="row align-items-center text-center">
            <div class="col-md-12 col-12">
                <h2 class="breadcrumb-title">{{ __('web.user.user_settings') }}</h2>
                <nav aria-label="breadcrumb" class="page-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="/">{{__('web.home.home')}}</a></li>
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
<div class="content settings-profile-content">
    <div class="container">
        <!-- Content Header -->
        <div class="content-header content-settings-header">
            <h4>{{__('web.common.settings')}}</h4>
        </div>
        <!-- /Content Header -->
        <div class="row">
            @include('frontend.user.user_sidebar')
            <!-- Settings Details -->
            <div class="col-lg-9">
                <div class="settings-info">
                    <div class="settings-sub-heading">
                        <h4>{{__('web.user.preferences')}}</h4>
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="preference-wrap">
                                <div class="preference-info">
                                    <h6>{{ __('web.user.language') }}</h6>
                                    <p>{{ __('web.user.select_display_language') }}</p>
                                </div>
                                <div class="preference-select">
                                    <select class="select custom-select2" id="language_id" name="language_id" data-placeholder="{{ __('web.common.select') }}">
                                        <option value="">{{ __('web.common.select') }}</option>
                                        @if ($languages->count() > 0)
                                            @foreach ($languages as $language)
                                                <option value="{{ $language->transLang->id }}">{{ $language->transLang->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="preference-wrap">
                                <div class="preference-info">
                                    <h6>{{ __('web.user.region') }} / {{ __('web.user.locale') }}</h6>
                                    <p>{{ __('web.user.select_region') }}</p>
                                </div>
                                <div class="preference-select">
                                    <select class="select custom-select2" id="region_id" name="region_id" data-placeholder="{{ __('web.common.select') }}">
                                        <option value="">{{ __('web.common.select') }}</option>
                                        @if ($countries->count() > 0)
                                            @foreach ($countries as $country)
                                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Settings Details -->
        </div>
    </div>
</div>
<!-- /Page Content -->
@endsection

@push('scripts')
<script src="{{ asset('frontend/assets/js/custom/user/preference.js') }}"></script>
@endpush
