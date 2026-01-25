<?php

namespace App\Services;

use App\Enums\StatusEnum;

class GuaranteeService
{
    public function mapGuarantee(array $data, string $rentId)
    {
        $data = [
            'rental_id' => $rentId,
            'user_id'   => auth()->user()->id,
            'type'      => $data['type'],
            'note'      => $data['note'] ?? null,
            'status' => StatusEnum::PENDING->value,
        ];

        return $data;
    }
}
