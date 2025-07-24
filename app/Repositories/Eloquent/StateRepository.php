<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Models\State;
use App\Repositories\Contracts\StateInterface;
use Illuminate\Database\Eloquent\Collection;

class StateRepository implements StateInterface
{
    protected $state;
    protected $country;

    public function __construct(State $state, Country $country)
    {
        $this->state = $state;
        $this->country = $country;
    }

    public function all(): Collection
    {
        return $this->state->with('country')->get();
    }

    public function find($id): ?State
    {
        return $this->state->with('country')->find($id);
    }

    public function create(array $data): State
    {
        return $this->state->create($data);
    }

    public function update($id, array $data): bool
    {
        return $this->state->find($id)->update($data);
    }

    public function delete($id): bool
    {
        return $this->state->destroy($id);
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->state->whereIn('id', $ids)->delete();
    }

    public function search($search, $status = null, $orderBy = 'desc'): Collection
    {
        return $this->state->with('country')
            ->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when(!is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('id', $orderBy)
            ->get();
    }

    public function getCountries(): Collection
    {
        return $this->country->select('id', 'name')->get();
    }
}
