<?php 

namespace App\Contracts\Interfaces;

interface UserInterface extends BaseInterface
{
     public function findByEmail(string $email);
}