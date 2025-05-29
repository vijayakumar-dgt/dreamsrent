<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;


class ImageResizer
{
    /**
     * Upload and resize profile photo (original, large, medium, thumbnail).
     *
     * @param UploadedFile $file
     * @param string|null $oldFilePath
     * @param string $baseFolder
     * @return string|null
     */
    public function uploadFile(UploadedFile $file, string $baseFolder, ?string $oldFilePath = null): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $basePath = storage_path("app/public/$baseFolder/");
        $extension = $file->getClientOriginalExtension();
        $uniqueName = Str::uuid() . '_' . time() . '.' . $extension;

        $sizes = [
            'original' => null,
            'large' => 1200,
            'medium' => 800,
            'thumbnail' => 300,
        ];

        // Ensure folders exist
        foreach ($sizes as $folder => $width) {
            $path = $basePath . ($folder === 'original' ? '' : $folder . '/');
            File::ensureDirectoryExists($path, 0755, true);
        }

        // Read original image
        $image = Image::read($file);

        // Save image in each size
        foreach ($sizes as $folder => $width) {
            $targetPath = $basePath . ($folder === 'original' ? '' : $folder . '/') . $uniqueName;

            $resized = clone $image;
            if ($width) {
                $resized->resize($width, $width, function ($c) {
                    $c->aspectRatio();
                    $c->upsize();
                });
            }
            $resized->save($targetPath);
        }

        // Delete old images if path provided
        if (!empty($oldFilePath)) {
            $oldFilename = basename($oldFilePath);
            foreach ($sizes as $folder => $_) {
                $oldPath = $basePath . ($folder === 'original' ? '' : $folder . '/') . $oldFilename;
                if (File::exists($oldPath)) {
                    File::delete($oldPath);
                }
            }
        }

        return "$baseFolder/$uniqueName"; // Return original (base) path
    }

}
