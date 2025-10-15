<?php

namespace Modules\CarInfo\Repositories\Support;

use App\Services\ImageResizer;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Modules\CarInfo\Models\Category;
use Modules\CarInfo\Models\VehicleDamage;
use Modules\CarInfo\Models\VehicleExtraService;
use Modules\CarInfo\Models\VehicleFaq;
use Modules\CarInfo\Models\VehicleInfo;
use Modules\CarInfo\Models\VehicleInsurance;
use Modules\CarInfo\Models\VehicleMeta;
use Modules\CarInfo\Models\VehicleSeason;
use Modules\CarInfo\Models\VehicleTarrif;

class VehicleInfoWriter extends VehicleRepositoryBase
{
    public function __construct(private readonly ImageResizer $imageResizer)
    {
    }

    public function create(Request $request): VehicleInfo
    {
        $authId = Auth::guard('admin')->id();

        $vehicle = VehicleInfo::create($this->buildVehicleAttributes(
            $request,
            null,
            $authId,
            false
        ));

        $this->syncInitialMedia($request, $vehicle);
        $this->syncFaqs($request, $vehicle, false);
        $this->syncInsurance($request, $vehicle, false);
        $this->syncTariffs($request, $vehicle);
        $this->syncSeasonals($request, $vehicle);
        $this->syncExtraServices($request, $vehicle, true);
        $this->syncDamages($request, $vehicle, 'vehicles/damages');

        return $vehicle;
    }

    public function update(Request $request): VehicleInfo
    {
        $vehicle = VehicleInfo::findOrFail($request->vehicle_id);
        $authId = Auth::guard('admin')->id();

        $vehicle->update($this->buildVehicleAttributes(
            $request,
            $vehicle,
            $authId,
            true
        ));

        $this->syncVehicleFiles($request, $vehicle);
        $this->syncFaqs($request, $vehicle, true);
        $this->syncInsurance($request, $vehicle, true);
        $this->syncTariffs($request, $vehicle);
        $this->syncSeasonals($request, $vehicle);
        $this->syncExtraServices($request, $vehicle, false);
        $this->syncDamages($request, $vehicle, 'vehicles/damage');

        return $vehicle;
    }

    private function buildVehiclePricePayload(Request $request): string
    {
        $mapping = [
            'daily'   => ['daily_price'],
            'weekly'  => ['weekly_price'],
            'monthly' => ['montly_price', 'monthly_price'],
            'yearly'  => ['yearly_price'],
        ];

        $prices = [];
        foreach ($mapping as $key => $fields) {
            foreach ($fields as $field) {
                if ($request->filled($field)) {
                    $prices[$key] = $request->input($field);
                    break;
                }
            }
        }

        return json_encode([$prices]);
    }

    private function determineKilometers(Request $request): array
    {
        if ($request->input('unlimited') === 'on') {
            return [null, null];
        }

        return [
            $request->input('basic_kilometer'),
            $request->input('extra_kilometer'),
        ];
    }

    private function getCategorySlug(?int $categoryId): ?string
    {
        return Category::find($categoryId)?->slug;
    }

    private function buildVehicleAttributes(Request $request, ?VehicleInfo $vehicle, ?int $authId, bool $isUpdate): array
    {
        [$baseKm, $extraKm] = $this->determineKilometers($request);
        $pricePayload = $this->buildVehiclePricePayload($request);
        $categorySlug = $this->getCategorySlug($request->vehicle_category_id);
        $imagePath = $this->uploadVehicleImage($request->file('vehicle_image'), $vehicle?->vehicle_image);

        $data = [
            'vehicle_image'        => $imagePath,
            'name'                 => $request->title,
            'slug'                 => Str::slug($request->title),
            'perma_link'           => $request->perma_link,
            'type_id'              => $request->vehicle_type_id,
            'brand_id'             => $request->vehicle_brand_id,
            'model_id'             => $request->vehicle_model_id,
            'category_id'          => $request->vehicle_category_id,
            'type'                 => $categorySlug,
            'plate_number'         => $request->plate_number,
            'vin'                  => $request->vin_number,
            'main_location_id'     => $request->main_location_id,
            'other_location'       => $request->other_location,
            'other_location_id'    => $request->other_location_id,
            'fuel_type_id'         => $request->vehicle_fuel_id,
            'odometer'             => $request->odometer,
            'color_id'             => $request->vehicle_color_id,
            'year'                 => $request->vehicle_year,
            'transmission_id'      => $request->vehicle_transmission_id,
            'mileage'              => $request->vehicle_mileage,
            'passenger_capacity'   => $request->vehicle_passenger,
            'water_tight'          => $request->water_tight,
            'sliding'              => $request->sliding,
            'hatch'                => $request->hatch,
            'vehicle_price'        => $pricePayload,
            'num_seats'            => $request->num_seats,
            'num_doors'            => $request->num_doors,
            'num_airbags'          => $request->num_airbags,
            'vehicle_basekm'       => $baseKm,
            'vehicle_extrakmprice' => $extraKm,
            'vehicle_video'        => $request->car_video,
            'vehicle_metatitle'    => $request->seo_title,
            'vehicle_metakeywords' => $request->seo_key,
            'vehicle_metadesc'     => $request->seo_description,
            'features'             => $request->feature_id,
            'description'          => $request->description,
            'created_by'           => $authId,
        ];

        if ($isUpdate) {
            $data['parent_id'] = (int) $request->parent_id;
            if ($request->filled('language_id')) {
                $data['language_id'] = (int) $request->language_id;
            }
        } else {
            $language = $request->input('lang_id', $request->input('language_id'));
            if ($language !== null) {
                $data['language_id'] = (int) $language;
            }
        }

        return $data;
    }

