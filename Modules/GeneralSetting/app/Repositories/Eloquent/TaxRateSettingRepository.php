<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\SubTax;
use Modules\GeneralSetting\Models\TaxGroup;
use Modules\GeneralSetting\Models\TaxRate;
use Modules\GeneralSetting\Repositories\Contracts\TaxRateSettingInterface;

class TaxRateSettingRepository implements TaxRateSettingInterface
{
    public function createOrUpdateTaxRate(array $data): bool
    {
        if (!isset($data['id'])) {
            return TaxRate::create($data) ? true : false;
        }

        return TaxRate::where('id', $data['id'])->update($data);
    }

    public function getTaxRateList(string $orderBy): mixed
    {
        return TaxRate::orderBy('id', $orderBy)->get();
    }

    public function deleteTaxRate(int $id): bool
    {
        return TaxRate::where('id', $id)->delete();
    }

    public function findTaxRate(int $id): mixed
    {
        return TaxRate::find($id);
    }

    public function createOrUpdateTaxGroup(array $data): bool
    {
        if (!isset($data['id'])) {
            $group = TaxGroup::create(['tax_name' => $data['tax_group_name']]);
            foreach ($data['sub_tax'] as $taxRateId) {
                SubTax::updateOrCreate([
                    'tax_group_id' => $group->id,
                    'tax_rate_id'  => $taxRateId
                ]);
            }
            return true;
        }

        TaxGroup::where('id', $data['id'])->update([
            'tax_name' => $data['tax_group_name'],
            'status'   => $data['status'] ?? 1,
        ]);
        SubTax::where('tax_group_id', $data['id'])->whereNotIn('tax_rate_id', $data['sub_tax'])->delete();
        foreach ($data['sub_tax'] as $taxRateId) {
            SubTax::updateOrCreate([
                'tax_group_id' => $data['id'],
                'tax_rate_id'  => $taxRateId
            ]);
        }
        return true;
    }

    public function getTaxGroupList(string $orderBy): mixed
    {
        return TaxGroup::with(['taxRates:id,tax_name,tax_rate'])
            ->orderBy('id', $orderBy)
            ->get();
    }

    public function deleteTaxGroup(int $id): bool
    {
        return TaxGroup::where('id', $id)->delete();
    }

    public function findTaxGroup(int $id): mixed
    {
        return TaxGroup::with('taxRates')->find($id);
    }

    public function getActiveTaxRates(): mixed
    {
        return TaxRate::where('status', 1)->get(['id', 'tax_name', 'tax_rate']);
    }
}
