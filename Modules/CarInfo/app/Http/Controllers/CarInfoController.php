<?php

namespace Modules\CarInfo\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\CarInfo\Models\VehicleInfo;
use Illuminate\Support\Str;
use Modules\CarInfo\Models\Cartype;
use Modules\CarInfo\Models\Brand;
use Modules\CarInfo\Models\CarModel;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\Location;
use Modules\CarInfo\Models\CarColor;
use Modules\CarInfo\Models\SafetyFeature;
use Illuminate\Support\Facades\Storage;
use Modules\Booking\Models\Booking;
use Modules\CarInfo\Models\CarFuel;
use Modules\CarInfo\Models\DamageType;
use Modules\CarInfo\Models\ExtraService;
use Modules\CarInfo\Models\PricingType;
use Modules\CarInfo\Models\Transmission;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\Language;
use Modules\GeneralSetting\Models\TranslationLanguage;
use Illuminate\Support\Facades\App;
use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Models\GeneralSetting;

class CarInfoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function vehiclelist()
    {
        $vechileName = VehicleInfo::orderBy('id', 'desc')->get();
        $vechileType = Cartype::orderBy('id', 'desc')->get();
        $vechileLocation = Location::orderBy('id', 'desc')->get();

        return view('carinfo::vehicle.index', compact("vechileName", "vechileType", "vechileLocation"));
    }

    public function vehicleadd()
    {
        $authUser = current_user();
        $language_id = $authUser->language_id;
        $carTypes = Cartype::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $Brands = Brand::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $CarModel = CarModel::where('status', 1)->orderBy('id', 'desc')->get();
        $Category = Category::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $Location = Location::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $CarColor = CarColor::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $CarFuel = CarFuel::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $Transmission = Transmission::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $SafetyFeature = SafetyFeature::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $DamageTypes = DamageType::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();
        $ExtraServices = ExtraService::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();

        $query = null;

        $ExtraServiceInfo = VehicleExtraService::where('vehicle_id', $query)
            ->whereIn('extra_service_id', $ExtraServices->pluck('id'))
            ->get();

        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where("language_id", $language_id)
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        $authUser = current_user();

        return view('carinfo::vehicle.add', compact('carTypes', 'Brands', 'CarModel', 'Category', 'Location', 'CarColor', 'CarFuel', 'Transmission', 'SafetyFeature', 'DamageTypes', 'ExtraServices', 'ExtraServiceInfo', 'insurances', 'priceType', 'authUser'));
    }
    public function vehicleedit($slug, Request $request)
    {
        if ($request->has('language_id')) {
            $language_id = $request->query('language_id');
            $language = Language::find($language_id);
            $languageId = $language->language_id;
        } else {
            $authId = current_user();
            $language_id = $authId->language_id ?? null;
            $language = $language_id; // Or however you associate default language
            $languageId = $language;
        }


        $languageCode = $languageId ? TranslationLanguage::find($languageId) : null;


        $query = VehicleInfo::select(
            "id",
            "language_id",
            "parent_id",
            "vehicle_image",
            "name",
            "slug",
            "type_id",
            "brand_id",
            "model_id",
            "category_id",
            "plate_number",
            "perma_link",
            "vin",
            "main_location_id",
            "other_location_id",
            "fuel_type_id",
            "odometer",
            "color_id",
            "year",
            "transmission_id",
            "mileage",
            "passenger_capacity",
            "num_seats",
            "num_doors",
            "num_airbags",
            "features",
            "vehicle_basekm",
            "vehicle_extrakmprice",
            "vehicle_price",
            'vehicle_metatitle',
            'vehicle_metadesc',
            'vehicle_metakeywords',
            'description',
            'vehicle_video',
        )->where("slug", $slug)->when($languageId, function ($q) use ($languageId) {
            $q->where('language_id', $languageId);
        })->first();


        if (!$query && $languageId) {
            $baseVehicle = VehicleInfo::where('slug', $slug)->whereNull('parent_id')->first();
            if ($baseVehicle) {
                $query = VehicleInfo::where('parent_id', $baseVehicle->id)
                    ->where('language_id', $languageId)
                    ->first();


                // If still not found, create an empty Page instance pre-filled with what we know
                if (!$query) {
                    $query = new VehicleInfo([
                        'language_id' => $languageId,
                        'parent_id' => $baseVehicle->id,
                    ]);
                }
            }
        }


        // If no page and no basePage, create a fresh one
        if (!$query) {
            $baseVehicle = VehicleInfo::where('slug', $slug)->first();

            if ($baseVehicle) {
                $query = VehicleInfo::where('id', $baseVehicle->parent_id)
                    ->where('language_id', $languageId)
                    ->first();

                if (!$query) {
                    $query = new VehicleInfo([
                        'language_id' => $languageId,
                        'parent_id' => $baseVehicle->parent_id,
                    ]);
                }
            }
        }

        $selectedFeatures = is_array($query->features) ? $query->features : json_decode($query->features, true);
        $vehiclePrices = json_decode($query->vehicle_price, true)[0] ?? [];

        $carTypes = Cartype::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $Brands = Brand::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $Models = CarModel::where('status', 1)
            ->where('brand_id', $query->brand_id)
            ->orderBy('id', 'desc')
            ->get();
        $Category = Category::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $Location = Location::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $CarFuel = CarFuel::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $CarColor = CarColor::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $Transmission = Transmission::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $SafetyFeature = SafetyFeature::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();
        $DamageTypes = DamageType::where('status', 1)->where("language_id", $languageId)->orderBy('id', 'desc')->get();

        $ExtraServices = ExtraService::where('status', 1)->where("language_id", $language_id)->orderBy('id', 'desc')->get();

        $ExtraServiceInfo = VehicleExtraService::where('vehicle_id', $query->id ?? null)
            ->whereIn('extra_service_id', $ExtraServices->pluck('id'))
            ->get();

        $insurances = Insurance::with('insuranceBenefits', 'priceType')
            ->where('status', 1)
            ->get();

        $priceType = PricingType::where('status', 1)->get();

        app()->setLocale($languageCode->code);

        return view('carinfo::vehicle.edit', compact('carTypes', 'Brands', 'Models', 'Category', 'Location', 'CarFuel', 'CarColor', 'Transmission', 'SafetyFeature', 'selectedFeatures', 'vehiclePrices', 'ExtraServices', 'ExtraServiceInfo', 'insurances', 'priceType', 'DamageTypes', 'query'));
    }

    public function getvehiclelist()
    {
        $carTypes = VehicleInfo::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => 'success',
            'code'   => 200,
            'data' => $carTypes
        ]);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('carinfo::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function vehiclesave(Request $request)
    {
        //
        echo "TEST ALL";
        $slug = Str::slug($request->name);
        $carType = new VehicleInfo();
        $carType->name = $request->name;
        $carType->type_id = $request->type_id;
        $carType->brand_id = $request->brand_id;
        $carType->model_id = $request->model_id;
        $carType->category_id = $request->category_id;
        $carType->plate_number = $request->plate_number;
        $carType->vin = $request->vin;
        $carType->year = $request->year;
        $carType->passenger_capacity = $request->passenger_capacity;
        $carType->mileage = $request->mileage;
        $carType->num_airbags = $request->num_airbags;
        $carType->num_seats = $request->num_seats;
        $carType->num_doors = $request->num_doors;
        $carType->main_location_id = $request->main_location_id;
        $carType->fuel_type_id = $request->fuel_type_id;
        $carType->odometer = $request->odometer;
        $carType->color_id = $request->color_id;
        $carType->slug = $slug;
        $carType->vehicle_pricetype = 1;
        $carType->vehicle_price = 100.2;
        $carType->vehicle_basekm = 100.2;
        $carType->vehicle_extrakmprice = 100.2;
        $carType->vehicle_metatitle = $request->name;
        $carType->save();
    }

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('carinfo::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('carinfo::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        //
    }

    //_____________________________________________________________________________________
    //_____________________________________________________________________________________
    //_____________________________________________________________________________________
    //Vehicles information
    //_____________________________________________________________________________________
    //_____________________________________________________________________________________
    //_____________________________________________________________________________________

    public function saveCarInfo(Request $request)
    {

        $authId = Auth::id();

        $rules = [
            'title' => 'nullable|max:255',
            'perma_link' => 'nullable|url',
        ];

        $messages = [
            'title.max' => 'The title must not exceed 255 characters.',
            'perma_link.url' => 'The permalink must be a valid URL.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        $vehiclePrice = [];

        if ($request->has('daily_price')) {
            $vehiclePrice['daily'] = $request->daily_price;
        }

        if ($request->has('weekly_price')) {
            $vehiclePrice['weekly'] = $request->weekly_price;
        }

        if ($request->has('montly_price')) {
            $vehiclePrice['monthly'] = $request->montly_price;
        }

        if ($request->has('yearly_price')) {
            $vehiclePrice['yearly'] = $request->yearly_price;
        }

        $vehiclePriceJson = json_encode([$vehiclePrice]);


        $slug = Str::slug($request->title);

        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');

            $vehicleImagePath = uploadFile($file, 'vehicles');
        } else {
            $vehicleImagePath = null;
        }

        $BaseKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
        $ExtraKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);

        $data = [
            "vehicle_image" => $vehicleImagePath,
            "language_id" => $request->lang_id,
            "name" => $request->title,
            'slug' => $slug,
            "perma_link" => $request->perma_link,
            "type_id" => $request->vehicle_type_id,
            "brand_id" => $request->vehicle_brand_id,
            "model_id" => $request->vehicle_model_id,
            "category_id" => $request->vehicle_category_id,
            "plate_number" => $request->plate_number,
            "vin" => $request->vin_number,
            "main_location_id" => $request->main_location_id,
            "other_location" => $request->other_location,
            "other_location_id" => $request->other_location_id,
            "fuel_type_id" => $request->vehicle_fuel_id,
            "odometer" => $request->odometer,
            "color_id" => $request->vehicle_color_id,
            "year" => $request->vehicle_year,
            "transmission_id" => $request->vehicle_transmission_id,
            "mileage" => $request->vehicle_mileage,
            "passenger_capacity" => $request->vehicle_passenger,
            "vehicle_price" => $vehiclePriceJson,
            "num_seats" => $request->num_seats,
            "num_doors" => $request->num_doors,
            "num_airbags" => $request->num_airbags,
            "vehicle_basekm" => $BaseKilo,
            "vehicle_extrakmprice" => $ExtraKilo,
            "vehicle_video" => $request->car_video,
            "vehicle_metatitle" => $request->seo_title,
            "vehicle_metakeywords" => $request->seo_key,
            "vehicle_metadesc" => $request->seo_description,
            "features" => $request->feature_id,
            "description" => $request->description,
            "created_by" => $authId,
        ];

        $save = VehicleInfo::create($data);

        //Mutipe Image Upload::
        if ($request->hasFile('car_images')) {
            $images = $request->file('car_images');
            $imagePaths = [];

            foreach ($images as $image) {
                $fileName = uploadMutipleFile($image, 'vehicles');

                $imagePaths[] = 'vehicles/' . $fileName;
            }

            if (!empty($imagePaths)) {
                VehicleMeta::create([
                    'vehicle_id' => $save->id,
                    'key'        => 'vehicle_image',
                    'value'      => json_encode($imagePaths),
                ]);
            }
        }

        // Handle car documents
        if ($request->hasFile('car_document')) {
            $carDocs = $request->file('car_document');
            $carDocPaths = [];

            foreach ($carDocs as $doc) {
                $fileName = uploadMutipleFile($doc, 'vehicleDoc');
                $carDocPaths[] = 'vehicleDoc/' . $fileName;
            }

            if (!empty($carDocPaths)) {
                VehicleMeta::create([
                    'vehicle_id' => $save->id,
                    'key'        => 'vehicle_doc',
                    'value'      => json_encode($carDocPaths),
                ]);
            }
        }

        // Handle policy documents
        if ($request->hasFile('policy_document')) {
            $policyDocs = $request->file('policy_document');
            $policyDocPaths = [];

            foreach ($policyDocs as $doc) {
                $fileName = uploadMutipleFile($doc, 'vehiclePolicy');
                $policyDocPaths[] = 'vehiclePolicy/' . $fileName;
            }

            if (!empty($policyDocPaths)) {
                VehicleMeta::create([
                    'vehicle_id' => $save->id,
                    'key'        => 'vehicle_policy',
                    'value'      => json_encode($policyDocPaths),
                ]);
            }
        }

        if ($request->has('vehicle_faq')) {
            $vehicleFaqs = json_decode($request->input('vehicle_faq'), true);

            if (is_array($vehicleFaqs)) {
                foreach ($vehicleFaqs as $faq) {
                    if (!empty($faq['id'])) {
                        VehicleFaq::where('id', $faq['id'])
                            ->where('vehicle_id', $save->id)
                            ->update([
                                'question' => $faq['question'],
                                'answer'   => $faq['answer'],
                            ]);
                    } else {
                        VehicleFaq::create([
                            'vehicle_id' => $save->id,
                            'question'   => $faq['question'],
                            'answer'     => $faq['answer'],
                        ]);
                    }
                }
            }
        }

        if ($request->has('vehicle_insurance')) {
            $vehicleInsurances = json_decode($request->input('vehicle_insurance'), true);

            if (is_array($vehicleInsurances)) {
                foreach ($vehicleInsurances as $insurance) {
                    // Ensure valid data before creating a record
                    if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                        VehicleInsurance::create([
                            'vehicle_id'    => $save->id,
                            'insurances_id' => $insurance['id'],  // Mapping ID to insurances_id
                            'value'         => $insurance['type'], // Mapping type to value
                            'price'         => $insurance['price'],
                        ]);
                    }
                }
            }
        }

        if ($request->has('tariff')) {
            $tariffs = json_decode($request->input('tariff'), true);

            if (is_array($tariffs)) {
                foreach ($tariffs as $tariff) {
                    if (!empty($tariff['id'])) {
                        VehicleTarrif::where('id', $tariff['id'])
                            ->where('vehicle_id', $save->id)
                            ->update([
                                'tariff_title' => $tariff['title'],
                                'tariff_daily_price' => $tariff['daily_price'],
                                'tariff_from_days'   => $tariff['from_days'],
                                'tariff_to_days'     => $tariff['to_days'],
                                'tariff_base_km'     => $tariff['base_km'],
                                'tariff_extra_price' => $tariff['extra_price'],
                            ]);
                    } else {
                        VehicleTarrif::create([
                            'vehicle_id'         => $save->id,
                            'tariff_title' => $tariff['title'],
                            'tariff_daily_price' => $tariff['daily_price'],
                            'tariff_from_days'   => $tariff['from_days'],
                            'tariff_to_days'     => $tariff['to_days'],
                            'tariff_base_km'     => $tariff['base_km'],
                            'tariff_extra_price' => $tariff['extra_price'],
                        ]);
                    }
                }
            }
        }

        // Vehicle Seasonal Pricing Update or Create
        if ($request->has('seasonal')) {
            $seasonals = json_decode($request->input('seasonal'), true);

            if (is_array($seasonals)) {
                foreach ($seasonals as $season) {
                    if (!empty($season['id'])) {
                        // Update existing seasonal price
                        VehicleSeason::where('id', $season['id'])
                            ->where('vehicle_id', $save->id)
                            ->update([
                                'seasonal_title'       => $season['title'],
                                'seasonal_start_date'  => $season['start_date'],
                                'seasonal_end_date'    => $season['end_date'],
                                'seasonal_daily_rate'  => $season['daily_rate'],
                                'seasonal_weekly_rate' => $season['weekly_rate'],
                                'seasonal_monthly_rate' => $season['monthly_rate'],
                                'seasonal_late_fee'    => $season['late_fee'],
                            ]);
                    } else {
                        // Create new seasonal pricing if ID is null
                        VehicleSeason::create([
                            'vehicle_id'           => $save->id,
                            'seasonal_title'       => $season['title'],
                            'seasonal_start_date'  => $season['start_date'],
                            'seasonal_end_date'    => $season['end_date'],
                            'seasonal_daily_rate'  => $season['daily_rate'],
                            'seasonal_weekly_rate' => $season['weekly_rate'],
                            'seasonal_monthly_rate' => $season['monthly_rate'],
                            'seasonal_late_fee'    => $season['late_fee'],
                        ]);
                    }
                }
            }
        }


        if ($request->has('extra_services')) {
            $extraServices = json_decode($request->input('extra_services'), true);

            if (is_array($extraServices)) {
                foreach ($extraServices as $service) {
                    $serviceId = $service['service_id'];  // Input service ID
                    $value = $service['value'];           // Service value
                    $price = $service['price'];           // Service price

                    // Check if the record exists for this vehicle and extra service
                    $existingService = VehicleExtraService::where('vehicle_id', $save->id)
                        ->where('extra_service_id', $serviceId)
                        ->first();

                    if ($existingService) {
                        // Update the existing record
                        $existingService->update([
                            'value' => $value,
                            'price' => $price,
                        ]);
                    } else {
                        // Create a new record if it doesn't exist
                        VehicleExtraService::create([
                            'vehicle_id' => $save->id,
                            'extra_service_id' => $serviceId,
                            'value' => $value,
                            'price' => $price,
                        ]);
                    }
                }
            }
        }


        if ($request->has('vehicle_damage')) {
            $vehicleDamages = json_decode($request->input('vehicle_damage'), true);

            if (is_array($vehicleDamages)) {
                foreach ($vehicleDamages as $index => $damage) {
                    $imageFile = $request->file('damage_image')[$index] ?? null; // Get uploaded file
                    $uploadedImage = $damage['image'] ?? null; // Default to existing image

                    if ($imageFile) {
                        // Store image in storage/app/public/VehicleDamage
                        $uploadedImage = $imageFile->store('VehicleDamage', 'public');
                    } elseif (!empty($uploadedImage) && strpos($uploadedImage, 'data:image') === 0) {
                        // Convert base64 to an image file and store
                        $imageData = explode(',', $uploadedImage)[1]; // Remove metadata
                        $imageName = 'VehicleDamage/' . uniqid() . '.png';
                        Storage::disk('public')->put($imageName, base64_decode($imageData));
                        $uploadedImage = $imageName;
                    }

                    if (!empty($damage['id'])) {
                        VehicleDamage::where('id', $damage['id'])
                            ->where('vehicle_id', $save->id)
                            ->update([
                                'damage_type'     => $damage['name'],
                                'damage_loaction' => $damage['location'],
                                'image'           => $uploadedImage,
                                'description'     => $damage['description'],
                            ]);
                    } else {
                        VehicleDamage::create([
                            'vehicle_id'      => $save->id,
                            'damage_type'     => $damage['name'],
                            'damage_loaction' => $damage['location'],
                            'image'           => $uploadedImage,
                            'description'     => $damage['description'],
                        ]);
                    }
                }
            }
        }


        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => 'Car information saved successfully.'
        ], 200);
    }


    public function updateCarInfo(Request $request)
    {
        $authId = Auth::id();

        $vehicleID  = $request->vehicle_id;
        $vehicle = VehicleInfo::find($vehicleID);

        $rules = [
            'title' => 'nullable|max:255',
            'perma_link' => 'nullable|url',
        ];

        $messages = [
            'title.max' => 'The title must not exceed 255 characters.',
            'perma_link.url' => 'The permalink must be a valid URL.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        $vehiclePrice = [];

        if ($request->has('daily_price')) {
            $vehiclePrice['daily'] = $request->daily_price;
        }

        if ($request->has('weekly_price')) {
            $vehiclePrice['weekly'] = $request->weekly_price;
        }

        if ($request->has('montly_price')) {
            $vehiclePrice['monthly'] = $request->montly_price;
        }

        if ($request->has('yearly_price')) {
            $vehiclePrice['yearly'] = $request->yearly_price;
        }

        $vehiclePriceJson = json_encode([$vehiclePrice]);

        $slug = Str::slug($request->title);

        if ($request->hasFile('vehicle_image')) {
            $file = $request->file('vehicle_image');
            $existingImage = $vehicle ? $vehicle->vehicle_image : null;

            $vehicleImagePath = uploadFile($file, 'vehicles', $existingImage);
        } else {
            $vehicleImagePath = $vehicle ? $vehicle->vehicle_image : null;
        }

        $BaseKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('basic_kilometer', null);
        $ExtraKilo = ($request->has('unlimited') && $request->unlimited === 'on') ? null : $request->input('extra_kilometer', null);

        $data = [
            "vehicle_image" => $vehicleImagePath,
            "parent_id" => (int) $request->parent_id,
            "name" => $request->title,
            'slug' => $slug,
            "perma_link" => $request->perma_link,
            "type_id" => $request->vehicle_type_id,
            "brand_id" => $request->vehicle_brand_id,
            "model_id" => $request->vehicle_model_id,
            "category_id" => $request->vehicle_category_id,
            "plate_number" => $request->plate_number,
            "vin" => $request->vin_number,
            "main_location_id" => $request->main_location_id,
            "other_location_id" => $request->other_location_id,
            "fuel_type_id" => $request->vehicle_fuel_id,
            "odometer" => $request->odometer,
            "color_id" => $request->vehicle_color_id,
            "year" => $request->vehicle_year,
            "transmission_id" => $request->vehicle_transmission_id,
            "mileage" => $request->vehicle_mileage,
            "passenger_capacity" => $request->vehicle_passenger,
            "vehicle_price" => $vehiclePriceJson,
            "num_seats" => $request->num_seats,
            "num_doors" => $request->num_doors,
            "num_airbags" => $request->num_airbags,
            "vehicle_basekm" => $BaseKilo,
            "vehicle_extrakmprice" => $ExtraKilo,
            "vehicle_video" => $request->car_video,
            "vehicle_metatitle" => $request->seo_title,
            "vehicle_metakeywords" => $request->seo_key,
            "vehicle_metadesc" => $request->seo_description,
            "features" => $request->feature_id,
            "description" => $request->description,
            "created_by" => $authId,
        ];

        if ($request->filled('language_id')) {
            $data['language_id'] = (int) $request->language_id;
        }

        if ($request->filled('vehicle_id')) {
            $update = VehicleInfo::findOrFail($request->vehicle_id);

            $update->update($data);
        } else {
            $data['language_id'] = (int) $request->language_id;
            $update = VehicleInfo::create($data);
        }

        if ($request->hasFile('car_images')) {
            $images = $request->file('car_images');
            $imagePaths = [];

            foreach ($images as $image) {
                $fileName = uploadMutipleFile($image, 'vehicles');
                $imagePaths[] = 'vehicles/' . $fileName;
            }

            $vehicleMeta = VehicleMeta::where('vehicle_id', $update->id)
                ->where('key', 'vehicle_image')
                ->first();

            if ($vehicleMeta) {
                $existingImages = json_decode($vehicleMeta->value, true) ?? [];
                $updatedImages = array_merge($existingImages, $imagePaths);

                $vehicleMeta->update([
                    'value' => json_encode($updatedImages),
                ]);
            } else {
                VehicleMeta::create([
                    'vehicle_id' => $update->id,
                    'key'        => 'vehicle_image',
                    'value'      => json_encode($imagePaths),
                ]);
            }
        }

        // Handle policy documents
        if ($request->hasFile('policy_document')) {
            $policyDocs = $request->file('policy_document');
            $policyDocPaths = [];

            foreach ($policyDocs as $doc) {
                $fileName = uploadMutipleFile($doc, 'vehiclePolicy');
                $policyDocPaths[] = 'vehiclePolicy/' . $fileName;
            }

            // Retrieve existing policy documents
            $vehicleMeta = VehicleMeta::where('vehicle_id', $update->id)
                ->where('key', 'vehicle_policy')
                ->first();

            if ($vehicleMeta) {
                $existingDocs = json_decode($vehicleMeta->value, true) ?? [];
                $updatedDocs = array_merge($existingDocs, $policyDocPaths); // Append new documents

                $vehicleMeta->update([
                    'value' => json_encode($updatedDocs),
                ]);
            } else {
                VehicleMeta::create([
                    'vehicle_id' => $update->id,
                    'key'        => 'vehicle_policy',
                    'value'      => json_encode($policyDocPaths),
                ]);
            }
        }


        if ($request->has('vehicle_faq')) {
            $vehicleFaqs = json_decode($request->input('vehicle_faq'), true);

            if (is_array($vehicleFaqs)) {
                // Delete existing FAQs for this vehicle
                VehicleFaq::where('vehicle_id', $update->id)->delete();

                // Insert new FAQs
                foreach ($vehicleFaqs as $faq) {
                    VehicleFaq::create([
                        'vehicle_id' => $update->id,
                        'question'   => $faq['question'],
                        'answer'     => $faq['answer'],
                    ]);
                }
            }
        }

        if ($request->has('vehicle_insurance')) {
            $vehicleInsurances = json_decode($request->input('vehicle_insurance'), true);

            if (is_array($vehicleInsurances)) {
                // Delete all existing records related to the vehicle_id
                VehicleInsurance::where('vehicle_id', $update->id)->delete();

                // Insert new records
                foreach ($vehicleInsurances as $insurance) {
                    if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                        VehicleInsurance::create([
                            'vehicle_id'    => $update->id,
                            'insurances_id' => $insurance['id'],
                            'value'         => $insurance['type'],
                            'price'         => $insurance['price'],
                        ]);
                    }
                }
            }
        }


        if ($request->has('tariff')) {
            $tariffs = json_decode($request->input('tariff'), true);

            if (is_array($tariffs)) {
                foreach ($tariffs as $tariff) {
                    if (!empty($tariff['id'])) {
                        VehicleTarrif::where('id', $tariff['id'])
                            ->where('vehicle_id', $update->id)
                            ->update([
                                'tariff_title' => $tariff['title'],
                                'tariff_daily_price' => $tariff['daily_price'],
                                'tariff_from_days'   => $tariff['from_days'],
                                'tariff_to_days'     => $tariff['to_days'],
                                'tariff_base_km'     => $tariff['base_km'],
                                'tariff_extra_price' => $tariff['extra_price'],
                            ]);
                    } else {
                        VehicleTarrif::create([
                            'vehicle_id'         => $update->id,
                            'tariff_title' => $tariff['title'],
                            'tariff_daily_price' => $tariff['daily_price'],
                            'tariff_from_days'   => $tariff['from_days'],
                            'tariff_to_days'     => $tariff['to_days'],
                            'tariff_base_km'     => $tariff['base_km'],
                            'tariff_extra_price' => $tariff['extra_price'],
                        ]);
                    }
                }
            }
        }

        // Vehicle Seasonal Pricing Update or Create
        if ($request->has('seasonal')) {
            $seasonals = json_decode($request->input('seasonal'), true);

            if (is_array($seasonals)) {
                foreach ($seasonals as $season) {
                    if (!empty($season['id'])) {
                        // Update existing seasonal price
                        VehicleSeason::where('id', $season['id'])
                            ->where('vehicle_id', $update->id)
                            ->update([
                                'seasonal_title'       => $season['title'],
                                'seasonal_start_date'  => $season['start_date'],
                                'seasonal_end_date'    => $season['end_date'],
                                'seasonal_daily_rate'  => $season['daily_rate'],
                                'seasonal_weekly_rate' => $season['weekly_rate'],
                                'seasonal_monthly_rate' => $season['monthly_rate'],
                                'seasonal_late_fee'    => $season['late_fee'],
                            ]);
                    } else {
                        // Create new seasonal pricing if ID is null
                        VehicleSeason::create([
                            'vehicle_id'           => $update->id,
                            'seasonal_title'       => $season['title'],
                            'seasonal_start_date'  => $season['start_date'],
                            'seasonal_end_date'    => $season['end_date'],
                            'seasonal_daily_rate'  => $season['daily_rate'],
                            'seasonal_weekly_rate' => $season['weekly_rate'],
                            'seasonal_monthly_rate' => $season['monthly_rate'],
                            'seasonal_late_fee'    => $season['late_fee'],
                        ]);
                    }
                }
            }
        }

        if ($request->has('extra_services')) {
            $extraServices = json_decode($request->input('extra_services'), true);

            if (is_array($extraServices)) {
                // Delete all existing extra services for this update
                VehicleExtraService::where('vehicle_id', $update->id)->delete();

                // Insert new records
                foreach ($extraServices as $service) {
                    VehicleExtraService::create([
                        'vehicle_id' => $update->id,
                        'extra_service_id' => $service['service_id'],
                        'value' => $service['value'],
                        'price' => $service['price'],
                    ]);
                }
            }
        }

        if ($request->has('vehicle_damage')) {
            $vehicleDamages = json_decode($request->input('vehicle_damage'), true);

            if (is_array($vehicleDamages)) {
                foreach ($vehicleDamages as $index => $damage) {
                    $imageFile = $request->file('damage_image')[$index] ?? null; // Get uploaded file
                    $uploadedImage = $damage['image'] ?? null; // Default to existing image

                    if ($imageFile) {
                        // Store image in storage/app/public/VehicleDamage
                        $uploadedImage = $imageFile->store('VehicleDamage', 'public');
                    } elseif (!empty($uploadedImage) && strpos($uploadedImage, 'data:image') === 0) {
                        // Convert base64 to an image file and store
                        $imageData = explode(',', $uploadedImage)[1]; // Remove metadata
                        $imageName = 'VehicleDamage/' . uniqid() . '.png';
                        Storage::disk('public')->put($imageName, base64_decode($imageData));
                        $uploadedImage = $imageName;
                    }

                    if (!empty($damage['id'])) {
                        VehicleDamage::where('id', $damage['id'])
                            ->where('vehicle_id', $update->id)
                            ->update([
                                'damage_type'     => $damage['name'],
                                'damage_loaction' => $damage['location'],
                                'image'           => $uploadedImage,
                                'description'     => $damage['description'],
                            ]);
                    } else {
                        VehicleDamage::create([
                            'vehicle_id'      => $update->id,
                            'damage_type'     => $damage['name'],
                            'damage_loaction' => $damage['location'],
                            'image'           => $uploadedImage,
                            'description'     => $damage['description'],
                        ]);
                    }
                }
            }
        }


        return response()->json([
            'code' => 200,
            'success' => true,
            'message' => 'Car information updated successfully.'
        ], 200);
    }

    public function vehicleListApi(Request $request)
    {
        $authId = current_user();

        $languageId = $authId->language_id;
        $query = VehicleInfo::with([
            'carType:id,name',
            'brand:id,brand_name',
            'category:id,name',
            'mainLocation:id,name',
            'color:id,name,value'
        ])->select(
            "id",
            "vehicle_image",
            "name",
            "slug",
            "type_id",
            "brand_id",
            "category_id",
            "main_location_id",
            "color_id",
            "vehicle_price",
            "vehicle_basekm",
            "created_at",
            "perma_link",
            "model_id",
            "plate_number",
            "vin",
            "other_location_id",
            "fuel_type_id",
            "odometer",
            "year",
            "transmission_id",
            "mileage",
            "passenger_capacity",
            "num_seats",
            "num_doors",
            "num_airbags",
            "vehicle_video",
            "vehicle_extrakmprice",
            "vehicle_metatitle",
            "vehicle_metadesc",
            "vehicle_metakeywords",
            "features"
        );

        // $adminID = Auth::guard('web')->user()->id;

        // Apply filters if provided
        if (!is_null($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (!is_null($request->vehicle_id) && is_array($request->vehicle_id)) {
            $query->whereIn('id', $request->vehicle_id);
        }

        if (!is_null($request->vehicle_brand_id) && is_array($request->vehicle_brand_id)) {
            $query->whereIn('brand_id', $request->vehicle_brand_id);
        }

        if (!is_null($request->vehicle_type_id) && is_array($request->vehicle_type_id)) {
            $query->whereIn('type_id', $request->vehicle_type_id);
        }

        if (!is_null($request->vehicle_location_id) && is_array($request->vehicle_location_id)) {
            $query->whereIn('main_location_id', $request->vehicle_location_id);
        }

        if (!is_null($request->status)) {
            $query->where('status', $request->status);
        }

        // Sorting logic with default to ascending
        $sortBy = $request->sort_by ?? 'ascending'; // Default to ascending if not provided

        switch ($sortBy) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'ascending':
                $query->orderBy('name', 'asc');
                break;
            case 'descending':
                $query->orderBy('name', 'desc');
                break;
            case 'last_month':
                $query->whereBetween('created_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]);
                break;
            case 'last_7_days':
                $query->where('created_at', '>=', now()->subDays(7));
                break;
        }

        if (!is_null($request->sort_by_date)) {
            $dates = explode(' - ', $request->sort_by_date);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => 400,
                        'message' => __('Invalid date format.'),
                    ], 400);
                }
            }
        }

        $vehicles = $query->where("language_id", $languageId)->get()->map(function ($vehicle) {
            $vehicle->vehicle_image = url('/storage/' . $vehicle->vehicle_image);

            $vehicleMetas = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();

            if ($vehicleMetas) {
                $images = $vehicleMetas->value ? json_decode($vehicleMetas->value) : [];
                $vehicle->multiple_vehicle_images = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $images);
            } else {
                $vehicle->multiple_vehicle_images = [];
            }
            $vehicle->has_multiple_image = count($vehicle->multiple_vehicle_images) > 1;

            $vehicle->avatar_image = 'https://cdn4.iconfinder.com/data/icons/avatars-21/512/avatar-circle-human-male-2-512.png';

            $damageCount = VehicleDamage::where('vehicle_id', $vehicle->id)->count();
            $vehicle->damage_count = $damageCount ?? 0;
            $vehicle->status = 1;

            return $vehicle;
        });

        return response()->json([
            'code' => 200,
            'message' => __('Vehicles list retrieved successfully.'),
            'data' => $vehicles,
        ], 200);
    }


    public function vehicleLists(Request $request)
    {
        $query = VehicleInfo::with([
            'carType:id,name',
            'brand:id,brand_name',
            'category:id,name',
            'mainLocation:id,name',
            'color:id,name,value',
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'reviews:id,vehicle_id,average_ratings'
        ]);

        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        // Initialize variables
        $pickupDatetime = $request->pickup_datetime;
        $returnDatetime = $request->return_datetime;
        $unavailableVehicleIds = [];

        if (!empty($request->location)) {
            $location = Location::where('name', 'LIKE', "%{$request->location}%")->first();

            if (!$location) {

                return response()->json([
                    'code' => 200,
                    'message' => __('Vehicles list retrieved successfully.'),
                    'data' => []
                ], 200);
            }

            if (!empty($pickupDatetime) && !empty($returnDatetime)) {
                $unavailableVehicleIds = Booking::where(function ($query) use ($pickupDatetime, $returnDatetime) {
                    $query->where('start_datetime', '<', $returnDatetime)
                        ->where('end_datetime', '>', $pickupDatetime);
                })->pluck('vehicle_id')->toArray();
            }

            $query->where('main_location_id', $location->id);

            if (!empty($unavailableVehicleIds)) {
                $query->whereNotIn('id', $unavailableVehicleIds);
            }
        }


        if (!empty($request->name)) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if (!empty($request->vehicle_id) && is_array($request->vehicle_id)) {
            $query->whereIn('id', $request->vehicle_id);
        }

        if (!empty($request->vehicle_brand_id) && is_array($request->vehicle_brand_id)) {
            $query->whereIn('brand_id', $request->vehicle_brand_id);
        }

        if (!empty($request->vehicle_type_id) && is_array($request->vehicle_type_id)) {
            $query->whereIn('type_id', $request->vehicle_type_id);
        }

        if (!empty($request->year) && is_array($request->year)) {
            $query->whereIn('year', $request->year);
        }

        if (!empty($request->fuel_type_id) && is_array($request->fuel_type_id)) {
            $query->whereIn('fuel_type_id', $request->fuel_type_id);
        }

        if (!empty($request->color_id) && is_array($request->color_id)) {
            $query->whereIn('color_id', $request->color_id);
        }

        if (!empty($request->transmission_id) && is_array($request->transmission_id)) {
            $query->whereIn('transmission_id', $request->transmission_id);
        }

        if (!empty($request->vehicle_capacity) && is_array($request->vehicle_capacity)) {
            $query->whereIn('passenger_capacity', $request->vehicle_capacity);
        }

        if (isset($request->mileage)) {
            if ($request->mileage == 0) {
                $query->where(function ($q) {
                    $q->where('mileage', 0)->orWhereNull('mileage');
                });
            } elseif ($request->mileage == 1) {
                $query->where('mileage', '>', 0);
            }
        }

        if (!empty($request->feature_id) && is_array($request->feature_id)) {
            $query->where(function ($q) use ($request) {
                foreach ($request->feature_id as $feature) {
                    $q->orWhereJsonContains('features', $feature);
                }
            });
        }

        if (!empty($request->rating) && is_array($request->rating)) {
            $query->whereHas('reviews', function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    foreach ($request->rating as $rate) {
                        $sub->orWhereBetween('average_ratings', [
                            (float) $rate,
                            (float) $rate + 0.99
                        ]);
                    }
                });
            });
        }


        $fromPrice = $request->from_price ?? 1;
        $toPrice = $request->to_price ?? null;

        if (!empty($toPrice)) {
            // If fromPrice is 0 or not set, default to 1
            $fromPrice = ($fromPrice <= 0) ? 1 : $fromPrice;

            $query->whereRaw("
                CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) BETWEEN ? AND ?
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) > 0
            ", [
                $fromPrice,
                $toPrice
            ]);
        }

        if (!empty($request->rent_type)) {
            $rentType = $request->rent_type;

            $query->whereRaw("
                JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) IS NOT NULL
                AND JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].$rentType')) > 0
            ");
        }

        if (!empty($request->vehicle_location_id) && is_array($request->vehicle_location_id)) {
            $query->whereIn('main_location_id', $request->vehicle_location_id);
        }

        if (!empty($request->status)) {
            $query->where('status', $request->status);
        }

        $sortBy = $request->sort_by ?? 'ascending';

        switch ($sortBy) {
            case 'latest':
                $query->orderBy('created_at', 'desc');
                break;

            case 'ascending':
                $query->orderBy('name', 'asc');
                break;

            case 'descending':
                $query->orderBy('name', 'desc');
                break;

            case 'low_to_high':
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) ASC");
                break;

            case 'high_to_low':
                $query->orderByRaw("CAST(JSON_UNQUOTE(JSON_EXTRACT(vehicle_price, '$[0].daily')) AS UNSIGNED) DESC");
                break;

            default:
                // Default sort if no specific sorting criteria
                $query->orderBy('created_at', 'desc');
                break;
        }


        if (!is_null($request->sort_by_date)) {
            $dates = explode(' - ', $request->sort_by_date);
            if (count($dates) === 2) {
                try {
                    $startDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[0]))->startOfDay();
                    $endDate = \Carbon\Carbon::createFromFormat('m/d/Y', trim($dates[1]))->endOfDay();
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                } catch (\Exception $e) {
                    return response()->json([
                        'code' => 400,
                        'message' => __('Invalid date format.'),
                    ], 400);
                }
            }
        }

        $perPage = $request->paginate ?? 1;
        $vehicles = $query->where("language_id", $lang_id)->paginate($perPage);

        $data = $vehicles->map(function ($vehicle) {
            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();

            $vehiclePrices = json_decode($vehicle->vehicle_price, true);
            $filteredPrices = [];

            if (!empty($vehiclePrices)) {
                foreach ($vehiclePrices as $price) {
                    foreach ($price as $key => $value) {
                        if ($value > 0) {
                            $filteredPrices[] = [$key => $value]; // Store each key-value pair as a separate object
                        }
                    }
                }
            }
            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
            if (!empty($vehicle->vehicle_image)) {
                array_unshift($multipleImages, $vehicle->vehicle_image);
            }
            $multipleImages = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $multipleImages);
            $user = null;
            $wishlist = null;
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $wishlist = Wishlist::where('user_id', Auth::guard('web')->id())->where('vehicle_id', $vehicle->id)->first();
            }

            $auth = current_user();
            $authId = $auth?->id;

            $wishlistExists = false;

            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }

            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currencySymbol = $currency->symbol ?? "$";


            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
            $review_count = Review::where("vehicle_id", $vehicle->id)->count();
            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'slug' => $vehicle->slug,
                'vehicle_image' => url('/storage/' . $vehicle->vehicle_image),
                'multiple_vehicle_images' => $multipleImages,
                'has_multiple_image' => count($multipleImages) > 1,
                'avatar_image' => 'https://cdn4.iconfinder.com/data/icons/avatars-21/512/avatar-circle-human-male-2-512.png',
                'brand' => $vehicle->brand->brand_name ?? null,
                'car_type' => $vehicle->carType->name ?? null,
                'category' => $vehicle->category->name ?? null,
                'location' => $vehicle->mainLocation->name ?? null,
                'color' => $vehicle->color->name ?? null,
                'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
                'transmission' => $vehicle->transmission->name ?? null,
                'year' => $vehicle->year,
                'mileage' => $vehicle->mileage,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'num_seats' => $vehicle->num_seats,
                'num_doors' => $vehicle->num_doors,
                'num_airbags' => $vehicle->num_airbags,
                'vehicle_video' => $vehicle->vehicle_video,
                'features' => $vehicle->features,
                'currency' => $currencySymbol,
                'rating' => $rating,
                'wishlist' => $wishlistExists,
                'review_count' => $review_count,
                'price' => !empty($filteredPrices) ? $filteredPrices : null,
                'is_featured' => (bool) rand(0, 1),
                'is_top_rated' => (bool) rand(0, 1),
                'seo_title' => $vehicle->vehicle_metatitle,
                'seo_key' => $vehicle->vehicle_metakeywords,
                'seo_description' => $vehicle->vehicle_metadesc,
                'authenticated' => $user ? true : false,
                'wishlist' => $wishlist ? true : false,
                'created_at' => $vehicle->created_at,
                'status' => $vehicle->status,
            ];
        });


        return response()->json([
            'code' => 200,
            'message' => __('Vehicles list retrieved successfully.'),
            'data' => $data,
            'pagination' => [
                'total' => $vehicles->total(), // Total vehicles count
                'per_page' => $vehicles->perPage(), // Vehicles per page
                'current_page' => $vehicles->currentPage(), // Current page number
                'last_page' => $vehicles->lastPage(), // Last page number
                'from' => $vehicles->firstItem(), // First item number on the page
                'to' => $vehicles->lastItem(), // Last item number on the page
                'next_page_url' => $vehicles->nextPageUrl(), // Next page URL
                'prev_page_url' => $vehicles->previousPageUrl(), // Previous page URL
            ],
        ], 200);
    }




    public function getCarInfo(Request $request)
    {
        try {
            $vehicleSlug = $request->get('vehicle_slug');

            // Find vehicle directly instead of checking twice
            $vehicle = VehicleInfo::where('slug', $vehicleSlug)->first();

            if (!$vehicle) {
                return response()->json(['exists' => 'no'], 404);
            }

            return response()->json(['exists' => 'yes']);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
    }

    public function seasonalInfo(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $vehicleSeasons = VehicleSeason::where("vehicle_id", $vehicleId)->get();

        if ($vehicleSeasons->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No seasonal info found'], 404);
        }

        return response()->json(['success' => true, 'data' => $vehicleSeasons], 200);
    }

    public function tarrifInfo(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $vehicleTrraifs = VehicleTarrif::where("vehicle_id", $vehicleId)->get();

        if ($vehicleTrraifs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No trraif info found'], 404);
        }

        return response()->json(['success' => true, 'data' => $vehicleTrraifs], 200);
    }

    public function documents(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $documents = VehicleMeta::where("vehicle_id", $vehicleId)->get();

        if ($documents->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No documents info found'], 404);
        }

        // Initialize response structure
        $response = [
            'vehicle_images' => [],
            'vehicle_docs' => [],
            'vehicle_policies' => [],
        ];

        foreach ($documents as $document) {
            // Decode JSON value (since values are stored as JSON arrays)
            $values = json_decode($document->value, true);

            if (!is_array($values)) {
                continue;
            }

            // Categorize based on key
            switch ($document->key) {
                case 'vehicle_image':
                    $formattedImages = array_map(function ($image) {
                        return asset('storage/' . $image); // Convert to URL format
                    }, $values);

                    $response['vehicle_images'] = array_merge($response['vehicle_images'], $formattedImages);
                    break;

                case 'vehicle_doc':
                    $response['vehicle_docs'] = array_merge($response['vehicle_docs'], $values);
                    break;

                case 'vehicle_policy':
                    $response['vehicle_policies'] = array_merge($response['vehicle_policies'], $values);
                    break;
            }
        }

        return response()->json(['success' => true, 'data' => $response], 200);
    }

    public function faq(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $vehicleFaqs = VehicleFaq::where("vehicle_id", $vehicleId)->get();

        if ($vehicleFaqs->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No faq info found'], 404);
        }

        return response()->json(['success' => true, 'data' => $vehicleFaqs], 200);
    }

    public function damage(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $vehicleDamages = VehicleDamage::where("vehicle_id", $vehicleId)->get();

        if ($vehicleDamages->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No vehicle damages info found'], 404);
        }

        return response()->json(['success' => true, 'data' => $vehicleDamages], 200);
    }

    public function insurance(Request $request)
    {
        $vehicleId = $request->vehicle_id;

        if (!$vehicleId) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $vehicleInsurance = VehicleInsurance::where("vehicle_id", $vehicleId)
            ->with(['insurance', 'insuranceBenefits']) // Load related data
            ->get();

        if ($vehicleInsurance->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No insurance data found'], 404);
        }

        // Format response data
        $insuranceData = $vehicleInsurance->map(function ($insurance) {
            $benefitCount = $insurance->insuranceBenefits->count();
            $formattedBenefits = str_pad($benefitCount, 2, '0', STR_PAD_LEFT);

            return [
                'id' => $insurance->id,
                'vehicle_id' => $insurance->vehicle_id,
                'insurances_id' => $insurance->insurances_id,
                'insurance_name' => optional($insurance->insurance)->insurance_name, // Get insurance name
                'value' => $insurance->value,
                'price' => $insurance->price,
                'benefits' => $formattedBenefits, // Count formatted
                'created_at' => $insurance->created_at,
                'updated_at' => $insurance->updated_at,
                'deleted_at' => $insurance->deleted_at,
            ];
        });

        return response()->json(['success' => true, 'data' => $insuranceData], 200);
    }

    public function getModel(Request $request)
    {
        $models = CarModel::where('brand_id', $request->brand_id)->get(['id', 'model_name']);

        return response()->json($models);
    }

    public  function vehicleDetailsList(Request $request)
    {
        $vehicleSlug = $request->vehicle_slug;

        if (!$vehicleSlug) {
            return response()->json(['success' => false, 'message' => 'Vehicle ID is required'], 400);
        }

        $query = VehicleInfo::with([
            'carType:id,name',
            'brand:id,brand_name',
            'category:id,name',
            'mainLocation:id,name',
            'color:id,name,value',
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'extraservices.extraService:id,name,icon,description,image',
            'faqs:id,vehicle_id,question,answer',
            'damages:id,Vehicle_id,damage_type,damage_loaction,image,description',
            'tariffs:id,vehicle_id,tariff_title,tariff_daily_price,tariff_from_days,tariff_to_days,tariff_base_km,tariff_extra_price',
            'seasonals:id,vehicle_id,seasonal_title,seasonal_start_date,seasonal_end_date,seasonal_daily_rate,seasonal_weekly_rate,seasonal_monthly_rate,seasonal_late_fee',
            'owner.userDetails:id,user_id,profile_image'
        ]);


        $vehicles = $query->where('slug', $vehicleSlug)->get();

        if ($vehicles->isEmpty()) {
            return response()->json([
                'code'  => 404,
                'success' => false,
                'message' => 'No vehicle found with the provided slug.',
                'data' => [],
            ], 404);
        }

        $data = [];

        foreach ($vehicles as $vehicle) {
            $featureIds = json_decode($vehicle->features, true) ?? [];
            $featureNames = SafetyFeature::whereIn('id', $featureIds)->pluck('feature');

            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();
            $vehiclepolicys = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_policy')
                ->first();
            $vehicleDoc = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_doc')
                ->first();

            $vehiclePrices = json_decode($vehicle->vehicle_price, true);
            $filteredPrices = [];

            if (!empty($vehiclePrices)) {
                foreach ($vehiclePrices as $price) {
                    foreach ($price as $key => $value) {
                        if ($value > 0) {
                            $filteredPrices[] = [$key => $value]; // Store each key-value pair as a separate object
                        }
                    }
                }
            }

            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
            $multiplePolicy = $vehiclepolicys ? json_decode($vehiclepolicys->value, true) : [];
            $multipleDoc = $vehicleDoc ? json_decode($vehicleDoc->value, true) : [];

            if (!empty($vehicle->vehicle_image)) {
                array_unshift($multipleImages, $vehicle->vehicle_image);
            }

            $multipleImages = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $multipleImages);
            $user = null;
            $wishlist = null;
            if (Auth::guard('web')->check()) {
                $user = Auth::guard('web')->user();
                $wishlist = Wishlist::where('user_id', Auth::id())->where('vehicle_id', $vehicle->id)->first();
            }
            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;

            $auth = current_user();
            $authId = $auth?->id;

            $wishlistExists = false;

            $currencySetting = GeneralSetting::where("key", "currency_symbol")->first();
            $currency = null;

            if ($currencySetting && $currencySetting->value) {
                $currency = Currency::find($currencySetting->value);
            }

            $currencySymbol = $currency->symbol ?? "$";

            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }
            $data = [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'slug' => $vehicle->slug,
                'vehicle_image' => url('/storage/' . $vehicle->vehicle_image),
                'multiple_vehicle_doc' => array_map(fn($doc) => url('storage/vehicleDoc/' . basename($doc)), $multipleDoc),
                'multiple_vehicle_policy' => array_map(fn($policy) => url('storage/vehiclePolicy/' . basename($policy)), $multiplePolicy),
                'multiple_vehicle_images' => $multipleImages,
                'has_multiple_image' => count($multipleImages) > 1,
                'avatar_image' => 'https://cdn4.iconfinder.com/data/icons/avatars-21/512/avatar-circle-human-male-2-512.png',
                'brand' => $vehicle->brand->brand_name ?? null,
                'car_type' => $vehicle->carType->name ?? null,
                'category' => $vehicle->category->name ?? null,
                'location' => $vehicle->mainLocation->name ?? null,
                'color' => $vehicle->color->name ?? null,
                'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
                'transmission' => $vehicle->transmission->name ?? null,
                'wishlist' => $wishlistExists,
                'year' => $vehicle->year,
                'mileage' => $vehicle->mileage,
                'vin' => $vehicle->vin,
                'rating' => $rating,
                'currency' => $currencySymbol,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'num_seats' => $vehicle->num_seats,
                'num_doors' => $vehicle->num_doors,
                'num_airbags' => $vehicle->num_airbags,
                'vehicle_video' => $vehicle->vehicle_video,
                'price' => !empty($filteredPrices) ? $filteredPrices : null,
                'created_at' => $vehicle->created_at,
                'features' => $featureNames,
                'currency' => getDefaultCurrencySymbol(),
                'seo_title' => $vehicle->vehicle_metatitle,
                'seo_key' => $vehicle->vehicle_metakeywords,
                'seo_description' => $vehicle->vehicle_metadesc,
                'is_featured' => (bool) rand(0, 1),
                'is_top_rated' => (bool) rand(0, 1),
                'authenticated' => $user ? true : false,
                'wishlist' => $wishlist ? true : false,
                'description' => $vehicle->description,
                'extraservice' => $vehicle->extraservices->map(function ($extraservice) {
                    return [
                        'extra_service_id' => $extraservice->extra_service_id,
                        'value' => $extraservice->value,
                        'price' => $extraservice->price,
                        'name' => optional($extraservice->extraService)->name,
                        'icon' => url('/storage/' . optional($extraservice->extraService)->icon), // Convert icon to full URL
                        'description' => optional($extraservice->extraService)->description,
                        'image' => url('/storage/' . optional($extraservice->extraService)->image), // Convert image to full URL
                    ];
                }),
                'tariff' => $vehicle->tariffs->map(function ($tariff) {
                    return [
                        'tariff_title' => $tariff->tariff_title,
                        'tariff_daily_price' => $tariff->tariff_daily_price,
                        'tariff_from_days' => $tariff->tariff_from_days,
                        'tariff_to_days' => $tariff->tariff_to_days,
                        'tariff_base_km' => $tariff->tariff_base_km,
                        'tariff_extra_price' => $tariff->tariff_extra_price,
                    ];
                }),
                'seasonal' => $vehicle->seasonals->map(function ($seasonal) {
                    return [
                        'seasonal_title' => $seasonal->seasonal_title,
                        'seasonal_start_date' => $seasonal->seasonal_start_date,
                        'seasonal_end_date' => $seasonal->seasonal_end_date,
                        'seasonal_daily_rate' => $seasonal->seasonal_daily_rate,
                        'seasonal_weekly_rate' => $seasonal->seasonal_weekly_rate,
                        'seasonal_monthly_rate' => $seasonal->seasonal_monthly_rate,
                        'seasonal_late_fee' => $seasonal->seasonal_late_fee,
                    ];
                }),
                'faqs' => $vehicle->faqs->map(function ($faq) {
                    return [
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    ];
                }),
                'damages' => $vehicle->damages->map(function ($damage) {
                    return [
                        'damage_type' => $damage->damage_type,
                        'damage_loaction' => $damage->damage_loaction,
                        'image' => $damage->image,
                        'description' => $damage->description,
                    ];
                }),
                'owner_details' => $vehicle->owner ? [
                    'name' => $vehicle->owner->name,
                    'phone_number' => $vehicle->owner->mobile_number,
                    'email' => $vehicle->owner->email,
                    'image' => $vehicle->owner->userDetails ? url('/storage/' . $vehicle->owner->userDetails->profile_image) : null
                ] : null
            ];
        }

        return response()->json(['code' => 200, 'success' => true, 'data' => $data], 200);
    }

    public function deleteVehicleImage(Request $request)
    {
        // dd($request);
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'image_path' => 'required|string',
        ]);

        $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
            ->where('key', 'vehicle_image')
            ->first();

        if (!$vehicleMeta) {
            return response()->json(['success' => false, 'message' => 'Vehicle images not found.'], 404);
        }

        $images = json_decode($vehicleMeta->value, true);

        // Find and remove image
        if (($key = array_search($request->image_path, $images)) !== false) {
            unset($images[$key]);
            Storage::delete($request->image_path); // Delete from storage
            $vehicleMeta->value = json_encode(array_values($images)); // Update JSON
            $vehicleMeta->save();

            return response()->json(['success' => true, 'message' => 'Image deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Image not found in database.'], 404);
    }

    public function deleteVehiclePolicy(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicle_metas,vehicle_id',
            'file_path'  => 'required|string',
        ]);

        // Ensure the correct path format
        $filePath = 'vehiclePolicy/' . $request->file_path;

        $vehicleMeta = VehicleMeta::where('vehicle_id', $request->vehicle_id)
            ->where('key', 'vehicle_policy')
            ->first();

        if (!$vehicleMeta) {
            return response()->json(['success' => false, 'message' => 'Policy files not found.'], 404);
        }

        $policyFiles = json_decode($vehicleMeta->value, true);

        // Find and remove the file from the array
        if (($key = array_search($filePath, $policyFiles)) !== false) {
            unset($policyFiles[$key]);
            Storage::delete($filePath); // Delete from storage
            $vehicleMeta->value = json_encode(array_values($policyFiles)); // Update JSON
            $vehicleMeta->save();

            return response()->json(['success' => true, 'message' => 'Policy file deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Policy file not found in database.'], 404);
    }

    public function vehicleIntrestLists(Request $request)
    {
        $authUser = current_user();

        $lang_id = null;

        if ($authUser && !empty($authUser->language_id)) {
            $lang_id = $authUser->language_id;
        } elseif (App::getLocale()) {
            $currentLocale = App::getLocale();
            $language = TranslationLanguage::where('code', $currentLocale)->first();
            $lang_id = $language->id ?? null;
        } else {
            $defaultLang = Language::select("language_id")->where("default", 1)->first();
            $lang_id = $defaultLang->language_id ?? 1;
        }

        $vehicles = VehicleInfo::with([
            'carType:id,name',
            'brand:id,brand_name',
            'category:id,name',
            'mainLocation:id,name',
            'color:id,name,value',
            'fuel_type:id,fuel_type',
            'transmission:id,name',
            'reviews:id,vehicle_id,average_ratings'
        ])->where("language_id", $lang_id)->take(6)->get();

        $data = $vehicles->map(function ($vehicle) {
            $vehicleImages = VehicleMeta::where('vehicle_id', $vehicle->id)
                ->where('key', 'vehicle_image')
                ->first();

            $vehiclePrices = json_decode($vehicle->vehicle_price, true);
            $filteredPrices = [];

            if (!empty($vehiclePrices)) {
                foreach ($vehiclePrices as $price) {
                    foreach ($price as $key => $value) {
                        if ($value > 0) {
                            $filteredPrices[] = [$key => $value];
                        }
                    }
                }
            }

            $multipleImages = $vehicleImages ? json_decode($vehicleImages->value, true) : [];
            if (!empty($vehicle->vehicle_image)) {
                array_unshift($multipleImages, $vehicle->vehicle_image);
            }
            $multipleImages = array_map(fn($img) => url('storage/vehicles/' . basename($img)), $multipleImages);

            $auth = current_user();
            $authId = $auth?->id;

            $wishlistExists = false;
            if ($authId) {
                $wishlistExists = Wishlist::where("user_id", $authId)
                    ->where("vehicle_id", $vehicle->id)
                    ->exists();
            }

            $rating = Review::where("vehicle_id", $vehicle->id)->value("average_ratings") ?? 0;
            $review_count = Review::where("vehicle_id", $vehicle->id)->count();

            return [
                'id' => $vehicle->id,
                'name' => $vehicle->name,
                'slug' => $vehicle->slug,
                'vehicle_image' => url('/storage/' . $vehicle->vehicle_image),
                'avatar_image' => 'https://cdn4.iconfinder.com/data/icons/avatars-21/512/avatar-circle-human-male-2-512.png',
                'brand' => $vehicle->brand->brand_name ?? null,
                'car_type' => $vehicle->carType->name ?? null,
                'category' => $vehicle->category->name ?? null,
                'location' => $vehicle->mainLocation->name ?? null,
                'color' => $vehicle->color->name ?? null,
                'fuel_type' => $vehicle->fuel_type->fuel_type ?? null,
                'transmission' => $vehicle->transmission->name ?? null,
                'year' => $vehicle->year,
                'mileage' => $vehicle->mileage,
                'passenger_capacity' => $vehicle->passenger_capacity,
                'num_seats' => $vehicle->num_seats,
                'num_doors' => $vehicle->num_doors,
                'num_airbags' => $vehicle->num_airbags,
                'vehicle_video' => $vehicle->vehicle_video,
                'features' => $vehicle->features,
                'currency' => "$",
                'rating' => $rating,
                'wishlist' => $wishlistExists,
                'review_count' => $review_count,
                'price' => !empty($filteredPrices) ? $filteredPrices : null,
                'is_featured' => (bool) rand(0, 1),
                'is_top_rated' => (bool) rand(0, 1),
                'seo_title' => $vehicle->vehicle_metatitle,
                'seo_key' => $vehicle->vehicle_metakeywords,
                'seo_description' => $vehicle->vehicle_metadesc,
                'authenticated' => Auth::guard('web')->check(),
                'created_at' => $vehicle->created_at,
                'status' => $vehicle->status,
            ];
        });

        $html = view('frontend.home.list.recommended-vehicles', compact('data'))->render();
        return response()->json([
            'code' => 200,
            'message' => __('Vehicles retrieved successfully.'),
            // 'data' => $data,
            'html' => $html
        ], 200);
    }
}
