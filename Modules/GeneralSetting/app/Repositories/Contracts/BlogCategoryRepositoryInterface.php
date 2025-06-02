<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

interface BlogCategoryRepositoryInterface
{
    public function blogCategory(): array;
    public function categoryStore(Request $request);
    public function categoryUpdate(Request $request, int $id);
    public function blogTags(): array;
    public function tagStore(Request $request);
    public function tagUpdate(Request $request, int $id);
    public function categoryDestroy(int $id);
    public function tagDestroy(int $id);
    public function blogComments(): array;
    public function blogs(): array;
    public function blogDetails(string $id);
    public function blogAdd(): array;
    public function blogStore(Request $request);
    public function blogDestroy(int $id);
    public function blogEdit(int $id): array;
    public function BlogUpdate(Request $request, int $id);
}
