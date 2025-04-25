@extends('admin.admin')
@section('content')

<div class="page-wrapper">
	<div class="content me-4 pb-0">
		<div class="mb-3">
			<a href="{{ route('vehicle.list') }}">
				<i class="ti ti-arrow-left me-1"></i>{{ __('admin.rentals.back_to_list') }}
			</a>
		</div>
		<div class="card mb-0">
			<div class="card-body">
				<div class="add-wizard car-steps">
					<ul class="nav d-flex align-items-center flex-wrap gap-3">
						<li class="nav-item active" id="firstBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-info-circle me-1"></i>{{ __('admin.rentals.basic') }}
							</a>
						</li>
						<li class="nav-item" id="secondBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-flame me-1"></i>{{ __('admin.rentals.features') }}
							</a>
						</li>
						<li class="nav-item" id="thirdBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-files me-1"></i>{{ __('admin.rentals.pricing') }}
							</a>
						</li>
						<li class="nav-item" id="forthBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-float-center me-1"></i>{{ __('admin.rentals.extra_services') }}
							</a>
						</li>
						<li class="nav-item" id="fifthBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-file-invoice me-1"></i>{{ __('admin.rentals.uploads') }}
							</a>
						</li>
						<li class="nav-item" id="sixthBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-id me-1"></i>{{ __('admin.rentals.damages') }}
							</a>
						</li>
						<li class="nav-item" id="seventhBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-question-mark me-1"></i>{{ __('admin.rentals.faq') }}
							</a>
						</li>
						<li class="nav-item" id="eightBar">
							<a href="javascript:void(0);" class="nav-link d-flex align-items-center">
								<i class="ti ti-seo me-1"></i>{{ __('admin.rentals.seo') }}
							</a>
						</li>
					</ul>
					<fieldset id="first-field">
						<form id="carBasicInfoForm" autocomplete="off">
							<input type="hidden" name="lang_id" id="lang_id" value="{{ $authUser->language_id }}">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-info-circle text-secondary me-2"></i>{{ __('admin.rentals.basic_info') }}</h4>
								@php
								$currentLang = $allLanguages->firstWhere('code', app()->getLocale());
								@endphp

								<div class="dropdown flag-dropdown">
									<a class="dropdown-toggle btn btn-white d-flex align-items-center justify-content-between p-1" data-bs-toggle="dropdown" href="javascript:void(0);">
										<img src="{{ asset('assets/img/flags/' . app()->getLocale() . '.png') }}"
											alt="Language"
											class="img-fluid rounded-circle me-2"
											style="width: 24px; height: 24px; object-fit: cover;">
										<span>{{ $currentLang->name ?? 'Language' }}</span>
									</a>
									<ul class="dropdown-menu p-2">
										@if ($allLanguages)
										@foreach ($allLanguages as $language)
										<li>
											<a href="javascript:void(0);"
												class="dropdown-item d-flex align-items-center change-language"
												data-id="{{ $language->id }}"
												data-language_code="{{ $language->code }}">
												<img src="{{ asset('assets/img/flags/' . $language->code . '.png') }}"
													alt="{{ $language->name }}"
													class="me-2 rounded-circle"
													style="width: 20px; height: 20px; object-fit: cover;">
												{{ $language->name }}
											</a>
										</li>
										@endforeach
										@endif
									</ul>
								</div>
							</div>
							<div class="border-bottom mb-4 pb-4">
								<div class="row row-gap-4">
									<div class="col-xl-3">
										<h6 class="mb-1">{{ __('admin.rentals.featured_image') }}</h6>
										<p>{{ __('admin.rentals.upload_featured_image') }}</p>
									</div>
									<div class="col-xl-9">
										<div class="d-flex align-items-center flex-wrap row-gap-3 upload-pic">
											<div class="d-flex align-items-center justify-content-center avatar avatar-xxl me-3 flex-shrink-0 border rounded-circle frames">
												<img src="/assets/img/car/car-02.jpg" class="img-fluid rounded-circle" alt="brands">
												<a href="javascript:void(0);" id="delImg" class="upload-img-trash trash-end btn btn-sm rounded-circle">
													<i class="ti ti-trash fs-12"></i>
												</a>
											</div>
											<div>
												<div class="drag-upload-btn btn btn-md btn-dark d-inline-flex align-items-center mb-2">
													<i class="ti ti-photo me-1"></i>{{ __('admin.rentals.change') }}
													<input type="file" name="vehicle_image" id="vehicle_image" class="form-control image-sign">
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
									<div class="col-xl-4">
										<h6 class="mb-1">{{ __('admin.rentals.vehicle_info') }}</h6>
										<p>{{ __('admin.rentals.add_vehicle_info') }}</p>
									</div>
									<div class="col-xl-8">
										<div class="mb-3">
											<label class="form-label">{{ __('admin.rentals.name') }} <span class="text-danger">*</span></label>
											<input type="text" name="title" id="title" class="form-control" maxlength="50">
											<span class="invalid-feedback" id="title_error"></span>
										</div>
										<div class="mb-3">
											<label class="form-label">{{ __('admin.rentals.permalink') }}</label>
											<input type="text" class="form-control" name="perma_link" id="perma_link" placeholder="https://www.example.com/vehicle/" maxlength="100">
											<p class="fs-13 fw-medium mt-1 mb-0">{{ __('admin.rentals.preview') }} : <a href="#" class="link-info">https://www.example.com/vehicle/</a></p>
											<span class="invalid-feedback" id="perma_link_error"></span>
										</div>
										<div class="row">
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<div class="d-flex align-items-center justify-content-between">
														<label class="form-label">{{ __('admin.rentals.vehicle_type') }} <span class="text-danger">*</span></label>
													</div>
													<select name="vehicle_type_id" class="form-control select" id="vehicle_type_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($carTypes as $carTypesValues)
															<option value="{{ $carTypesValues->id }}">{{ ucwords(strtolower($carTypesValues->name)) }}</option>
														@endforeach
													</select>
													
													<span class="invalid-feedback" id="vehicle_type_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<div class="d-flex align-items-center justify-content-between">
														<label class="form-label">{{ __('admin.rentals.brand') }} <span class="text-danger">*</span></label>
													</div>
													<select name="vehicle_brand_id" class="form-control select" id="vehicle_brand_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($Brands as $BrandValues)
														<option value="{{ $BrandValues->id }}">{{ ucwords(strtolower($BrandValues->brand_name )) }}</option>
														@endforeach 
													</select>
													<span class="invalid-feedback" id="vehicle_brand_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<div class="d-flex align-items-center justify-content-between">
														<label class="form-label">{{ __('admin.rentals.model') }} <span class="text-danger">*</span></label>
													</div>
													<select name="vehicle_model_id" class="form-control select" id="vehicle_model_id">
														<option value="">{{ __('admin.rentals.select_model') }}</option>
													</select>
													<span class="invalid-feedback" id="vehicle_model_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.category') }} <span class="text-danger">*</span></label>
													<select name="vehicle_category_id" class="form-control select" id="vehicle_category_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($Category as $CategoryValues)
														<option value="{{ $CategoryValues->id }}">{{ ucwords(strtolower($CategoryValues->name)) }}</option>
														@endforeach 
													</select>
													<span class="invalid-feedback" id="vehicle_category_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.plate_number') }}</label>
													<input type="text" name="plate_number" maxlength="10" placeholder="{{ __('admin.rentals.plate_number_placeholder') }}" id="plate_number" class="form-control">
													<span class="invalid-feedback" id="plate_number_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.vin_number') }}</label>
													<input type="text" name="vin_number" id="vin_number" maxlength="10" placeholder="{{ __('admin.rentals.vin_number_placeholder') }}" class="form-control">
													<span class="invalid-feedback" id="vin_number_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.main_location') }} <span class="text-danger">*</span></label>
													<select name="main_location_id" class="form-control select" id="main_location_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($Location as $LocationValues)
														<option value="{{ $LocationValues->id }}">{{ ucwords(strtolower($LocationValues->name )) }}</option>
														@endforeach 
													</select>
													<span class="invalid-feedback" id="main_location_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.link_other_location') }}</label>
													<select name="other_location_id[]" id="other_location_id" placeholder="Select" class="form-control select" multiple>
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($Location as $LocationValues)
														<option value="{{ $LocationValues->id }}">{{ $LocationValues->name }}</option>
														@endforeach
													</select>
													<span class="invalid-feedback" id="other_location_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.fuel') }}</label>
													<select name="vehicle_fuel_id" class="form-control select" id="vehicle_fuel_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($CarFuel as $CarFuelValues)
														<option value="{{ $CarFuelValues->id }}">{{ $CarFuelValues->fuel_type }}</option>
														@endforeach
													</select>
													<span class="invalid-feedback" id="vehicle_fuel_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.odometer') }}</label>
													<input name="odometer" id="odometer" maxlength="5" type="text" class="form-control">
													<span class="invalid-feedback" id="odometer_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.color') }} <span class="text-danger">*</span></label>
													<select name="vehicle_color_id" class="form-control select slectedColor" id="vehicle_color_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($CarColor as $CarColorValues)
														<option value="{{ $CarColorValues->id }}" data-color="{{ $CarColorValues->value }}">
															{{ ucwords(strtolower($CarColorValues->name)) }}
														</option> 
														@endforeach
													</select>
													<span class="invalid-feedback" id="vehicle_color_id_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.year') }} <span class="text-danger">*</span></label>
													<div class="input-icon-end position-relative">
														<input type="text" name="vehicle_year" placeholder="{{ __('admin.rentals.year_placeholder') }}" id="vehicle_year" class="form-control yearpickerVehicle">
														<span class="input-icon-addon">
															<i class="ti ti-calendar"></i>
														</span>
													</div>
													<span class="invalid-feedback" id="vehicle_year_error"></span>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.transmission') }}</label>
													<select name="vehicle_transmission_id" class="form-control select" id="vehicle_transmission_id">
														<option value="">{{ __('admin.rentals.select') }}</option>
														@foreach($Transmission as $TransmissionValues)
														<option value="{{ $TransmissionValues->id }}">{{ ucwords(strtolower($TransmissionValues->name )) }}</option>
														@endforeach 
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.mileage') }}</label>
													<input name="vehicle_mileage" id="vehicle_mileage" maxlength="2" placeholder="{{ __('admin.rentals.mileage_placeholder') }}" type="text" class="form-control">
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.passengers') }} <span class="text-danger">*</span></label></label>
													<input name="vehicle_passenger" id="vehicle_passenger" maxlength="2" placeholder="{{ __('admin.rentals.passengers_placeholder') }}" type="text" class="form-control">
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.no_of_seats') }}</label>
													<select name="num_seats" class="form-control select" id="num_seats">
														<option value="">{{ __('admin.rentals.select') }}</option>
														<option value="2">2</option>
														<option value="4">4</option>
														<option value="5">5</option>
														<option value="6">6</option>
														<option value="7">7</option>
														<option value="8">8</option>
														<option value="10">10</option>
														<option value="12">12</option>
														<option value="14">14</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.no_of_doors') }}</label>
													<select name="num_doors" class="form-control select" id="num_doors">
														<option value="">{{ __('admin.rentals.select') }}</option>
														<option value="2">2</option>
														<option value="4">4</option>
														<option value="6">6</option>
														<option value="8">8</option>
													</select>
												</div>
											</div>
											<div class="col-lg-4 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.no_of_airbags') }}</label>
													<input type="text" class="form-control" maxlength="1" placeholder="{{ __('admin.rentals.airbags_placeholder') }}" name="num_airbags" id="num_airbags">
												</div>
											</div>

											<div class="col-lg-12 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.description') }}</label>
													<textarea type="text" class="form-control summernote" maxlength="500" placeholder="E{{ __('admin.rentals.description_placeholder') }}" name="description" id="description"></textarea>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-light d-flex align-items-center me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.cancel') }}</button>
								<button id="featAmenNext" type="button" class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_features') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="second-field">
						<form id="featuresForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-flame text-secondary me-2"></i>{{ __('admin.rentals.features_and_amenities') }}</h4>
							</div>
							<div class="border-bottom mb-2 pb-2">
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
														<input class="form-check-input" type="checkbox" id="selectall_feature">
														<label class="form-check-label" for="amenity">
															{{ __('admin.rentals.check_all') }}
														</label>
													</div>
												</div>
											</div>
											@foreach($SafetyFeature as $SafetyFeatureValues)
											<div class="col-lg-4 col-md-6 cursor-pointer">
												<div class="mb-3 position-relative vechicle-add">
													<div class="form-check form-checkbox mb-0">
														<input
															class="form-check-input singleCheckbox"
															name="feature_id[]"
															type="checkbox"
															id="amenity_{{ $SafetyFeatureValues->id }}"
															value="{{ $SafetyFeatureValues->id }}">
													</div>
													<label class="form-check-label" for="amenity_{{ $SafetyFeatureValues->id }}">
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
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button id="priceTariffNext" type="button" class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_tariff_pricing') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="third-field">
						<form id="priceTariffForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-files text-secondary me-2"></i>{{ __('admin.rentals.pricing_and_tariff') }}
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
											<div class="col-lg-12">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.pricing_type') }} <span class="text-danger">*</span></label>
													<div class="d-flex align-items-center flex-wrap gap-3">
														<div class="form-check mb-0">
															<input class="form-check-input price-checkbox" type="checkbox" name="daily" id="daily">
															<label class="form-check-label" for="price">
																{{ __('admin.rentals.daily') }}
															</label>
														</div>
														<div class="form-check mb-0">
															<input class="form-check-input price-checkbox" type="checkbox" name="weekly" id="weekly">
															<label class="form-check-label" for="price1">
																{{ __('admin.rentals.weekly') }}
															</label>
														</div>
														<div class="form-check mb-0">
															<input class="form-check-input price-checkbox" type="checkbox" name="montly" id="montly">
															<label class="form-check-label" for="price2">
																{{ __('admin.rentals.monthly') }}
															</label>
														</div>
														<div class="form-check mb-0">
															<input class="form-check-input price-checkbox" type="checkbox" name="yearly" id="yearly">
															<label class="form-check-label" for="price3">
																{{ __('admin.rentals.yearly') }}
															</label>
														</div>
													</div>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.daily_price') }} <span class="text-danger">*</span></label>
													<input type="text" class="form-control price-input priceLimit" name="daily_price" id="daily_price" placeholder="{{ __('admin.rentals.enter_daily_price') }}" disabled>
													<span class="invalid-feedback" id="daily_price_error"></span>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.weekly_price') }} <span class="text-danger">*</span></label>
													<input type="text" class="form-control price-input priceLimit" name="weekly_price" id="weekly_price" placeholder="{{ __('admin.rentals.enter_weekly_price') }}" disabled>
													<span class="invalid-feedback" id="weekly_price_error"></span>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.monthly_price') }} <span class="text-danger">*</span></label>
													<input type="text" class="form-control price-input priceLimit" name="montly_price" id="montly_price" placeholder="{{ __('admin.rentals.enter_monthly_price') }}" disabled>
													<span class="invalid-feedback" id="montly_price_error"></span>
												</div>
											</div>
											<div class="col-lg-3 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.yearly_price') }} <span class="text-danger">*</span></label>
													<input type="text" class="form-control price-input priceLimit" name="yearly_price" id="yearly_price" placeholder="{{ __('admin.rentals.enter_yearly_price') }}" disabled>
													<span class="invalid-feedback" id="yearly_price_error"></span>
												</div>
											</div>
											<div class="col-lg-6 col-md-6">
												<div class="mb-3">
													<div class="d-flex align-items-center justify-content-between">
														<label class="form-label">{{ __('admin.rentals.base_kilometers_per_day') }} <span class="text-danger">*</span></label>
														<div class="form-check mb-2">
															<input class="form-check-input" type="checkbox" name="unlimited" id="Baseunlimited">
															<label class="form-check-label" for="unlimited">
																{{ __('admin.rentals.unlimited') }}
															</label>
														</div>
													</div>
													<input type="text" name="basic_kilometer" id="basic_kilometer" maxlength="8" placeholder="{{ __('admin.rentals.enter_base_kilometers') }}" class="form-control priceLimit">
													<span class="invalid-feedback" id="basic_kilometer_error"></span>
												</div>
											</div>
											<div class="col-lg-6 col-md-6">
												<div class="mb-3">
													<label class="form-label">{{ __('admin.rentals.extra_kilometers_price') }} <span class="text-danger">*</span></label>
													<input type="text" name="extra_kilometer" id="extra_kilometer" maxlength="8" placeholder="{{ __('admin.rentals.enter_extra_kilometers_price') }}" class="form-control priceLimit">
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
										<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
											<a href="#" class="btn btn-dark btn-md d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add-tarrif"><i class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_new_tariff_rate') }}</a>
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
										<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
											<a href="#" class="btn btn-dark btn-md d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#add_price"><i class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_new_seasonal_pricing') }}</a>
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
										<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
											<a href="#" class="btn btn-dark btn-md d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#select_insurance"><i class="ti ti-plus me-1"></i>{{ __('admin.rentals.select_new_insurance') }}</a>
										</div>
										<div class="empty-data bg-light text-center mb-3">
											<p class="fw-medium">{{ __('admin.rentals.no_insurance_data') }}</p>
										</div>
										<div class="card bg-light mb-3">
											<div class="card-body pb-3" id="insurance_car_append">
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button id="extraServiceNext" class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_extra_services') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="forth-field">
						<form id="extraServiceForm">
							<div class="filterbox  p-20 mb-4 p-20 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-float-center text-secondary me-2"></i>{{ __('admin.rentals.extra_services') }}</h4>
							</div>
							<div class="border-bottom mb-2 pb-1 extra-service">
								<div class="text-end">
									<a href="#" class="link-purple text-decoration-underline fw-medium d-inline-block" data-bs-toggle="modal" data-bs-target="#edit_price">{{ __('admin.rentals.edit_price') }}</a>
								</div>
								<div class="row">
									@foreach ($ExtraServices as $service)
									@php
									// Find matching extra service info for the current service
									$serviceInfo = $ExtraServiceInfo->firstWhere('extra_service_id', $service->id);
									$serviceValue = $serviceInfo->value ?? 'one_time';
									$servicePrice = $serviceInfo->price ?? '00.00';

									// Format service value for display
									$formattedServiceValue = match ($serviceValue) {
									'per_day' => __('admin.rentals.per_day'),
									'one_time' => __('admin.rentals.one_time'),
									default => ucfirst(str_replace('_', ' ', $serviceValue)), // Handles other potential cases
									};
									@endphp

									<div class="col-xxl-4 col-md-6 d-flex">
										<div class="form-check extra-service-card form-checkbox d-flex align-items-center justify-content-between flex-wrap gap-3 flex-fill">
											<div class="d-flex align-items-center">
												<input type="checkbox"
													id="extra_service_{{ $service->id }}"
													name="extra_service[]"
													value="{{ $service->id }}"
													@if($ExtraServiceInfo->contains('extra_service_id', $service->id)) checked @endif>
												<input class="form-check-input" type="hidden" id="service_id" name="service_id[]" value="{{ $service->id }}">
												<span class="service-icon bg-dark d-flex align-items-center justify-content-center me-2 ms-2">
													<img src="{{ asset('storage/' . $service->icon) }}" alt="{{ $service->name }} Icon" width="50">
												</span>
												<div>
													<h6 class="fs-14 fw-semibold mb-1" id="service_name">{{ $service->name }}</h6>
													<p class="fs-13">{{ $service->description }}</p>
												</div>
											</div>
											<div>
												<p class="fs-13 mb-1" id="set_value">{{ $formattedServiceValue }}</p>
												<input type="hidden" name="service_value[]" id="service_value" value="{{ $serviceValue }}">
												<h6 class="fs-14 fw-semibold" id="set_price">${{ number_format($servicePrice, 2) }}</h6>
												<input type="hidden" name="service_price[]" id="service_price" value="{{ $servicePrice }}">
											</div>
										</div>
									</div>
									@endforeach
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button type="button" class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.upload_documents') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="fifth-field">
						<form id="carDocumentForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-file-invoice text-secondary me-2"></i>{{ __('admin.rentals.documents') }}</h4>
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
													<img src="{{ asset('assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
													<p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span class="text-info text-decoration-underline"></span></p>
													<p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
													<input type="file" class="form-control image-sign" name="car_document[]" id="car_document" multiple>
												</div>
												<div class="mb-3">
													<p class="fs-13 mb-1">{{ __('admin.rentals.upload_insurance_registration') }}</p>
													<p class="fs-13">{{ __('admin.rentals.supported_formats') }}</p>
												</div>
												<div id="car_doc_append">

												</div>
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
													<img src="{{ asset('assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
													<p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span class="text-info text-decoration-underline"></span></p>
													<p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
													<input type="file" class="form-control image-sign" name="policy_document[]" id="policy_document" multiple>
												</div>
												<div class="mb-3">
													<p class="fs-13 mb-1">{{ __('admin.rentals.upload_car_policy_documents') }}</p>
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
													<img src="{{ asset('assets/img/icons/upload-icon.svg') }}" alt="img" class="mb-2">
													<p class="mb-2">{{ __('admin.rentals.drop_files_or_browse') }} <span class="text-info text-decoration-underline"></span></p>
													<p class="fs-12 mb-0">{{ __('admin.rentals.max_file_size') }}</p>
													<input type="file" class="form-control image-sign" name="car_images[]" id="car_images" multiple>
												</div>
												<div class="d-flex align-items-center flex-wrap gap-3" id="car_images_append">

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
													<label class="form-label">{{ __('admin.rentals.platform') }} <span class="text-danger"></span></label>
													<select class="select">
														<option>{{ __('admin.rentals.youtube') }}</option>
														<option>{{ __('admin.rentals.vimeo') }}</option>
													</select>
												</div>
											</div>
											<div class="col-md-8">
												<div class="mb-4">
													<label class="form-label">{{ __('admin.rentals.video_link') }}</label>
													<input type="text" class="form-control" name="car_video" id="car_video" placeholder="https://www.youtube.com/cars_add/">
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
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button id="carDamageNext" class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.add_damage') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="sixth-field">
						<form id="carDamageForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-id text-secondary me-2"></i>{{ __('admin.rentals.damages_title') }}</h4>
							</div>
							<div class="border-bottom mb-2 pb-4">
								<div class="row row-gap-4">
									<div class="col-xl-3">
										<h6 class="mb-1">{{ __('admin.rentals.damages_title') }}</h6>
										<p>{{ __('admin.rentals.damages_description') }}</p>
									</div>
									<div class="col-xl-9">
										<a href="#" class="btn btn-dark btn-md d-inline-flex align-items-center mb-3" data-bs-toggle="modal" data-bs-target="#add-damage" id="damage_car"><i class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_damage_button') }} </a>
										<div class="card border-0 bg-light mb-0">
											<div class="card-body">
												<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3">
													<h6>{{ __('admin.rentals.total_damages') }} : <span id="damage_count">00</span></h6>
												</div>
												<div id="car_damage_append">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button id="faqNext" type="button" class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.add_faq_button') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset id="seventh-field">
						<form id="carFaqForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-question-mark text-secondary me-2"></i>{{ __('admin.rentals.faq_title') }}</h4>
							</div>
							<div class="border-bottom mb-2 pb-4">
								<div class="row row-gap-4">
									<div class="col-xl-3">
										<h6 class="mb-1">{{ __('admin.rentals.faq_title') }}</h6>
										<p>{{ __('admin.rentals.faq_description') }}</p>
									</div>
									<div class="col-xl-9">
										<a href="#" class="btn btn-dark btn-md d-inline-flex align-items-center mb-3" data-bs-toggle="modal" data-bs-target="#add-faq"><i class="ti ti-plus me-1"></i>{{ __('admin.rentals.add_faq_button') }}</a>
										<div class="card border-0 bg-light mb-0">
											<div class="card-body">
												<h6 class="mb-3">{{ __('admin.rentals.total_faq') }} : <span id="faq_count">00</span></h6>
												<div class="faq-accordion car_faq_append" id="faqaccordion">
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }} </button>
								<button type="button" class="btn btn-primary wizard-next d-flex align-items-center">{{ __('admin.rentals.add_seo') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
					<fieldset>
						<form id="carSeoForm">
							<div class="filterbox  p-20 mb-4 info-box d-flex align-items-center justify-content-between flex-wrap gap-3">
								<h4 class="d-flex align-items-center"><i class="ti ti-question-mark text-secondary me-2"></i>{{ __('admin.rentals.seo_title') }}</h4>
							</div>
							<div class="border-bottom mb-2 pb-2">
								<div class="row row-gap-4">
									<div class="col-xl-3">
										<h6 class="mb-1">{{ __('admin.rentals.seo_title') }}</h6>
										<p>{{ __('admin.rentals.seo_description') }}</p>
									</div>
									<div class="col-xl-9">
										<div class="mb-3">
											<label class="form-label">{{ __('admin.rentals.seo_meta_title_label') }} <span class="text-danger">*</span></label>
											<input type="text" name="seo_title" id="seo_title" class="form-control">
										</div>
										<div class="mb-3">
											<label class="form-label">{{ __('admin.rentals.seo_keywords_label') }} <span class="text-danger">*</span></label>
											<input type="text" name="seo_key" id="seo_key" class="form-control">
										</div>
										<div class="mb-3">
											<label class="form-label">{{ __('admin.rentals.seo_description_label') }} <span class="text-danger">*</span></label>
											<textarea class="form-control" name="seo_description" id="seo_description" rows="3"></textarea>
										</div>
									</div>
								</div>
							</div>
							<div class="d-flex align-items-center justify-content-end pt-3">
								<button type="button" class="btn btn-outline-light border wizard-prev me-2"><i class="ti ti-chevron-left me-1"></i>{{ __('admin.rentals.back') }}</button>
								<button id="seoFinalBtn" type="submit" class="btn btn-primary d-flex align-items-center">{{ __('admin.rentals.save_exit_button') }}<i class="ti ti-chevron-right ms-1"></i></button>
							</div>
						</form>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade addmodal" id="add-tarrif">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title mb-0" id="tarrif_title">{{ __('admin.rentals.add_tariff') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form action="">
				<div class="modal-body pb-1">
					<div class="row">
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.rentals.tariff_name') }} <span class="text-danger">*</span></label>
								<input type="text" name="t_name" id="t_name" maxlength="50" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.rentals.daily_price') }} <span class="text-danger">*</span></label>
								<input type="text" name="t_price" id="t_price" maxlength="5" class="form-control priceLimit">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.rentals.from_days') }} <span class="text-danger">*</span></label>
								<input type="text" name="t_fromday" id="t_fromday" maxlength="10" class="form-control">
							</div>
						</div>
						<div class="col-md-6">
							<div class="mb-3">
								<label class="form-label">{{ __('admin.rentals.to_days') }} <span class="text-danger">*</span></label>
								<input type="text" name="t_today" id="t_today" maxlength="10" class="form-control">
							</div>
						</div>
						<div class="col-md-12">
							<div class="mb-3">
								<div class="d-flex align-items-center justify-content-between">
									<label class="form-label">{{ __('admin.rentals.base_km_per_day') }} <span class="text-danger">*</span></label>
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
								<label class="form-label">{{ __('admin.rentals.km_extra_price') }} <span class="text-danger">*</span></label>
								<input type="text" id="t_extra" name="t_extra" maxlength="5" class="form-control priceLimit">
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
						<button type="button" class="btn btn-primary" id="tarrif_btn">{{ __('admin.rentals.create_tariff') }}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade deletemodal" id="delete_tarrif">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<form action="">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">{{ __('admin.rentals.delete_tariff') }}</h4>
					<p class="mb-3">{{ __('admin.rentals.delete_tariff_confirmation') }}</p>
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
						<button type="button" class="btn btn-primary">{{ __('admin.rentals.confirm_delete_tariff') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade addmodal" id="add_price">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title mb-0" id="seas_title">{{ __('admin.rentals.create_seasonal_price') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<div class="modal-body pb-1">
				<div class="row">
					<div class="col-md-12">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.season_name') }} <span class="text-danger">*</span></label>
							<input type="text" name="s_name" id="s_name" maxlength="50" class="form-control">
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.start_date') }} <span class="text-danger">*</span></label>
							<div class="input-icon-end position-relative">
								<input type="text" name="s_strdate" id="s_strdate" class="form-control datetimepickerVehicle" placeholder="dd/mm/yyyy">
								<span class="input-icon-addon">
									<i class="ti ti-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.end_date') }} <span class="text-danger">*</span></label>
							<div class="input-icon-end position-relative">
								<input type="text" name="s_enddate" id="s_enddate" class="form-control datetimepickerVehicle" placeholder="dd/mm/yyyy">
								<span class="input-icon-addon">
									<i class="ti ti-calendar"></i>
								</span>
							</div>
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.daily_rate') }} <span class="text-danger">*</span></label>
							<input type="text" name="s_drate" id="s_drate" maxlength="5" class="form-control priceLimit">
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.weekly_rate') }} <span class="text-danger">*</span></label>
							<input type="text" name="s_wrate" id="s_wrate" maxlength="5" class="form-control priceLimit">
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.monthly_rate') }} <span class="text-danger">*</span></label>
							<input type="text" name="s_mrate" id="s_mrate" maxlength="5" class="form-control priceLimit">
						</div>
					</div>
					<div class="col-md-6">
						<div class="mb-3">
							<label class="form-label">{{ __('admin.rentals.late_fees') }} <span class="text-danger">*</span></label>
							<input type="text" name="s_lrate" id="s_lrate" maxlength="5" class="form-control priceLimit">
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
					<button type="button" class="btn btn-primary" id="price_btn">{{ __('admin.rentals.create_new') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade deletemodal" id="delete_price">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<form action="">
					<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
						<i class="ti ti-trash-x fs-26"></i>
					</span>
					<h4 class="mb-1">{{ __('admin.rentals.delete_pricing') }}</h4>
					<p class="mb-3">{{ __('admin.rentals.delete_pricing_confirmation') }}</p>
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
						<button type="button" class="btn btn-primary">{{ __('admin.rentals.confirm_delete_pricing') }}</button>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="modal fade addmodal" id="edit_price">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title mb-0">{{ __('admin.rentals.edit_pricing_title') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<div class="modal-body pb-1">
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
						// Find matching extra service info for the current service
						$serviceInfo = $ExtraServiceInfo->firstWhere('extra_service_id', $service->id);
						$selectedValue = $serviceInfo->value ?? 'per_day'; // Default to "per_day"
						$selectedPrice = $serviceInfo->price ?? '00.00'; // Default to "00.00"
						@endphp

						<tr>
							<td class="fw-medium text-gray-9" id="extra_name">{{ $service->name }}</td>
							<td>
								<div class="d-flex align-items-center">
									<select class="form-control" id="extra_value" name="extra_value[{{ $service->id }}]">
										<option value="per_day" {{ $selectedValue == 'per_day' ? 'selected' : '' }}>{{ __('admin.rentals.per_day') }}</option>
										<option value="one_time" {{ $selectedValue == 'one_time' ? 'selected' : '' }}>{{ __('admin.rentals.one_time') }}</option>
									</select>
									<div class="input-icon-start position-relative w-100 ms-2">
										<span class="input-icon-addon">
											<i class="ti ti-currency-dollar"></i>
										</span>
										<input type="text" class="form-control" id="extra_price" name="extra_price[{{ $service->id }}]" value="{{ number_format($selectedPrice, 2) }}">
									</div>
								</div>
							</td>
						</tr>
						@endforeach

					</tbody>
				</table>
			</div>
			<div class="modal-footer">
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</a>
					<button type="button" class="btn btn-primary" id="service_save_btn">{{ __('admin.rentals.save_changes') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="add-damage">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title mb-0" id="damage_title">{{ __('admin.rentals.add_damage') }}</h5>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form action="add-car.html">
				<div class="modal-body pb-1">
					<div class="mb-3">
						<label class="form-label">{{ __('admin.rentals.damage_image_label') }} <span class="text-danger">*</span></label>
						<input type="file" name="dam_image" id="dam_image" class="form-control">
						<img id="image_preview" src="" class="mt-2 d-none" width="100">
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('admin.rentals.damage_location_label') }} <span class="text-danger">*</span></label>
						<select class="select" name="dam_name" id="dam_name">
							<option value="">{{ __('admin.rentals.select') }}</option>
							<option value="interior">{{ __('admin.rentals.interior') }}</option>
							<option value="exterior">{{ __('admin.rentals.exterior') }}</option>
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('admin.rentals.damage_type_label') }} <span class="text-danger">*</span></label>
						<select class="select" name="dam_type" id="dam_type">
							<option value="">Select Type</option>
							@foreach($DamageTypes as $DamageTypesValue)
							<option value="{{ $DamageTypesValue->id }}" class="{{ $DamageTypesValue->id }}">{{ $DamageTypesValue->damage_type }}</option>
							@endforeach
						</select>
					</div>
					<div class="mb-3">
						<label class="form-label">{{ __('admin.rentals.description_label') }}</label>
						<textarea class="form-control" name="dam_dis" id="dam_dis" maxlength="120" rows="3"></textarea>
					</div>
				</div>
				<div class="modal-footer">
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</a>
						<button type="button" class="btn btn-primary" id="damage_btn">{{ __('admin.rentals.create_new') }}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade deletemodal" id="delete_damage">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
					<i class="ti ti-trash-x fs-26"></i>
				</span>
				<h4 class="mb-1">{{ __('admin.rentals.delete_damage_title') }}</h4>
				<p class="mb-3">{{ __('admin.rentals.delete_damage_confirmation') }}</p>
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</a>
					<button type="button" class="btn btn-primary" id="dete-damage">{{ __('admin.rentals.yes_delete') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade addmodal" id="add-faq">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title mb-0" id="faq_title">{{ __('admin.rentals.create_faq_title') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<div class="modal-body pb-1">
				<div class="mb-3">
					<label class="form-label">{{ __('admin.rentals.question_label') }} <span class="text-danger">*</span></label>
					<input type="text" name="f_q" id="f_q" maxlength="60" class="form-control">
				</div>
				<div class="mb-3">
					<label class="form-label">{{ __('admin.rentals.answer_label') }} <span class="text-danger">*</span></label>
					<textarea class="form-control" name="f_a" id="f_a" maxlength="120" rows="3"></textarea>
				</div>
			</div>
			<div class="modal-footer">
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</a>
					<a class="btn btn-primary" id="faq_btn">{{ __('admin.rentals.create_new') }}</a>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade deletemodal" id="delete_faq">
	<div class="modal-dialog modal-dialog-centered modal-sm">
		<div class="modal-content">
			<div class="modal-body text-center">
				<span class="avatar avatar-lg bg-transparent-danger rounded-circle text-danger mb-3">
					<i class="ti ti-trash-x fs-26"></i>
				</span>
				<h4 class="mb-1">{{ __('admin.rentals.delete_faq_title') }}</h4>
				<p class="mb-3">{{ __('admin.rentals.delete_faq_confirmation') }}</p>
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.rentals.cancel') }}</a>
					<button type="button" class="btn btn-primary" id="dete-faq">{{ __('admin.rentals.yes_delete') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal fade addmodal" id="select_insurance">
	<div class="modal-dialog modal-dialog-centered modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title mb-0">{{ __('admin.rentals.select_insurance') }}</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<form id="set_value">
				<div class="modal-body pb-1">
					@foreach($insurances as $insurance)
					<div class="d-flex align-items-center justify-content-between flex-wrap bg-white gap-3 border br-5 p-20 mb-3" id="inCont">
						<input type="hidden" id="insurance_id" value="{{ $insurance->id }}">
						<div>
							<h6 class="fs-14 fw-semibold d-inline-flex align-items-center mb-1">
								{{ $insurance->rentals }}
							</h6>
							<input type="hidden" id="insurance_name" value="{{ $insurance->insurance_name }}">
							<input type="hidden" id="insurance_price_type" value="{{ $insurance->priceType->pricing_type }}">
							<div class="d-flex align-items-center gap-2 flex-wrap">
								<p class="fs-13 fw-medium border-end pe-2 mb-0">
									{{ __('admin.rentals.price') }}: <span class="text-gray-9">${{ number_format($insurance->price, 2) }}</span>
									<input type="hidden" id="insurance_price" value="{{ $insurance->price }}">
								</p>
								<p class="fs-13 fw-medium mb-0">
									Benefits: <span class="text-gray-9">{{ $insurance->insuranceBenefits->count() }}</span>
									<input type="hidden" id="insurance_count" value="{{ $insurance->insuranceBenefits->count() }}">
									@if($insurance->insuranceBenefits->isNotEmpty())
									<i class="ti ti-info-circle-filled text-gray-5 ms-1"
										data-bs-toggle="tooltip"
										data-bs-placement="top"
										title="{{ $insurance->insuranceBenefits->first()->benefit }}">
									</i>
									@endif
								</p>
							</div>
						</div>
						<div class="d-flex align-items-center icon-list delivery-add">
							<a href="#"><i class="ti ti-plus plus-active"></i><i class="ti ti-check check-active"></i></a>
							<input type="checkbox" id="insurance_checked" hidden>
						</div>
					</div>
					@endforeach
				</div>
				<div class="modal-footer">
					<div class="d-flex justify-content-center">
						<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
						<button type="button" class="btn btn-primary" id="in_btn">{{ __('admin.rentals.create_new') }}</button>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="modal fade" id="edit_insurance">
	<div class="modal-dialog modal-dialog-centered">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="mb-0">Edit Insurance</h4>
				<button type="button" class="btn-close custom-btn-close" data-bs-dismiss="modal" aria-label="Close">
					<i class="ti ti-x fs-16"></i>
				</button>
			</div>
			<div class="modal-body">
				<div class="mb-3">
					<label class="form-label">{{ __('admin.rentals.price_type') }} <span class="text-danger"> *</span></label>
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
								{{ __('admin.rentals.percentage') }}
							</label>
						</div>
					</div>
				</div>
				<div class="mb-3">
					<label class="form-label">Price <span class="text-danger"> *</span></label>
					<input type="text" class="form-control priceLimit	" id="price" maxlength="5" value="">
				</div>
			</div>
			<div class="modal-footer">
				<div class="d-flex justify-content-center">
					<a href="javascript:void(0);" class="btn btn-light me-3" data-bs-dismiss="modal">{{ __('admin.common.cancel') }}</a>
					<button type="button" class="btn btn-primary" id="save_update">{{ __('admin.rentals.create_new') }}</button>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@push('scripts')
<script src="{{ asset('assets/js/add-car.js') }}"></script>
@endpush