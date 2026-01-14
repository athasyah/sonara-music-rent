<?php

namespace App\Services;

use App\Traits\UploadTrait;


class UserService
{
    use UploadTrait;

    public function mappingDataUser(array $data)
    {
        $result = [
            'name' => $data['name'],
            'email' => $data['email'],
            'number_phone' => $data['number_phone'],
            'password' => bcrypt($data['password']),
        ];

        if (isset($data['image'])) {
            $result['image'] = $this->upload('users', $data['image']);
        }

        return $result;
    }
}
