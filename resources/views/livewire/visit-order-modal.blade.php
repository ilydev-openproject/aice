<!-- MODAL INPUT ORDER -->
<div x-data="{ open: @entangle('showOrderModal') }" x-show="open" x-on:keydown.escape.window="open = false"
    class="z-151 fixed inset-0 flex items-end justify-center bg-black bg-opacity-50" x-cloak>

    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full"
        class="relative h-[100vh] w-full max-w-md overflow-y-auto bg-white shadow-2xl">

        <!-- Header -->
        <div class="py- sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6">
            <button wire:click="$parent.close" class="rounded-full p-2 transition hover:bg-gray-100">
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
        <!-- Parent Wrapper -->
        <div x-data="{
                    products: @entangle('products'),
                    get totalBox() {
                        return this.products.reduce((sum, p) => sum + Number(p.jumlah_box || 0), 0)
                    },
                    get totalHarga() {
                        return this.products.reduce((sum, p) => {
                            const qty = Number(p.jumlah_box || 0);
                            const price = Number(p.hpp?.toString().replace(/\./g, '') || 0); // Hapus titik ribuan
                            return sum + (qty * price);
                        }, 0);
                    }
                }">
            <!-- Form -->
            <form wire:submit.prevent="saveOrder" class="space-y-2 px-3" id="order-form">
                <!-- Cari Produk -->
                <div>
                    <input type="text" wire:model.live="searchProduct"
                        class="w-full rounded-full border border-gray-300 px-4 py-2 focus:border-transparent focus:ring-2 focus:ring-purple-500"
                        placeholder="Ketik nama produk...">
                </div>

                <!-- Daftar Produk -->
                <div class="h-[calc(100vh-140px)] w-full overflow-y-auto rounded-xl pb-16">
                    @forelse($products as $index => $product)
                        <div x-data
                            :class="products[{{ $index }}].jumlah_box > 0 ?
                                                'bg-green-500/60 border border-green-200' :
                                                '{{ $product['is_available'] == 0 ? 'bg-red-50/50 border border-red-200' : 'bg-white' }}'"
                            class="mb-2 overflow-hidden rounded-lg shadow-sm transition-all duration-300">

                            <!-- Container utama flex row -->
                            <div class="flex items-center gap-3 p-1">
                                <!-- Gambar Produk -->
                                <div
                                    class="relative flex h-16 w-16 flex-shrink-0 items-center justify-center overflow-hidden rounded-lg bg-gray-100">
                                    @if ($product['is_available'] == 0)
                                        <div
                                            class="absolute inset-0 z-10 flex items-center justify-center bg-white/60 backdrop-blur-sm">
                                            <span
                                                class="rounded-full border border-red-200 bg-red-100 px-2 py-0.5 text-[10px] font-bold text-red-700">
                                                Habis
                                            </span>
                                        </div>
                                    @endif

                                    @if ($product['foto'])
                                        <img src="{{ asset('storage/' . $product['foto']) }}"
                                            alt="{{ $product['nama_produk'] }}"
                                            class="{{ $product['is_available'] == 0 ? 'grayscale opacity-60' : 'hover:scale-105 transition-transform duration-200' }} h-full w-full object-contain"
                                            loading="lazy">
                                    @else
                                        <x-lucide-image-off class="h-6 w-6 text-gray-400" />
                                    @endif
                                </div>

                                <!-- Info Produk -->
                                <div class="flex-1">
                                    <p
                                        class="{{ $product['is_available'] == 0 ? 'line-through text-red-500' : '' }} text-[10px] font-medium">
                                        {{ $product['nama_produk'] }}
                                    </p>
                                    <p class="text-[10px] font-medium text-gray-500">
                                        Rp {{ number_format($product['harga_jual'], 0, ',', '.') }}
                                    </p>
                                    <p class="text-[10px] font-medium text-gray-500">
                                        HPP: Rp {{ number_format($product['hpp'], 0, ',', '.') }}/box
                                    </p>
                                </div>

                                <!-- Control jumlah -->
                                <div class="flex items-center gap-2">
                                    <button type="button"
                                        @click="if(products[{{ $index }}].jumlah_box > 0) products[{{ $index }}].jumlah_box--"
                                        class="{{ $product['is_available'] == 0 ? 'opacity-50 cursor-not-allowed' : '' }} flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300"
                                        {{ $product['is_available'] == 0 ? 'disabled' : '' }}>
                                        -
                                    </button>
                                    <input type="number" min="0" x-model="products[{{ $index }}].jumlah_box"
                                        class="no-spinner {{ $product['is_available'] == 0 ? 'bg-red-50 text-red-400 border-red-200' : '' }} w-8 rounded border border-gray-300 px-2 py-1 text-center text-[10px]"
                                        {{ $product['is_available'] == 0 ? 'readonly' : '' }}>
                                    <button type="button" @click="products[{{ $index }}].jumlah_box++"
                                        class="{{ $product['is_available'] == 0 ? 'opacity-50 cursor-not-allowed' : '' }} flex h-8 w-8 items-center justify-center rounded-full bg-gray-200 hover:bg-gray-300"
                                        {{ $product['is_available'] == 0 ? 'disabled' : '' }}>
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="py-4 text-center text-gray-500">
                            Tidak ada produk ditemukan.
                        </div>
                    @endforelse
                </div>

            </form>

            <!-- Action Buttons & Summary -->
            <div class="fixed bottom-3 w-full max-w-md border-t border-gray-100 bg-white px-6 backdrop-blur-sm">
                <!-- Total Harga & Jumlah Box -->
                <div class="rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 px-4 py-2">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] font-bold">Total Box:</span>
                            <span class="text-[12px] font-bold text-blue-600" x-text="totalBox + ' Box'"></span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[12px] font-bold">Total Harga:</span>
                            <span class="text-[12px] font-bold text-green-600"
                                x-text="'Rp ' + totalHarga.toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="button" wire:click="$parent.close"
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