<?php

namespace Modules\CarInfo\Repositories\Contracts;

use Illuminate\Http\Request;

interface SeasonRepositoryInterface
{
    public function save(Request $request);
    public function getSeasons(Request $request): array;
    public function getSeason($id): array;
    public function delete(Request $request): array;
}
