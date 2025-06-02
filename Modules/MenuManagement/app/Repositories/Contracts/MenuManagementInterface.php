<?php

namespace Modules\MenuManagement\Repositories\Contracts;

interface MenuManagementInterface
{
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function find(int $id);
    public function all(array $filters = []);
    public function exists(array $conditions);
}