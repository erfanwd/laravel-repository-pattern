<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function list(array $columns = ['*'], array $relations = [])
    {
        return $this->model->query()->select($columns)->with($relations)->get();
    }

    public function listPaginated(array $columns = ['*'], array $relations = [], int $perPage = 50)
    {
        return $this->model->query()->select($columns)->with($relations)->paginate($perPage);
    }

    public function findById(int $modelId, array $columns = ['*'], array $relations = [])
    {
        return $this->model->query()->select($columns)->with($relations)->findOrFail($modelId);
    }

    public function findManyById(array $ids, array $columns = ['*'], array $relations = [])
    {
        return $this->model->query()->select($columns)->with($relations)->findMany($ids);
    }

    public function create(array $payload)
    {
        return $this->model->query()->create($payload);
    }

    public function update(int $modelId, array $payload)
    {
        $model = $this->model->query()->findOrFail($modelId);
        $model->update($payload);
        return $model;
    }

    public function deleteById(int $modelId)
    {
        return $this->model->query()->findOrFail($modelId)->delete();
    }

    public function bulkInsert(array $payload)
    {
        return $this->model->query()->insert($payload);
    }
}
