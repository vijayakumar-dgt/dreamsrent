<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use App\Services\ImageResizer;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Repositories\Contracts\AppearanceSettingRepositoryInterface;

class AppearanceSettingRepository implements AppearanceSettingRepositoryInterface
{
    private const APP_PUBLIC = 'app/public/';

    public function __construct(private readonly ImageResizer $imageResizer)
    {
    }

    public function storeSeoSettings(array $data, ?int $groupId = 6): void
    {
        $file = $data['metaImage'] ?? null;
        unset($data['metaImage'], $data['_token']);

        $existing = GeneralSetting::where('key', 'metaImage')->first();
        $oldPath = $existing?->value;

        if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
            $seoPhotoPath = 'seo';
            $seoPhotoStoragePath = $this->imageResizer->uploadFile($file, $seoPhotoPath, $oldPath);

            GeneralSetting::updateOrCreate(
                ['key' => 'metaImage'],
                [
                    'value'    => $seoPhotoStoragePath,
                    'group_id' => $groupId
                ]
            );
        }

        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['key' => $key],
                [
                    'value'    => $value,
                    'group_id' => $groupId
                ]
            );
        }

        Cache::forget('seo_settings');
    }

    public function storeLogoSettings(array $files, int $groupId = 16): array
    {
        $paths = [];
        $logoFields = [
            'logo_image'    => 'logo',
            'favicon_image' => 'favicon',
            'small_image'   => 'small',
            'dark_logo'     => 'dark',
        ];

        foreach ($logoFields as $field => $folderName) {
            if (!empty($files[$field])) {
                $file = $files[$field];

                $existing = GeneralSetting::where('key', $field)->first();
                $oldPath = $existing->value ?? null;

                $relativePath = $this->imageResizer->uploadFile($file, 'logo', $oldPath);
                GeneralSetting::updateOrCreate(
                    ['key' => $field],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );

                $paths[$field] = $relativePath;
            }
        }

        return $paths;
    }

    public function storeMaintenanceSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'];
            $file = $data['maintenance_image'] ?? null;
            $isRemove = $data['is_remove_image'] ?? false;
            unset($data['_token'], $data['maintenance_image'], $data['is_remove_image']);

            if (!empty($file) && $file instanceof \Illuminate\Http\UploadedFile) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                $oldPath = $existing->value ?? null;

                $relativePath = $this->imageResizer->uploadFile($file, 'maintenance', $oldPath);

                GeneralSetting::updateOrCreate(
                    ['key' => 'maintenance_image'],
                    ['value' => $relativePath, 'group_id' => $groupId]
                );
            }

            if ($isRemove) {
                $existing = GeneralSetting::where('key', 'maintenance_image')->first();
                if ($existing && $existing->value) {
                    $paths = [
                        storage_path(self::APP_PUBLIC . $existing->value),
                        storage_path(self::APP_PUBLIC . str_replace('maintenance/', 'maintenance/thumbnail/', $existing->value)),
                    ];
                    foreach ($paths as $path) {
                        if (File::exists($path)) {
                            File::delete($path);
                        }
                    }
                    $existing->update(['value' => '']);
                }
            }

            foreach ($data as $key => $value) {
                GeneralSetting::updateOrCreate(
                    ['key' => $key],
                    ['value' => $value, 'group_id' => $groupId]
                );
            }
        } catch (Exception $e) {
            \Log::error('Maintenance settings update failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    public function updateThemeSettings(array $data): void
    {
        try {
            $groupId = $data['group_id'];

            foreach ($data as $key => $value) {
                if ($key !== 'group_id') {
                    GeneralSetting::updateOrCreate(
                        ['key' => $key],
                        [
                            'value'    => $value,
                            'group_id' => $groupId
                        ]
                    );
                }
            }
        } catch (Exception $e) {
            \Log::error('Theme settings update failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
