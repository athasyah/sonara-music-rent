<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\CategoryInterface;
use App\Models\Category;

class CategoryRepository extends BaseRepository implements CategoryInterface
{
    public function __construct(Category $user)
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
        $query = $this->model->query()
            ->orderBy('updated_at', 'desc');

        if (!empty($data['type'])) {
            return $query->where('type', $data['type']);
        }

        if (!empty($data['create_from'])) {
            return $query->where('create_at', $data['create_from']);
        }

        if (!empty($data['create_until'])) {
            return $query->where('create_at', $data['create_until']);
        }

        if (!empty($data['search'])) {
            $query->where(function ($q) use ($data) {
                $q->where('name', 'like', '%' . $data['search'] . '%');
            });
        }

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function noPaginate(array $data): mixed
    {
        $query = $this->model->query()
            ->orderBy('updated_at', 'desc')
            ->get();

        return $query;
    }
}
