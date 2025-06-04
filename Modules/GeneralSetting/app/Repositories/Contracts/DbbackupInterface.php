<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Support\Collection;

interface DbbackupInterface
{
    public function getDatabaseBackups(): Collection;
    public function getSystemBackups(): Collection;
    public function deleteBackup(int $id): bool;
    public function getTotalBackupCount(): int;
}
