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
    public function uploadFile(UploadedFile $file, ?string $oldFilePath = null, string $baseFolder): ?string
    {
        if (!$file->isValid()) {
            return null;
        }

        $basePath = storage_path("app/public/$baseFolder/");
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $uniqueName = time() . '-' . Str::slug($originalName) . '.' . $extension;

        $sizes = [
            'original'  => null,
            'large'     => 1200,
            'medium'    => 800,
            'thumbnail' => 300,
        ];

        // Ensure base and size directories exist
        foreach ($sizes as $folder => $width) {
            $path = $basePath . ($folder === 'original' ? '' : $folder . '/');
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
            }
        }

        // Read original image
        $image = Image::read($file);

        // Save each size
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

        // Delete old images if exist
        if (!empty($oldFilePath)) {
            $oldFilename = basename($oldFilePath);
            foreach ($sizes as $folder => $_) {
                $path = $basePath . ($folder === 'original' ? '' : $folder . '/') . $oldFilename;
                if (File::exists($path)) {
                    File::delete($path);
                }
            }
        }

        // Return relative storage path to original image
        return "$baseFolder/$uniqueName";
    }
}
