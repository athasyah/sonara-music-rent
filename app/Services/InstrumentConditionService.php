<?php

namespace App\Services;

use App\Contracts\Interfaces\InstrumentConditionInterface;
use App\Traits\UploadTrait;

class InstrumentConditionService
{
    use UploadTrait;
    public function mappingInstrumentCondition(array $data)
    {
        $data = [
            'rental_id' => $data['rental_id'],
            'instrument_id' => $data['instrument_id'],
            'condition' => $data['condition'],
            'note' => $data['note'],
            'image' => $data['image'] ?? null,
        ];

        if (isset($data['image'])) {
            $data['image'] = $this->upload('instrument_conditions', $data['image']);
        }

        return $data;
    }
}
