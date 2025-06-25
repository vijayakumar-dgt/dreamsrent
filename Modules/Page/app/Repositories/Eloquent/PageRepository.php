<?php

namespace Modules\Page\Repositories\Eloquent;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Page\Models\Page;
use Modules\Page\Repositories\Contracts\PageInterface;

class PageRepository implements PageInterface
{
    public function index(): Collection
    {
        return Page::all();
    }

    public function create(array $data)
    {
        return Page::create($data);
    }

    public function update(int $id, array $data)
    {
        $page = Page::findOrFail($id);
        $page->update($data);
        return $page;
    }

    public function delete(int $id): bool
    {
        return Page::destroy($id);
    }

    public function findBySlug(string $slug)
    {
        return Page::where('slug', $slug)->first();
    }

    public function findById(int $id)
    {
        return Page::findOrFail($id);
    }

    public function getPagesWithFilters(array $filters): LengthAwarePaginator
    {
        $query = Page::query();

        if (!empty($filters['search'])) {
            $query->where('page_title', 'LIKE', "%{$filters['search']}%");
        }

        if (!is_null($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['language_id'])) {
            $query->where('language_id', $filters['language_id']);
        }

        if ($filters['sort'] === 'asc') {
            $query->orderBy('created_at', 'asc');
        } elseif ($filters['sort'] === 'desc') {
            $query->orderBy('created_at', 'desc');
        } elseif ($filters['sort'] === 'last_month') {
            $query->whereBetween('created_at', [now()->subMonth(), now()]);
        } elseif ($filters['sort'] === 'last_7_days') {
            $query->whereBetween('created_at', [now()->subDays(7), now()]);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        return $query->paginate($filters['per_page'] ?? 10);
    }

    public function getTranslatedPage(int $parentId, int $languageId)
    {
        return Page::where('parent_id', $parentId)
            ->where('language_id', $languageId)
            ->first();
    }

    public function pageBuilderApi(array $request)
    {
    }
}
