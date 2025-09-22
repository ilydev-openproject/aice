<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Visit;
use App\Models\VisitItem;
use Carbon\Carbon;

class VisitList extends Component
{
    use WithFileUploads;

    public $search = '';
    public $showModal = false; // Modal kunjungan
    public $showOrderModal = false; // Modal order
    public $currentVisitId = null;

    // Form Kunjungan
    public $outlet_id = '';
    public $catatan = '';
    public $foto_bukti;
    public $tanggal_kunjungan;

    // Form Order
    public $products = [];
    public $searchProduct = '';
    public $totalHarga = 0;

    // Filter
    public $filter = 'today'; // 'today', 'custom'
    public $custom_date = '';

    public function mount()
    {
        $this->tanggal_kunjungan = now()->format('Y-m-d');
        $this->custom_date = now()->format('Y-m-d');
        $this->loadProducts();
    }

    // --- Modal Kunjungan ---
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    // --- Modal Order ---
    public function openOrderModal($visitId)
    {
        $this->currentVisitId = $visitId;

        $this->searchProduct = '';
        $this->loadProducts();

        // Tampilkan loading sebentar
        $this->dispatch('loading');

        // Load saved data
        $visit = Visit::with('visitItems')->findOrFail($visitId);
        $savedItems = $visit->visitItems->keyBy('product_id');

        foreach ($this->products as $index => &$product) {
            $product['jumlah_box'] = $savedItems[$product['id']]->jumlah_box ?? 0;
        }

        $this->updatedProducts();

        // 🔥 TUTUP MODAL KUNJUNGAN saat buka modal order
        $this->showModal = false;

        $this->showOrderModal = true;
    }

    public function closeOrderModal()
    {
        $this->showOrderModal = false;
        $this->showModal = false; // 👈 Tambahkan ini!
        $this->resetValidation();
    }

    public function resetOrderForm()
    {
        $this->searchProduct = '';
        $this->loadProducts();
    }

