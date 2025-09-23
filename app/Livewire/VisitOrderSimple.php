<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\VisitItem;
use App\Models\Product;

class VisitOrderSimple extends Component
{
    public $show = false;
    public $visitId = null;
    public $products = [];
    public $searchProduct = '';

    // Method untuk buka modal
    public function open($visitId)
    {
        $this->visitId = $visitId;
        $this->searchProduct = '';
        $this->loadProducts();

        // Load data order lama
        $visit = Visit::with('visitItems')->findOrFail($visitId);
        $savedItems = $visit->visitItems->keyBy('product_id');

        foreach ($this->products as &$product) {
            $product['jumlah_box'] = $savedItems[$product['id']]->jumlah_box ?? 0;
        }

        $this->show = true;
    }

    // Load produk
    public function loadProducts()
    {
        $query = Product::select('id', 'nama_produk', 'hpp', 'het as harga_jual', 'foto', 'is_available');

        if ($this->searchProduct) {
            $query->where('nama_produk', 'like', '%' . $this->searchProduct . '%');
        }

        $this->products = $query->limit(20)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'nama_produk' => $product->nama_produk,
                    'hpp' => (int) $product->hpp,
                    'harga_jual' => (int) $product->harga_jual,
                    'foto' => $product->foto,
                    'is_available' => $product->is_available,
                    'jumlah_box' => 0,
                ];
            })->toArray();
    }

    // Update search
    public function updatedSearchProduct()
    {
        $this->loadProducts();
    }

    // Simpan order
    public function saveOrder()
    {
        $visit = Visit::findOrFail($this->visitId);

        // Hapus order lama
        VisitItem::where('visit_id', $visit->id)->delete();

        // Simpan order baru
        $totalHarga = 0;
        foreach ($this->products as $product) {
            if ($product['jumlah_box'] > 0) {
                VisitItem::create([
                    'visit_id' => $visit->id,
                    'product_id' => $product['id'],
                    'jumlah_box' => $product['jumlah_box'],
                    'harga_per_box' => $product['hpp'],
                    'total_harga' => $product['jumlah_box'] * $product['hpp'],
                ]);
                $totalHarga += $product['jumlah_box'] * $product['hpp'];
            }
        }

        $visit->update(['total_harga' => $totalHarga]);

        session()->flash('success', 'Order berhasil disimpan!');
        $this->show = false; // Tutup modal
        $this->dispatch('visitUpdated'); // Refresh parent
    }

    // Tutup modal
    public function close()
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.visit-order-simple');
    }
}