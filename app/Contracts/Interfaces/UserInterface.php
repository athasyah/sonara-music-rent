<?php

namespace App\Contracts\Interfaces;

interface UserInterface extends BaseInterface
{
     public function findByEmail(string $email);
     public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed;
     public function noPaginate(array $data): mixed;
}
