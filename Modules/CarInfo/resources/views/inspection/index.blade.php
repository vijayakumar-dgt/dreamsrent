@extends('admin.admin')

@section('meta_title', __('admin.common.inspections') . ' || ' . $companyName)

@section('content')
    <!-- Page Wrapper -->
    <div class="page-wrapper">
        <div class="content me-4">
            <!-- Breadcrumb -->
            <div class="d-md-flex d-block align-items-center justify-content-between page-breadcrumb mb-3">
                <div class="my-auto mb-2">
                    <h2 class="mb-1">{{ __('admin.common.inspections') }}</h2>
                    <nav>
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard') }}">{{ __('admin.common.home') }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('admin.common.inspections') }}</li>
                        </ol>
                    </nav>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap ">
                    
                    <div class="mb-2">
                        @if (hasPermission($permissions, 'inspections', 'create'))
                        <button type="button" data-bs-toggle="modal" data-bs-target="#add_inspection" class="btn btn-primary d-flex align-items-center" id="add_new_inspection"><i class="ti ti-plus me-2"></i>{{ __('admin.rentals.add_new_inspection') }}</button>
                    @endif
                    </div>
                </div>
            </div>
            <!-- /Breadcrumb -->
            <div class="d-flex align-items-center justify-content-between flex-wrap row-gap-3 mb-3">
                <div class="d-flex align-items-center flex-wrap row-gap-3"> 
                    <div class="top-search">
                        <div class="top-search-group">
                            <span class="input-icon">
                                <i class="ti ti-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="{{ __('admin.common.search') }}" id="search">
                        </div>
                    </div>
                </div>
                <div class="d-flex my-xl-auto right-content align-items-center flex-wrap row-gap-3">               
                    <div class="dropdown">
                        <button type="button" class="dropdown-toggle btn btn-white d-inline-flex align-items-center" data-bs-toggle="dropdown">
                            <i class="ti ti-badge me-1"></i> <span id="status_text"> {{ __('admin.common.status') }}</span>
                        </button>
                        <ul class="dropdown-menu  dropdown-menu-end p-2">
                            <li>
                                <button type="button" class="dropdown-item rounded-1 statusfilter" data-status="completed">{{ __('admin.rentals.completed') }}</button>
                            </li>
                            <li>
                                <button  type="button" class="dropdown-item rounded-1 statusfilter" data-status="inprogress">{{ __('admin.rentals.inprogress') }}</button>
                            </li>
                            <li>
                                <button  type="button" class="dropdown-item rounded-1 statusfilter" data-status="pending">{{ __('admin.rentals.pending') }}</button>
                            </li>
                            <li>
                                <button  type="button" class="dropdown-item rounded-1 statusfilter" data-status="onhold">{{ __('admin.rentals.onhold') }}</button>
                            </li>
                            <li>
                                <button  type="button" class="dropdown-item rounded-1 statusfilter" data-status="rejected">{{ __('admin.rentals.rejected') }}</button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Custom Data Table -->
            <div class="custom-datatable-filter table-responsive table-loader position-relative vh-10">
                @include('admin.content-loader')
            </div>
            <div class="d-none real-table">
                <div class="custom-datatable-filter table-responsive brandstable">
                    <table class="table datatable" id="inspectionTable">
                        <thead class="thead-light">
                            <tr>
                                <th>{{ strtoupper(__('admin.common.vehicle')) }}</th>
                                <th>{{ strtoupper(__('admin.rentals.inspection_date')) }}</th>
                                <th>{{ strtoupper(__('admin.rentals.inspected_by')) }}</th>
                                <th>{{ strtoupper(__('admin.rentals.inspection_status')) }}</th>
                                <th>{{ strtoupper(__('admin.rentals.repair_status')) }}</th>
                                @if (hasPermission($permissions, 'inspections', 'edit') || hasPermission($permissions, 'inspections', 'delete'))
                                <th>{{ strtoupper(__('admin.common.action')) }}</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
                <!-- Custom Data Table -->    
                <div class="table-footer d-none"></div>	
            </div>
        </div>
        @include('admin.partials.footer')
    </div>
    <!-- /Page Wrapper -->

    <!-- Add Inspection Modal Start-->  
    <div class="modal fade" id="add_inspection">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mb-0">{{ __('admin.rentals.create_inspection') }}</h4>
                    <button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ti ti-x fs-16"></i>
                    </button>
                </div>
                <form action="" id="inspectionForm">
                    @csrf
                    <input type="hidden" name="id" id="id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.common.vehicle') }} <span class="text-danger">*</span></label>
                            <select name="vehicle_info_id" id="vehicle_info_id" class="form-control" data-placeholder="{{ __('admin.common.select') }}">
                                <option value="">{{ __('admin.common.select') }}</option>
                            </select>
                            <span id="vehicle_info_id_error" class="text-danger error-text"></span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inspection_date" class="form-label">{{ __('admin.rentals.inspection_date') }} <em class="text-danger">*</em></label>
                                <input type="text" name="inspection_date" class="form-control inspection_date" id="inspection_date">
                                <span class="text-danger error-text" id="inspection_date_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="inspection_by" class="form-label">{{ __('admin.rentals.inspection_by') }} <em class="text-danger">*</em></label>
                                <select name="inspection_by" id="inspection_by" class="form-control select2" data-placeholder="{{ __('admin.common.select') }}">
                                    @if (!empty($users) && $users->count() > 0)
                                        <option value="">{{ __('admin.common.select') }}</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    @else
                                        <option value="">{{ __('admin.common.no_data_found') }}</option>
                                    @endif
                                </select>
                                <span class="text-danger error-text" id="inspection_by_error"></span>
                            </div>
                        </div>
                        <p class="text-dark fs-16 mb-3">{{ __('admin.rentals.incoming_details') }}</p>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="odometer" class="form-label">{{ __('admin.rentals.odometer') }} <em class="text-danger">*</em></label>
                                <input type="number" name="odometer" class="form-control" id="odometer">
                                <span class="text-danger error-text" id="odometer_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="fuel" class="form-label">{{ __('admin.rentals.fuel') }} <em class="text-danger">*</em></label>
                                <input type="number" name="fuel" class="form-control" id="fuel">
                                <span class="text-danger error-text" id="fuel_error"></span>
                            </div>
                        </div>
                        <p class="text-dark fs-16 mb-3">{{ __('admin.rentals.checklist') }}</p>
                        <span class="text-danger error-text" id="checklist_error"></span>
                        <div class="row mb-3">
                            @if (!empty($checklists) && $checklists->count() > 0)
                                @foreach ($checklists as $checklist)
                                    <div class="col-md-6">
                                        <div class="form-check form-check-md">
                                            <label class="form-check-label form-label mt-0 mb-0">
                                                <input class="form-check-input form-label me-2 checklist" type="checkbox" value="{{ $checklist->id }}" name="checklist_id[]" id="checklist_id_{{ $checklist->id }}">
                                                {{ $checklist->name }}
                                            </label>
                                            <p class="text-muted">{{ $checklist->description }}</p>
                                            <span class="text-danger error-text" id="checklist_id_error_{{ $checklist->id }}"></span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <option value="">{{ __('admin.common.no_data_found') }}</option>
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('admin.rentals.notes') }}</label>
                            <textarea class="form-control" name="notes" id="notes" rows="3"></textarea>
                            <span class="text-danger error-text" id="notes_error"></span>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="inspection_status" class="form-label">{{ __('admin.rentals.inspection_status') }} <em class="text-danger">*</em></label>
                                <select name="inspection_status" id="inspection_status" class="form-control select" data-placeholder="{{ __('admin.common.select') }}">
                                    <option value="">{{ __('admin.common.select') }}</option>
                                    <option value="completed">{{ __('admin.rentals.completed') }}</option>
                                    <option value="inprogress">{{ __('admin.rentals.inprogress') }}</option>
                                    <option value="pending">{{ __('admin.rentals.pending') }}</option>
                                    <option value="onhold">{{ __('admin.rentals.onhold') }}</option>
                                    <option value="rejected">{{ __('admin.rentals.rejected') }}</option>
                                </select>
                                <span class="text-danger error-text" id="inspection_status_error"></span>
                            </div>
                            <div class="col-md-6">
                                <label for="repair_status" class="form-label">{{ __('admin.rentals.repair_status') }} <em class="text-danger">*</em></label>
                                <select name="repair_status" id="repair_status" class="form-control select" data-placeholder="{{ __('admin.common.select') }}">
                                    <option value="">{{ __('admin.common.select') }}</option>
                                    <option value="completed">{{ __('admin.rentals.completed') }}</option>
                                    <option value="inprogress">{{ __('admin.rentals.inprogress') }}</option>
                                    <option value="pending">{{ __('admin.rentals.pending') }}</option>
                                    <option value="onhold">{{ __('admin.rentals.onhold') }}</option>
                                    <option value="rejected">{{ __('admin.rentals.rejected') }}</option>
                                </select>
                                <span class="text-danger error-text" id="repair_status_error"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="d-flex justify-content-end align-items-center w-100">
                            <div class="form-check form-check-md form-switch me-2 d-none" id="statusDiv">
                                <label for="status" class="form-check-label form-label mt-0 mb-0">
                                    <input class="form-check-input form-label me-2 status" id="status" type="checkbox" role="switch" checked>
                                    {{ __('admin.common.status') }}
                                </label>
                            </div>
                            <div class="d-flex justify-content-center">
                                <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                                <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.create_new') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Add Inspection Modal End-->

    <!-- Delete Modal Start-->
    <div class="modal fade deletemodal" id="delete-modal">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <form action="" id="deleteInspection">
                    @csrf
                    <input type="hidden" name="delete_id" id="delete_id">
                <div class="modal-body text-center">
                    <span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
                        <i class="ti ti-trash-x fs-26"></i>
                    </span>
                    <h4 class="mb-1">{{ __('admin.rentals.delete_inspection') }}</h4>
                    <p class="mb-3">{{ __('admin.rentals.delete_inspection_confirmation') }}</p>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
                        <button type="submit" class="btn btn-primary submitbtn">{{ __('admin.common.yes_delete') }}</button>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Delete Modal End -->
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/vehicleinfo/inspection.js') }}"></script>
@endpush