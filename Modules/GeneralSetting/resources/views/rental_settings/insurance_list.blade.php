@extends('admin.admin')

@section('meta_title', __('admin.common.insurance') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <x-admin.breadcrumb :title="__('admin.general_settings.settings')" :breadcrumbs="[
            __('admin.general_settings.settings') => ''
        ]" />
            <div class="row">
                @include('admin.partials.general_settings_side_menu')
                <div class="col-xl-9">
                    <div class="card">
                        <div class="card-header">
                            <h5>{{ __('admin.general_settings.rental_settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="mb-3">{{ __('admin.common.insurance') }}</h6>
                                <div class="d-flex align-items-center">
                                    <div class="top-search me-2">
                                        <div class="top-search-group">
                                            <span class="input-icon">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input type="text" class="form-control" name="search" id="search"
                                                placeholder="{{ __('admin.common.search') }}">
                                        </div>
                                    </div>
                                    @if (hasPermission($permissions, 'rental_settings', 'create'))
                                        <a href="javascript:void(0);" class="btn btn-primary" id="add_insurance"
                                            data-bs-toggle="modal" data-bs-target="#insurance_modal"><i
                                                class="ti ti-plus me-1"></i>{{ __('admin.general_settings.add_new_insurance') }}</a>
                                    @endif
                                </div>
                            </div>
                            <div class="custom-datatable-filter table-responsive position-relative vh-10 table-loader">
                                @include('admin.content-loader')
                            </div>
                            <div class="custom-datatable-filter table-responsive d-none real-table">
                                <table class="table" id="insuranceTable">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>{{ strtoupper(__('admin.general_settings.insurance_name')) }}</th>
                                            <th>{{ strtoupper(__('admin.common.price')) }}</th>
                                            <th>{{ strtoupper(__('admin.common.benefits')) }}</th>
                                            <th>{{ strtoupper(__('admin.common.status')) }}</th>
                                            @if (hasPermission($permissions, 'rental_settings', 'edit') || hasPermission($permissions, 'rental_settings', 'delete'))
                                                <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                            <div class="table-footer"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Insurance Modal -->
    <x-admin.modal className="addmodal" id="insurance_modal" :title="__('admin.general_settings.create_insurance')"
        formId="insuranceForm" dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <input type="hidden" name="id" id="id">
            <input type="hidden" name="language_id" id="language_id">

            <div class="mb-3">
                <label class="form-label">{{ __('admin.general_settings.insurance_name') }} <span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control" name="insurance_name" id="insurance_name">
                <span class="error-text text-danger" id="insurance_name_error"></span>
            </div>

            <div class="mb-3">
                <label class="form-label">{{ __('admin.common.price_type') }} <span class="text-danger">*</span></label>
                <div class="d-flex align-items-center">
                    @foreach ($priceTypes ?? [] as $priceType)
                        <div class="form-check me-3">
                            <input class="form-check-input price_type" type="radio" name="price_type_id"
                                id="price_type_{{ $priceType->id }}" value="{{ $priceType->id }}"
                                data-price_type="{{ $priceType->pricing_type }}">
                            <label class="form-check-label" for="price_type_{{ $priceType->id }}">
                                {{ ucfirst($priceType->pricing_type) }}
                            </label>
                        </div>
                    @endforeach
                </div>
                <span class="error-text text-danger" id="price_type_error"></span>
            </div>

            <div class="mb-3">
                <label class="form-label" id="price_label">{{ __('admin.common.price') }} <span
                        class="text-danger">*</span></label>
                <input type="text" class="form-control" name="price" id="price">
                <span class="error-text text-danger" id="price_error"></span>
            </div>

            <div class="add-insurance-benefit">
                <div class="mb-1">
                    <label class="form-label">{{ __('admin.common.benefit') }} <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="benefit[]" id="benefit">
                    <span class="error-text text-danger" id="benefit_error"></span>
                </div>
            </div>

            <a href="#" class="d-inline-flex align-items-center text-info" id="add_new_benefit">
                <i class="ti ti-plus me-1"></i>{{ __('admin.common.add_new') }}
            </a>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-between align-items-center w-100" id="modalfootdiv">
                <div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
                    <label class="form-check-label form-label mt-0 mb-0">
                        <input class="form-check-input form-label me-2" type="checkbox" role="switch" name="status"
                            id="status" checked>
                        {{ __('admin.common.status') }}
                    </label>
                </div>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">
                        {{ __('admin.common.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary submitBtn">
                        {{ __('admin.common.create_new') }}
                    </button>
                </div>
            </div>
        </x-slot>
    </x-admin.modal>
    <!-- /Insurance Modal -->

    <!-- Insurance Benefits -->
    <x-admin.modal className="benefitmodal" id="view-benifits" :title="__('admin.common.benefits')"
        dialogClass="modal-dialog-centered modal-md">
        <x-slot name="body">
            <div id="benefitsList"></div>
        </x-slot>
    </x-admin.modal>

    <!-- /Insurance Benefits -->

    <!-- Delete Insurance -->
    <x-admin.delete-modal className="deletemodal" id="delete-modal" action="" formId="deleteInsurance"
        :hiddenInputs="['delete_id' => '']" :title="__('admin.general_settings.delete_insurance')"
        :description="__('admin.general_settings.delete_insurance_confirmation')">
    </x-admin.delete-modal>
    <!-- /Delete Insurance -->

@endsection

@push('scripts')
    <script src="{{ asset('backend/assets/js/general_setting/insurance.js') }}"></script>
@endpush