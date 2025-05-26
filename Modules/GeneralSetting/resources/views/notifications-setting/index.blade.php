@extends('admin.admin')

@section('meta_title', __('admin.general_settings.notifications') . ' || ' . $companyName)

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
            <!-- Settings Prefix -->
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <form action="" id="notificationsSettingForm" enctype="multipart/form-data">
                        <input type="hidden" name="group_id" id="group_id" class="form-control" value="2" >
                        <div class="card mb-0">
                            <div class="card-header">
                                <h5>{{ __('admin.general_settings.account_settings') }}</h5>
                            </div>
                            @include('admin.general_settings_loader')
                            <div class="card-body d-none real-card">
                                <div class="security-content">
                                    <h6 class="mb-3">{{ __('admin.general_settings.notifications') }}</h6>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="notification-settings">
                                                <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.notify_me_about') }}</h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="notificationPreference" id="notifyAll">
                                                        <label class="form-check-label" for="notifyAll">
                                                            {{ __('admin.general_settings.all_new_messages') }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="notificationPreference" id="notifyMentions">
                                                        <label class="form-check-label" for="notifyMentions">
                                                            {{ __('admin.general_settings.mentions_only') }}
                                                        </label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="notificationPreference" id="notifyNothing">
                                                        <label class="form-check-label" for="notifyNothing">
                                                            {{ __('admin.general_settings.nothing') }}
                                                        </label>
                                                    </div>
                                                </div>
                                                <span id="notificationPreference_error" class="text-danger error-text"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.desktop_notifications') }}</h6>
                                                    <p class="fs-13">{{ __('admin.general_settings.desktop_notifications_description') }}</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="desktopNotifications" name="desktopNotifications" checked>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="desktopNotifications_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.unread_notification_badge') }}</h6>
                                                    <p class="fs-13">{{ __('admin.general_settings.unread_notification_badges_description') }}</p>
                                                </div>
                                                <div class="d-flex justify-content-end">
                                                    <div class="form-check form-check-md form-switch me-2">
                                                        <input class="form-check-input" type="checkbox" role="switch" id="unreadBadge" name="unreadBadge" checked>
                                                    </div>
                                                </div>
                                            </div>
                                            <span id="unreadBadge_error" class="text-danger error-text"></span>
                                        </div>
                                    </div>
                                    <h6 class="mb-3">{{ __('admin.general_settings.notification_type') }}</h6>
                                    <div class="notification-type">
                                        <ul>
                                            <li>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.booking_rental_updates') }}</h6>
                                                        <p class="fs-13">{{ __('admin.general_settings.booking_rental_updates_description') }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check form-check-md form-switch me-2">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="bookingUpdates" name="bookingUpdates" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="bookingUpdates_error" class="text-danger error-text"></span>
                                            </li>
                                            <li>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.payment_invoice_notifications') }}</h6>
                                                        <p class="fs-13">{{ __('admin.general_settings.payment_invoice_notifications_description') }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check form-check-md form-switch me-2">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="paymentNotifications" name="paymentNotifications" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="paymentNotifications_error" class="text-danger error-text"></span>
                                            </li>
                                            <li>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.user_tenant_notifications') }}</h6>
                                                        <p class="fs-13">{{ __('admin.general_settings.user_tenant_notifications_description') }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check form-check-md form-switch me-2">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="userTenantNotifications" name="userTenantNotifications" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="userTenantNotifications_error" class="text-danger error-text"></span>
                                            </li>
                                            <li>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.vehicle_management') }}</h6>
                                                        <p class="fs-13">{{ __('admin.general_settings.vehicle_management_description') }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check form-check-md form-switch me-2">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="vehicleManagement" name="vehicleManagement" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="vehicleManagement_error" class="text-danger error-text"></span>
                                            </li>
                                            <li>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div>
                                                        <h6 class="fs-14 fw-medium mb-1">{{ __('admin.general_settings.discounts_offers') }}</h6>
                                                        <p class="fs-13">{{ __('admin.general_settings.discounts_offers_description') }}</p>
                                                    </div>
                                                    <div class="d-flex justify-content-end">
                                                        <div class="form-check form-check-md form-switch me-2">
                                                            <input class="form-check-input" type="checkbox" role="switch" id="discountOffers" name="discountOffers" checked>
                                                        </div>
                                                    </div>
                                                </div>
                                                <span id="discountOffers_error" class="text-danger error-text"></span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-none real-card">
                                <div class="d-flex justify-content-end">
                                    <a href="{{ route('dashboard') }}" class="btn btn-light me-3" >{{ __('admin.general_settings.cancel') }}</a>
                                    <button type="submit" class="btn btn-primary">{{ __('admin.general_settings.save_changes') }}</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /Settings Prefix -->
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/notificationsSetting.js') }}"></script>
@endpush










