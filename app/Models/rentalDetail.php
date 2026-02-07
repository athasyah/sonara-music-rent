<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class RentalDetail extends Model
{
    use HasUuids;
    protected $guarded = [];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }
}
