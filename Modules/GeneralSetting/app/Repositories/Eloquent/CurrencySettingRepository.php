<?php

namespace Modules\GeneralSetting\Repositories\Eloquent;

use Modules\GeneralSetting\Models\Currency;
use Modules\GeneralSetting\Repositories\Contracts\CurrencySettingInterface;

class CurrencySettingRepository implements CurrencySettingInterface
{
    public function createOrUpdateCurrency(array $data): bool
    {
        $currencyData = [
            'currency_name' => $data['currency_name'],
            'code' => $data['code'],
            'symbol' => $data['symbol'],
            'exchange_rate' => $data['exchange_rate'] ?? 0,
        ];

        // Creation: always set status = 1
        if (!isset($data['id'])) {
            $currencyData['status'] = 1;
            return Currency::create($currencyData) ? true : false;
        }

        // Update: use incoming status or default to 0
        $currencyData['status'] = $data['status'] ?? 0;
        return Currency::where('id', $data['id'])->update($currencyData);
    }


    public function getCurrencyList(array $filters = [], int $perPage = 10): mixed
    {
        $query = Currency::query();

        if (!empty($filters['keyword'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('currency_name', 'like', '%' . $filters['keyword'] . '%')
                    ->orWhere('code', 'like', '%' . $filters['keyword'] . '%');
            });
        }

        if (isset($filters['order_by'])) {
            $query->orderBy('currency_name', $filters['order_by']);
        }

        if (isset($filters['paginate']) && $filters['paginate'] === false) {
            return $query->get();
        }

        return $query->paginate($perPage);
    }


    public function findCurrency(int $id): mixed
    {
        return Currency::find($id);
    }

    public function deleteCurrency(int $id): bool
    {
        return Currency::where('id', $id)->delete();
    }
}