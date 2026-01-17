<?php

namespace App\Contracts\Repositories;

use App\Contracts\Interfaces\RentalDetailInterface;
use App\Models\RentalDetail;

class RentalDetailRepository extends BaseRepository implements RentalDetailInterface
{
    public function __construct(RentalDetail $user)
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
            ->with(['instrument', 'rental'])
            ->orderBy('updated_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function noPaginate(array $data): mixed
    {
        $query = $this->model->query()
            ->orderBy('updated_at', 'desc')
            ->with(['instrument', 'rental'])
            ->get();
        return $query;
    }

    public function deleteByRentalId(string $rentalId)
    {
        return $this->model
            ->where('rental_id', $rentalId)
            ->delete();
    }
}
