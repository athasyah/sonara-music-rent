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
        return $this->show($id)->update($data);
    }

    public function delete(mixed $id)
    {
        return $this->show($id)->delete();
    }

    public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed
    {
        return $this->model->query()
            ->paginate($perPage, ['*'], 'page', $page);
    }
}
