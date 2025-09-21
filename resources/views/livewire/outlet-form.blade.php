<layout>
    <div class="min-h-screen">
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
                    <div class="text-[#756783] flex items-center justify-center pl-5">
                        <x-lucide-search class="text-[#756783]" />
                    </div>
                    <input wire:model.live.debounce.300ms="search"
                        class="form-input flex w-full min-w-0 flex-1 resize-none overflow-hidden rounded-full text-[#141217] focus:outline-0 focus:ring-0 border-none bg-white focus:border-none h-full placeholder:text-[#756783] px-4 text-base font-normal leading-normal"
                        placeholder="Cari nama toko..." />
                </div>
            </label>
        </div>

        <!-- Daftar Toko -->
        <div class="p-4 space-y-4 pb-24">
            @forelse($outlets as $outlet)
                <div class="p-4 bg-white rounded-lg shadow-sm flex items-center justify-between group">
                    <div>
                        <p class="font-bold text-lg">{{ $outlet->nama_toko }}</p>
                        @if($outlet->kode_toko)
                            <p class="text-sm text-gray-600">Kode: {{ $outlet->kode_toko }}</p>
                        @endif
                        @if($outlet->jam_buka && $outlet->jam_tutup)
                            <p class="text-sm text-gray-600">{{ $outlet->jam_buka }} - {{ $outlet->jam_tutup }}</p>
                        @endif
                        @if($outlet->nomor_wa)
                            <a href="https://wa.me/{{ $outlet->nomor_wa }}" target="_blank"
                                class="text-green-600 text-sm hover:underline flex items-center gap-1">
                                <x-lucide-message-square class="w-4 h-4" /> {{ $outlet->nomor_wa }}
                            </a>
                        @endif
                        @if($outlet->alamat)
                            <p class="text-xs text-gray-500 mt-1">{{ $outlet->alamat }}</p>
                        @endif
                    </div>
                    <div class="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                        <button wire:click="editOutlet({{ $outlet->id }})"
                            class="p-2 bg-blue-100 text-blue-600 rounded-full hover:bg-blue-200">
                            <x-lucide-pencil class="w-4 h-4" />
                        </button>
                        <button wire:click="deleteOutlet({{ $outlet->id }})" wire:confirm="Yakin ingin menghapus toko ini?"
                            class="p-2 bg-red-100 text-red-600 rounded-full hover:bg-red-200">
                            <x-lucide-trash-2 class="w-4 h-4" />
                        </button>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-500">
                    Belum ada toko yang didaftarkan.
                </div>
            @endforelse
        </div>

        <!-- MODAL -->
        <div x-data="{ open: @entangle('showModal') }" x-show="open" x-on:keydown.escape.window="open = false"
            class="fixed inset-0 z-151 flex items-end justify-center bg-black bg-opacity-50">

            <div x-show="open" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="transform translate-y-full" x-transition:enter-end="transform translate-y-0"
                x-transition:leave="ease-in duration-200" x-transition:leave-start="transform translate-y-0"
                x-transition:leave-end="transform translate-y-full"
                class="relative w-full max-w-md bg-white rounded-t-3xl shadow-2xl overflow-y-auto max-h-[100vh]">

                <!-- Header -->
                <div
                    class="sticky top-0 bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between z-10">
                    <button @click="$wire.closeModal()" class="p-2 rounded-full hover:bg-gray-100 transition">
                        <x-lucide-arrow-left class="w-6 h-6 text-[#141217]" />
                    </button>
                    <h2 class="text-lg font-bold text-[#141217] text-center flex-1">
                        {{ $editingOutletId ? 'Edit Toko' : 'Tambah Toko Baru' }}
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
                <form wire:submit.prevent="saveOutlet" id="outlet-form" class="px-6 space-y-6 mt-6">
                    <!-- Nama Toko -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Toko *</label>
                        <input type="text" wire:model="nama_toko"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Contoh: Toko Maju Jaya">
                        @error('nama_toko') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kode Toko -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kode Toko (Opsional)</label>
                        <input type="text" wire:model="kode_toko"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="Contoh: MJ001">
                        @error('kode_toko') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jam Buka & Tutup -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Buka</label>
                            <input type="time" wire:model="jam_buka"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Jam Tutup</label>
                            <input type="time" wire:model="jam_tutup"
                                class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Nomor WA -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor WA (628xxx)</label>
                        <input type="string" minlength="10" wire:model="nomor_wa"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="628123456789">
                        @error('nomor_wa') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Format: 628123456789 (tanpa + atau 0)</p>
                    </div>

                    <!-- Link Google Maps -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">🗺️ Link Google Maps *</label>
                        <input type="url" wire:model="link"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            placeholder="https://goo.gl/maps/...">
                        @error('link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <p class="text-xs text-gray-500 mt-1">Contoh: https://goo.gl/maps/ABC123</p>
                    </div>

                    <!-- Alamat -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap (Opsional)</label>
                        <textarea wire:model="alamat"
                            class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                            rows="3" placeholder="Alamat lengkap toko"></textarea>
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
                        <button type="submit" form="outlet-form" wire:loading.attr="disabled"
                            class="flex-1 py-3 px-4 bg-gradient-to-r from-[var(--brand-pink)] to-[var(--brand-yellow)] text-white font-bold rounded-full shadow hover:shadow-lg transition transform hover:scale-[1.02] active:scale-[0.98]">
                            <span wire:loading.remove>{{ $editingOutletId ? 'Update Toko' : 'Simpan Toko' }}</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</layout>