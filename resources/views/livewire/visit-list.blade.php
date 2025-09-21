<layout>
    <div class="min-h-screen">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 pb-2">
            <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="">
            <div class="flex w-12 items-center justify-end">
                <button wire:click="openModal"
                    class="flex h-12 w-12 max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full bg-[#BCA1D4]/30 text-base font-bold leading-normal tracking-[0.015em] text-[#141217] shadow-sm backdrop-blur-sm">
                    <x-lucide-plus class="h-8 w-8" style="color: var(--brand-purple);" />
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="px-4 py-3">
            <label class="flex h-14 w-full min-w-40 flex-col">
                <div class="flex h-full w-full flex-1 items-stretch rounded-full bg-white shadow-sm">
                    <div class="flex items-center justify-center pl-5 text-[#756783]">
                        <x-lucide-search class="text-[#756783]" />
                    </div>
                    <input wire:model.live.debounce.300ms="search"
                        class="form-input flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full border-none bg-white px-4 text-base font-normal leading-normal text-[#141217] placeholder:text-[#756783] focus:border-none focus:outline-0 focus:ring-0"
                        placeholder="Cari nama toko..." />
                </div>
            </label>
        </div>

        <!-- Filter Tabs -->
        <div class="px-4 py-3">
            <div class="flex flex-wrap items-center justify-center">
                <!-- Tab Hari Ini -->
                <button wire:click="$set('filter', 'today')"
                    class="{{ $filter === 'today' ? 'bg-purple-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} flex items-center gap-1.5 whitespace-nowrap rounded-l-full px-4 py-2 text-sm font-medium transition">
                    <x-lucide-calendar-days class="h-4 w-4" />
                    <span>Hari Ini</span>
                </button>

                <!-- Tab Pilih Tanggal -->
                <button wire:click="$set('filter', 'custom')"
                    class="{{ $filter === 'custom' ? 'bg-purple-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }} flex items-center gap-1.5 whitespace-nowrap rounded-r-full px-4 py-2 text-sm font-medium transition">
                    <x-lucide-calendar-range class="h-4 w-4" />
                    <span>Pilih Tanggal</span>
                </button>
            </div>

            @if ($filter !== 'today' || $search)
                <!-- Input Tanggal (Hanya muncul jika filter = custom) -->
                <div x-data="{ open: @entangle('filter').isEqualTo('custom') }" x-show="open" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                    x-transition:enter-end="opacity-100 transform translate-y-0"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 transform translate-y-0"
                    x-transition:leave-end="opacity-0 transform -translate-y-2"
                    class="mt-3 flex w-full max-w-xs items-center gap-3 rounded-full bg-gray-50 p-2">
                    <span class="whitespace-nowrap text-sm font-medium text-gray-600">Tanggal:</span>
                    <input type="date" wire:model.live="custom_date"
                        class="flex-1 border-0 bg-transparent text-sm font-medium outline-0" />
                </div>
            @endif
        </div>

        <!-- Daftar Kunjungan -->
        <div class="space-y-4 p-4">
            @if ($visits->isEmpty())
                <div class="rounded-xl bg-gray-50 py-12 text-center">
                    <x-lucide-package class="mx-auto mb-3 h-12 w-12 text-gray-300" />
                    <p class="font-medium text-gray-500">Belum ada kunjungan</p>
                    <p class="mt-1 text-sm text-gray-400">
                        @if ($filter === 'today')
                            Tidak ada kunjungan hari ini.
                        @elseif($filter === 'custom')
                            Tidak ada kunjungan pada tanggal
                            {{ \Carbon\Carbon::parse($custom_date)->translatedFormat('d F Y') }}.
                        @else
                            Tidak ada kunjungan yang sesuai pencarian.
                        @endif
                    </p>
                </div>
            @else
                @foreach ($visits as $visit)
                    <div
                        class="relative rounded-xl border border-gray-100 bg-white p-5 shadow-sm transition hover:shadow-md">
                        <!-- Jam visit -->
                        <div class="absolute bottom-0 left-4 rounded-t-md bg-gray-300 p-1 text-end text-[10px]">
                            <p>{{ $visit->created_at->format('H:i') }}</p>
                        </div>
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-start gap-3">
                                    <div
                                        class="flex h-8 w-8 items-center justify-center overflow-hidden rounded-full bg-gradient-to-br from-purple-500 to-pink-500 p-2">
                                        <span class="text-lg font-bold text-white">
                                            {{ substr($visit->outlet->nama_toko, 0, 1) }}
                                        </span>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-bold leading-tight text-gray-800">
                                            {{ $visit->outlet->nama_toko }}
                                        </h3>
                                        @if ($visit->outlet->kode_toko)
                                            <p class="text-sm font-medium text-purple-600">Kode:
                                                {{ $visit->outlet->kode_toko }}</p>
                                        @endif
                                        <p class="mt-1 flex items-center gap-1.5 text-sm text-gray-500">
                                            <x-lucide-calendar-days class="h-4 w-4" />
                                            <span>{{ Carbon\Carbon::parse($visit->tanggal_kunjungan)->translatedFormat('D m Y') }}</span>
                                        </p>

                                        <!-- Info Kunjungan & Order -->
                                        <div class="mt-1 flex items-center gap-1.5 text-sm text-gray-500">
                                            <x-lucide-package class="h-4 w-4" />
                                            <span class="text-sm text-slate-500">Order:
                                                {{ $visit->visitItems->sum('jumlah_box') }} Box</span>
                                        </div>

                                        @if ($visit->total_harga > 0)
                                            <p class="mt-1 flex items-center gap-1.5 text-sm font-bold text-green-600">
                                                <x-lucide-wallet class="h-4 w-4" />
                                                <span>Rp {{ number_format($visit->total_harga, 0, ',', '.') }}</span>
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="ml-4 flex flex-col gap-2">
                                <button wire:click="openOrderModal({{ $visit->id }})"
                                    class="rounded-xl bg-purple-500 p-3 text-white shadow-sm transition hover:bg-purple-600 hover:shadow"
                                    title="Input Order">
                                    <x-lucide-shopping-cart class="h-5 w-5" />
                                </button>
                                <a href="https://wa.me/?text={{ urlencode(
                                    "==============================\n" .
                                        "           LAPORAN KUNJUNGAN\n" .
                                        "==============================\n" .
                                        'No Urut                 : ' .
                                        $visit->visit_order_today .
                                        "\n" .
                                        'Nama Toko            : ' .
                                        $visit->outlet->nama_toko .
                                        "\n" .
                                        'Kode Toko             : ' .
                                        ($visit->outlet->kode_toko ?? '-') .
                                        "\n" .
                                        "───────────────────\n" .
                                        'Kunjungan ke         : ' .
                                        $this->getTotalVisitsThisMonth($visit->outlet->id) .
                                        " (bln ini)\n" .
                                        'Total order             : ' .
                                        $visit->visitItems->sum('jumlah_box') .
                                        " box\n" .
                                        'Total bln ini            : ' .
                                        $this->getTotalBoxesThisMonth($visit->outlet->id) .
                                        " box\n" .
                                        "───────────────────\n" .
                                        'Jam Operasional    : ' .
                                        ($visit->outlet->jam_buka ? \Carbon\Carbon::parse($visit->outlet->jam_buka)->format('H.i') : '-') .
                                        '-' .
                                        ($visit->outlet->jam_tutup ? \Carbon\Carbon::parse($visit->outlet->jam_tutup)->format('H.i') : '-') .
                                        "\n" .
                                        'No HP                     : ' .
                                        ($visit->outlet->nomor_wa ?? '-') .
                                        "\n" .
                                        "───────────────────\n" .
                                        '==============================',
                                ) }}"
                                    target="_blank"
                                    class="rounded-xl bg-green-500 p-3 text-white shadow-sm transition hover:bg-green-600 hover:shadow"
                                    title="Kirim via WA">
                                    <x-lucide-message-square class="h-5 w-5" />
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- MODAL INPUT KUNJUNGAN -->
        <div x-data="{ open: @entangle('showModal') }" x-show="open" x-on:keydown.escape.window="open = false"
            class="z-151 fixed inset-0 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="transform translate-y-0"
                x-transition:leave-end="transform translate-y-full"
                class="relative h-[100vh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white shadow-2xl">

                <!-- Header -->
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                    <button wire:click="closeModal" class="rounded-full p-2 transition hover:bg-gray-100">
                        <x-lucide-arrow-left class="h-6 w-6 text-[#141217]" />
                    </button>
                    <h2 class="flex-1 text-center text-lg font-bold text-[#141217]">
                        Input Kunjungan
                    </h2>
                    <div class="w-10"></div>
                </div>

                <!-- Flash Message -->
                @if (session()->has('success'))
                    <div class="mx-6 mt-4 rounded-lg border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit.prevent="saveVisit" class="mt-6 space-y-6 px-6" id="visit-form">
                    <div class="grid grid-cols-1 gap-4">
                        <label class="flex flex-col gap-2">
                            <span class="mb-2 block text-sm font-medium text-gray-700">Pilih Toko *</span>
                            <div x-data="{
                                open: false,
                                selectedLabel: 'Pilih Toko',
                                search: '',
                                filteredOutlets() {
                                    if (!this.search) return @js($outlets->toArray());
                                    return @js($outlets->toArray()).filter(outlet =>
                                        outlet.nama_toko.toLowerCase().includes(this.search.toLowerCase()) ||
                                        (outlet.kode_toko && outlet.kode_toko.toLowerCase().includes(this.search.toLowerCase()))
                                    );
                                }
                            }" class="relative">
                                <!-- Trigger Button -->
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

                                <!-- Dropdown List -->
                                <div x-show="open" @click.away="open = false"
                                    class="absolute z-10 mt-1 w-full overflow-hidden rounded-md border border-slate-200 bg-white text-base shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none sm:text-sm">

                                    <!-- Search Input -->
                                    <div class="border-b border-slate-200 p-3">
                                        <input type="text" x-model="search" @click.stop
                                            placeholder="Cari toko atau kode..."
                                            class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-500" />
                                    </div>

                                    <!-- Results -->
                                    <div class="max-h-60 overflow-y-auto">
                                        <ul>
                                            <!-- Option Default -->
                                            <li @click="selectedLabel = 'Pilih Toko'; open = false; $wire.set('outlet_id', ''); search = ''"
                                                class="relative cursor-pointer select-none py-2 pl-3 pr-9 text-slate-900 hover:bg-purple-50">
                                                <span class="block truncate font-medium text-gray-500">Pilih Toko</span>
                                            </li>

                                            <!-- Daftar Outlet (Filtered) -->
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

                                            <!-- No Results -->
                                            <template x-if="filteredOutlets().length === 0 && search">
                                                <li class="px-3 py-2 text-sm italic text-gray-500">
                                                    Toko tidak ditemukan
                                                </li>
                                            </template>
                                        </ul>
                                    </div>
                                </div>

                                <!-- Hidden Input for Livewire -->
                                <input type="hidden" wire:model.live="outlet_id">
                            </div>
                            @error('outlet_id')
                                <span class="mt-1 text-xs text-red-500">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <!-- Tanggal Kunjungan -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Tanggal Kunjungan</label>
                        <input type="date" wire:model="tanggal_kunjungan"
                            class="w-full rounded-full border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-purple-500">
                    </div>

                    <!-- Catatan & Foto -->
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Catatan (Opsional)</label>
                        <textarea wire:model="catatan"
                            class="w-full rounded-lg border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-purple-500"
                            rows="3" placeholder="Catatan khusus, komplain, dll"></textarea>
                    </div>

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
                <div
                    class="fixed bottom-8 w-full max-w-md border-t border-gray-100 bg-white px-6 py-4 backdrop-blur-sm">
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeModal"
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

        <!-- MODAL INPUT ORDER -->
        <div x-data="{ open: @entangle('showOrderModal') }" x-show="open" x-on:keydown.escape.window="open = false"
            class="z-151 fixed inset-0 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="transform translate-y-0"
                x-transition:leave-end="transform translate-y-full"
                class="relative h-[100vh] w-full max-w-md overflow-y-auto rounded-t-3xl bg-white shadow-2xl">

                <!-- Header -->
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                    <button wire:click="closeOrderModal" class="rounded-full p-2 transition hover:bg-gray-100">
                        <x-lucide-arrow-left class="h-6 w-6 text-[#141217]" />
                    </button>
                    <h2 class="flex-1 text-center text-lg font-bold text-[#141217]">
                        Input Order
                    </h2>
                    <div class="w-10"></div>
                </div>

                <!-- Flash Message -->
                @if (session()->has('success'))
                    <div class="mx-6 mt-4 rounded-lg border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Form -->
                <form wire:submit.prevent="saveOrder" class="mt-6 space-y-6 px-6" id="order-form">
                    <!-- Cari Produk -->
                    <div>
                        <input type="text" wire:model.live="searchProduct"
                            class="w-full rounded-full border border-gray-300 px-4 py-3 focus:border-transparent focus:ring-2 focus:ring-purple-500"
                            placeholder="Ketik nama produk...">
                    </div>

                    <!-- Daftar Produk -->
