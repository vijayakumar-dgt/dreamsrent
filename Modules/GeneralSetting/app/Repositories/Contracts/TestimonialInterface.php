<?php

namespace Modules\GeneralSetting\Repositories\Contracts;

interface TestimonialInterface
{
    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function find(int $id);

    public function all(array $filters = []);

    public function paginate(int $perPage = 10, array $filters = []);
}
