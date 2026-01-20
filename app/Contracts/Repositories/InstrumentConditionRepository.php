<?php

namespace App\Http\Controllers;

use App\Contracts\Interfaces\InstrumentConditionInterface;
use App\Contracts\Repositories\BaseRepository;
use App\Models\InstrumentCondition;

class InstrumentConditionRepository extends BaseRepository implements InstrumentConditionInterface
{
    public function __construct(InstrumentCondition $user)
    {
        $this->model = $user;
    }

    public function get()
    {
        return $this->model->get();
    }

    public function show(mixed $id)
    {
        return $this->model->find($id);
    }

    public function store(array $data)
    {
        return $this->model->create($data);
    }

    public function update(mixed $id, array $data): mixed
    {
        $model = $this->show($id);
        $model->update($data);

        return $model->fresh();
    }

    public function delete(mixed $id)
    {
        return $this->show($id)->delete();
    }

    public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed
    {
        return $this->model->query()
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function noPaginate(array $data): mixed
    {
        $query = $this->model->query()
            ->orderBy('updated_at', 'desc')
            ->get();
        return $query;
    }
}
