<div>
    @if ($show)
        <div class="fixed inset-0 z-50 flex items-end justify-center bg-black bg-opacity-50">
            <div class="relative h-[100vh] w-full max-w-md overflow-y-auto bg-white shadow-2xl">

                <!-- Header -->
                <div
                    class="sticky top-0 z-10 flex items-center justify-between border-b border-gray-100 bg-white px-6 py-4">
                    <button wire:click="close" class="rounded-full p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <h2 class="text-lg font-bold">Input Order</h2>
                </div>

                <!-- Flash -->
                @if (session()->has('success'))
                    <div class="mx-6 mt-4 rounded-lg border border-green-400 bg-green-100 px-4 py-3 text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Search -->
                <div class="px-6 py-4">
                    <input type="text" wire:model.live="searchProduct" placeholder="Cari produk..."
                        class="w-full rounded-full border border-gray-300 px-4 py-2 focus:ring-2 focus:ring-purple-500">
                </div>

                <!-- Products -->
                <div class="px-6 pb-24">
                    @forelse($products as $index => $product)
                        <div class="mb-4 flex items-center justify-between rounded-lg border p-3">
                            <div>
                                <p class="font-medium">{{ $product['nama_produk'] }}</p>
                                <p class="text-sm text-gray-500">HPP: Rp {{ number_format($product['hpp'], 0, ',', '.') }}</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="decrement({{ $index }})" @disabled(!$product['is_available'] || $product['jumlah_box'] <= 0) class="rounded-full bg-gray-200 px-3 py-1">
                                    -
                                </button>
                                <span class="w-8 text-center font-medium">{{ $product['jumlah_box'] }}</span>
                                <button wire:click="increment({{ $index }})" @disabled(!$product['is_available'])
                                    class="rounded-full bg-gray-200 px-3 py-1">
                                    +
                                </button>
                            </div>
                        </div>
                    @empty
                        <p class="px-6 py-4 text-center text-gray-500">Tidak ada produk.</p>
                    @endforelse
                </div>

                <!-- Footer -->
                <div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4">
                    <div class="flex gap-2">
                        <button wire:click="close" class="flex-1 rounded-full border py-2 font-medium">
                            Batal
                        </button>
                        <button wire:click="saveOrder" wire:loading.attr="disabled"
                            class="flex-1 rounded-full bg-purple-500 py-2 font-bold text-white">
                            <span wire:loading.remove>Simpan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Method increment & decrement -->
    @php
        if (!isset($increment)) {
            $this->increment = function ($index) {
                if (isset($this->products[$index]) && $this->products[$index]['is_available']) {
                    $this->products[$index]['jumlah_box']++;
                }
            };

            $this->decrement = function ($index) {
                if (isset($this->products[$index]) && $this->products[$index]['jumlah_box'] > 0) {
                    $this->products[$index]['jumlah_box']--;
                }
            };
        }
    @endphp
</div>