<div class="max-h-128 w-full overflow-y-auto rounded-xl">
    @forelse($products as $index => $product)
        <div class="mb-2 flex items-center gap-3 rounded-lg p-3 shadow-sm transition-all duration-200
            {{ $product['jumlah_box'] > 0 ? 'bg-purple-300/60 border border-purple-200' : 'bg-white' }}">
            <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-lg bg-gray-100">
                @if ($product['foto'])
                    <img src="{{ asset('storage/' . $product['foto']) }}"
                        alt="{{ $product['nama_produk'] }}"
                        class="h-full w-full rounded-lg object-contain">
                @else
                    <x-lucide-image-off class="h-6 w-6 text-gray-400" />
                @endif
            </div>

            <div class="flex-1">
                <p class="text-[10px] font-medium">{{ $product['nama_produk'] }}</p>
                <p class="text-[10px] font-medium text-gray-500">Rp
                    {{ number_format($product['harga_jual'], 0, ',', '.') }}
                </p>
                <p class="text-[10px] font-medium text-gray-500">HPP: Rp
                    {{ number_format($product['hpp'], 0, ',', '.') }}/box
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" wire:click="decrement({{ $index }})"
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300">
                    -
                </button>
                <input type="number" min="0"
                    wire:model.debounce.300ms="products.{{ $index }}.jumlah_box"
                    class="no-spinner w-8 rounded border border-gray-300 px-2 py-1 text-center text-[10px]">
                <button type="button" wire:click="increment({{ $index }})"
                    class="flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300">
                    +
                </button>
            </div>
        </div>
    @empty
        <div class="py-4 text-center text-gray-500">
            Tidak ada produk ditemukan.
        </div>
    @endforelse
</div>
                </form>

                <!-- Action Buttons -->
                <div
                    class="fixed bottom-8 w-full max-w-md border-t border-gray-100 bg-white px-6 py-4 backdrop-blur-sm">
                    <!-- Total Harga -->
                    <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 p-4">
                        <div class="flex items-center justify-between">
                            <span class="text-lg font-bold">Total Order:</span>
                            <span class="text-2xl font-bold text-green-600">
                                Rp {{ number_format($totalHarga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button type="button" wire:click="closeOrderModal"
                            class="flex-1 rounded-full border border-gray-300 px-4 py-3 font-medium text-gray-700 transition hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="submit" form="order-form" wire:loading.attr="disabled"
                            class="flex-1 transform rounded-full bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] px-4 py-3 font-bold text-white shadow transition hover:scale-[1.02] hover:shadow-lg active:scale-[0.98]">
                            <span wire:loading.remove>Simpan Order</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</layout>
