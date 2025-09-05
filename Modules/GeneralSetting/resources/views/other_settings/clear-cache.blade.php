@extends('admin.admin')

@section('meta_title', __('admin.general_settings.clear_cache') . ' || ' . $companyName)

@section('content')
<!-- Page Wrapper -->
<div class="page-wrapper">
    <div class="content me-0 me-md-0 me-lg-4">
        <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
                    __('admin.general_settings.settings') => ''
                ]" />
        <!-- Settings Prefix -->
        <div class="row">
            @include('admin.partials.general_settings_side_menu')
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('admin.general_settings.other_settings') }}</h5>
                    </div>
                    <div class="card-body">
                        <div>
                            <div>
                                <h6 class="mb-3">{{ __('admin.general_settings.clear_cache') }}</h6>
                                <P class="mb-3">{{ __('admin.general_settings.cache_description') }}</P>
                                <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#clear_cache"
                                    class="btn btn-primary">{{ __('admin.general_settings.clear_cache') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /Settings Prefix -->
    </div>
    @include('admin.partials.footer')
</div>
<!-- /Page Wrapper -->
<!-- Clear Cache Modal -->
<x-admin.delete-modal className="deletemodal" id="clear_cache" action="{{ route('admin.clear-cache') }}" method="POST"
    :title="__('admin.general_settings.clear_cache')" :description="__('admin.general_settings.want_to_clear_cache')"
    modalIconClass="ti ti-trash-x fs-26" deleteBtnType="button" deleteBtnId="clear-cache"
    deleteBtnText="{{ __('admin.general_settings.yes_clear_cache') }}">
</x-admin.delete-modal>
<!-- /Clear Cache Modal -->

@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/clear-cache.js') }}"></script>
@endpush
