<div x-data="{ open: @entangle('showModal') }" x-show="open" x-on:keydown.escape.window="open = false"
    class="fixed inset-0 z-50 flex items-end justify-center bg-black bg-opacity-50">

    <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="transform translate-y-full"
        x-transition:enter-end="transform translate-y-0" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="transform translate-y-0" x-transition:leave-end="transform translate-y-full"
        @click.outside="open = false"
        class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl overflow-y-auto max-h-[100vh]">

        <!-- Header -->
        <div class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
            <button @click="$wire.closeModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
                <x-lucide-arrow-left class="w-6 h-6 text-[#141217]" />
            </button>
            <h2 class="text-lg font-bold text-[#141217] text-center flex-1">
                Input Kunjungan & Order
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
        <form wire:submit.prevent="saveVisit" id="visit-form" class="px-6 space-y-6 mt-6">
            <!-- Pilih Toko -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Toko *</label>
                <select wire:model="outlet_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
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
                    class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>

            <!-- Ada Order? -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Ada Order?</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center">
                        <input type="radio" wire:model="is_order" value="1" class="mr-2">
                        Ya
                    </label>
                    <label class="flex items-center">
                        <input type="radio" wire:model="is_order" value="0" class="mr-2">
                        Tidak
                    </label>
                </div>
            </div>

            @if($is_order)
                <!-- Cari Produk -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cari Produk Ice Cream</label>
                    <input type="text" wire:model.live="searchProduct"
                        class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                        placeholder="Ketik nama produk...">
                </div>

                <!-- Daftar Produk -->
                <div class="space-y-3 max-h-96 overflow-y-auto p-4 bg-gray-50 rounded-lg">
                    @forelse($products as $index => $product)
                        <div class="flex items-center gap-4 p-3 bg-white rounded-lg shadow-sm">
                            <div class="flex-1">
                                <p class="font-medium">{{ $product['nama_produk'] }}</p>
                                <p class="text-sm text-gray-500">HPP: Rp {{ number_format($product['hpp'], 0, ',', '.') }}/box
                                </p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="button" wire:click="decrement({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    -
                                </button>
                                <input type="number" min="0" wire:model="products.{{ $index }}.jumlah_box"
                                    class="w-16 text-center border border-gray-300 rounded px-2 py-1">
                                <button type="button" wire:click="increment({{ $index }})"
                                    class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    +
                                </button>
                            </div>
                            @if($product['jumlah_box'] > 0)
                                <div class="text-green-600 font-bold">
                                    Rp {{ number_format($product['total_harga'], 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4 text-gray-500">
                            Tidak ada produk ditemukan.
                        </div>
                    @endforelse
                </div>

                <!-- Total Harga -->
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 p-4 rounded-lg">
                    <div class="flex justify-between items-center">
                        <span class="font-bold text-lg">Total Order:</span>
                        <span class="text-2xl font-bold text-green-600">
                            Rp <span x-text="$wire.entangle('totalHarga') || '0'" x-data="{ totalHarga: 0 }"
                                x-init="$wire.on('update-total', data => totalHarga = data.total)"
                                x-text="new Intl.NumberFormat('id-ID').format(totalHarga)"></span>
                        </span>
                    </div>
                </div>
            @endif

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

            <!-- Spacer -->
            <div class="h-8"></div>
        </form>

        <!-- Action Buttons (Sticky Bottom) -->
        <div class="sticky bottom-0 bg-white border-t border-gray-100 px-6 py-4 backdrop-blur-sm">
            <div class="flex gap-3">
                <button type="button" wire:click="closeModal"
                    class="flex-1 py-3 px-4 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition">
                    Batal
                </button>
                <button type="submit" form="visit-form" wire:loading.attr="disabled"
                    class="flex-1 py-3 px-4 bg-gradient-to-r from-brand-pink to-brand-yellow text-white font-bold rounded-full shadow hover:shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98]">
                    <span wire:loading.remove>Simpan Kunjungan</span>
                    <span wire:loading>Menyimpan...</span>
                </button>
            </div>
        </div>
    </div>
</div>