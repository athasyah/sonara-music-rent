<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class InstrumentCondition extends Model
{
    use HasUuids;
    protected $guarded = [];

    public function instrument()
    {
        return $this->belongsTo(Instrument::class);
    }

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
