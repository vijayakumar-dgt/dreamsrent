<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\GeneralSetting\Models\SignatureSetting;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;
use App\Services\ImageResizer;

class SignatureSettingRepository implements SignatureSettingInterface
{
    protected ImageResizer $imageResizer;

    public function __construct(ImageResizer $imageResizer)
    {
        $this->imageResizer = $imageResizer;
    }
    public function getAllSignatures(string|null $search)
    {
        return SignatureSetting::when($search, function ($query) use ($search) {
            $query->where('signature_name', 'like', "%{$search}%");
        })
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($signature) {
                $signature->signature_image = uploadedAsset($signature->signature_image ?? '', 'default');
                return $signature;
            });
    }

    public function createSignature(array $data, UploadedFile|null $image)
    {
        $imagePath = $image ? $this->uploadSignatureImage($image) : null;

        if (!empty($data['is_default'])) {
            $this->resetDefaultSignature();
        }

        return SignatureSetting::create([
            'signature_name' => $data['signature_name'],
            'signature_image' => $imagePath,
            'status' => 1,
            'is_default' => !empty($data['is_default']) ? 1 : 0,
        ]);
    }

    public function updateSignature(int $id, array $data, UploadedFile|null $image)
    {
        $signature = SignatureSetting::findOrFail($id);

        if ($image) {
            $this->deleteSignatureImage($signature->signature_image);
            $signature->signature_image = $this->uploadSignatureImage($image);
        }

        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->resetDefaultSignature($id);
        }

        $isDefault = (isset($data['is_default']) && $data['is_default'] == 1) ? 1 : 0;

        $signature->update([
            'signature_name' => $data['signature_name'],
            'is_default' => $isDefault,
            'status' => !empty($data['status']) ? 1 : 0
        ]);

        return $signature;
    }

    public function deleteSignature(int $id)
    {
        $signature = SignatureSetting::findOrFail($id);
        $this->deleteSignatureImage($signature->signature_image);
        $signature->delete();
        return SignatureSetting::count();
    }

    protected function resetDefaultSignature(int|null $excludeId = null)
    {
        $query = SignatureSetting::where('is_default', 1);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $query->update(['is_default' => 0]);
    }

    protected function uploadSignatureImage(UploadedFile $file): string
    {
        return $this->imageResizer->uploadFile($file, 'signatures', null);
    }

    protected function deleteSignatureImage(?string $imagePath)
    {
        if ($imagePath) {
            Storage::delete($imagePath);
        }
    }

    public function getTotalSignaturesCount()
    {
        return SignatureSetting::count();
    }
}
