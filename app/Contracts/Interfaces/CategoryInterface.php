<?php 

namespace App\Contracts\Interfaces;

interface CategoryInterface extends BaseInterface
{
        public function customPaginate(int $perPage = 10, int $page = 1, ?array $data): mixed;

}