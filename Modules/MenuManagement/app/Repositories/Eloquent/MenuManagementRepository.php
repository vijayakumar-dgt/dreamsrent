<?php

namespace Modules\MenuManagement\Repositories\Eloquent;

use Modules\MenuManagement\Models\Menu;
use Modules\MenuManagement\Repositories\Contracts\MenuManagementInterface;
use Illuminate\Database\Eloquent\Collection;

class MenuManagementRepository implements MenuManagementInterface
{
    protected $model;

    public function __construct(Menu $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $menu = $this->model->findOrFail($id);
        $menu->update($data);
        return $menu;
    }

    public function delete(int $id)
    {
        $menu = $this->model->findOrFail($id);
        $menu->delete();
        return true;
    }

    public function find(int $id)
    {
        return $this->model->findOrFail($id);
    }

    public function all(array $filters = [])
    {
        $query = $this->model->query();

        if (isset($filters['language_id'])) {
            $query->where('language_id', $filters['language_id']);
        }

        if (isset($filters['search'])) {
            $searchTerm = $filters['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('menu_type', 'like', '%' . $searchTerm . '%')
                  ->orWhere('permenantlink', 'like', '%' . $searchTerm . '%');
            });
        }

        if (isset($filters['sort'])) {
            switch ($filters['sort']) {
                case 'ascending':
                    $query->orderBy('name', 'asc');
                    break;
                case 'descending':
                    $query->orderBy('name', 'desc');
                    break;
                case 'last month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'last 7 days':
                    $query->where('created_at', '>=', now()->subDays(7));
                    break;
                case 'latest':
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->get();
    }

    public function exists(array $conditions)
    {
        return $this->model->where($conditions)->exists();
    }
}