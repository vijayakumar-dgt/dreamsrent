@extends('admin.admin')

@section('meta_title', __('admin.general_settings.gdpr_cookies') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-0 me-md-0 me-lg-4">
            <x-admin.breadcrumb 
                :title="__('admin.general_settings.settings')" 
                :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]" 
            />
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <form id="cookiesSettingForm">
                         @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5>{{ __('admin.general_settings.system_settings') }}</h5>
                            </div>
                            @include('admin.general_settings_loader')
                            <div class="card-body d-none real-card">
                                <div class="sms-gateway">
                                    <h6 class="fw-bold mb-3">{{ __('admin.general_settings.gdpr_cookies') }}</h6>
                                    <input type="hidden" name="group_id" id="group_id" class="form-control" value="7">
                                        <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.language') }} <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="">
                                            <select class="form-select" id="language" name="language">
                                                @foreach($languages as $language)
                                                    <option value="{{ $language->language_id }}">
                                                        {{ $language->transLang->name ?? 'N/A' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            </div>
                                            <span id="cookiesContentText_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Cookies Content Text -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.cookies_content_text') }}  <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-8">
                                            <div class="">
                                            <textarea id="cookiesContentText" name="cookiesContentText" class="form-control summernote"></textarea>
                                            <p class="mt-2">{{ __('admin.general_settings.maximum_60_words') }}</p>
                                            </div>
                                            <span id="cookiesContentText_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Cookies Position -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.cookies_position') }}<span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="">
                                                <select id="cookiesPosition" name="cookiesPosition" class="form-control select">
                                                    <option value="">{{ __('admin.general_settings.select') }}</option>
                                                    <option value="right">{{ __('admin.general_settings.right') }}</option>
                                                    <option value="left">{{ __('admin.general_settings.left') }}</option>
                                                </select>
                                            </div>
                                            <span id="cookiesPosition_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Agree Button Text -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.agree_button_text') }} <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <input type="text" id="agreeButtonText" name="agreeButtonText" class="form-control">
                                            <span id="agreeButtonText_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Decline Button Text -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.decline_button_text') }} <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <input type="text" id="declineButtonText" name="declineButtonText" class="form-control">
                                            <span id="declineButtonText_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Show Decline Button -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.show_decline_button') }} <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <div class="form-check form-check-md form-switch">
                                                <input class="form-check-input" type="checkbox" id="showDeclineButton" name="showDeclineButton" role="switch" value="1" checked>
                                            </div>
                                            <span id="showDeclineButton_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <!-- Links for Cookies Page -->
                                    <div class="row mb-3 align-items-center">
                                        <div class="col-xl-4 d-flex">
                                            <h6 class="fw-medium fs-14">{{ __('admin.general_settings.links_for_cookies_page') }}  <span class="text-danger">*</span></h6>
                                        </div>
                                        <div class="col-xl-6">
                                            <input type="text" id="cookiesPageLink" name="cookiesPageLink" class="form-control">
                                            <span id="cookiesPageLink_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-none real-card">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{ __('admin.general_settings.cancel') }}</a>
                                    @if (hasPermission($permissions, 'system_settings', 'edit'))
                                    <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.save_changes') }}</button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/cookies-setting.js') }}"></script>
@endpush