    private function uploadVehicleImage(?UploadedFile $file, ?string $existing = null): ?string
    {
        if (!$file instanceof UploadedFile || !$file->isValid()) {
            return $existing;
        }

        return $this->imageResizer->uploadFile($file, self::VEHICLE_IMAGE_PATH, $existing);
    }

    private function syncInitialMedia(Request $request, VehicleInfo $vehicle): void
    {
        $images = $request->file('car_images');
        if ($images) {
            $paths = [];
            foreach ((array) $images as $image) {
                if ($image instanceof UploadedFile && $image->isValid()) {
                    $paths[] = $this->imageResizer->uploadFile($image, self::VEHICLE_IMAGE_PATH);
                }
            }

            if ($paths) {
                VehicleMeta::create([
                    'vehicle_id' => $vehicle->id,
                    'key'        => 'vehicle_image',
                    'value'      => json_encode($paths),
                ]);
            }
        }

        $this->storeDocumentFiles($request->file('car_document'), $vehicle->id, 'vehicle_doc', 'vehicles/document');
        $this->storeDocumentFiles($request->file('policy_document'), $vehicle->id, 'vehicle_policy', 'vehicles/policy');
    }

    private function storeDocumentFiles($files, int $vehicleId, string $key, string $path): void
    {
        if (empty($files)) {
            return;
        }

        $paths = [];
        foreach ((array) $files as $file) {
            $paths[] = uploadFile($file, $path);
        }

        if ($paths) {
            VehicleMeta::create([
                'vehicle_id' => $vehicleId,
                'key'        => $key,
                'value'      => json_encode($paths),
            ]);
        }
    }

    private function syncVehicleFiles(Request $request, VehicleInfo $vehicle): void
    {
        $this->mergeUploadedFiles($request->file('car_images'), $vehicle, 'vehicle_image', self::VEHICLE_IMAGE_PATH);
        $this->mergeUploadedFiles($request->file('policy_document'), $vehicle, 'vehicle_policy', 'vehicles/policy');
        $this->mergeUploadedFiles($request->file('car_document'), $vehicle, 'vehicle_doc', 'vehicles/document');
    }

