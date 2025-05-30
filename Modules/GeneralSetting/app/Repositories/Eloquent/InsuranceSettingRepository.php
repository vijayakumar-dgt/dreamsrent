<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Illuminate\Support\Facades\Auth;
use Modules\GeneralSetting\Models\Insurance;
use Modules\GeneralSetting\Models\InsuranceBenefit;
use Modules\CarInfo\Models\PricingType;
use Modules\GeneralSetting\Repositories\Contracts\InsuranceSettingInterface;
use Illuminate\Database\Eloquent\Collection;

class InsuranceSettingRepository implements InsuranceSettingInterface
{
    public function getPricingTypes(): Collection
    {
        return PricingType::where('type', 2)->get();
    }

    public function saveInsurance(array $data, array $benefits, ?int $id = null): Insurance
    {
        if (is_null($id)) {
            $insurance = Insurance::create($data);
            $this->createBenefits($insurance->id, $benefits);
        } else {
            $insurance = Insurance::findOrFail($id);
            $insurance->update($data);
            $this->syncBenefits($insurance->id, $benefits);
        }

        return $insurance;
    }

    public function getInsuranceList(array $params): array
    {
        $languageId = Auth::user()->language_id ?? null;
        $query = Insurance::where("language_id", $languageId)
            ->with(['insuranceBenefits', 'priceType'])
            ->withCount('insuranceBenefits');

        // Search
        if (!empty($params['search'])) {
            $search = $params['search'];
            $query->where(function($q) use ($search) {
                $q->where('insurance_name', 'like', "%{$search}%")
                  ->orWhere('price', 'like', "%{$search}%");
            });
        }

        // Sorting
        $columnName = $params['columns'][$params['order'][0]['column']]['data'] ?? 'insurance_name';
        $orderDir = $params['order'][0]['dir'] ?? 'desc';
        $query->orderBy($columnName, $orderDir);

        // Pagination
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 10;
        
        $filterTotal = $query->count();
        $totalRecords = Insurance::count();
        
        $data = $query->skip($start)->take($length)->get()->map(function ($item) {
            $priceType = $item->priceType->pricing_type ?? '';
            $item->price = ($priceType == 'percentage') 
                ? $item->price . '%' 
                : getDefaultCurrencySymbol() . $item->price;
            return $item;
        });

        return [
            'draw' => intval($params['draw']),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filterTotal,
            'data' => $data,
        ];
    }

    public function getInsuranceWithBenefits(int $id): Insurance
    {
        return Insurance::with('insuranceBenefits')->findOrFail($id);
    }

    public function deleteInsurance(int $id): void
    {
        $insurance = Insurance::findOrFail($id);
        InsuranceBenefit::where('insurance_id', $id)->delete();
        $insurance->delete();
    }

    public function getVehicleInsurances(array $vehicleIds): Collection
    {
        return Insurance::with(['insuranceBenefits:id,insurance_id,benefit'])
            ->select(
                'insurances.id',
                'insurances.insurance_name',
                'vehicle_insurances.value as insurance_type',
                'vehicle_insurances.price'
            )
            ->withCount('insuranceBenefits')
            ->join('vehicle_insurances', 'vehicle_insurances.insurances_id', '=', 'insurances.id')
            ->whereIn('vehicle_insurances.vehicle_id', $vehicleIds)
            ->get()
            ->map(function ($item) {
                $item->price = number_format((float) $item->price, 0);
                $item->insurance_type = strtolower($item->insurance_type);
                return $item;
            });
    }

    protected function createBenefits(int $insuranceId, array $benefits): void
    {
        foreach ($benefits as $benefit) {
            if (!empty($benefit)) {
                InsuranceBenefit::create([
                    'insurance_id' => $insuranceId,
                    'benefit' => $benefit
                ]);
            }
        }
    }

    protected function syncBenefits(int $insuranceId, array $benefits): void
    {
        InsuranceBenefit::where('insurance_id', $insuranceId)
            ->whereNotIn('id', array_keys($benefits))
            ->delete();

        foreach ($benefits as $key => $benefit) {
            if ($key === 'new' && is_array($benefit)) {
                foreach ($benefit as $newBenefit) {
                    if (!empty($newBenefit)) {
                        InsuranceBenefit::create([
                            'insurance_id' => $insuranceId,
                            'benefit' => $newBenefit
                        ]);
                    }
                }
            } else {
                InsuranceBenefit::where('id', $key)->update([
                    'benefit' => $benefit
                ]);
            }
        }
    }
}