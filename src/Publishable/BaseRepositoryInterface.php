<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{
    public function list(array $columns = ['*'], array $relations = []);

    public function listPaginated(array $columns = ['*'], array $relations = [], int $perPage = 50);

    public function findById(int $modelId, array $columns = ['*'], array $relations = []);

    public function findManyById(array $ids, array $columns = ['*'], array $relations = []);

    public function create(array $payload);

    public function update(int $modelId, array $payload);

    public function deleteById(int $modelId);

    public function bulkInsert(array $payload);
}
