<?php

namespace App\Contracts\Interfaces;

interface RentalDetailInterface extends BaseInterface
{
    public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed;
    public function noPaginate(array $data): mixed;
    public function deleteByRentalId(string $rentalId);

    public function instrumentExistsInRental(string $rentalId, string $instrumentId);
    public function hasDateConflict(int $instrumentId, $rentDate, $returnDate): bool;

}
