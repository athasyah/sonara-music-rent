<?php

namespace App\Http\Resources;

use App\Models\Instrument;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'product_count' => $this->instruments()->count() + Instrument::where('brand_id', $this->id)->count(),
            'description' => $this->description,
            'created_at' => $this->created_at,
        ];
    }
}
