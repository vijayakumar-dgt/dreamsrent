<?php

namespace App\Repositories\Eloquent;

use App\Models\Country;
use App\Repositories\Contracts\CountryInterface;
use Illuminate\Database\Eloquent\Collection;


class CountryRepository implements CountryInterface
{
    protected $model;

    public function __construct(Country $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->all();
    }

    public function find($id): ?Country
    {
        return $this->model->find($id);
    }

    public function create(array $data): Country
    {
        return $this->model->create($data);
    }

    public function update($id, array $data): bool
    {
        return $this->model->find(id: $id)->update($data);
    }

    public function delete($id): bool
    {
        return $this->model->destroy($id);
    }

    public function bulkDelete(array $ids): bool
    {
        return $this->model->whereIn('id', $ids)->delete();
    }

    public function search($search, $status = null, $orderBy = 'desc'): Collection
    {
        return $this->model->when($search, function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            })
            ->when(!is_null($status), function ($query) use ($status) {
                $query->where('status', $status);
            })
            ->orderBy('id', $orderBy)
            ->get();
    }
}