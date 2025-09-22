<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Outlet;
use Livewire\WithFileUploads;

class OutletForm extends Component
{
    use WithFileUploads;

    public $nama_toko = '';
    public $kode_toko = '';
    public $jam_buka = '';
    public $jam_tutup = '';
    public $nomor_wa = '';
    public $link = '';
    public $alamat = '';

    public $editingOutletId = null;
    public $search = '';

    // State untuk kontrol modal
    public $showModal = false;

    public function render()
    {
        $outlets = Outlet::when($this->search, function ($query) {
            $query->where('nama_toko', 'like', '%' . $this->search . '%')
                ->orWhere('kode_toko', 'like', '%' . $this->search . '%');
        })->latest()->get();

        return view('livewire.outlet-form', [
            'outlets' => $outlets,
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

    // Simpan toko — DIPERBAIKI AGAR WORK SAAT EDIT
    public function saveOutlet()
    {
        // Hapus validasi strict — biar copas langsung bisa
        $this->validate([
            'nama_toko' => 'required|string|max:255',
            'kode_toko' => 'nullable|string|max:50|unique:outlets,kode_toko,' . $this->editingOutletId,
            'jam_buka' => 'nullable',
            'jam_tutup' => 'nullable',
            'nomor_wa' => 'nullable',
            'link' => 'nullable|string|max:1000', // biarin apa aja, panjangin max
            'alamat' => 'nullable',
        ]);

        try {
            if ($this->editingOutletId) {
                $outlet = Outlet::findOrFail($this->editingOutletId);
                $outlet->update([
                    'nama_toko' => $this->nama_toko,
                    'kode_toko' => $this->kode_toko,
                    'jam_buka' => $this->jam_buka,
                    'jam_tutup' => $this->jam_tutup,
                    'nomor_wa' => trim($this->nomor_wa),
                    'alamat' => $this->alamat,
                    'link' => trim($this->link), // bersihkan spasi
                ]);
                session()->flash('success', 'Toko berhasil diupdate!');
            } else {
                Outlet::create([
                    'nama_toko' => $this->nama_toko,
                    'kode_toko' => $this->kode_toko,
                    'jam_buka' => $this->jam_buka,
                    'jam_tutup' => $this->jam_tutup,
                    'nomor_wa' => trim($this->nomor_wa),
                    'alamat' => $this->alamat,
                    'link' => trim($this->link),
                ]);
                session()->flash('success', 'Toko berhasil ditambahkan!');
            }

            // Pastikan modal TUTUP setelah simpan — ini kunci!
            $this->closeModal();

        } catch (\Exception $e) {
            // Jika masih error, munculkan notifikasi
            session()->flash('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    // Edit toko
    public function editOutlet($outletId)
    {
        $outlet = Outlet::findOrFail($outletId);
        $this->editingOutletId = $outlet->id;
        $this->nama_toko = $outlet->nama_toko;
        $this->kode_toko = $outlet->kode_toko;
        $this->jam_buka = $outlet->jam_buka;
        $this->jam_tutup = $outlet->jam_tutup;
        $this->nomor_wa = $outlet->nomor_wa;
        $this->link = $outlet->link;
        $this->alamat = $outlet->alamat;
        $this->showModal = true;
    }

    // Hapus toko
    public function deleteOutlet($outletId)
    {
        Outlet::findOrFail($outletId)->delete();
        session()->flash('success', 'Toko berhasil dihapus!');
    }

    // Reset form
    public function resetForm()
    {
        $this->nama_toko = '';
        $this->kode_toko = '';
        $this->jam_buka = '';
        $this->jam_tutup = '';
        $this->nomor_wa = '';
        $this->link = '';
        $this->alamat = '';
        $this->editingOutletId = null;
    }
}