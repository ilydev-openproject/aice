<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Outlet;
use App\Models\Visit;
use Carbon\Carbon;

class VisitModal extends Component
{
    use WithFileUploads;

    public $show = false; // 👈 WAJIB
    public $currentVisitId = null;

    // Form
    public $outlet_id = '';
    public $catatan = '';
    public $foto_bukti;
    public $tanggal_kunjungan;

    protected $rules = [
        'outlet_id' => 'required|exists:outlets,id',
        'tanggal_kunjungan' => 'required|date',
    ];

    // 👇 WAJIB — dengarkan event dari parent
    protected $listeners = ['openVisitModal' => 'open'];

    public function mount()
    {
        $this->resetForm();
    }

    public function open($visitId = null)
    {
        \Log::info('VisitModal open triggered'); // ← cek log

        $this->resetErrorBag();
        $this->resetValidation();

        if ($visitId) {
            $visit = Visit::findOrFail($visitId);
            $this->currentVisitId = $visit->id;
            $this->outlet_id = $visit->outlet_id;
            $this->catatan = $visit->catatan;
            $this->tanggal_kunjungan = Carbon::parse($visit->tanggal_kunjungan)->format('Y-m-d');
        } else {
            $this->currentVisitId = null;
            $this->resetForm();
        }

        $this->show = true; // 👈 WAJIB true!
    }

    public function save()
    {
        $this->validate();

        $path = $this->foto_bukti ? $this->foto_bukti->store('visits', 'public') : null;
        $fullDateTime = $this->tanggal_kunjungan . ' ' . now()->format('H:i:s');

        if ($this->currentVisitId) {
            $visit = Visit::findOrFail($this->currentVisitId);
            $visit->update([
                'outlet_id' => $this->outlet_id,
                'catatan' => $this->catatan,
                'foto_bukti' => $path ?? $visit->foto_bukti,
                'tanggal_kunjungan' => $fullDateTime,
            ]);
            session()->flash('success', 'Kunjungan berhasil diperbarui!');
        } else {
            Visit::create([
                'outlet_id' => $this->outlet_id,
                'catatan' => $this->catatan,
                'foto_bukti' => $path,
                'tanggal_kunjungan' => $fullDateTime,
                'total_harga' => 0,
            ]);
            session()->flash('success', 'Kunjungan berhasil disimpan!');
        }

        $this->close();
        $this->dispatch('visitUpdated'); // kirim event ke parent
    }

    public function close()
    {
        $this->show = false;
        $this->resetForm();
        $this->resetValidation();
    }

    public function resetForm()
    {
        $this->outlet_id = '';
        $this->catatan = '';
        $this->foto_bukti = null;
        $this->tanggal_kunjungan = now()->format('Y-m-d');
    }

    public function render()
    {
        $outlets = Outlet::select('id', 'nama_toko', 'kode_toko')->get();

        return view('livewire.visit-modal', [
            'outlets' => $outlets,
        ]);
    }
}