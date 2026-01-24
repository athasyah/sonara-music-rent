<?php

namespace App\Contracts\Interfaces;

interface ReviewInterface extends BaseInterface
{
    public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed;
    public function noPaginate(array $data): mixed;
    public function existsByRentalAndInstrument(string $rentalId, string $instrumentId);
}
