<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProductCatalog extends Component
{
    use WithFileUploads;

    public $search = '';
    public $showModal = false;
    public $filter = '';
    public $is_available = true;
    // State form
    public $nama_produk, $hpp, $harga_jual_per_item, $isi_per_box, $margin;
    public $foto; // handle upload file
    public $fotoPath; // simpan path foto lama saat edit
    public $editingProductId = null;

    public function render()
    {
        $query = Product::when($this->search, function ($query) {
            $query->where('nama_produk', 'like', '%' . $this->search . '%');
        });

        // Handle Filter
        if ($this->filter === 'termurah') {
            $query->orderBy('hpp', 'asc');
        } elseif ($this->filter === 'termahal') {
            $query->orderBy('hpp', 'desc');
        } elseif ($this->filter === 'untung_gede') {
            $query->orderBy('margin', 'desc');
        } elseif ($this->filter === 'isi_per_box') {
            $query->orderBy('isi_per_box', 'desc');
        } elseif ($this->filter === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        }

        $products = $query->get();

        return view('livewire.product-catalog', [
            'products' => $products,
        ]);
    }

    // Buka modal
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    // Tutup modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
        $this->resetValidation();
    }

    // Reset form
    public function resetForm()
    {
        $this->nama_produk = '';
        $this->hpp = '';
        $this->harga_jual_per_item = '';
        $this->isi_per_box = '';
        $this->margin = '';
        $this->foto = null;
        $this->fotoPath = null;
        $this->is_available = true; // <-- default true
        $this->editingProductId = null;
    }

    // Simpan produk
    public function saveProduct()
    {
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'hpp' => 'required|numeric|min:0',
            'harga_jual_per_item' => 'required|numeric|min:0',
            'isi_per_box' => 'required|integer|min:1',
            'foto' => 'nullable|image|max:1024',
            'is_available' => 'required|boolean', // <-- tambahkan ini
        ];

        $this->validate($rules);

        // Hitung margin otomatis
        $this->margin = ($this->harga_jual_per_item * $this->isi_per_box) - $this->hpp;

        $path = null;
        if ($this->foto) {
            $path = $this->foto->store('products', 'public');
        }

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $oldPath = $product->foto;

            $product->update([
                'nama_produk' => $this->nama_produk,
                'hpp' => $this->hpp,
                'het' => $this->harga_jual_per_item,
                'isi_per_box' => $this->isi_per_box,
                'margin' => $this->margin,
                'foto' => $path ?: $oldPath,
                'is_available' => $this->is_available, // <-- simpan
            ]);

            if ($path && $oldPath && $oldPath !== $path) {
                \Storage::disk('public')->delete($oldPath);
            }

            session()->flash('success', 'Produk berhasil diupdate!');
        } else {
            Product::create([
                'nama_produk' => $this->nama_produk,
                'hpp' => $this->hpp,
                'het' => $this->harga_jual_per_item,
                'isi_per_box' => $this->isi_per_box,
                'margin' => $this->margin,
                'foto' => $path,
                'is_available' => $this->is_available, // <-- simpan
            ]);

            session()->flash('success', 'Produk berhasil ditambahkan!');
        }

        $this->closeModal();
    }

    public function updatedFoto()
    {
        if (!$this->editingProductId) {
            $this->validate([
                'foto' => 'required|image|max:1024',
            ]);
        }
    }

    // Edit produk
    public function editProduct($productId)
    {
        $product = Product::findOrFail($productId);

        $this->editingProductId = $product->id;
        $this->nama_produk = $product->nama_produk;
        $this->hpp = $product->hpp;
        $this->harga_jual_per_item = $product->het;
        $this->isi_per_box = $product->isi_per_box;
        $this->foto = null;
        $this->fotoPath = $product->foto;
        $this->is_available = $product->is_available; // <-- tambahkan ini

        $this->showModal = true;
    }

    // Hapus produk
    public function deleteProduct($productId)
    {
        $product = Product::findOrFail($productId);

        if ($product->foto) {
            \Storage::disk('public')->delete($product->foto);
        }

        $product->delete();

        session()->flash('success', 'Produk berhasil dihapus!');
    }

    // Hapus foto
    public function removePhoto()
    {
        if ($this->editingProductId && $this->fotoPath) {
            \Storage::disk('public')->delete($this->fotoPath);
            $this->fotoPath = null;
        }

        $this->foto = null;
    }
}