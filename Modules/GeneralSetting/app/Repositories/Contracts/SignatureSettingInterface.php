<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Http\UploadedFile;

interface SignatureSettingInterface
{
    public function getAllSignatures(string $search = null);
    public function createSignature(array $data, UploadedFile $image = null);
    public function updateSignature(int $id, array $data, UploadedFile $image = null);
    public function deleteSignature(int $id);
    public function getTotalSignaturesCount();
}
