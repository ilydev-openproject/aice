<layout>
    <!-- Header -->
    <div class="flex items-center p-4 pb-2 justify-between">
        <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="">
        <div class="flex w-12 items-center justify-end">
            <button wire:click="openModal"
                class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full h-12 w-12 bg-[#BCA1D4]/30 backdrop-blur-sm text-[#141217] text-base font-bold leading-normal tracking-[0.015em] shadow-sm">
                <x-lucide-plus class="w-8 h-8" style="color: var(--brand-purple);" />
            </button>
        </div>
    </div>

    <!-- Search -->
    <div class="px-4 py-3">
        <label class="flex flex-col min-w-40 h-14 w-full">
            <div class="flex w-full flex-1 items-stretch rounded-full h-full bg-white shadow-sm">
                <div class="text-[#756783] flex items-center justify-center pl-5">
                    <x-lucide-search class="text-[#756783]" />
                </div>
                <input wire:model.live.debounce.300ms="search"
                    class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-[#141217] focus:outline-0 focus:ring-0 border-none bg-white focus:border-none h-full placeholder:text-[#756783] px-4 text-base font-normal leading-normal"
                    placeholder="Cari nama toko..." />
            </div>
        </label>
    </div>

    <!-- Filter Tabs -->
    <div class="px-4 py-3">
        <div class="flex flex-wrap justify-center items-center">
            <!-- Tab Hari Ini -->
            <button wire:click="$set('filter', 'today')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-l-full text-sm font-medium transition whitespace-nowrap
                {{ $filter === 'today' ? 'bg-purple-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <x-lucide-calendar-days class="w-4 h-4" />
                <span>Hari Ini</span>
            </button>

            <!-- Tab Pilih Tanggal -->
            <button wire:click="$set('filter', 'custom')"
                class="flex items-center gap-1.5 px-4 py-2 rounded-r-full text-sm font-medium transition whitespace-nowrap
                {{ $filter === 'custom' ? 'bg-purple-500 text-white shadow' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                <x-lucide-calendar-range class="w-4 h-4" />
                <span>Pilih Tanggal</span>
            </button>
        </div>

        @if($filter !== 'today' || $search)
            <!-- Input Tanggal (Hanya muncul jika filter = custom) -->
            <div x-data="{ open: @entangle('filter').isEqualTo('custom') }" x-show="open"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform -translate-y-2"
                x-transition:enter-end="opacity-100 transform translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 transform translate-y-0"
                x-transition:leave-end="opacity-0 transform -translate-y-2"
                class="mt-3 flex items-center gap-3 bg-gray-50 rounded-full p-2 w-full max-w-xs">
                <span class="text-sm text-gray-600 font-medium whitespace-nowrap">Tanggal:</span>
                <input type="date" wire:model.live="custom_date"
                    class="flex-1 border-0 outline-0 bg-transparent text-sm font-medium" />
            </div>
        @endif
    </div>

    <!-- Daftar Kunjungan -->
    <div class="p-4 space-y-4">
        @if($visits->isEmpty())
            <div class="text-center py-12 bg-gray-50 rounded-xl">
                <x-lucide-package class="w-12 h-12 text-gray-300 mx-auto mb-3" />
                <p class="text-gray-500 font-medium">Belum ada kunjungan</p>
                <p class="text-sm text-gray-400 mt-1">
                    @if($filter === 'today')
                        Tidak ada kunjungan hari ini.
                    @elseif($filter === 'custom')
                        Tidak ada kunjungan pada tanggal {{ \Carbon\Carbon::parse($custom_date)->translatedFormat('d F Y') }}.
                    @else
                        Tidak ada kunjungan yang sesuai pencarian.
                    @endif
                </p>
            </div>
        @else
            @foreach($visits as $visit)
                <div class="relative bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                    <!-- Jam visit -->
                    <div class="absolute left-4 bottom-0 rounded-t-md text-end text-[10px] p-1 bg-gray-300">
                        <p>{{ $visit->created_at->format('H:i') }}</p>
                    </div>
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-start gap-3">
                                <div
                                    class="w-8 h-8 overflow-hidden p-2 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center">
                                    <span class="text-white font-bold text-lg">
                                        {{ substr($visit->outlet->nama_toko, 0, 1) }}
                                    </span>
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg text-gray-800 leading-tight">
                                        {{ $visit->outlet->nama_toko }}
                                    </h3>
                                    @if($visit->outlet->kode_toko)
                                        <p class="text-sm text-purple-600 font-medium">Kode: {{ $visit->outlet->kode_toko }}</p>
                                    @endif
                                    <p class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                                        <x-lucide-calendar-days class="w-4 h-4" />
                                        <span>{{ Carbon\Carbon::parse($visit->tanggal_kunjungan)->translatedFormat('D m Y') }}</span>
                                    </p>

                                    <!-- Info Kunjungan & Order -->
                                    <div class="text-sm text-gray-500 mt-1 flex items-center gap-1.5">
                                        <x-lucide-package class="w-4 h-4" />
                                        <span class="text-slate-500 text-sm">Order:
                                            {{ $visit->visitItems->sum('jumlah_box') }} Box</span>
                                    </div>

                                    @if($visit->total_harga > 0)
                                        <p class="text-sm font-bold text-green-600 mt-1 flex items-center gap-1.5">
                                            <x-lucide-wallet class="w-4 h-4" />
                                            <span>Rp {{ number_format($visit->total_harga, 0, ',', '.') }}</span>
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 ml-4">
                            <button wire:click="openOrderModal({{ $visit->id }})"
                                class="p-3 bg-purple-500 text-white rounded-xl hover:bg-purple-600 transition shadow-sm hover:shadow"
                                title="Input Order">
                                <x-lucide-shopping-cart class="w-5 h-5" />
                            </button>
                            <a href="https://wa.me/?text={{ urlencode(
                    "==============================\n" .
                    "           LAPORAN KUNJUNGAN\n" .
                    "==============================\n" .
                    "No Urut                 : " . $visit->visit_order_today . "\n" .
                    "Nama Toko            : " . $visit->outlet->nama_toko . "\n" .
                    "Kode Toko             : " . ($visit->outlet->kode_toko ?? '-') . "\n" .
                    "────────────────────────────────\n" .
                    "Kunjungan ke         : " . $this->getTotalVisitsThisMonth($visit->outlet->id) . " (bln ini)\n" .
                    "Total order             : " . $visit->visitItems->sum('jumlah_box') . " box\n" .
                    "Total bln ini            : " . $this->getTotalBoxesThisMonth($visit->outlet->id) . " box\n" .
                    "────────────────────────────────\n" .
                    "Jam Operasional    : " .
                    ($visit->outlet->jam_buka ? \Carbon\Carbon::parse($visit->outlet->jam_buka)->format('H.i') : '-') . "-" .
                    ($visit->outlet->jam_tutup ? \Carbon\Carbon::parse($visit->outlet->jam_tutup)->format('H.i') : '-') . "\n" .
                    "No HP                     : " . ($visit->outlet->nomor_wa ?? '-') . "\n" .
                    "────────────────────────────────\n" .

                    "=============================="
                ) }}" target="_blank"
                                class="p-3 bg-green-500 text-white rounded-xl hover:bg-green-600 transition shadow-sm hover:shadow"
                                title="Kirim via WA">
                                <x-lucide-message-square class="w-5 h-5" />
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    <!-- MODAL INPUT KUNJUNGAN -->
    <div x-data="{ open: @entangle('showModal') }" x-show="open" x-on:keydown.escape.window="open = false"
        class="fixed inset-0 z-50 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

        <div x-show="open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="transform translate-y-0"
            x-transition:leave-end="transform translate-y-full"
            class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl overflow-y-auto max-h-[100vh]">

            <!-- Header -->
            <div
                class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <button wire:click="closeModal" class="p-2 rounded-full hover:bg-gray-100 transition">
                    <x-lucide-arrow-left class="w-6 h-6 text-[#141217]" />
                </button>
                <h2 class="text-lg font-bold text-[#141217] text-center flex-1">
                    Input Kunjungan
                </h2>
                <div class="w-10"></div>
            </div>

            <!-- Flash Message -->
            @if (session()->has('success'))
                <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="saveVisit" class="px-6 space-y-6 mt-6" id="visit-form">
                <!-- Pilih Toko -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Toko *</label>
                    <select wire:model="outlet_id"
                        class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        <option value="">-- Pilih Toko --</option>
                        @foreach($outlets as $outlet)
                            <option value="{{ $outlet->id }}">
                                {{ $outlet->nama_toko }} {{ $outlet->kode_toko ? '(' . $outlet->kode_toko . ')' : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('outlet_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Tanggal Kunjungan -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tanggal Kunjungan</label>
                    <input type="date" wire:model="tanggal_kunjungan"
                        class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                </div>

                <!-- Catatan & Foto -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Catatan (Opsional)</label>
                    <textarea wire:model="catatan"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        rows="3" placeholder="Catatan khusus, komplain, dll"></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto Bukti (Opsional)</label>
                    <input type="file" wire:model="foto_bukti"
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-100">
                    @error('foto_bukti') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    @if($foto_bukti)
                        <div class="mt-2">
                            <img src="{{ $foto_bukti->temporaryUrl() }}" class="w-20 h-20 object-cover rounded">
                        </div>
                    @endif
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 backdrop-blur-sm">
                <div class="flex gap-3">
                    <button type="button" wire:click="closeModal"
                        class="flex-1 py-3 px-4 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition">
                        Batal
                    </button>
                    <button type="submit" form="visit-form" wire:loading.attr="disabled"
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] text-white font-bold rounded-full shadow hover:shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98]">
                        <span wire:loading.remove>Simpan</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL INPUT ORDER -->
    <div x-data="{ open: @entangle('showOrderModal') }" x-show="open" x-on:keydown.escape.window="open = false"
        class="fixed inset-0 z-151 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

        <div x-show="open" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="transform translate-y-0"
            x-transition:leave-end="transform translate-y-full"
            class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl overflow-y-auto h-[100vh]">

            <!-- Header -->
            <div
                class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                <button wire:click="closeOrderModal" class="p-2 rounded-full hover:bg-gray-100 transition">
                    <x-lucide-arrow-left class="w-6 h-6 text-[#141217]" />
                </button>
                <h2 class="text-lg font-bold text-[#141217] text-center flex-1">
                    Input Order
                </h2>
                <div class="w-10"></div>
            </div>

            <!-- Flash Message -->
            @if (session()->has('success'))
                <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Form -->
            <form wire:submit.prevent="saveOrder" class="px-6 space-y-6 mt-6" id="order-form">
                <!-- Cari Produk -->
                <div>
                    <input type="text" wire:model.live="searchProduct"
                        class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Ketik nama produk...">
                </div>

                <!-- Daftar Produk -->
                <div class=" w-full max-h-128 overflow-y-auto rounded-xl">
                    @forelse($products as $index => $product)
                        <div class="flex items-center gap-3 p-3 bg-white rounded-lg shadow-sm mb-2">
                            <div class="w-16 h-16 flex-shrink-0 bg-gray-100 rounded-lg flex items-center justify-center">
                                @if($product['foto'])
                                    <img src="{{ asset('storage/' . $product['foto']) }}" alt="{{ $product['nama_produk'] }}"
                                        class="w-full h-full object-contain rounded-lg">
                                @else
                                    <x-lucide-image-off class="w-6 h-6 text-gray-400" />
                                @endif
                            </div>

                            <div class="flex-1">
                                <p class="font-medium text-[10px]">{{ $product['nama_produk'] }}</p>
                                <p class="font-medium text-[10px] text-gray-500">Rp
                                    {{ number_format($product['harga_jual'], 0, ',', '.') }}
                                </p>
                                <p class="font-medium text-[10px] text-gray-500">HPP: Rp
                                    {{ number_format($product['hpp'], 0, ',', '.') }}/box
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="decrement({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    -
                                </button>
                                <input type="number" min="0" wire:model="products.{{ $index }}.jumlah_box"
                                    class="w-8 text-center border border-gray-300 rounded px-2 py-1 text-[10px] no-spinner">
                                <button type="button" wire:click="increment({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    +
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            Tidak ada produk ditemukan.
                        </div>
                    @endforelse
                </div>
            </form>

            <!-- Action Buttons -->
            <div class="fixed bottom-8 w-full max-w-md bg-white border-t border-gray-100 px-6 py-4 backdrop-blur-sm">
                <!-- Total Harga -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-xl">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Total Order:</span>
                        <span class="text-2xl font-bold text-green-600">
                            Rp {{ number_format($totalHarga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="closeOrderModal"
                        class="flex-1 py-3 px-4 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition">
                        Batal
                    </button>
                    <button type="submit" form="order-form" wire:loading.attr="disabled"
                        class="flex-1 py-3 px-4 bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] text-white font-bold rounded-full shadow hover:shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98]">
                        <span wire:loading.remove>Simpan Order</span>
                        <span wire:loading>Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</layout>