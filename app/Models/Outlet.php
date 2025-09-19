<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    protected $guarded = [];

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }
}
