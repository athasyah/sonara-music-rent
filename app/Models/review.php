<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
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

    public function customer()
    {
        return $this->belongsTo(User::class);
    }
}
