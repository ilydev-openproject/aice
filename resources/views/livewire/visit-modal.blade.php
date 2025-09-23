<div x-data="{ open: @entangle('show') }" x-show="open" x-on:keydown.escape.window="open = false"
    class="z-151 fixed inset-0 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full"
        class="relative h-[100vh] w-full max-w-md overflow-y-auto bg-white shadow-2xl">

        <!-- Header -->
        <div class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
            <button wire:click="close" class="rounded-full p-2 transition hover:bg-gray-100">
                <x-lucide-arrow-left class="h-6 w-6 text-[#141217]" />
            </button>
            <h2 class="text-lg font-bold">
                {{ $currentVisitId ? 'Edit Kunjungan' : 'Tambah Kunjungan' }}
            </h2>
            <div class="w-10"></div>
        </div>

        <!-- Flash -->
        @if (session()->has('success'))
            <div class="mx-6 mt-4 rounded-lg border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="save" class="mt-6 space-y-6 px-6" id="visit-form">
            <!-- Pilih Toko -->
            <div class="grid grid-cols-1 gap-4">
                <label class="flex flex-col gap-2">
                    <span class="mb-2 block text-sm font-medium text-gray-700">Pilih Toko *</span>
                    <div x-data="{
                        open: false,
                        selectedLabel: 'Pilih Toko',
                        search: '',
                        outlets: @js($outlets->toArray()),
                        filteredOutlets() {
                            if (!this.search) return this.outlets
                            return this.outlets.filter(outlet =>
                                outlet.nama_toko.toLowerCase().includes(this.search.toLowerCase()) ||
                                (outlet.kode_toko && outlet.kode_toko.toLowerCase().includes(this.search.toLowerCase()))
                            )
                        },
                        init() {
                            $watch('$wire.outlet_id', value => {
                                if (value) {
                                    let outlet = this.outlets.find(o => o.id == value)
                                    if (outlet) {
                                        this.selectedLabel = outlet.nama_toko + (outlet.kode_toko ? ' (' + outlet.kode_toko + ')' : '')
                                    }
                                } else {
                                    this.selectedLabel = 'Pilih Toko'
                                }
                            })
                        }
                    }" class="relative">
                        <div @click="open = !open"
                            class="form-select flex h-12 w-full cursor-pointer items-center justify-between rounded-lg border border-slate-300 bg-white px-3 py-2 text-base text-slate-900 shadow-sm placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            <span x-text="selectedLabel"
                                :class="selectedLabel === 'Pilih Toko' ? 'text-gray-400' : 'text-slate-900'"></span>
                            <svg class="ml-2 h-5 w-5 text-slate-400" xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>

                        <div x-show="open" @click.away="open = false"
                            class="absolute z-10 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">
                            <div class="border-b border-slate-200 p-3">
                                <input type="text" x-model="search" @click.stop placeholder="Cari toko atau kode..."
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div class="max-h-60 overflow-y-auto">
                                <ul>
                                    <li @click="selectedLabel = 'Pilih Toko'; open = false; $wire.set('outlet_id', ''); search = ''"
                                        class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-slate-900 hover:bg-purple-50">
                                        <span class="block truncate font-medium text-gray-500">Pilih Toko</span>
                                    </li>
                                    <template x-for="outlet in filteredOutlets()" :key="outlet.id">
                                        <li @click="selectedLabel = outlet.nama_toko + (outlet.kode_toko ? ' (' + outlet.kode_toko + ')' : ''); open = false; $wire.set('outlet_id', outlet.id); search = ''"
                                            class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-slate-900 transition hover:bg-purple-50">
                                            <span class="block truncate">
                                                <span x-text="outlet.nama_toko"></span>
                                                <template x-if="outlet.kode_toko">
                                                    <span class="text-gray-500"
                                                        x-text="'(' + outlet.kode_toko + ')'"></span>
                                                </template>
                                            </span>
                                        </li>
                                    </template>
                                </ul>
                            </div>
                        </div>
                        <input type="hidden" wire:model.live="outlet_id">
                    </div>
                    @error('outlet_id')
                        <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                    @enderror
                </label>
            </div>

            <!-- Tanggal -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                <input type="date" wire:model="tanggal_kunjungan"
                    class="w-full rounded-full border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-purple-500">
            </div>

            <!-- Catatan -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                <textarea wire:model="catatan"
                    class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-purple-500"
                    rows="3" placeholder="Catatan khusus, komplain, dll"></textarea>
            </div>

            <!-- Foto -->
            <div>
                <label class="mb-2 block text-sm font-medium text-gray-700">Foto Bukti (Opsional)</label>
                <input type="file" wire:model="foto_bukti"
                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded file:border-0 file:bg-purple-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-purple-700 hover:file:bg-purple-100">
                @error('foto_bukti')
                    <span class="text-xs text-red-500">{{ $message }}</span>
                @enderror
                @if ($foto_bukti)
                    <div class="mt-2">
                        <img src="{{ $foto_bukti->temporaryUrl() }}" class="h-20 w-20 rounded object-cover">
                    </div>
                @endif
            </div>
        </form>

        <!-- Action Buttons -->
        <div class="fixed bottom-8 w-full max-w-md border-t border-gray-100 bg-white px-6 py-4 backdrop-blur-sm">
            <div class="flex gap-3">
                <button type="button" wire:click="close"
                    class="flex-1 rounded-full border border-gray-300 px-4 py-3 font-medium text-gray-700 transition hover:bg-gray-50">
                    Batal
                </button>
                <button type="submit" form="visit-form" wire:loading.attr="disabled"
                    class="flex-1 transform rounded-full bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] px-4 py-3 font-bold text-white shadow transition hover:scale-[1.02] hover:shadow-lg active:scale-[0.98]">
                    <span wire:loading.remove>Simpan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>