    private function mergeUploadedFiles($files, VehicleInfo $vehicle, string $key, string $path): void
    {
        if (empty($files)) {
            return;
        }

        $files = is_array($files) ? $files : [$files];
        $uploaded = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploaded[] = $file->store($path, 'public');
            } else {
                $uploaded[] = (string) $file;
            }
        }

        if (!$uploaded) {
            return;
        }

        $meta = VehicleMeta::firstOrCreate(
            ['vehicle_id' => $vehicle->id, 'key' => $key],
            ['value' => json_encode([])]
        );

        $existing = json_decode($meta->value, true) ?? [];
        $meta->update(['value' => json_encode(array_merge($existing, $uploaded))]);
    }

    private function syncFaqs(Request $request, VehicleInfo $vehicle, bool $replace): void
    {
        if (!$request->has('vehicle_faq')) {
            return;
        }

        $faqs = json_decode($request->vehicle_faq, true);
        if (!is_array($faqs)) {
            return;
        }

        if ($replace) {
            VehicleFaq::where('vehicle_id', $vehicle->id)->delete();
            foreach ($faqs as $faq) {
                VehicleFaq::create([
                    'vehicle_id' => $vehicle->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'],
                ]);
            }
            return;
        }

        foreach ($faqs as $faq) {
            if (!empty($faq['id'])) {
                VehicleFaq::where('id', $faq['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update([
                        'question' => $faq['question'],
                        'answer'   => $faq['answer'],
                    ]);
            } else {
                VehicleFaq::create([
                    'vehicle_id' => $vehicle->id,
                    'question'   => $faq['question'],
                    'answer'     => $faq['answer'],
                ]);
            }
        }
    }

    private function syncInsurance(Request $request, VehicleInfo $vehicle, bool $replace): void
    {
        if (!$request->has('vehicle_insurance')) {
            return;
        }

        $insurances = json_decode($request->vehicle_insurance, true);
        if (!is_array($insurances)) {
            return;
        }

        if ($replace) {
            VehicleInsurance::where('vehicle_id', $vehicle->id)->delete();
        }

        foreach ($insurances as $insurance) {
            if (!empty($insurance['id']) && !empty($insurance['price']) && !empty($insurance['type'])) {
                $type = strtolower($insurance['type']) === '%' || strtolower($insurance['type']) === 'percentage' ? 'Percentage' : 'Fixed';
                VehicleInsurance::create([
                    'vehicle_id'    => $vehicle->id,
                    'insurances_id' => $insurance['id'],
                    'value'         => $type,
                    'price'         => $insurance['price'],
                ]);
            }
        }
    }

    private function syncTariffs(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('tariff')) {
            return;
        }

        $tariffs = json_decode($request->tariff, true);
        if (!is_array($tariffs)) {
            return;
        }

        foreach ($tariffs as $tariff) {
            $data = [
                'tariff_title'       => $tariff['title'],
                'tariff_daily_price' => $tariff['daily_price'],
                'tariff_from_days'   => $tariff['from_days'],
                'tariff_to_days'     => $tariff['to_days'],
                'tariff_base_km'     => $tariff['base_km'],
                'tariff_extra_price' => $tariff['extra_price'],
            ];

            if (!empty($tariff['id'])) {
                VehicleTarrif::where('id', $tariff['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update($data);
            } else {
                VehicleTarrif::create(array_merge($data, ['vehicle_id' => $vehicle->id]));
            }
        }
    }

    private function syncSeasonals(Request $request, VehicleInfo $vehicle): void
    {
        if (!$request->has('seasonal')) {
            return;
        }

        $seasonals = json_decode($request->seasonal, true);
        if (!is_array($seasonals)) {
            return;
        }

        foreach ($seasonals as $season) {
            $data = [
                'seasonal_title'        => $season['title'],
                'seasonal_start_date'   => $season['start_date'],
                'seasonal_end_date'     => $season['end_date'],
                'seasonal_daily_rate'   => $season['daily_rate'],
                'seasonal_weekly_rate'  => $season['weekly_rate'],
                'seasonal_monthly_rate' => $season['monthly_rate'],
                'seasonal_late_fee'     => $season['late_fee'],
            ];

            if (!empty($season['id'])) {
                VehicleSeason::where('id', $season['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update($data);
            } else {
                VehicleSeason::create(array_merge($data, ['vehicle_id' => $vehicle->id]));
            }
        }
    }

    private function syncExtraServices(Request $request, VehicleInfo $vehicle, bool $replace): void
    {
        if (!$request->has('extra_services')) {
            return;
        }

        $services = json_decode($request->extra_services, true);
        if (!is_array($services)) {
            return;
        }

        if ($replace) {
            VehicleExtraService::where('vehicle_id', $vehicle->id)->delete();
        }

        foreach ($services as $service) {
            $data = [
                'vehicle_id'       => $vehicle->id,
                'extra_service_id' => $service['service_id'],
                'value'            => $service['value'],
                'price'            => $service['price'],
            ];

            if ($replace) {
                VehicleExtraService::create($data);
                continue;
            }

            $existing = VehicleExtraService::where('vehicle_id', $vehicle->id)
                ->where('extra_service_id', $service['service_id'])
                ->first();

            if ($existing) {
                $existing->update($data);
            } else {
                VehicleExtraService::create($data);
            }
        }
    }

    private function syncDamages(Request $request, VehicleInfo $vehicle, string $storagePath): void
    {
        if (!$request->has('vehicle_damage')) {
            return;
        }

        $damages = json_decode($request->vehicle_damage, true);
        if (!is_array($damages)) {
            return;
        }

        $damageFiles = $request->allFiles()['damage_image'] ?? null;

        foreach ($damages as $index => $damage) {
            $uploadedImage = $damage['image'] ?? null;
            $imageFile = is_array($damageFiles) ? $damageFiles[$index] ?? null : ($damageFiles ?? null);

            if ($imageFile instanceof UploadedFile) {
                $uploadedImage = $imageFile->store($storagePath, 'public');
            } elseif (!empty($uploadedImage) && str_starts_with($uploadedImage, 'data:image')) {
                $imageData = explode(',', $uploadedImage)[1];
                $imageName = $storagePath . '/' . uniqid('', true) . '.png';
                Storage::disk('public')->put($imageName, base64_decode($imageData));
                $uploadedImage = $imageName;
            }

            $data = [
                'vehicle_id'      => $vehicle->id,
                'damage_type'     => $damage['name'],
                'damage_loaction' => $damage['location'],
                'image'           => $uploadedImage,
                'description'     => $damage['description'],
            ];

            if (!empty($damage['id'])) {
                VehicleDamage::where('id', $damage['id'])
                    ->where('vehicle_id', $vehicle->id)
                    ->update($data);
            } else {
                VehicleDamage::create($data);
            }
        }
    }
}
