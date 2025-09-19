<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitItem extends Model
{
    protected $guarded = [];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
