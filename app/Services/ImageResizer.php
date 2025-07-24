<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ImageResizer
{
    /**
     * Upload and resize image (original, large, medium, thumbnail).
     * SVG will be stored as-is (not resized).
     */
    public function uploadFile(UploadedFile $file, string $baseFolder, ?string $oldFilePath = null): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $extension = strtolower($file->getClientOriginalExtension());
        $isSvg = $extension === 'svg';

        $uniqueName = Str::uuid() . '_' . time() . '.' . $extension;
        $basePath = storage_path("app/public/$baseFolder/");
        $sizes = [
            'original'   => null,
            'large'      => [1200, 1000],
            'medium'     => [900, 600],
            'small'      => [690, 420],
            'thumbnail'  => [300, 200],
        ];

        foreach (array_keys($sizes) as $folder) {
            $path = $basePath . ($folder === 'original' ? '' : "$folder/");
            File::ensureDirectoryExists($path, 0755, true);
        }

        if ($isSvg) {
            $svgPath = $basePath . $uniqueName;
            $file->move(dirname($svgPath), basename($svgPath));

            if ($oldFilePath) {
                $oldFilename = basename($oldFilePath);
                foreach (array_keys($sizes) as $folder) {
                    $oldPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $oldFilename;
                    if (File::exists($oldPath)) {
                        File::delete($oldPath);
                    }
                }
            }

            return "$baseFolder/$uniqueName";
        }

        try {
            $image = Image::read($file);
        } catch (\Exception $e) {
            \Log::error('Image read error: ' . $e->getMessage());
            return null;
        }

        foreach ($sizes as $folder => $dimensions) {
            $targetPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $uniqueName;
            $resized = clone $image;

            if ($dimensions) {
                $resized->resize($dimensions[0], $dimensions[1]);
            }

            try {
                $resized->save($targetPath);
            } catch (\Exception $e) {
                \Log::error("Failed to save image [$targetPath]: " . $e->getMessage());
            }
        }

        if ($oldFilePath) {
            $oldFilename = basename($oldFilePath);
            foreach (array_keys($sizes) as $folder) {
                $oldPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $oldFilename;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        return "$baseFolder/$uniqueName";
    }
}
