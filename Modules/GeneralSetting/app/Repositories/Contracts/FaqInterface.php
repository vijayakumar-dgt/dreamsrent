<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface FaqInterface
{
    public function store(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function list(array $filters);
}
