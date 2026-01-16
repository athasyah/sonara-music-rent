<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Instrument extends Model
{
    use HasUuids, SoftDeletes;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
