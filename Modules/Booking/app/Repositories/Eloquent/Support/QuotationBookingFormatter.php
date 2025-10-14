<?php

namespace Modules\Booking\Repositories\Eloquent\Support;

use Modules\Booking\Models\Booking;
use Modules\Booking\Repositories\Eloquent\QuotationRepository;
use Modules\CarInfo\Models\ExtraService;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;

class QuotationBookingFormatter
{
    public function formatListBooking($booking)
    {
        $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
        $booking->vehicle_image = uploadedAsset($this->resolveVehicleImage($booking->vehicle_image));
        $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);

        return $booking;
    }

    public function formatBasicDetails($booking): void
    {
        $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
        $booking->vehicle_image = uploadedAsset($this->resolveVehicleImage($booking->vehicle_image));

        if (!empty($booking->insurance)) {
            $booking->insurance_formatted = json_decode($booking->insurance, true);
        }

        if ($booking->extra_service) {
            $booking->extra_service_formatted = json_decode($booking->extra_service, true);
        }

        $booking->booking_status_text = Booking::getStatusLabel((int) $booking->booking_status);
    }

    public function formatDetailedBooking($booking): void
    {
        $this->formatImages($booking);
        $this->formatExtraServices($booking);
        $this->formatInsurance($booking);
        $this->formatMisc($booking);
    }

    private function formatImages($booking): void
    {
        $booking->customer_image = uploadedAsset($booking->customer_image, 'profile');
        $booking->driver_image = uploadedAsset($booking->driver_image, 'profile');
        $booking->vehicle_image = uploadedAsset($this->resolveVehicleImage($booking->vehicle_image));
    }

    private function formatExtraServices($booking): void
    {
        $booking->extra_service_count = 0;
        $booking->extra_service_names = [];
        $booking->extra_service_formatted = [];

        if (empty($booking->extra_service)) {
            return;
        }

        $extraServiceArray = json_decode($booking->extra_service, true);

        if (!is_array($extraServiceArray)) {
            return;
        }

        $booking->extra_service_formatted = $extraServiceArray;
        $booking->extra_service_count = count($extraServiceArray);

        $extraServiceIds = collect($extraServiceArray)
            ->pluck('id')
            ->toArray();

        $booking->extra_service_names = ExtraService::whereIn('id', $extraServiceIds)
            ->pluck('name')
            ->toArray();
    }

    private function formatInsurance($booking): void
    {
        $booking->insurance_count = 0;
        $booking->insurance_names = [];
        $booking->insurance_benefits_formatted = [];
        $booking->insurance_formatted = [];

        if (empty($booking->insurance)) {
            return;
        }

        $insuranceArray = json_decode($booking->insurance, true);

        if (!is_array($insuranceArray)) {
            return;
        }

        $booking->insurance_formatted = $insuranceArray;
        $booking->insurance_count = count($insuranceArray);

        $insuranceIds = collect($insuranceArray)
            ->pluck('id')
            ->toArray();

        $booking->insurance_names = Insurance::whereIn('id', $insuranceIds)
            ->pluck('insurance_name')
            ->toArray();

        $booking->insurance_benefits_formatted = InsuranceBenefit::whereIn('insurance_id', $insuranceIds)
            ->pluck('benefit')
            ->toArray();
    }

    private function formatMisc($booking): void
    {
        $status = is_numeric($booking->booking_status) ? (int) $booking->booking_status : 4;
        $booking->booking_status_text = Booking::getStatusLabel($status);
        $booking->currency_symbol = getDefaultCurrencySymbol();

        if ($booking->delivery_type) {
            $booking->delivery_type = $booking->delivery_type === 'self_pickup' ? 'Self Pickup' : 'Delivery';
        }

        $fields = [
            'driver_price',
            'vehicle_price',
            'vehicle_total_price',
            'total_insurance_price',
            'total_extra_service_price',
            'final_price',
        ];

        foreach ($fields as $field) {
            $booking->$field = number_format((float) ($booking->$field ?? 0), 2, '.', '');
        }
    }

    private function resolveVehicleImage(?string $vehicleImagePath): string
    {
        $vehicleImagePath = $vehicleImagePath ?? '';
        $filename = basename($vehicleImagePath);
        $newPath = QuotationRepository::VEHICLE_IMAGE_PATH . $filename;
        $file = public_path(QuotationRepository::STORAGE_PATH . $newPath);

        if ($filename && file_exists($file)) {
            return $newPath;
        }

        return $vehicleImagePath;
    }
}
