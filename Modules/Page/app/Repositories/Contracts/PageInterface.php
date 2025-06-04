<?php

namespace Modules\Page\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PageInterface
{
    public function index(): Collection;
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id): bool;
    public function findBySlug(string $slug);
    public function findById(int $id);
    public function getPagesWithFilters(array $filters): LengthAwarePaginator;
    public function getTranslatedPage(int $parentId, int $languageId);
    public function pageBuilderApi(array $request);
}
