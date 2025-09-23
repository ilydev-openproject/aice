<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Visit;
use App\Models\VisitItem;
use App\Models\Product;

class SimpleOrderModal extends Component
{
    // Properti untuk mengontrol tampilan modal
    public bool $show = false;

    // Properti untuk menyimpan ID kunjungan yang sedang diedit
    public int $visitId = 0;

    // Properti untuk pencarian
    public string $search = '';

    // Properti utama: Daftar produk dengan jumlah box-nya
    // Format: [ ['id' => 1, 'name' => 'Produk A', 'price' => 10000, 'stock' => true, 'qty' => 0], ... ]
    public array $items = [];

    // Listener untuk membuka modal
    protected $listeners = ['openSimpleOrderModal' => 'loadData'];

    /**
     * Method utama untuk memuat data awal
     */
    public function loadData(int $visitId): void
    {
        $this->visitId = $visitId;
        $this->search = '';
        $this->loadItems();
        $this->loadExistingOrder();
        $this->show = true;
    }

    /**
     * Memuat daftar produk dari database
     */
    public function loadItems(): void
    {
        $query = Product::select('id', 'nama_produk', 'het as price', 'is_available');

        if (!empty($this->search)) {
            $query->where('nama_produk', 'like', '%' . $this->search . '%');
        }

        $products = $query->limit(50)->get();

        // Reset array items
        $this->items = [];

        foreach ($products as $product) {
            $this->items[] = [
                'id' => (int) $product->id,
                'name' => (string) $product->nama_produk,
                'price' => (int) $product->price,
                'stock' => (bool) $product->is_available,
                'qty' => 0, // Default 0, akan diisi di loadExistingOrder
            ];
        }
    }

    /**
     * Memuat order yang sudah ada (jika ada)
     */
    public function loadExistingOrder(): void
    {
        $existingItems = VisitItem::where('visit_id', $this->visitId)->get()->keyBy('product_id');

        foreach ($this->items as $index => $item) {
            if (isset($existingItems[$item['id']])) {
                $this->items[$index]['qty'] = (int) $existingItems[$item['id']]->jumlah_box;
            }
        }
    }

    /**
     * Event handler saat input pencarian berubah
     */
    public function updatedSearch(): void
    {
        $this->loadItems();
        $this->loadExistingOrder(); // Opsional: bisa dihapus jika ingin reset qty saat search
    }

    /**
     * Menambah jumlah box
     */
    public function increment(int $index): void
    {
        if (isset($this->items[$index]) && $this->items[$index]['stock']) {
            $this->items[$index]['qty']++;
        }
    }

    /**
     * Mengurangi jumlah box
     */
    public function decrement(int $index): void
    {
        if (isset($this->items[$index]) && $this->items[$index]['qty'] > 0) {
            $this->items[$index]['qty']--;
        }
    }

    /**
     * Menyimpan order ke database
     */
    public function save(): void
    {
        $visit = Visit::findOrFail($this->visitId);

        // Hapus order lama
        VisitItem::where('visit_id', $visit->id)->delete();

        $totalHarga = 0;
        $totalBox = 0;

        // Simpan order baru
        foreach ($this->items as $item) {
            if ($item['qty'] > 0) {
                VisitItem::create([
                    'visit_id' => $visit->id,
                    'product_id' => $item['id'],
                    'jumlah_box' => $item['qty'],
                    'harga_per_box' => $item['price'], // Asumsikan HPP = Harga Jual untuk sementara
                    'total_harga' => $item['qty'] * $item['price'],
                ]);

                $totalHarga += $item['qty'] * $item['price'];
                $totalBox += $item['qty'];
            }
        }

        // Update total harga di tabel visits
        $visit->update(['total_harga' => $totalHarga]);

        // Flash message
        $message = $totalBox > 0 ? 'Order berhasil disimpan!' : 'Order kosong disimpan.';
        session()->flash('success', $message);

        // Tutup modal dan beri tahu parent component
        $this->show = false;
        $this->dispatch('visitUpdated');
    }

    /**
     * Menutup modal tanpa menyimpan
     */
    public function cancel(): void
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.simple-order-modal');
    }
}