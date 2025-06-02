<?php

namespace App\Repositories\Eloquent;

use App\Models\City;
use App\Models\State;
use App\Repositories\Contracts\CityInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CityRepository implements CityInterface
{
    protected $city;
    protected $state;

    public function __construct(City $city, State $state)
    {
        $this->city = $city;
        $this->state = $state;
    }

    public function all(): Collection
    {
        return $this->city->with(['state.country'])->get();
    }

    public function find($id): ?City
    {
        return $this->city->with(['state.country'])->find($id);
    }

    public function create(array $data): City
    {
        return $this->city->create($data);
    }

    public function update($id, array $data): bool
    {
        return $this->city->find($id)->update($data);
    }

    public function delete($id): bool
    {
        return $this->city->destroy($id);
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->city->whereIn('id', $ids)->delete();
    }

    public function getStates(): Collection
    {
        return $this->state->select('id', 'name')->get();
    }

    public function datatable(array $params): array
    {
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 10;
        $search = $params['search'] ?? null;
        $orderColumn = $params['order_column'] ?? 'name';
        $orderDirection = $params['order_dir'] ?? 'asc';
        $status = $params['status'] ?? null;

        $query = $this->city->with(['state.country']);

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhereHas('state', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%")
                        ->orWhereHas('country', function($q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if (isset($status)) {
            $query->where('status', $status);
        }

        $total = $query->count();

        $cities = $query->orderBy($orderColumn, $orderDirection)
            ->skip($start)
            ->take($length)
            ->get();

        return [
            'data' => $cities,
            'total' => $total,
            'filtered' => $total,
        ];
    }
}