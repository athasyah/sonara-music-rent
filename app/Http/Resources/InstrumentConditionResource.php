<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstrumentConditionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rental_id' => $this->rental_id,
            'instrument_id' => $this->instrument_id,
            'instrument_name' => $this->instrument->name,
            'condition' => $this->condition,
            'note' => $this->note,
            'image' => $this->image,
        ];
    }
}
