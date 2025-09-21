<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\Outlet;
use Carbon\Carbon;

class Dashboard extends Component
{
    public function getStatsProperty()
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('m');
        $thisYear = now()->format('Y');

        // Target kunjungan bulan ini
        $target = 200;

        // Kunjungan bulan ini
        $kunjunganBulanIni = Visit::whereYear('tanggal_kunjungan', $thisYear)
            ->whereMonth('tanggal_kunjungan', $thisMonth)
            ->count();

        // Total penjualan bulan ini
        $totalPenjualan = Visit::whereYear('tanggal_kunjungan', $thisYear)
            ->whereMonth('tanggal_kunjungan', $thisMonth)
            ->sum('total_harga');

        // Outlet baru bulan ini
        $outletBaru = Outlet::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();

        // Aktivitas terakhir (kunjungan)
        $aktivitasTerakhir = Visit::with('outlet')
            ->orderBy('tanggal_kunjungan', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($visit) {
                return [
                    'type' => 'kunjungan',
                    'title' => 'Kunjungan ke ' . $visit->outlet->nama_toko,
                    'time' => $visit->tanggal_kunjungan->diffForHumans(),
                    'amount' => $visit->total_harga > 0 ? $visit->total_harga : null,
                ];
            });

        // Tambah aktivitas outlet baru
        $outletBaruAktivitas = Outlet::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->orderBy('created_at', 'desc')
            ->limit(1)
            ->get()
            ->map(function ($outlet) {
                return [
                    'type' => 'outlet_baru',
                    'title' => 'Outlet baru: ' . $outlet->nama_toko,
                    'time' => $outlet->created_at->diffForHumans(),
                    'amount' => null,
                ];
            });

        $aktivitas = $aktivitasTerakhir->merge($outletBaruAktivitas)
            ->sortByDesc('time')
            ->take(3);

        return [
            'target_kunjungan' => $target,
            'kunjungan_bulan_ini' => $kunjunganBulanIni,
            'total_penjualan' => $totalPenjualan,
            'outlet_baru' => $outletBaru,
            'aktivitas_terakhir' => $aktivitas,
            'progress' => $target > 0 ? ($kunjunganBulanIni / $target) * 100 : 0,
        ];
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->stats,
        ]);
    }
}