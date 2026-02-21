<?php

namespace App\Services;

use App\Contracts\Interfaces\RentalDetailInterface;
use Carbon\Carbon;

class CartService {
    protected $rentalRepository;

    public function __construct(RentalDetailInterface $rentalRepository)
    {
        $this->rentalRepository = $rentalRepository;
    }

    public function getBlockedDates(array $instrumentIds)
    {
        $rentals = $this->rentalRepository
            ->getOverlappingByInstrumentIds($instrumentIds);

        $blocked = [];

        foreach ($rentals as $detail) {
            $start = Carbon::parse($detail->rental->rent_date);
            $end   = Carbon::parse($detail->rental->return_date);

            while ($start <= $end) {
                $date = $start->format('Y-m-d');

                $blocked[$date][] = [
                    'instrument_id' => $detail->instrument_id,
                    'instrument_name' => $detail->instrument->name,
                ];

                $start->addDay();
            }
        }

        return $blocked;
    }
}
