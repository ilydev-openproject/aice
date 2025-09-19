<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;
use App\Models\Product;
use App\Models\Visit;
use App\Models\VisitItem;
use Livewire\WithFileUploads;

class VisitForm extends Component
{
    use WithFileUploads;

    public $outlet_id = '';
    public $catatan = '';
    public $foto_bukti;
    public $tanggal_kunjungan;

    public $products = []; // daftar produk yang diorder
    public $searchProduct = '';

    public function mount()
    {
        $this->tanggal_kunjungan = now()->format('Y-m-d');
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
                'hpp' => $product->hpp,
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
        // Hitung ulang total harga
        $total = 0;
        foreach ($this->products as &$item) {
            $item['total_harga'] = $item['jumlah_box'] * $item['hpp'];
            $total += $item['total_harga'];
        }
        $this->dispatch('update-total', total: $total);
    }

    public function saveVisit()
    {
        $this->validate([
            'outlet_id' => 'required|exists:outlets,id',
            'tanggal_kunjungan' => 'required|date',
        ]);

        // Pastikan ada produk yang diorder
        $hasOrder = collect($this->products)->sum('jumlah_box') > 0;
        if (!$hasOrder) {
            $this->addError('products', 'Minimal 1 produk harus diisi jumlahnya.');
            return;
        }

        $path = null;
        if ($this->foto_bukti) {
            $path = $this->foto_bukti->store('visits', 'public');
        }

        // Hitung total harga
        $totalHarga = 0;
        foreach ($this->products as $item) {
            if ($item['jumlah_box'] > 0) {
                $totalHarga += $item['jumlah_box'] * $item['hpp'];
            }
        }

        // Simpan kunjungan
        $visit = Visit::create([
            'outlet_id' => $this->outlet_id,
            'catatan' => $this->catatan,
            'foto_bukti' => $path,
            'tanggal_kunjungan' => $this->tanggal_kunjungan,
            'total_harga' => $totalHarga,
        ]);

        // Simpan detail order
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

        session()->flash('success', 'Kunjungan dan order berhasil disimpan!');
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->outlet_id = '';
        $this->catatan = '';
        $this->foto_bukti = null;
        $this->tanggal_kunjungan = now()->format('Y-m-d');
        $this->searchProduct = '';
        $this->loadProducts();
    }

    public function render()
    {
        $outlets = Outlet::all(); // Ambil semua toko

        return view('livewire.visit-form', [
            'outlets' => $outlets,
        ]);
    }
}