    public function loadProducts()
    {
        $this->products = Product::when($this->searchProduct, function ($query) {
            $query->where('nama_produk', 'like', '%' . $this->searchProduct . '%');
        })->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'nama_produk' => $product->nama_produk,
                'hpp' => (int) $product->hpp,
                'harga_jual' => (int) $product->het,
                'foto' => $product->foto,
                'is_available' => $product->is_available,
                'jumlah_box' => 0,
                'total_harga' => 0,
            ];
        })->toArray();
    }

    public function updatedSearchProduct()
    {
        $this->loadProducts();
    }

    public function updatedProducts()
    {
        $total = 0;
        foreach ($this->products as &$item) {
            $item['total_harga'] = $item['jumlah_box'] * $item['hpp'];
            $total += $item['total_harga'];
        }

        $this->totalHarga = $total;
        $this->dispatch('update-total', total: $total);
    }

    public function increment($index)
    {
        if (isset($this->products[$index])) {
            $this->products[$index]['jumlah_box']++;
            $this->updatedProducts();
        }
    }

    public function decrement($index)
    {
        if (isset($this->products[$index]) && $this->products[$index]['jumlah_box'] > 0) {
            $this->products[$index]['jumlah_box']--;
            $this->updatedProducts();
        }
    }

    public function openEditVisit($visitId)
    {
        $visit = Visit::findOrFail($visitId);

        // Isi form dengan data lama
        $this->currentVisitId = $visit->id;
        $this->outlet_id = $visit->outlet_id;
        $this->catatan = $visit->catatan;
        $this->tanggal_kunjungan = Carbon::parse($visit->tanggal_kunjungan)->format('Y-m-d');

        $this->showModal = true; // tampilkan modal edit
    }

    // Simpan Kunjungan
    public function saveVisit()
    {
        $this->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'tanggal_kunjungan' => 'required|date',
        ]);

        $path = $this->foto_bukti ? $this->foto_bukti->store('visits', 'public') : null;

        $fullDateTime = $this->tanggal_kunjungan . ' ' . now()->format('H:i:s');

        if ($this->currentVisitId) {
            // 🔹 Update
            $visit = Visit::findOrFail($this->currentVisitId);
            $visit->update([
                'outlet_id' => $this->outlet_id,
                'catatan' => $this->catatan,
                'foto_bukti' => $path ?? $visit->foto_bukti,
                'tanggal_kunjungan' => $fullDateTime,
            ]);
            session()->flash('success', 'Kunjungan berhasil diperbarui!');
        } else {
            // 🔹 Create
            Visit::create([
                'outlet_id' => $this->outlet_id,
                'catatan' => $this->catatan,
                'foto_bukti' => $path,
                'tanggal_kunjungan' => $fullDateTime,
                'total_harga' => 0,
            ]);
            session()->flash('success', 'Kunjungan berhasil disimpan!');
        }

        $this->closeModal();
    }


    // Simpan Order
    public function saveOrder()
    {
        if (!$this->currentVisitId) {
            $this->addError('order', 'Kunjungan tidak ditemukan.');
            return;
        }

        $hasOrder = collect($this->products)->sum('jumlah_box') > 0;
        if (!$hasOrder) {
            $this->addError('products', 'Minimal 1 produk harus diisi jumlahnya.');
            return;
        }

        $visit = Visit::findOrFail($this->currentVisitId);

        // Hitung total harga & total box
        $totalHarga = 0;
        $totalBox = 0;
        foreach ($this->products as $item) {
            if ($item['jumlah_box'] > 0) {
                $totalHarga += $item['jumlah_box'] * $item['hpp'];
                $totalBox += $item['jumlah_box'];
            }
        }

        // Hapus order lama
        VisitItem::where('visit_id', $visit->id)->delete();

        // Simpan order baru
        foreach ($this->products as $item) {
            if ($item['jumlah_box'] > 0) {
                VisitItem::create([
                    'visit_id' => $visit->id,
                    'product_id' => $item['id'],
                    'jumlah_box' => $item['jumlah_box'],
                    'harga_per_box' => $item['hpp'],
                    'total_harga' => $item['jumlah_box'] * $item['hpp'],
                ]);
            }
        }

        // Update kunjungan
        $visit->update(['total_harga' => $totalHarga]);

        session()->flash('success', 'Order berhasil disimpan!');
        $this->closeOrderModal();
    }

    public function resetForm()
    {
        $this->outlet_id = '';
        $this->catatan = '';
        $this->foto_bukti = null;
        $this->tanggal_kunjungan = now()->format('Y-m-d');
    }

    // Hitung urutan kunjungan hari ini untuk toko tertentu
    private function calculateGlobalVisitOrderToday($visitDate, $currentVisitId)
    {
        // Ambil SEMUA kunjungan hari ini, urutkan berdasarkan waktu simpan
        $visitIdsOrdered = Visit::whereDate('tanggal_kunjungan', $visitDate)
            ->orderBy('created_at', 'asc')
            ->pluck('id')
            ->toArray();

        $position = array_search($currentVisitId, $visitIdsOrdered);

        return $position !== false ? $position + 1 : 1;
    }

    public function getTotalVisitsThisMonth($outletId)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return Visit::where('outlet_id', $outletId)
            ->whereBetween('tanggal_kunjungan', [$startOfMonth, $endOfMonth])
            ->count();
    }
    public function getActiveFilterLabelProperty()
    {
        if ($this->filter === 'today') {
            return 'Hari Ini';
        } elseif ($this->filter === 'custom' && $this->custom_date) {
            return 'Tanggal: ' . Carbon::parse($this->custom_date)->translatedFormat('d F Y');
        }
        return 'Semua Kunjungan';
    }

    // Hitung total box bulan ini untuk toko
    public function getTotalBoxesThisMonth($outletId)
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        return VisitItem::whereHas('visit', function ($q) use ($outletId, $startOfMonth, $endOfMonth) {
            $q->where('outlet_id', $outletId)
                ->whereBetween('tanggal_kunjungan', [$startOfMonth, $endOfMonth]);
        })->sum('jumlah_box');
    }
    public function render()
    {
        $query = Visit::with('outlet', 'visitItems');

        if ($this->filter === 'today') {
            $query->whereDate('tanggal_kunjungan', today());
        } elseif ($this->filter === 'custom' && $this->custom_date) {
            $query->whereDate('tanggal_kunjungan', $this->custom_date);
        }

        if ($this->search) {
            $query->whereHas('outlet', function ($q) {
                $q->where('nama_toko', 'like', '%' . $this->search . '%')
                    ->orWhere('kode_toko', 'like', '%' . $this->search . '%');
            });
        }

        $visits = $query->latest()->get();

        // Hitung urutan global berdasarkan created_at
        foreach ($visits as $visit) {
            $visit->visit_order_today = $this->calculateGlobalVisitOrderToday(
                $visit->tanggal_kunjungan->format('Y-m-d'),
                $visit->id
            );
        }

        $outlets = Outlet::all();

        return view('livewire.visit-list', [
            'visits' => $visits,
            'outlets' => $outlets,
        ]);
    }
}