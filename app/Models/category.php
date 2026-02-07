<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasUuids;
    protected $guarded = [];

    public function instruments()
    {
        return $this->hasMany(Instrument::class);
    }
}
