<layouts>
    <div class="min-h-screen">
        <div class="pb-24">
            <div class="relative flex h-auto min-h-screen max-w-sm flex-col justify-between group/design-root overflow-x-hidden"
                style='font-family: "Plus Jakarta Sans", "Noto Sans", sans-serif;'>
                <div class="relative">
                    <!-- Header -->
                    <div class="flex items-center p-4 pb-2 justify-between relative">
                        <img src="{{ asset('logo.png') }}" class="h-12 w-auto" alt="">

                        <div class="flex items-center justify-end ">
                            <button wire:click="openModal"
                                class="flex max-w-[480px] bg-[#BCA1D4]/30 py-1 px-2 rounded-full cursor-pointer items-center justify-center overflow-hidden ">
                                <span class="text-[12px] text-slate-700 me-2">Tambah</span>
                                <x-lucide-plus
                                    class="w-6 h-6 rounded-full bg-[#BCA1D4]/30 backdrop-blur-sm text-[#141217] text-base font-bold leading-normal tracking-[0.015em] shadow-sm"
                                    style="color: var(--brand-purple);" />
                            </button>
                        </div>
                    </div>
                    <!-- Search -->
                    <div class="px-4">
                        <label class="flex flex-col min-w-40 h-10 w-full">
                            <div class="flex w-full flex-1 items-stretch rounded-full h-full bg-white shadow-sm">
                                <input wire:model.live.debounce.300ms="search"
                                    class="form-input flex h-full w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full border-none bg-white px-4 text-base font-normal leading-normal text-[#141217] placeholder:text-[#756783] focus:border-none focus:outline-0 focus:ring-0"
                                    placeholder="Cari produk ice cream..." />
                            </div>
                        </label>
                    </div>

                    <!-- Tabs Filter Produk -->
                    <div class=" overflow-x-scroll me-4 [&::-webkit-scrollbar]:hidden ">
                        <div class="flex border-b border-[#e0dde4] ps-4 gap-4 whitespace-nowrap me-8">
                            @php
                                $filters = [
                                    '' => 'Semua',
                                    'termurah' => 'Termurah',
                                    'termahal' => 'Termahal',
                                    'untung_gede' => 'Untung Gede',
                                    'isi_per_box' => 'QTY',
                                ];
                            @endphp

                            @foreach($filters as $key => $label)
                                <button wire:click="$set('filter', '{{ $key }}')" wire:loading.class="opacity-50"
                                    class="relative flex flex-col items-center justify-center pb-[13px] pt-4 transition
                                                                                                                                                                                                                                                                                                                                                                                                                                        {{ $filter === $key ? 'text-[#141217] font-bold' : 'text-[#756783] font-medium' }}">
                                    <p class="text-base text-[12px] leading-normal tracking-[0.015em]">{{ $label }}</p>
                                    @if($filter === $key)
                                        <div class="absolute bottom-0 h-1 w-full gradient-bg rounded-t-full"></div>
                                    @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Grid Produk -->
                    <div class="grid grid-cols-[repeat(auto-fit,minmax(158px,1fr))] gap-4 p-2">
                        @forelse($products as $product)
                            <div class="flex flex-col gap-3 group">
                                <!-- Card Container -->
                                <div
                                    class="rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition-all duration-300 {{ !$product->is_available ? 'bg-red-50/50 border-red-200' : 'bg-white border-gray-100' }}">
                                    <!-- Gambar -->
                                    <div class="w-full overflow-hidden bg-gray-100 relative">
                                        @if(!$product->is_available)
                                            <div
                                                class="absolute inset-0 bg-white/50 backdrop-blur-sm flex items-center justify-center z-10">
                                                <span
                                                    class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full border border-red-200">
                                                    Tidak Tersedia
                                                </span>
                                            </div>
                                        @endif
                                        @if($product->foto)
                                            <img src="{{ asset('storage/' . $product->foto) }}"
                                                class="w-auto h-[120px] mx-auto p-4 object-contain hover:scale-105 transition-transform duration-300 {{ !$product->is_available ? 'grayscale' : '' }}"
                                                alt="{{ $product->nama_produk }}">
                                        @else
                                            <div class="w-full h-full bg-gray-50 flex items-center justify-center">
                                                <span class="text-gray-300 text-sm">No Image</span>
                                            </div>
                                        @endif

                                        <!-- Tombol Edit & Hapus -->
                                        <div class="absolute top-2 right-2 flex gap-1">
                                            <button wire:click="editProduct({{ $product->id }})"
                                                class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-sm text-blue-600 hover:bg-blue-50 transition">
                                                <x-lucide-pencil class="w-4 h-4" />
                                            </button>
                                            <button wire:click="deleteProduct({{ $product->id }})"
                                                wire:confirm="Yakin ingin menghapus produk ini?"
                                                class="p-2 bg-white/90 backdrop-blur-sm rounded-full shadow-sm text-red-600 hover:bg-red-50 transition">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Info Produk -->
                                    <div class="px-4 pb-4">
                                        <p class="text-[#141217] text-[12px] font-bold leading-tight line-clamp-2 truncate">
                                            {{ $product->nama_produk }}
                                        </p>
                                        <div class="flex items-center gap-1 text-[12px] font-medium mt-1">
                                            Jual: Rp {{ number_format($product->het, 0, ',', '.') }}
                                        </div>
                                        <div class="flex items-center gap-1 text-[12px] leading-normal mt-1">
                                            HPP: Rp {{ number_format($product->hpp, 0, ',', '.') }}
                                        </div>
                                        <div class="flex items-center gap-1 text-[12px] leading-normal mt-1">
                                            Qty: {{ $product->isi_per_box }}
                                        </div>
                                        <div class="flex items-center gap-1 text-green-600 text-[12px] font-bold mt-1">
                                            Untung: Rp {{ number_format($product->margin, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-span-full text-center py-10 text-gray-500">
                                Tidak ada produk ditemukan.
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            <!-- MODAL TAMBAH PRODUK -->
            <div x-data="{ open: @entangle('showModal') }" x-show="open" x-on:keydown.escape.window="open = false"
                x-on:open-modal.window="open = true"
                class="fixed inset-0 z-150 flex items-end justify-center bg-black bg-opacity-50 overflow-hidden"
                x-cloak>

                <!-- Overlay Background -->
                <div x-show="open" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    @click="open = false; $wire.closeModal()" class="absolute inset-0"></div>

                <!-- Modal Content -->
                <div x-show="open" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="transform translate-y-full"
                    x-transition:enter-end="transform translate-y-0" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="transform translate-y-0"
                    x-transition:leave-end="transform translate-y-full"
                    class="relative w-full max-w-md bg-white shadow-2xl overflow-y-auto max-h-[100vh] pb-8"
                    @click.outside="open = false">

                    <!-- Header -->
                    <div
                        class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                        <button @click="open = false; $wire.closeModal()"
                            class="p-2 rounded-full hover:bg-gray-100 transition">
                            <x-lucide-arrow-left class="w-6 h-6 text-[#141217]" />
                        </button>
                        <h2 class="text-lg font-bold text-[#141217] text-center flex-1">
                            {{ $editingProductId ? 'Edit Produk' : 'Tambah Produk Baru' }}
                        </h2>
                        <div class="w-10"></div> <!-- Spacer untuk center title -->
                    </div>

                    <!-- Flash Message -->
                    @if (session()->has('success'))
                        <div class="mx-6 mt-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Form -->
                    <form wire:submit.prevent="saveProduct" class="px-6 space-y-6 mt-6 mb-6" id="product-form">
                        <!-- Nama Produk -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nama Produk</label>
                            <input type="text" wire:model="nama_produk"
                                class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Contoh: Magnum Almond">
                            @error('nama_produk') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Harga Jual per Item -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Harga Jual per Item</label>
                            <input type="number" step="100" wire:model.live="harga_jual_per_item"
                                class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Contoh: 15000">
                            @error('harga_jual_per_item') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- HPP -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Modal per Box</label>
                            <input type="number" step="100" wire:model.live="hpp"
                                class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Contoh: 240000">
                            @error('hpp') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Isi per Box -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Isi per Box</label>
                            <input type="number" wire:model.live="isi_per_box"
                                class="w-full border border-gray-300 rounded-full px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent transition"
                                placeholder="Contoh: 24">
                            @error('isi_per_box') <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Keuntungan Toko (Selalu Tampil) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Keuntungan Toko per Box</label>
                            @php
                                $calculatedMargin = 0;
                                if ($this->harga_jual_per_item && $this->isi_per_box && $this->hpp) {
                                    $calculatedMargin = ($this->harga_jual_per_item * $this->isi_per_box) - $this->hpp;
                                }
                            @endphp
                            <div
                                class="w-full bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-full px-4 py-3 font-bold text-green-700 flex items-center justify-between">
                                <span>Rp {{ number_format($calculatedMargin, 0, ',', '.') }} /box</span>
                                <x-lucide-coins class="w-5 h-5 text-green-600" />
                            </div>
                            @if($this->harga_jual_per_item && $this->isi_per_box && $this->hpp)
                                <p class="text-xs text-gray-500 mt-1">
                                    ({{ $this->isi_per_box }} × Rp
                                    {{ number_format($this->harga_jual_per_item, 0, ',', '.') }})
                                    - Rp {{ number_format($this->hpp, 0, ',', '.') }}
                                </p>
                            @else
                                <p class="text-xs text-gray-400 mt-1">Isi semua field untuk melihat perhitungan otomatis</p>
                            @endif
                        </div>

                        <!-- Upload Foto (Diperbaiki) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Foto Produk</label>
                            <div class="border-2 border-dashed border-gray-300 rounded-2xl p-4 text-center hover:border-purple-400 transition cursor-pointer"
                                x-data="{ fileName: @entangle('foto').defer }" x-on:click="$refs.fileInput.click()"
                                x-bind:class="fileName ? 'border-purple-500 bg-purple-50' : ''">
                                <input type="file" wire:model.blur="foto" x-ref="fileInput" class="hidden" />

                                <!-- Preview Gambar -->
                                <div class="flex flex-col items-center space-y-2">
                                    @if($foto)
                                        <!-- Tampilkan foto baru (temporary) -->
                                        <img src="{{ $foto->temporaryUrl() }}"
                                            class="w-24 h-24 object-cover rounded-xl shadow-md border">
                                        <p class="text-sm text-purple-700 font-medium">{{ $foto->getClientOriginalName() }}
                                        </p>
                                    @elseif($fotoPath)
                                        <!-- Tampilkan foto lama -->
                                        <img src="{{ asset('storage/' . $fotoPath) }}"
                                            class="w-24 h-24 object-cover rounded-xl shadow-md border">
                                        <p class="text-sm text-gray-500">Foto saat ini</p>
                                    @else
                                        <!-- Tampilkan placeholder -->
                                        <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                                            <x-lucide-upload class="w-6 h-6 text-purple-600" />
                                        </div>
                                        <p class="text-sm text-gray-500">Klik atau drag foto produk ke sini</p>
                                        <p class="text-xs text-gray-400">PNG, JPG, GIF up to 1MB</p>
                                    @endif
                                </div>
                            </div>
                            <button type="button" wire:click="removePhoto"
                                class="text-xs text-red-500 hover:text-red-700 flex items-center gap-1 py-2 px-4 mt-2 bg-violet-400/20 rounded-full">
                                <x-lucide-trash-2 class="w-4 h-4 mr-1" />
                                Hapus foto
                            </button>

                            <!-- Error Message -->
                            @error('foto')
                                <span class="text-red-500 text-xs mt-2 flex items-center">
                                    <x-lucide-alert-triangle class="w-4 h-4 mr-1" />
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                        <!-- Status Ketersediaan -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-800 mb-3">Status Ketersediaan</label>
                            <div class="flex gap-4 p-2 bg-gray-50 rounded-xl border border-gray-200">
                                <!-- Opsi Tersedia -->
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" wire:model="is_available" value="1" class="sr-only peer">
                                    <div class="flex items-center justify-center gap-2 py-3 px-4 rounded-lg border-2 transition-all duration-200 
                        peer-checked:border-green-500 peer-checked:bg-green-50 peer-checked:shadow-md 
                        hover:bg-green-50 hover:border-green-400">
                                        <x-lucide-check
                                            class="w-5 h-5 text-green-600 transition-transform group-hover:scale-110" />
                                        <span class="font-medium text-green-700">Tersedia</span>
                                    </div>
                                </label>

                                <!-- Opsi Tidak Tersedia -->
                                <label class="relative flex-1 cursor-pointer group">
                                    <input type="radio" wire:model="is_available" value="0" class="sr-only peer">
                                    <div class="flex items-center justify-center gap-2 py-3 px-4 rounded-lg border-2 transition-all duration-200 
                        peer-checked:border-red-500 peer-checked:bg-red-50 peer-checked:shadow-md 
                        hover:bg-red-50 hover:border-red-400">
                                        <x-lucide-x
                                            class="w-5 h-5 text-red-600 transition-transform group-hover:scale-110" />
                                        <span class="font-medium text-red-700">Tidak Tersedia</span>
                                    </div>
                                </label>
                            </div>
                            @error('is_available')
                                <div class="mt-2 flex items-center gap-1 text-red-500 text-sm animate-pulse">
                                    <x-lucide-alert-triangle class="w-4 h-4" />
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>
                        <!-- Spacer untuk tombol tidak tertutup oleh keyboard di mobile -->
                        <div class="h-8"></div>
                    </form>

                    <!-- Action Buttons (Sticky Bottom) -->
                    <div
                        class="fixed w-full max-w-md bottom-0 bg-white border-t border-gray-100 px-6 py-2 backdrop-blur-sm">
                        <div class="flex gap-3">
                            <button type="button" wire:click="closeModal"
                                class="flex-1 py-3 px-4 border border-gray-300 rounded-full text-gray-700 hover:bg-gray-50 font-medium transition">
                                Batal
                            </button>
                            <button type="submit" form="product-form"
                                class="flex-1 py-3 px-4 bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] text-white font-bold rounded-full shadow hover:shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98]">
                                {{ $editingProductId ? 'Update Produk' : 'Simpan Produk' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</layouts>