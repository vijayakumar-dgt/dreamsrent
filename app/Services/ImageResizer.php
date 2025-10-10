<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
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
        $uploadedPath = null;

        if ($file->isValid()) {
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

            // Ensure directories exist
            foreach (array_keys($sizes) as $folder) {
                File::ensureDirectoryExists($basePath . ($folder === 'original' ? '' : "$folder/"), 0755, true);
            }

            if ($isSvg) {
                $uploadedPath = $this->handleSvg($file, $basePath, $uniqueName, $sizes, $oldFilePath, $baseFolder);
            } else {
                $uploadedPath = $this->handleRasterImage($file, $basePath, $uniqueName, $sizes, $oldFilePath, $baseFolder);
            }
        }

        return $uploadedPath;
    }

    /**
     * Handle SVG files
     */
    protected function handleSvg(UploadedFile $file, string $basePath, string $uniqueName, array $sizes, ?string $oldFilePath, string $baseFolder): string
    {
        $svgPath = $basePath . $uniqueName;
        $file->move(dirname($svgPath), basename($svgPath));

        if ($oldFilePath) {
            $this->deleteOldFiles($oldFilePath, $basePath, array_keys($sizes));
        }

        return "$baseFolder/$uniqueName";
    }

    /**
     * Handle raster images
     */
    protected function handleRasterImage(UploadedFile $file, string $basePath, string $uniqueName, array $sizes, ?string $oldFilePath, string $baseFolder): ?string
    {
        try {
            $image = Image::read($file);
        } catch (\Exception $e) {
            Log::error('Image read error: ' . $e->getMessage());
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
                Log::error("Failed to save image [$targetPath]: " . $e->getMessage());
            }
        }

        if ($oldFilePath) {
            $this->deleteOldFiles($oldFilePath, $basePath, array_keys($sizes));
        }

        return "$baseFolder/$uniqueName";
    }

    /**
     * Delete old files in all size folders
     */
    protected function deleteOldFiles(string $oldFilePath, string $basePath, array $folders): void
    {
        $oldFilename = basename($oldFilePath);

        foreach ($folders as $folder) {
            $oldPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $oldFilename;
            if (File::exists($oldPath)) {
                File::delete($oldPath);
            }
        }
    }
}
