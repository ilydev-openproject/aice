<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VisitList extends Component
{
    // Hanya simpan state yang dibutuhkan untuk filter & search
    public $search = '';
    public $filter = 'today'; // 'today', 'custom'
    public $custom_date = '';

    // Listener untuk refresh saat child component simpan data
    protected $listeners = ['visitUpdated' => '$refresh'];

    public function mount()
    {
        $this->custom_date = now()->format('Y-m-d');
    }

    // Untuk menampilkan label filter di UI
    public function getActiveFilterLabelProperty()
    {
        if ($this->filter === 'today') {
            return 'Hari Ini';
        } elseif ($this->filter === 'custom' && $this->custom_date) {
            return 'Tanggal: ' . Carbon::parse($this->custom_date)->translatedFormat('d F Y');
        }
        return 'Semua Kunjungan';
    }

    public function render()
    {
        // 👇 Preload stats per outlet_id untuk bulan ini — HANYA 1 QUERY!
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();

        $outletStats = Visit::select('outlet_id')
            ->selectRaw('COUNT(*) as total_visits')
            ->selectRaw('COALESCE(SUM(vi.jumlah_box), 0) as total_boxes')
            ->leftJoin('visit_items as vi', 'visits.id', '=', 'vi.visit_id')
            ->whereBetween('tanggal_kunjungan', [$monthStart, $monthEnd])
            ->groupBy('outlet_id')
            ->get()
            ->keyBy('outlet_id') // jadikan associative array dengan key = outlet_id
            ->map(function ($item) {
                return [
                    'total_visits' => $item->total_visits,
                    'total_boxes' => $item->total_boxes,
                ];
            });

        // 👇 Query utama kunjungan
        $query = Visit::with('outlet', 'visitItems');

        if ($this->filter === 'today') {
            $date = today();
            $query->whereDate('tanggal_kunjungan', $date)
                ->select('*', DB::raw("ROW_NUMBER() OVER (ORDER BY created_at) as visit_order_today"));
        } elseif ($this->filter === 'custom' && $this->custom_date) {
            $date = $this->custom_date;
            $query->whereDate('tanggal_kunjungan', $date)
                ->select('*', DB::raw("ROW_NUMBER() OVER (ORDER BY created_at) as visit_order_today"));
        }

        if ($this->search) {
            $query->whereHas('outlet', function ($q) {
                $q->where('nama_toko', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_toko', 'like', '%' . $this->search . '%');
            });
        }

        // 👇 Ambil data dengan pagination
        $visits = $query->latest()->paginate(10);

        // 👇 Tambahkan stats ke setiap visit — agar bisa dipakai di Blade tanpa query tambahan
        foreach ($visits as $visit) {
            $stats = $outletStats[$visit->outlet_id] ?? ['total_visits' => 0, 'total_boxes' => 0];
            $visit->total_visits_this_month = $stats['total_visits'];
            $visit->total_boxes_this_month = $stats['total_boxes'];
        }

        // 👇 Return view — TIDAK ADA HTML/BLADE DI SINI!
        return view('livewire.visit-list', [
            'visits' => $visits,
        ]);
    }
}