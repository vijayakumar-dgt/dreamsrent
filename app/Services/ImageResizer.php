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
     *
     * @param UploadedFile $file
     * @param string $baseFolder
     * @param string|null $oldFilePath
     * @return string|null
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
            'large'      => 1200,
            'medium'     => 800,
            'thumbnail'  => 300,
        ];

        // Create required folders
        foreach ($sizes as $folder => $_) {
            $path = $basePath . ($folder === 'original' ? '' : "$folder/");
            File::ensureDirectoryExists($path, 0755, true);
        }

        if ($isSvg) {
            // Save SVG as original only
            $svgPath = $basePath . $uniqueName;
            $file->move(dirname($svgPath), basename($svgPath));

            // Delete old SVG if exists
            if ($oldFilePath) {
                $oldFilename = basename($oldFilePath);
                foreach ($sizes as $folder => $_) {
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

        // Save resized images
        foreach ($sizes as $folder => $width) {
            $targetPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $uniqueName;
            $resized = clone $image;

            if ($width) {
                $resized->resize($width, $width, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            }

            try {
                $resized->save($targetPath);
            } catch (\Exception $e) {
                \Log::error("Failed to save image [$targetPath]: " . $e->getMessage());
            }
        }

        // Delete old files if needed
        if ($oldFilePath) {
            $oldFilename = basename($oldFilePath);
            foreach ($sizes as $folder => $_) {
                $oldPath = $basePath . ($folder === 'original' ? '' : "$folder/") . $oldFilename;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        return "$baseFolder/$uniqueName";
    }
}
