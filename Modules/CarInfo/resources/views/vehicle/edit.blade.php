@extends('admin.admin')

@section('meta_title', __('admin.rentals.edit_vehicle') . ' || ' . $companyName)

@section('content')
<div class="page-wrapper">
    <div class="content me-4 pb-0">
        <div class="mb-3">
            <a href="{{ route('vehicle.list') }}" class="d-inline-flex align-items-center fw-medium"><i
                    class="ti ti-arrow-left me-1"></i>{{ __('admin.common.back_to_list') }}</a>
        </div>
        <div class="card mb-0">
            <div class="card-body">
                <div class="add-wizard car-steps">
                    <ul class="nav d-flex align-items-center flex-wrap gap-3">
                        <li class="nav-item active" id="firstBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-info-circle me-1"></i>{{ __('admin.rentals.basic') }}
                            </button>
                        </li>
                        <li class="nav-item" id="secondBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-flame me-1"></i>{{ __('admin.rentals.features') }}
                            </button>
                        </li>
                        <li class="nav-item" id="thirdBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-files me-1"></i>{{ __('admin.rentals.pricing') }}
                            </button>
                        </li>
                        <li class="nav-item" id="forthBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-float-center me-1"></i>{{ __('admin.rentals.extra_services') }}
                            </button>
                        </li>
                        <li class="nav-item" id="fifthBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-file-invoice me-1"></i>{{ __('admin.rentals.uploads') }}
                            </button>
                        </li>
                        <li class="nav-item" id="sixthBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-id me-1"></i>{{ __('admin.rentals.damages') }}
                            </button>
                        </li>
                        <li class="nav-item" id="seventhBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-question-mark me-1"></i>{{ __('admin.cms.faq') }}
                            </button>
                        </li>
                        <li class="nav-item" id="eightBar">
                            <button type="button" class="nav-link d-flex align-items-center">
                                <i class="ti ti-seo me-1"></i>{{ __('admin.rentals.seo') }}
                            </button>
                        </li>
                    </ul>
                    <fieldset id="first-field">
                        <form id="carBasicInfoForm" autocomplete="off">
                            <input type="hidden" name="currency" id="currency" value="{{ $currencySymbol }}">
                            <input type="hidden" name="vehicle_id" id="vehicle_id" value="{{ $query->id }}">
                            <input type="hidden" name="language_id" id="language_id" value="{{ $query->language_id }}">
                            <input type="hidden" name="parent_id" id="parent_id" value="{{ $query->parent_id }}">
                            <div
                                class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-info-circle text-secondary me-2"></i>{{ __('admin.rentals.basic_info') }}
                                </h4>
                                @php
                                $currentLang = $allLanguages->firstWhere('code', app()->getLocale());
                                @endphp
                                @if ($allLanguages)
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ asset('backend/assets/img/flags/' . app()->getLocale() . '.svg') }}"
                                        class="img-fluid rounded-circle lang-flag" alt="Language">
                                    <select id="languageSelector" class="form-select w-auto">
                                        @foreach ($allLanguages as $language)
                                        <option value="{{ $language->id }}" data-code="{{ $language->code }}"
                                            {{ app()->getLocale() === $language->code ? 'selected' : '' }}>
                                            {{ $language->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                            </div>
                            <div class="border-bottom mb-4 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.featured_image') }}</h6>
                                        <p>{{ __('admin.rentals.upload_featured_image') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="d-flex align-items-center flex-wrap row-gap-3 upload-pic">
                                            <div
                                                class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 border rounded-circle frames">
                                                <img src="{{ $query->vehicle_image_url }}"
                                                    class="img-fluid rounded-circle" alt="Brands">
                                                <button type="button" id="delImg"
                                                    class="upload-img-trash trash-end btn btn-sm rounded-circle">
                                                    <i class="ti ti-trash fs-12"></i>
                                                </button>
                                            </div>
                                            <div>
                                                <div
                                                    class="drag-upload-btn btn btn-md btn-dark d-inline-flex align-items-center mb-2">
                                                    <i class="ti ti-photo me-1"></i>{{ __('admin.rentals.change') }}
                                                    <input type="file" name="vehicle_image" id="vehicle_image"
                                                        class="form-control image-sign">
                                                </div>
                                                <p>{{ __('admin.rentals.recommended_size') }}</p>
                                            </div>
                                        </div>
                                        <div id="vehicle_image_error_container" class="text-danger mt-1"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.vehicle_info') }}</h6>
                                        <p>{{ __('admin.rentals.add_vehicle_info') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">{{ __('admin.common.name') }}<span
                                                    class="text-danger">*</span></label>
                                            <input type="text" name="title" id="title" class="form-control"
                                                value="{{ $query->name }}">
                                            <span class="invalid-feedback" id="title_error"></span>
                                        </div>
                                        <div class="mb-3">
                                            <label for="perma_link"
                                                class="form-label">{{ __('admin.rentals.permalink') }}</label>
                                            <input type="text" class="form-control" id="perma_link" name="perma_link"
                                                value="{{ $query->perma_link }}">
                                            <p class="fs-13 fw-medium mt-1">{{ __('admin.rentals.preview') }} : <a
                                                    href="#" class="link-info">https://www.example.com/vehicles/</a></p>
                                        </div>
                                        <div class="row">
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_category_id"
                                                        class="form-label">{{ __('admin.rentals.category') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="vehicle_category_id" class="form-control select"
                                                        id="vehicle_category_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach($Category as $CategoryValues)
                                                        <option value="{{ $CategoryValues->id }}"
                                                            data-slug="{{ $CategoryValues->slug }}"
                                                            {{ $query->category_id == $CategoryValues->id ? 'selected' : '' }}>
                                                            {{ ucwords(strtolower($CategoryValues->name)) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback"
                                                        id="vehicle_category_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label for="vehicle_type_id"
                                                            class="form-label">{{ __('admin.rentals.vehicle_type') }}
                                                            <span class="text-danger">*</span></label>
                                                    </div>
                                                    <input type="hidden" id="type_id" value="{{ $query->type_id }}">
                                                    <select name="vehicle_type_id" class="form-control select"
                                                        id="vehicle_type_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                    </select>
                                                    <span class="invalid-feedback" id="vehicle_type_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label for="vehicle_brand_id"
                                                            class="form-label">{{ __('admin.rentals.brand') }} <span
                                                                class="text-danger">*</span></label>
                                                    </div>
                                                    <input type="hidden" id="brand_id" value="{{ $query->brand_id }}">
                                                    <select name="vehicle_brand_id" class="form-control select"
                                                        id="vehicle_brand_id">
                                                        <option value="">Select</option>
                                                    </select>
                                                    <span class="invalid-feedback" id="vehicle_brand_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <label for="vehicle_model_id"
                                                            class="form-label">{{ __('admin.rentals.model') }} <span
                                                                class="text-danger">*</span></label>
                                                    </div>
                                                    <select name="vehicle_model_id" class="form-control select"
                                                        id="vehicle_model_id">
                                                        <option value="">{{ __('admin.rentals.select_model') }}</option>
                                                        @foreach($Models as $ModelValues)
                                                        <option value="{{ $ModelValues->id }}"
                                                            {{ $query->model_id == $ModelValues->id ? 'selected' : '' }}>
                                                            {{ $ModelValues->model_name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback" id="vehicle_model_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="plate_number"
                                                        class="form-label">{{ __('admin.rentals.plate_number') }}</label>
                                                    <input type="text" name="plate_number" id="plate_number"
                                                        value="{{ $query->plate_number }}" class="form-control">
                                                    <span class="invalid-feedback" id="plate_number_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vin_number"
                                                        class="form-label">{{ __('admin.rentals.vin_number') }}</label>
                                                    <input type="text" name="vin_number" id="vin_number"
                                                        value="{{ $query->vin }}" class="form-control">
                                                    <span class="invalid-feedback" id="vin_number_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="main_location_id"
                                                        class="form-label">{{ __('admin.rentals.main_location') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="main_location_id" class="form-control select"
                                                        id="main_location_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach($Location as $LocationValues)
                                                        <option value="{{ $LocationValues->id }}"
                                                            {{ $query->main_location_id == $LocationValues->id ? 'selected' : '' }}>
                                                            {{ ucwords(strtolower($LocationValues->name )) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback" id="main_location_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="other_location_id"
                                                        class="form-label">{{ __('admin.rentals.link_other_location') }}</label>
                                                    <select class="form-control select" name="other_location_id[]"
                                                        id="other_location_id" multiple>
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @php
                                                        $selectedLocations = json_decode($query->other_location_id,
                                                        true) ?? [];
                                                        @endphp
                                                        @foreach($Location as $LocationValues)
                                                        <option value="{{ $LocationValues->id }}"
                                                            {{ in_array($LocationValues->id, $selectedLocations) ? 'selected' : '' }}>
                                                            {{ $LocationValues->name }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_fuel_id"
                                                        class="form-label">{{ __('admin.rentals.fuel') }}</label>
                                                    <select name="vehicle_fuel_id" class="form-control select"
                                                        id="vehicle_fuel_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach($CarFuel as $CarFuelValues)
                                                        <option value="{{ $CarFuelValues->id }}">
                                                            {{ $CarFuelValues->fuel_type }}</option>
                                                        @endforeach
                                                        @foreach($CarFuel as $CarFuelValues)
                                                        <option value="{{ $CarFuelValues->id }}"
                                                            {{ $query->fuel_type_id == $CarFuelValues->id ? 'selected' : '' }}>
                                                            {{ $CarFuelValues->fuel_type }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback" id="vehicle_fuel_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="odometer"
                                                        class="form-label">{{ __('admin.rentals.odometer') }}</label>
                                                    <input name="odometer" id="odometer" maxlength="5" type="text"
                                                        value="{{ $query->odometer }}" class="form-control">
                                                    <span class="invalid-feedback" id="odometer_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_color_id"
                                                        class="form-label">{{ __('admin.rentals.color') }} <span
                                                            class="text-danger">*</span></label>
                                                    <select name="vehicle_color_id"
                                                        class="form-control select slectedColor" id="vehicle_color_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach($CarColor as $CarColorValues)
                                                        <option value="{{ $CarColorValues->id }}"
                                                            {{ $query->color_id == $CarColorValues->id ? 'selected' : '' }}>
                                                            {{ ucwords(strtolower($CarColorValues->name)) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                    <span class="invalid-feedback" id="vehicle_color_id_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_year"
                                                        class="form-label">{{ __('admin.rentals.year') }} <span
                                                            class="text-danger">*</span></label>
                                                    <div class="input-icon-end position-relative">
                                                        <input type="text" name="vehicle_year" id="vehicle_year"
                                                            value="{{ $query->year }}"
                                                            class="form-control yearpickerVehicle">
                                                        <span class="input-icon-addon">
                                                            <i class="ti ti-calendar"></i>
                                                        </span>
                                                    </div>
                                                    <span class="invalid-feedback" id="vehicle_year_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_transmission_id"
                                                        class="form-label">{{ __('admin.rentals.transmission') }}</label>
                                                    <select name="vehicle_transmission_id" class="form-control select"
                                                        id="vehicle_transmission_id">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach($Transmission as $TransmissionValues)
                                                        <option value="{{ $TransmissionValues->id }}"
                                                            {{ $query->transmission_id == $TransmissionValues->id ? 'selected' : '' }}>
                                                            {{ ucwords(strtolower($TransmissionValues->name )) }}
                                                        </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_mileage"
                                                        class="form-label">{{ __('admin.rentals.mileage') }}</label>
                                                    <input name="vehicle_mileage" maxlength="2" id="vehicle_mileage"
                                                        value="{{ $query->mileage !== null ? (int) $query->mileage : '' }}"
                                                        type="text" class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6">
                                                <div class="mb-3">
                                                    <label for="vehicle_passenger"
                                                        class="form-label">{{ __('admin.dashboard.passengers') }} <span
                                                            class="text-danger">*</span></label></label>
                                                    <input name="vehicle_passenger" maxlength="2" id="vehicle_passenger"
                                                        value="{{ $query->passenger_capacity }}" type="text"
                                                        class="form-control">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 CarContain">
                                                <div class="mb-3">
                                                    <label for="num_seats"
                                                        class="form-label">{{ __('admin.rentals.no_of_seats') }}</label>
                                                    <select name="num_seats" class="form-control select" id="num_seats">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @foreach ([2, 4, 5, 6, 7, 8, 10, 12, 14] as $seat)
                                                        <option value="{{ $seat }}"
                                                            {{ $query->num_seats == $seat ? 'selected' : '' }}>
                                                            {{ $seat }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 CarContain car-doors-field">
                                                <div class="mb-3">
                                                    <label for="num_doors"
                                                        class="form-label">{{ __('admin.rentals.no_of_doors') }}</label>
                                                    <select name="num_doors" class="form-control select" id="num_doors">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @for ($i = 2; $i <= 10; $i++) <option value="{{ $i }}"
                                                            {{ $query->num_doors == $i ? 'selected' : '' }}>{{ $i }}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 CarContain">
                                                <div class="mb-3">
                                                    <label for="num_airbags"
                                                        class="form-label">{{ __('admin.rentals.no_of_airbags') }}</label>
                                                    <input type="text" class="form-control" maxlength="1"
                                                        name="num_airbags" id="num_airbags"
                                                        value="{{ $query->num_airbags }}">
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 BoatContain">
                                                <div class="mb-3">
                                                    <label for="water_tight"
                                                        class="form-label">{{ __('admin.rentals.water_tight') }}</label>
                                                    <select name="water_tight" class="form-control select"
                                                        id="water_tight">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @for ($i = 1; $i <= 20; $i++) <option value="{{ $i }}"
                                                            {{ $query->water_tight == $i ? 'selected' : '' }}>{{ $i }}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 BoatContain">
                                                <div class="mb-3">
                                                    <label for="sliding"
                                                        class="form-label">{{ __('admin.rentals.sliding') }}</label>
                                                    <select name="sliding" class="form-control select" id="sliding">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @for ($i = 1; $i <= 20; $i++) <option value="{{ $i }}"
                                                            {{ $query->sliding == $i ? 'selected' : '' }}>{{ $i }}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-4 col-md-6 BoatContain">
                                                <div class="mb-3">
                                                    <label for="hatch"
                                                        class="form-label">{{ __('admin.rentals.hatch') }}</label>
                                                    <select name="hatch" class="form-control select" id="hatch">
                                                        <option value="">{{ __('admin.rentals.select') }}</option>
                                                        @for ($i = 1; $i <= 20; $i++) <option value="{{ $i }}"
                                                            {{ $query->hatch == $i ? 'selected' : '' }}>{{ $i }}
                                                            </option>
                                                            @endfor
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg-12 col-md-6">
                                                <div class="mb-3">
                                                    <label for="description"
                                                        class="form-label">{{ __('admin.common.description') }}</label>
                                                    <textarea type="text" class="form-control summernote"
                                                        maxlength="500" placeholder="Enter description"
                                                        name="description"
                                                        id="description">{{ $query->description }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-light d-flex align-items-center me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.cancel') }}</button>
                                <button id="featAmenNext" type="button"
                                    class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_features') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="second-field">
                        <form id="featuresForm">
                            <div
                                class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-flame text-secondary me-2"></i>{{ __('admin.rentals.features_and_amenities') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-2 pb-2 amenity-wrap">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.features_and_amenities') }}</h6>
                                        <p>{{ __('admin.rentals.add_vehicle_info') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input select-all" type="checkbox"
                                                            id="select-all1">
                                                        <label for="select-all1" class="form-check-label" for="amenity">
                                                            Check All
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                            @foreach($SafetyFeature as $SafetyFeatureValues)
                                            <div class="col-lg-4 col-md-6 cursor-pointer">
                                                <div class="mb-3 position-relative vechicle-add">
                                                    <div class="form-check form-checkbox mb-0">
                                                        <input class="form-check-input" name="feature_id[]"
                                                            type="checkbox" value="{{ $SafetyFeatureValues->id }}"
                                                            id="feature_id_{{ $SafetyFeatureValues->id }}"
                                                            {{ in_array($SafetyFeatureValues->id, $selectedFeatures ?? []) ? 'checked' : '' }}>
                                                    </div>
                                                    <label class="form-check-label"
                                                        for="feature_id_{{ $SafetyFeatureValues->id }}">
                                                        {{ $SafetyFeatureValues->feature }}
                                                    </label>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button id="priceTariffNext" type="button"
                                    class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.add_tariff_pricing') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="third-field">
                        <form id="priceTariffForm">
                            <div
                                class="filterbox p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-files text-secondary me-2"></i>{{ __('admin.rentals.pricing_and_tariff') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-4 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.back') }}</h6>
                                        <p>{{ __('admin.rentals.add_pricing_for_vehicle') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            @php
                                            $dailyPrice = $vehiclePrices['daily'] ?? 0;
                                            $weeklyPrice = $vehiclePrices['weekly'] ?? 0;
                                            $monthlyPrice = $vehiclePrices['monthly'] ?? 0;
                                            $yearlyPrice = $vehiclePrices['yearly'] ?? 0;
                                            @endphp
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <div class="form-label">{{ __('admin.rentals.pricing_type') }} <span
                                                            class="text-danger">*</span></div>
                                                    <div class="d-flex align-items-center flex-wrap gap-3">
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input price-checkbox"
                                                                type="checkbox" name="daily" id="daily"
                                                                {{ $dailyPrice > 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="daily">{{ __('admin.rentals.daily') }}</label>
                                                        </div>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input price-checkbox"
                                                                type="checkbox" name="weekly" id="weekly"
                                                                {{ $weeklyPrice > 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="weekly">{{ __('admin.rentals.weekly') }}</label>
                                                        </div>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input price-checkbox"
                                                                type="checkbox" name="monthly" id="monthly"
                                                                {{ $monthlyPrice > 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="monthly">{{ __('admin.rentals.monthly') }}</label>
                                                        </div>
                                                        <div class="form-check mb-0">
                                                            <input class="form-check-input price-checkbox"
                                                                type="checkbox" name="yearly" id="yearly"
                                                                {{ $yearlyPrice > 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label"
                                                                for="yearly">{{ __('admin.rentals.yearly') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label for="daily_price"
                                                        class="form-label">{{ __('admin.rentals.daily_price') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control price-input priceLimit"
                                                        name="daily_price" id="daily_price"
                                                        value="{{ $dailyPrice > 0 ? $dailyPrice : '' }}"
                                                        placeholder="{{ __('admin.rentals.enter_daily_price') }}"
                                                        {{ $dailyPrice > 0 ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label for="weekly_price"
                                                        class="form-label">{{ __('admin.rentals.weekly_price') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control price-input priceLimit"
                                                        name="weekly_price" id="weekly_price"
                                                        value="{{ $weeklyPrice > 0 ? $weeklyPrice : '' }}"
                                                        placeholder="{{ __('admin.rentals.enter_weekly_price') }}"
                                                        {{ $weeklyPrice > 0 ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label for="monthly_price"
                                                        class="form-label">{{ __('admin.rentals.monthly_price') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control price-input priceLimit"
                                                        name="monthly_price" id="monthly_price"
                                                        value="{{ $monthlyPrice > 0 ? $monthlyPrice : '' }}"
                                                        placeholder="{{ __('admin.rentals.enter_monthly_price') }}"
                                                        {{ $monthlyPrice > 0 ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-md-6">
                                                <div class="mb-3">
                                                    <label for="yearly_price"
                                                        class="form-label">{{ __('admin.rentals.yearly_price') }} <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control price-input priceLimit"
                                                        name="yearly_price" id="yearly_price"
                                                        value="{{ $yearlyPrice > 0 ? $yearlyPrice : '' }}"
                                                        placeholder="{{ __('admin.rentals.enter_yearly_price') }}"
                                                        {{ $yearlyPrice > 0 ? '' : 'disabled' }}>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="form-label">
                                                            {{ __('admin.rentals.base_kilometers_per_day') }} <span
                                                                class="text-danger">*</span></div>
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="checkbox"
                                                                value="{{ $query->vehicle_basekm }}" name="unlimited"
                                                                id="Baseunlimited"
                                                                {{ is_null($query->vehicle_basekm) || $query->vehicle_basekm == 0 ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="Baseunlimited">
                                                                {{ __('admin.rentals.unlimited') }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <input type="text" name="basic_kilometer" id="basic_kilometer"
                                                        value="{{ $query->vehicle_basekm }}"
                                                        class="form-control priceLimit">
                                                    <span class="invalid-feedback" id="basic_kilometer_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6">
                                                <div class="mb-3">
                                                    <label for="extra_kilometer"
                                                        class="form-label">{{ __('admin.rentals.extra_kilometers_price') }}
                                                        <span class="text-danger">*</span></label>
                                                    <input type="text" name="extra_kilometer" id="extra_kilometer"
                                                        value="{{ $query->vehicle_extrakmprice }}"
                                                        class="form-control priceLimit">
                                                </div>
                                                <span class="invalid-feedback" id="extra_kilometer_error"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-4 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.tariff') }}</h6>
                                        <p>{{ __('admin.rentals.add_tariff_pricing_for_vehicle') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div
                                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                            <button type="button" class="btn btn-dark btn-md d-flex align-items-center"
                                                id="add_tariff_btn" data-bs-toggle="modal"
                                                data-bs-target="#add-tarrif"><i
                                                    class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_new_tariff_rate') }}</button>
                                        </div>
                                        <div class="card bg-light mb-3">
                                            <div class="card-body pb-3" id="tariff_append">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-4 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.seasonal_pricing') }}</h6>
                                        <p>{{ __('admin.rentals.add_seasonal_pricing_for_vehicle') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div
                                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                            <button type="button" class="btn btn-dark btn-md d-flex align-items-center"
                                                id="add_seasonal_price_btn" data-bs-toggle="modal"
                                                data-bs-target="#add_price"><i
                                                    class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_new_seasonal_pricing') }}</button>
                                        </div>
                                        <div class="empty-data bg-light text-center mb-3">
                                        </div>
                                        <div class="card bg-light mb-3">
                                            <div class="card-body pb-3" id="seasonal_append">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.insurance') }}</h6>
                                        <p>{{ __('admin.rentals.add_insurance_for_vehicle') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div
                                            class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                            <button type="button" class="btn btn-dark btn-md d-flex align-items-center"
                                                data-bs-toggle="modal" data-bs-target="#select_insurance"><i
                                                    class="ti ti-plus me-1"></i>{{ __('admin.rentals.select_new_insurance') }}</button>
                                        </div>
                                        <div class="empty-data bg-light text-center mb-3">
                                            <p class="fw-medium">{{ __('admin.rentals.no_insurance_data') }}</p>
                                        </div>
                                        <div class="card bg-light mb-3">
                                            <div class="card-body pb-3" id="insurance_car_append">
                                                <p class="fw-medium">{{ __('admin.rentals.no_insurance_data') }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button id="extraServiceNext" type="button" id="extraServiceNext"
                                    class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_extra_services') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="forth-field">
                        <form id="extraServiceForm">
                            <div
                                class="filterbox p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-float-center text-secondary me-2"></i>{{ __('admin.rentals.extra_services') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-2 pb-1 extra-service">
                                <div class="text-end">
                                    <a href="#" class="link-purple text-decoration-underline fw-medium d-inline-block"
                                        data-bs-toggle="modal"
                                        data-bs-target="#edit_price">{{ __('admin.rentals.edit_price') }}</a>
                                </div>
                                <div class="row">
                                    @foreach ($ExtraServices as $service)
                                    @php
                                    $serviceInfo = $ExtraServiceInfo->firstWhere('extra_service_id', $service->id);
                                    $serviceValue = $serviceInfo->value ?? 'one_time';
                                    $servicePrice = $serviceInfo->price ?? '00.00';

                                    $formattedServiceValue = match ($serviceValue) {
                                    'per_day' => __('admin.rentals.per_day'),
                                    'one_time' => __('admin.rentals.one_time'),
                                    default => ucfirst(str_replace('_', ' ', $serviceValue)),
                                    };
                                    @endphp
                                    <div class="col-xxl-4 col-md-6 d-flex">
                                        <div
                                            class="form-check extra-service-card form-checkbox d-flex align-items-center justify-content-between flex-wrap gap-3 flex-fill">
                                            <div class="d-flex align-items-center">
                                                <input type="checkbox" id="extra_service_{{ $service->id }}"
                                                    name="extra_service[]" value="{{ $service->id }}"
                                                    @if($ExtraServiceInfo->contains('extra_service_id', $service->id))
                                                checked @endif>
                                                <input class="form-check-input service_id" type="hidden"
                                                    id="service_id_{{ $service->id }}" name="service_id[]"
                                                    value="{{ $service->id }}">
                                                <span
                                                    class="service-icon bg-dark d-flex align-items-center justify-content-center me-2 ms-2">
                                                    <img src="{{ asset('storage/' . $service->icon) }}"
                                                        alt="Extra Service">
                                                </span>
                                                <div>
                                                    <h6 class="fs-14 fw-semibold mb-1" id="service_name">
                                                        {{ $service->name }}</h6>
                                                    <p class="fs-13">{{ $service->description }}</p>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="fs-13 mb-1" id="set_value">{{ $formattedServiceValue }}</p>
                                                <input type="hidden" class="service_value" name="service_value[]"
                                                    id="service_value_{{ $service->id }}" value="{{ $serviceValue }}">
                                                <h6 class="fs-14 fw-semibold" id="set_price">
                                                    {{ $currencySymbol }}{{ number_format($servicePrice, 2) }}</h6>
                                                <input type="hidden" class="service_price" name="service_price[]"
                                                    id="service_price_{{ $service->id }}" value="{{ $servicePrice }}">
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button type="button"
                                    class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.upload_documents') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="fifth-field">
                        <form id="carDocumentForm">
                            <div
                                class="filterbox p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-file-invoice text-secondary me-2"></i>{{ __('admin.rentals.documents') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-4 pb-3">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.vehicle_documents') }}</h6>
                                        <p>{{ __('admin.rentals.vehicle_documents_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-xxl-8 col-lg-10">
                                                <h6 class="mb-3">{{ __('admin.rentals.upload_document') }}</h6>
                                                <div class="document-upload text-center bg-light br-5 mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}"
                                                        class="mb-2" alt="img">
                                                    <p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span
                                                            class="text-info text-decoration-underline"></span></p>
                                                    <p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
                                                    <input type="file" class="form-control image-sign"
                                                        name="car_document[]" id="car_document" multiple>
                                                </div>
                                                <div class="mb-3">
                                                    <p class="fs-13 mb-1">
                                                        {{ __('admin.rentals.upload_insurance_registration') }}</p>
                                                    <p class="fs-13">{{ __('admin.rentals.supported_formats') }}</p>
                                                </div>
                                                <div id="car_doc_append"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-4 pb-3">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.policies') }}</h6>
                                        <p>{{ __('admin.rentals.add_policies_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-xxl-8 col-lg-10">
                                                <h6 class="mb-3">{{ __('admin.rentals.upload_policy') }}</h6>
                                                <div class="document-upload text-center bg-light br-5 mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}"
                                                        class="mb-2" alt="img">
                                                    <p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span
                                                            class="text-info text-decoration-underline"></span></p>
                                                    <p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
                                                    <input type="file" class="form-control image-sign"
                                                        name="policy_document[]" id="policy_document" multiple>
                                                </div>
                                                <div class="mb-3">
                                                    <p class="fs-13 mb-1">
                                                        {{ __('admin.rentals.upload_car_policy_documents') }}</p>
                                                    <p class="fs-13">{{ __('admin.rentals.supported_formats') }}</p>
                                                </div>
                                                <div id="car_policy_append">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-4 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.image') }}</h6>
                                        <p>{{ __('admin.rentals.add_image_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-xxl-8 col-lg-10">
                                                <h6 class="mb-3">{{ __('admin.rentals.upload_image') }}</h6>
                                                <div class="document-upload text-center bg-light br-5 mb-3">
                                                    <img src="{{ asset('backend/assets/img/icons/upload-icon.svg') }}"
                                                        class="mb-2" alt="img">
                                                    <p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span
                                                            class="text-info text-decoration-underline"></span></p>
                                                    <p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
                                                    <input type="file" class="form-control image-sign"
                                                        name="car_images[]" id="car_images" multiple>
                                                </div>
                                                <div class="d-flex align-items-center flex-wrap gap-3"
                                                    id="car_images_append">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="border-bottom mb-2 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.video') }}</h6>
                                        <p>{{ __('admin.rentals.add_video_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-4">
                                                    <label for="platform" class="form-label">{{ __('admin.rentals.platform') }} <span
                                                            class="text-danger"></span></label>
                                                    <select class="select">
                                                        <option>{{ __('admin.rentals.youtube') }}</option>
                                                        <option>{{ __('admin.rentals.vimeo') }}</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="mb-4">
                                                    <label for="car_video"
                                                        class="form-label">{{ __('admin.rentals.video_link') }}</label>
                                                    <input type="text" class="form-control" name="car_video"
                                                        id="car_video" value="{{ $query->vehicle_video }}"
                                                        placeholder="https://www.youtube.com/watch?v=abcd1234">
                                                    <span class="invalid-feedback" id="car_video_error"></span>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="uploaded-video" id="car_video_append">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button id="carDamageNext" type="button"
                                    class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_damage') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="sixth-field">
                        <form id="carDamageForm">
                            <div
                                class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-id text-secondary me-2"></i>{{ __('admin.rentals.damages_title') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-2 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.damages_title') }}</h6>
                                        <p>{{ __('admin.rentals.damages_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <button type="button"
                                            class="btn btn-dark btn-md d-inline-flex align-items-center mb-3"
                                            data-bs-toggle="modal" data-bs-target="#add-damage" id="damage_car"><i
                                                class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_damage_button') }}
                                        </button>
                                        <div class="card border-0 bg-light mb-0">
                                            <div class="card-body">
                                                <div
                                                    class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
                                                    <h6>{{ __('admin.rentals.total_damages') }} : <span
                                                            id="damage_count">00</span></h6>
                                                </div>
                                                <div id="car_damage_append">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button id="faqNext" type="button"
                                    class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.add_faq_button') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset id="seventh-field">
                        <form id="carFaqForm">
                            <div
                                class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-question-mark text-secondary me-2"></i>{{ __('admin.rentals.faq_title') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-2 pb-4">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.faq_title') }}</h6>
                                        <p>{{ __('admin.rentals.faq_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <button type="button"
                                            class="btn btn-dark btn-md d-inline-flex align-items-center mb-3"
                                            id="add_faq_btn" data-bs-toggle="modal" data-bs-target="#add-faq"><i
                                                class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_faq_button') }}</button>
                                        <div class="card border-0 bg-light mb-0">
                                            <div class="card-body">
                                                <h6 class="mb-3">{{ __('admin.rentals.total_faq') }} : <span
                                                        id="faq_count">00</span></h6>
                                                <div class="faq-accordion car_faq_append" id="faqaccordion">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }} </button>
                                <button type="button"
                                    class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.add_seo') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                    <fieldset>
                        <form id="carSeoForm">
                            <div
                                class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
                                <h4 class="d-flex align-items-center"><i
                                        class="ti ti-question-mark text-secondary me-2"></i>{{ __('admin.rentals.seo_title') }}
                                </h4>
                            </div>
                            <div class="border-bottom mb-2 pb-2">
                                <div class="row row-gap-4">
                                    <div class="col-xl-3">
                                        <h6 class="mb-1">{{ __('admin.rentals.seo_title') }}</h6>
                                        <p>{{ __('admin.rentals.seo_description') }}</p>
                                    </div>
                                    <div class="col-xl-9">
                                        <div class="mb-3">
                                            <label for="seo_title"
                                                class="form-label">{{ __('admin.rentals.seo_meta_title_label') }}</label>
                                            <input type="text" name="seo_title" id="seo_title"
                                                value="{{ $query->vehicle_metatitle }}" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="seo_key"
                                                class="form-label">{{ __('admin.rentals.seo_keywords_label') }}</label>
                                            <input type="text" name="seo_key" value="{{ $query->vehicle_metakeywords }}"
                                                id="seo_key" class="form-control">
                                        </div>
                                        <div class="mb-3">
                                            <label for="seo_description"
                                                class="form-label">{{ __('admin.rentals.seo_description_label') }}</label>
                                            <textarea class="form-control" name="seo_description" id="seo_description"
                                                rows="3">{{ $query->vehicle_metakeywords }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-end pt-3">
                                <button type="button" class="btn btn-outline-light border wizard-prev me-2"><i
                                        class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
                                <button id="seoFinalBtn" type="submit"
                                    class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.save_exit_button') }}<i
                                        class="ti ti-chevron-right ms-1"></i></button>
                            </div>
                        </form>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</div>

<x-admin.modal className="addmodal" id="add-tarrif" :title="__('admin.rentals.create_tariff')"
    modalTitleId="tarrif_title">
    <x-slot name="body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="t_name" class="form-label">{{ __('admin.rentals.tariff_name') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="t_name" id="t_name" maxlength="50" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="t_price" class="form-label">{{ __('admin.rentals.daily_price') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="t_price" id="t_price" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="t_fromday" class="form-label">{{ __('admin.rentals.from_days') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="t_fromday" id="t_fromday" maxlength="10" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="t_today" class="form-label">{{ __('admin.rentals.to_days') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="t_today" id="t_today" maxlength="10" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <label for="t_base" class="form-label">{{ __('admin.rentals.base_km_per_day') }} <span
                                class="text-danger">*</span></label>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="unlimited1" id="unlimited1">
                            <label class="form-check-label" for="unlimited1">
                                {{ __('admin.rentals.unlimited') }}
                            </label>
                        </div>
                    </div>
                    <input type="text" name="t_base" id="t_base" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="t_extra" class="form-label">{{ __('admin.rentals.km_extra_price') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" id="t_extra" name="t_extra" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="button" class="btn btn-primary"
                id="tarrif_btn">{{ __('admin.rentals.create_tariff') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.delete-modal :config="[
    'className'      => 'deletemodal',
    'id'             => 'delete_tarrif',
    'title'          => __('admin.rentals.delete_tariff'),
    'description'    => __('admin.rentals.delete_tariff_confirmation'),
    'deleteBtnType'  => 'button'
]"/>

<x-admin.modal className="addmodal" id="add_price" :title="__('admin.rentals.create_seasonal_price')"
    modalTitleId="seas_title">
    <x-slot name="body">
        <div class="row">
            <div class="col-md-12">
                <div class="mb-3">
                    <label for="s_name" class="form-label">{{ __('admin.rentals.season_name') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="s_name" id="s_name" maxlength="50" class="form-control">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_strdate" class="form-label">{{ __('admin.rentals.start_date') }} <span
                            class="text-danger">*</span></label>
                    <div class="input-icon-end position-relative">
                        <input type="text" name="s_strdate" id="s_strdate" class="form-control datetimepickerVehicle"
                            placeholder="dd/mm/yyyy">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_enddate" class="form-label">{{ __('admin.rentals.end_date') }} <span
                            class="text-danger">*</span></label>
                    <div class="input-icon-end position-relative">
                        <input type="text" name="s_enddate" id="s_enddate" class="form-control datetimepickerVehicle"
                            placeholder="dd/mm/yyyy">
                        <span class="input-icon-addon">
                            <i class="ti ti-calendar"></i>
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_drate" class="form-label">{{ __('admin.rentals.daily_rate') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="s_drate" id="s_drate" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_wrate" class="form-label">{{ __('admin.rentals.weekly_rate') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="s_wrate" id="s_wrate" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_mrate" class="form-label">{{ __('admin.rentals.monthly_rate') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="s_mrate" id="s_mrate" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <label for="s_lrate" class="form-label">{{ __('admin.rentals.late_fees') }} <span
                            class="text-danger">*</span></label>
                    <input type="text" name="s_lrate" id="s_lrate" maxlength="5" class="form-control priceLimit">
                </div>
            </div>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="price_btn">{{ __('admin.common.create_new') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.delete-modal :config="[
    'className'      => 'deletemodal',
    'id'             => 'delete_price',
    'title'          => __('admin.rentals.delete_pricing'),
    'description'    => __('admin.rentals.delete_pricing_confirmation'),
    'deleteBtnType'  => 'button'
]"/>

<x-admin.modal className="addmodal" id="edit_price" :title="__('admin.rentals.edit_pricing_title')">
    <x-slot name="body">
        <table class="table custom-table1">
            <thead class="thead-white">
                <tr>
                    <th class="py-0">{{ __('admin.rentals.extra_features') }}</th>
                    <th class="py-0">{{ __('admin.rentals.pricing') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($ExtraServices as $service)
                @php
                $serviceInfo = $ExtraServiceInfo->firstWhere('extra_service_id', $service->id);
                $selectedValue = $serviceInfo->value ?? 'per_day';
                $selectedPrice = $serviceInfo->price ?? '00.00';
                @endphp
                <tr>
                    <td class="fw-medium text-gray-9" id="extra_name">{{ $service->name }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <select class="form-control extra_value" id="extra_value_{{ $service->id }}"
                                name="extra_value[{{ $service->id }}]">
                                <option value="per_day" {{ $selectedValue == 'per_day' ? 'selected' : '' }}>
                                    {{ __('admin.rentals.per_day') }}</option>
                                <option value="one_time" {{ $selectedValue == 'one_time' ? 'selected' : '' }}>
                                    {{ __('admin.rentals.one_time') }}</option>
                            </select>
                            <div class="input-icon-start position-relative w-100 ms-2">
                                <span class="input-icon-addon">
                                    <i class="ti ti-currency-dollar"></i>
                                </span>
                                <input type="text" class="form-control extra_price" id="extra_price_{{ $service->id }}"
                                    name="extra_price[{{ $service->id }}]"
                                    value="{{ number_format($selectedPrice, 2) }}">
                            </div>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</button>
            <button type="button" class="btn btn-primary"
                id="service_save_btn">{{ __('admin.common.save_changes') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.modal className="addmodal" id="add-damage" :title="__('admin.rentals.add_damage')" modalTitleId="damage_title">
    <x-slot name="body">
        <div class="mb-3">
            <label for="dam_image" class="form-label">{{ __('admin.rentals.damage_image_label') }} <span
                    class="text-danger">*</span></label>
            <input type="file" name="dam_image" id="dam_image" class="form-control">
            <img src="{{ uploadedAsset('', 'default') }}" class="mt-2 d-none" id="image_preview" alt="Damage Preview">
        </div>
        <div class="mb-3">
            <label for="dam_name" class="form-label">{{ __('admin.rentals.damage_location_label') }} <span
                    class="text-danger">*</span></label>
            <select class="form-control custom-select" name="dam_name" id="dam_name">
                <option>{{ __('admin.rentals.select') }}</option>
                <option>{{ __('admin.rentals.interior') }}</option>
                <option>{{ __('admin.rentals.exterior') }}</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="dam_type" class="form-label">{{ __('admin.rentals.damage_type_label') }} <span
                    class="text-danger">*</span></label>
            <select class="form-control custom-select" name="dam_type" id="dam_type">
                <option value="">{{ __('admin.rentals.select') }}</option>
                @foreach($DamageTypes as $DamageTypesValue)
                <option class="{{ $DamageTypesValue->id }}">{{ $DamageTypesValue->damage_type }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="dam_dis" class="form-label">{{ __('admin.rentals.description_label') }}</label>
            <textarea class="form-control" name="dam_dis" id="dam_dis" maxlength="120" rows="3"></textarea>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="damage_btn">{{ __('admin.common.create_new') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.delete-modal :config="[
    'className'      => 'deletemodal',
    'id'             => 'delete_damage',
    'title'          => __('admin.rentals.delete_damage_title'),
    'description'    => __('admin.rentals.delete_damage_confirmation'),
    'deleteBtnType'  => 'button',
    'deleteBtnId'    => 'dete-damage'
]"/>

<x-admin.modal className="addmodal" id="add-faq" :title="__('admin.rentals.create_faq_title')" modalTitleId="faq_title">
    <x-slot name="body">
        <div class="mb-3">
            <label for="f_q" class="form-label">{{ __('admin.rentals.question_label') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="f_q" id="f_q" maxlength="60" class="form-control">
        </div>
        <div class="mb-3">
            <label for="f_a" class="form-label">{{ __('admin.rentals.answer_label') }} <span
                    class="text-danger">*</span></label>
            <textarea class="form-control" name="f_a" id="f_a" maxlength="120" rows="3"></textarea>
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="faq_btn">{{ __('admin.common.create_new') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.delete-modal :config="[
    'className'      => 'deletemodal',
    'id'             => 'delete_faq',
    'title'          => __('admin.rentals.delete_faq_title'),
    'description'    => __('admin.rentals.delete_faq_confirmation'),
    'deleteBtnType'  => 'button',
    'deleteBtnId'    => 'dete-faq'
]"/>

<x-admin.modal className="addmodal" id="select_insurance" :title="__('admin.rentals.select_insurance')"
    formId="set_value">
    <x-slot name="body">
        @foreach($insurances as $insurance)
        <div class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-3"
            id="inCont">
            <input type="hidden" class="insurance_id" id="insurance_id_{{ $insurance->id }}"
                value="{{ $insurance->id }}">
            <div>
                <h6 class="fs-14 fw-semibold d-inline-flex align-items-center mb-1">
                    {{ $insurance->insurance_name }}
                </h6>
                <input type="hidden" class="insurance_name" id="insurance_name_{{ $insurance->id }}"
                    value="{{ $insurance->insurance_name }}">
                <input type="hidden" class="insurance_price_type" id="insurance_price_type_{{ $insurance->id }}"
                    value="{{ $insurance->priceType->pricing_type }}">
                <input type="hidden" class="insurance_price_type_id" id="insurance_price_type_id_{{ $insurance->id }}"
                    value="{{ $insurance->price_type_id }}">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <p class="fs-13 fw-medium border-end pe-2 mb-0">
                        {{ __('admin.common.price') }}: <span class="text-gray-9">
                            @if ($insurance->price_type_id == 7)
                            {{ rtrim(rtrim(number_format($insurance->price, 2), '0'), '.') }}%
                            @else
                            {{ $currencySymbol }}{{ number_format($insurance->price, 2) }}
                            @endif</span>
                        <input type="hidden" class="insurance_price" id="insurance_price_{{ $insurance->id }}"
                            value="{{ $insurance->price }}">
                    </p>
                    <p class="fs-13 fw-medium mb-0">
                        {{ __('admin.common.benefits') }}: <span
                            class="text-gray-9">{{ $insurance->insuranceBenefits->count() }}</span>
                        <input type="hidden" class="insurance_count" id="insurance_count_{{ $insurance->id }}"
                            value="{{ $insurance->insuranceBenefits->count() }}">
                        @if($insurance->insuranceBenefits->isNotEmpty())
                        <i class="ti ti-info-circle-filled text-gray-5 ms-1" data-bs-toggle="tooltip"
                            data-bs-placement="top" title="{{ $insurance->insuranceBenefits->first()->benefit }}">
                        </i>
                        @endif
                    </p>
                </div>
            </div>
            <div class="d-flex align-items-center icon-list delivery-add">
                <button type="button" class="bg-transparent border-0">
                    <i class="ti ti-plus plus-active"></i>
                    <i class="ti ti-check check-active d-none"></i>
                </button>
                <input type="checkbox" class="insurance_checked" id="insurance_checked_{{ $insurance->id }}" hidden>
            </div>
        </div>
        @endforeach
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="in_btn">{{ __('admin.general_settings.add') }}</button>
        </div>
    </x-slot>
</x-admin.modal>

<x-admin.modal className="addmodal" id="edit_insurance" :title="__('admin.rentals.edit_insurance')">
    <x-slot name="body">
        <div class="mb-3">
            <div class="form-label">{{ __('admin.common.price_type') }} <span class="text-danger"> *</span></div>
            <div class="d-flex align-items-center">
                <div class="form-check me-3">
                    <input class="form-check-input" type="radio" name="Radio" id="Radio-sm" checked>
                    <label class="form-check-label" for="Radio-sm">
                        {{ __('admin.rentals.daily') }}
                    </label>
                </div>
                <div class="form-check me-3">
                    <input class="form-check-input" type="radio" name="Radio" id="Radio-sm2">
                    <label class="form-check-label" for="Radio-sm2">
                        {{ __('admin.rentals.fixed') }}
                    </label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="Radio" id="Radio-sm3">
                    <label class="form-check-label" for="Radio-sm3">
                        {{ __('admin.common.percentage') }}
                    </label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="price" class="form-label">{{ __('admin.common.price') }} <span class="text-danger">
                    *</span></label>
            <input type="text" class="form-control priceLimit" id="price" maxlength="5" value="">
        </div>
    </x-slot>
    <x-slot name="footer">
        <div class="d-flex justify-content-center">
            <button type="button" class="btn btn-light me-3"
                data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</button>
            <button type="button" class="btn btn-primary" id="save_update">{{ __('admin.common.update') }}</button>
        </div>
    </x-slot>
</x-admin.modal>
@endsection

@push('scripts')
<script src="{{ asset('backend/assets/js/edit-vehicle.js') }}"></script>
@endpush
