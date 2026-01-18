<?php

namespace App\Services;

use App\Contracts\Interfaces\InstrumentInterface;
use App\Enums\StatusEnum;
use Carbon\Carbon;

class RentalService
{
    private $instrumentInterface;

    public function __construct(InstrumentInterface $instrumentInterface)
    {
        $this->instrumentInterface = $instrumentInterface;
    }
    public function rentalStore(array $data, int $totalPrice)
    {
        $data = [
            'customer_id' => auth()->user()->id,
            'rent_date' => $data['rent_date'],
            'return_date' => $data['return_date'],
            'total_price' => $totalPrice,
            'status' => StatusEnum::PENDING->value,
        ];

        return $data;
    }

    public function mapRentalDetails(array $items, int $totalDays): array
    {
        return collect($items)->map(function ($item) use ($totalDays) {

            $instrument = $this->instrumentInterface->show($item['instrument_id']);

            if (!$instrument) {
                throw new \Exception('Instrument tidak ditemukan', 404);
            }

            //Cek status instrument
            if ($instrument->status !== StatusEnum::AVAILABLE->value) {
                throw new \Exception(
                    "Instrument {$instrument->name} sedang tidak tersedia",
                    422
                );
            }

            return [
                'instrument_id' => $instrument->id,
                'price_per_day' => $instrument->price_per_day,
                'day'           => $totalDays,
                'subtotal'      => $instrument->price_per_day * $totalDays,
            ];
        })->toArray();
    }



    public function calculateRentalDays(string $rentDate, string $returnDate): int
    {
        $rent   = Carbon::parse($rentDate);
        $return = Carbon::parse($returnDate);

        $diffInHours = $rent->diffInHours($return);

        return max(1, ceil($diffInHours / 24));
    }
}
