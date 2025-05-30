<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Modules\GeneralSetting\Models\SignatureSetting;
use Modules\GeneralSetting\Repositories\Contracts\SignatureSettingInterface;

class SignatureSettingRepository implements SignatureSettingInterface
{
    public function getAllSignatures(string $search = null)
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

    public function createSignature(array $data, UploadedFile $image = null)
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

    public function updateSignature(int $id, array $data, UploadedFile $image = null)
    {
        $signature = SignatureSetting::findOrFail($id);

        if ($image) {
            $this->deleteSignatureImage($signature->signature_image);
            $signature->signature_image = $this->uploadSignatureImage($image);
        }

        if (isset($data['is_default']) && $data['is_default'] == 1) {
            $this->resetDefaultSignature();
        }


        $signature->update([
            'signature_name' => $data['signature_name'],
            'is_default' => isset($data['is_default']) ? 1 : 0,
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

    protected function resetDefaultSignature()
    {
        SignatureSetting::where('is_default', 1)->update(['is_default' => 0]);
    }

    protected function uploadSignatureImage(UploadedFile $file): string
    {
        return uploadFile($file, 'signatures');
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
