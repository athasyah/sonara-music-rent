<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class rentalDetail extends Model
{
    use HasUuids, SoftDeletes;
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
