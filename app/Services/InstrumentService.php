<?php 

namespace App\Services;

use App\Traits\UploadTrait;

class InstrumentService
{
    use UploadTrait;
    public function mappingInstrument(array $data)
    {
        $result = [
            'category_id' => $data['category_id'],
            'brand_id' => $data['brand_id'],
            'name' => $data['name'],
            'price_per_day' => $data['price_per_day'],
            'status' => $data['status'],
        ];

            if (isset($data['image'])) {
            $result['image'] = $this->upload('instruments', $data['image']);
        }

        return $result;
    }
}