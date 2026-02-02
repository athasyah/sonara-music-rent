<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PenaltyResource extends JsonResource
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
            'user_id' => $this->user_id,
            'user_name' =>$this->user->name,
            'rental_id' => $this->rental_id,
            'title' => $this->title,
            'reason' => $this->reason,
            'amount' => $this->amount,
        ];
    }
}
