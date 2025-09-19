<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    protected $fillable = [
        'outlet_id',
        'catatan',
        'foto_bukti',
        'tanggal_kunjungan',
        'total_harga',
    ];
    protected $casts = [
        'tanggal_kunjungan' => 'datetime',
    ];

    // ✅ Tambahkan relasi ini
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    // Jika Anda juga ingin akses ke produk yang dipesan (VisitItem), tambahkan:
    public function visitItems()
    {
        return $this->hasMany(VisitItem::class);
    }
}
