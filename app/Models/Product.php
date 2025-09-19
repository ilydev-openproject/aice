<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'nama_produk',
        'hpp',
        'het',
        'isi_per_box',
        'foto',
        'margin',
    ];
}
