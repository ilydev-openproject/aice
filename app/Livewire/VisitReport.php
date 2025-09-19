<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Visit;
use App\Models\Outlet;
use Livewire\Component;
use App\Models\VisitItem;

class VisitReport extends Component
{
    public $outlet_id = '';
    public $tanggal = '';

    public function mount()
    {
        $this->tanggal = now()->format('Y-m');
    }

    public function getReportProperty()
    {
        if (!$this->outlet_id) {
            return null;
        }

        $outlet = Outlet::findOrFail($this->outlet_id);
        $visits = Visit::where('outlet_id', $this->outlet_id)
            ->whereYear('tanggal_kunjungan', now()->year)
            ->whereMonth('tanggal_kunjungan', now()->month)
            ->get();

        $totalOrderBulanIni = VisitItem::whereHas('visit', function ($query) {
            $query->where('outlet_id', $this->outlet_id)
                ->whereYear('tanggal_kunjungan', now()->year)
                ->whereMonth('tanggal_kunjungan', now()->month);
        })->sum('jumlah_box');

        return [
            'outlet' => $outlet,
            'kunjungan_ke' => $visits->count(),
            'total_order' => $visits->sum('total_harga') > 0 ? $visits->sum('total_harga') : 0,
            'total_order_bulan_ini' => $totalOrderBulanIni,
        ];
    }

    public function render()
    {
        $outlets = Outlet::all();

        return view('livewire.visit-report', [
            'outlets' => $outlets,
            'report' => $this->report,
        ]);
    }
}