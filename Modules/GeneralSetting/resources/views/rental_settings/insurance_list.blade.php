@extends('admin.admin')

@section('meta_title', __('admin.common.insurance') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4 pb-0">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ __('admin.general_settings.settings') }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.general_settings.settings') }}</li>
                        </ol>
                    </nav>
                </div>
            </div>
            <!-- /Breadcrumb -->
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
                                    <div class="skeleton label-skeleton label-loader me-2"></div>
                                    <div class="top-search me-2 d-none real-label">
                                        <div class="top-search-group">
                                            <span class="input-icon">
                                                <i class="ti ti-search"></i>
                                            </span>
                                            <input type="text" class="form-control" name="search" id="search" placeholder="{{ __('admin.common.search') }}">
                                        </div>
                                    </div>
                                    @if (hasPermission($permissions, 'rental_settings', 'create'))
                                    <div class="skeleton label-skeleton label-loader"></div>
                                    <a href="javascript:void(0);" class="btn btn-primary d-none real-label" id="add_insurance" data-bs-toggle="modal" data-bs-target="#insurance_modal"><i class="ti ti-plus me-1"></i>{{ __('admin.general_settings.add_new_insurance') }}</a>
                                    @endif
                                </div>
                            </div>
                            <div class="custom-datatable-filter table-responsive table-loader">
                                <table class="table table-bordered">
                                    <thead class="thead-light">
                                        <tr>
                                            <th class="text-center">
                                                <div class="skeleton th-skeleton th-loader"></div>
                                            </th>
                                            <th>
                                                <div class="skeleton th-skeleton th-loader"></div>
                                            </th>
                                            <th>
                                                <div class="skeleton th-skeleton th-loader"></div>
                                            </th>
                                            <th>
                                                <div class="skeleton th-skeleton th-loader"></div>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                            <td>
                                                <div class="skeleton data-skeleton data-loader"></div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="custom-datatable-filter d-none real-table">
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

    <!-- Insurance Modal-->
    <div class="modal fade" id="insurance_modal">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0 modal-title">{{ __('admin.general_settings.create_insurance') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form id="insuranceForm" autocomplete="off">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="language_id" id="language_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.general_settings.insurance_name') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="insurance_name" id="insurance_name">
                            <span class="error-text text-danger" id="insurance_name_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.common.price_type') }}<span class="text-danger"> *</span></label>
                            <div class="d-flex align-items-center">
                                @if ($priceTypes)
                                @foreach ($priceTypes as $priceType)
                                    <div class="form-check me-3">
                                        <input class="form-check-input price_type" type="radio" name="price_type_id" id="price_type_{{ $priceType->id }}" value="{{ $priceType->id }}" data-price_type="{{ $priceType->pricing_type }}">
                                        <label class="form-check-label" for="price_type_{{ $priceType->id }}">{{ ucfirst($priceType->pricing_type) }}</label>
                                    </div>
                                @endforeach
                                @endif
                            </div>
                            <span class="error-text text-danger" id="price_type_error"></span>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.common.price') }}<span class="text-danger"> *</span></label>
                            <input type="text" class="form-control" name="price" id="price">
                            <span class="error-text text-danger" id="price_error"></span>
                        </div>
                        <div class="add-insurance-benefit">
                            <div class="mb-1">
                                <label class="form-label">{{ __('admin.common.benefit') }} <span class="text-danger"> *</span></label>
                                <input type="text" class="form-control" name="benefit[]" id="benefit">
                                <span class="error-text text-danger" id="benefit_error"></span>
                            </div>
                        </div>
                        <a href="#" class="d-inline-flex align-items-center text-info" id="add_new_benefit"><i class="ti ti-plus me-1"></i>{{ __('admin.common.add_new') }}</a>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-between align-items-center w-100">
                            <div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
                                <label for="status" class="form-check-label form-label mt-0 mb-0">
                                <input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked>
                                {{ __('admin.common.status') }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                                <button type="submit" class="btn btn-primary submitBtn" data-save="Save">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- /Insurance Modal -->

    <!-- Insurance Benefits -->
    <div class="modal fade" id="view-benifits">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="mb-0">{{ __('admin.common.benefits') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="benefitsList">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- /Insurance Benefits -->

    <!-- Delete  -->
    <div class="modal fade" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <form id="deleteInsurance">
                        <input type="hidden" name="delete_id" id="delete_id">
                        <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                            <i class="ti ti-trash-x fs-26"></i>
                        </span>
                        <h4 class="mb-1">{{ __('admin.general_settings.delete_insurance') }}</h4>
                        <p class="mb-3">{{ __('admin.general_settings.delete_insurance_confirmation') }}</p>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
                            <button type="submit" class="btn btn-primary">{{ __('admin.common.yes_delete') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- /Delete -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/general_setting/insurance.js') }}"></script>
@endpush
