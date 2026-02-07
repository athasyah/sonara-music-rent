<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Guarantee extends Model
{
    use HasUuids;
    protected $guarded = [];

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }
}
