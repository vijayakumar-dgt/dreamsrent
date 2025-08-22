<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Http\UploadedFile;

interface SignatureSettingInterface
{
    public function getAllSignatures(string|null $search);

    public function createSignature(array $data, UploadedFile|null $image);

    public function updateSignature(int $id, array $data, UploadedFile|null $image);

    public function deleteSignature(int $id);

    public function getTotalSignaturesCount();
}
