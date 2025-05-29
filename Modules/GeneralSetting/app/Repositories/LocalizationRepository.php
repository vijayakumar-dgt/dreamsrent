<?php

namespace Modules\GeneralSetting\Repositories;

use Modules\GeneralSetting\Models\GeneralSetting;
use Modules\GeneralSetting\Models\Timezone;

class LocalizationRepository
{
    protected $groupId = 5;

    /**
     * Get all timezones
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTimezones()
    {
        return Timezone::all();
    }

    /**
     * Search timezones
     *
     * @param string $search
     * @return \Illuminate\Support\Collection
     */
    public function searchTimezones($search)
    {
        return Timezone::where('name', 'like', "%$search%")
            ->take(10)
            ->get()
            ->map(function ($timezone) {
                return [
                    'id' => $timezone->id,
                    'text' => $timezone->name
                ];
            });
    }

    /**
     * Get current timezone setting
     *
     * @return mixed
     */
    public function getCurrentTimezone()
    {
        return GeneralSetting::where('group_id', $this->groupId)
            ->where('key', 'timezone')
            ->first();
    }

    /**
     * Update localization settings
     *
     * @param array $data
     * @return void
     */
    public function updateLocalization(array $data)
    {
        foreach ($data as $key => $value) {
            GeneralSetting::updateOrCreate(
                ['group_id' => $this->groupId, 'key' => $key],
                ['value' => $value]
            );
        }
    }

    /**
     * Get timezone by ID
     *
     * @param int $id
     * @return \Modules\GeneralSetting\Models\Timezone|null
     */
    public function getTimezoneById($id)
    {
        return Timezone::find($id);
    }
}