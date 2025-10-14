<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Exception;
use Illuminate\Support\Facades\File;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\BusinessSettingRepositoryInterface;

class BusinessSettingRepository implements BusinessSettingRepositoryInterface
{
    private const APP_PUBLIC = 'app/public/';

    public function __construct(private readonly ImageResizer $imageResizer)
    {
    }

    public function saveRentalSettings(array $data): void
    {
        $settings = [
            'minAdvanceReservation' => $data['minAdvanceReservation'] ?? null,
            'maxAdvanceReservation' => $data['maxAdvanceReservation'] ?? null,
            'cancellationBuffer'    => $data['cancellationBuffer'] ?? null,
            'rescheduleBuffer'      => $data['rescheduleBuffer'] ?? null,
            'faq'                   => $data['faq'] ?? null,
            'damages'               => $data['damages'] ?? null,
            'extraService'          => $data['extraService'] ?? null,
            'booking'               => $data['booking'] ?? null,
            'enquiries'             => $data['enquiries'] ?? null,
            'reservation'           => $data['reservation'] ?? null,
            'seasonalPricing'       => $data['seasonalPricing'] ?? null,
            'pricing'               => $data['pricing'] ?? null,
        ];

        foreach ($settings as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function saveInvoiceSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'] ?? 9;

            $this->handleInvoiceLogo($data['invoice_logo'] ?? null, $data['is_remove_image'] ?? false, $groupId);

            $settings = [
                'invoice_prefix'       => $data['invoice_prefix'] ?? null,
                'invoice_due'          => $data['invoice_due'] ?? null,
                'invoice_round_off'    => $data['invoice_round_off'] ?? null,
                'round_off_enabled'    => ($data['round_off_enabled'] ?? 'off') === 'on' ? 1 : 0,
                'show_company_details' => ($data['show_company_details'] ?? 'off') === 'on' ? 1 : 0,
                'invoice_terms'        => $data['invoice_terms'] ?? null,
            ];

            $this->saveSettings($settings, $groupId);
        } catch (Exception $e) {
            \Log::error('Invoice settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    private function handleInvoiceLogo(?\Illuminate\Http\UploadedFile $file, bool $isRemove, int $groupId): void
    {
        if ($file instanceof \Illuminate\Http\UploadedFile) {
            $existing = GeneralSetting::where('key', 'invoice_logo')->first();
            $oldPath = $existing->value ?? null;

            $relativePath = $this->imageResizer->uploadFile(
                $file,
                'invoices',
                $oldPath,
                [
                    'width'     => 300,
                    'height'    => 150,
                    'thumbnail' => true
                ]
            );

            GeneralSetting::updateOrCreate(
                ['key' => 'invoice_logo'],
                ['value' => $relativePath, 'group_id' => $groupId]
            );
        }

        if ($isRemove) {
            $this->removeInvoiceLogo();
        }
    }

    private function removeInvoiceLogo(): void
    {
        $existing = GeneralSetting::where('key', 'invoice_logo')->first();
        if (!$existing || empty($existing->value)) {
            return;
        }

        $paths = [
            storage_path(self::APP_PUBLIC . $existing->value),
            storage_path(self::APP_PUBLIC . str_replace('invoices/', 'invoices/thumbnail/', $existing->value)),
        ];

        foreach ($paths as $path) {
            if (File::exists($path)) {
                File::delete($path);
            }
        }

        $existing->update(['value' => '']);
    }

    private function saveSettings(array $settings, int $groupId): void
    {
        foreach ($settings as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group_id' => $groupId]
            );
        }
    }
}
