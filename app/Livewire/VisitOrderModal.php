<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\VisitItem;
use App\Models\Product;

class VisitOrderModal extends Component
{
    public $show = false;
    public $visitId = null;

    public $searchProduct = '';
    public $products = [];

    protected $listeners = ['openOrderModal'];

    public function openOrderModal($visitId)
    {
        $this->visitId = $visitId;
        $this->searchProduct = '';
        $this->loadProducts();

        $visit = Visit::with('visitItems')->findOrFail($visitId);
        $savedItems = $visit->visitItems->keyBy('product_id');

        foreach ($this->products as &$product) {
            $product['jumlah_box'] = $savedItems[$product['id']]->jumlah_box ?? 0;
        }

        $this->show = true;
    }

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
                    'total_harga' => 0,
                ];
            })->toArray();
    }

    public function updatedSearchProduct()
    {
        $this->loadProducts();
    }

    public function increment($index)
    {
        if (isset($this->products[$index]) && $this->products[$index]['is_available']) {
            $this->products[$index]['jumlah_box']++;
        }
    }

    public function decrement($index)
    {
        if (isset($this->products[$index]) && $this->products[$index]['jumlah_box'] > 0) {
            $this->products[$index]['jumlah_box']--;
        }
    }

    public function saveOrder()
    {
        $visit = Visit::findOrFail($this->visitId);

        $totalHarga = 0;
        $totalBox = 0;

        foreach ($this->products as $item) {
            if ($item['jumlah_box'] > 0) {
                $totalHarga += $item['jumlah_box'] * $item['hpp'];
                $totalBox += $item['jumlah_box'];
            }
        }

        VisitItem::where('visit_id', $visit->id)->delete();

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

        $visit->update(['total_harga' => $totalHarga]);

        $message = $totalBox > 0 ? 'Order berhasil disimpan!' : 'Order kosong berhasil disimpan.';
        session()->flash('success', $message);

        $this->show = false;
        $this->dispatch('visitUpdated'); // ← kirim event ke parent
    }

    public function render()
    {
        return view('livewire.visit-order-modal');
    }
}