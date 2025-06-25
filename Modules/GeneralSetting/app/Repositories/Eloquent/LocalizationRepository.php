<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Collection as SupportCollection;
use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Timezone;
use Modules\GeneralSetting\Repositories\Contracts\LocalizationInterface;

class LocalizationRepository implements LocalizationInterface
{
    protected int $groupId = 5;

    public function getTimezones(): Collection
    {
        return Timezone::all();
    }

    public function searchTimezones(string $search): SupportCollection
    {
        return Timezone::where('name', 'like', "%$search%")
            ->take(10)
            ->get()
            ->map(function ($timezone) {
                return [
                    'id'   => $timezone->id,
                    'text' => $timezone->name
                ];
            });
    }

    public function getCurrentTimezone(): ?GeneralSetting
    {
        return GeneralSetting::where('group_id', $this->groupId)
            ->where('key', 'timezone')
            ->first();
    }

    public function updateLocalization(array $data): void
    {
        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['group_id' => $this->groupId, 'key' => $key],
                ['value' => $value]
            );
        }
    }

    public function getTimezoneById(int $id): ?Timezone
    {
        return Timezone::find($id);
    }
}
