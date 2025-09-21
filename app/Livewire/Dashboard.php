<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Visit;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\VisitItem;

class Dashboard extends Component
{
    public function getStatsProperty()
    {
        $today = now()->format('Y-m-d');
        $thisMonth = now()->format('m');
        $thisYear = now()->format('Y');

        // Target kunjungan hari ini
        $target = 40;

        // Kunjungan bulan ini (jumlah toko dikunjungi)
        $kunjunganBulanIni = Visit::whereYear('tanggal_kunjungan', $thisYear)
            ->whereMonth('tanggal_kunjungan', $thisMonth)
            ->whereDate('tanggal_kunjungan', $today)
            ->count();

        // Total BOX terjual hari ini (bukan rupiah!)
        $totalBoxTerjual = VisitItem::whereHas('visit', function ($query) use ($thisYear, $thisMonth, $today) {
            $query->whereYear('tanggal_kunjungan', $thisYear)
                ->whereMonth('tanggal_kunjungan', $thisMonth)
                ->whereDate('tanggal_kunjungan', $today);
        })->sum('jumlah_box');

        // Outlet baru bulan ini
        $outletBaru = Outlet::whereYear('created_at', $thisYear)
            ->whereMonth('created_at', $thisMonth)
            ->count();

        // Aktivitas terakhir (kunjungan) — tampilkan jumlah box
        $aktivitasTerakhir = Visit::with(['outlet', 'visitItems'])
            ->orderBy('tanggal_kunjungan', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($visit) {
                $totalBox = $visit->visitItems()->sum('jumlah_box');
                return [
                    'type' => 'kunjungan',
                    'title' => 'Kunjungan ke ' . $visit->outlet->nama_toko,
                    'time' => $visit->tanggal_kunjungan->diffForHumans(),
                    'amount' => $totalBox > 0 ? $totalBox : null, // <-- Jumlah box, bukan rupiah
                    'unit' => 'box',
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
                    'unit' => null,
                ];
            });

        $aktivitas = $aktivitasTerakhir->merge($outletBaruAktivitas)
            ->sortByDesc('time')
            ->take(3);

        return [
            'target_kunjungan' => $target,
            'kunjungan_bulan_ini' => $kunjunganBulanIni,
            'total_box_terjual' => $totalBoxTerjual, // <-- Ganti nama
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