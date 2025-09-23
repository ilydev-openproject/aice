<layout>
    <div class="min-h-screen">
        <!-- Header -->
        <div class="flex items-center justify-between p-4 pb-2">
            <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="">
            <div class="flex items-center justify-end">
                <button wire:click="$dispatch('openVisitModal')"
                    class="flex max-w-[480px] cursor-pointer items-center justify-center overflow-hidden rounded-full bg-[#BCA1D4]/30 px-2 py-1">
                    <span class="me-2 text-[12px] text-slate-700">Tambah</span>
                    <x-lucide-plus
                        class="h-6 w-6 rounded-full bg-[#BCA1D4]/30 text-base font-bold leading-normal tracking-[0.015em] text-[#141217] shadow-sm backdrop-blur-sm"
                        style="color: var(--brand-purple);" />
                </button>
            </div>
        </div>

        <!-- Search -->
        <div class="px-4">
            <label class="flex h-10 w-full min-w-40 flex-col">
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

        <!-- ✅ Child Components — taruh di LUAR conditional rendering -->
        @livewire('visit-modal')
        @livewire('visit-order-modal')

        <!-- Daftar Kunjungan -->
        <div class="space-y-2 px-4">
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
                    <div wire:click="$dispatch('openVisitModal', {{ $visit->id }})"
                        class="relative cursor-pointer rounded-xl border border-gray-100 bg-white p-2 shadow-sm transition hover:shadow-md">
                        <!-- Jam visit -->
                        <div class="absolute bottom-0 left-2 rounded-t-md bg-gray-300 p-1 text-end text-[10px]">
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
                                            <span>{{ \Carbon\Carbon::parse($visit->tanggal_kunjungan)->translatedFormat('D m Y') }}</span>
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
                                <button wire:click="$dispatch('openOrderModal', {{ $visit->id }})"
                                    class="rounded-xl bg-purple-500 p-3 text-white shadow-sm transition hover:bg-purple-600 hover:shadow"
                                    title="Input Order">
                                    <x-lucide-shopping-cart class="h-5 w-5" />
                                </button>
                                <a href="https://wa.me/?text={{ urlencode(
                                    '==============================\n' .
                                    '           LAPORAN KUNJUNGAN\n' .
                                    '==============================\n' .
                                    'No Urut                 : ' . $visit->visit_order_today . "\n" .
                                    'Nama Toko            : ' . $visit->outlet->nama_toko . "\n" .
                                    'Kode Toko             : ' . ($visit->outlet->kode_toko ?? '-') . "\n" .
                                    '───────────────────\n' .
                                    'Kunjungan ke         : ' . $visit->total_visits_this_month . ' (bln ini)' . "\n" .
                                    'Total order             : ' . $visit->visitItems->sum('jumlah_box') . " box\n" .
                                    'Total bln ini            : ' . $visit->total_boxes_this_month . " box\n" .
                                    '───────────────────\n' .
                                    'Jam Operasional    : ' . ($visit->outlet->jam_buka ? \Carbon\Carbon::parse($visit->outlet->jam_buka)->format('H.i') : '-') . '-' . ($visit->outlet->jam_tutup ? \Carbon\Carbon::parse($visit->outlet->jam_tutup)->format('H.i') : '-') . "\n" .
                                    'No HP                     : ' . ($visit->outlet->nomor_wa ?? '-') . "\n" .
                                    '───────────────────\n' .
                                    '=============================='
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $visits->links() }}
                </div>
            @endif
        </div>
    </div>
</layout>