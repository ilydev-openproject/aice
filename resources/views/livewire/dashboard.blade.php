<layout>
    <div class="flex flex-col min-h-screen">
        <!-- Main Content -->
        <div class="flex-grow p-4 space-y-6">
            <!-- Header -->
            <div class="flex-shrink-0 bg-background-light">
                <div class="flex items-center justify-between p-4">
                    <div class="flex items-center gap-2">
                        <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('profile.png') }}" alt="Profile">
                        <div class="text-subtle-light">
                            <p class="text-sm">Selamat datang,</p>
                            <p class="font-bold">Ilyas</p>
                        </div>
                    </div>
                    <button class="p-2 rounded-full hover:bg-purple-400/10">
                        <img class="w-auto h-10 object-contain" src="{{ asset('logo.png') }}" alt="">
                    </button>
                </div>
            </div>
            <!-- Progress Kunjungan -->
            <div class="rounded-xl bg-gradient-to-tr bg-purple-800 to-purple-500 py-4 px-8 shadow-lg">
                <div class="flex justify-between items-start">
                    <div class="text-content-light">
                        <p class="text-lg font-bold text-slate-50">Total Kunjungan Hari Ini</p>
                        <p class="text-4xl font-bold text-slate-50 mt-2">
                            {{ number_format($stats['kunjungan_bulan_ini'], 0, ',', '.') }}
                        </p>
                    </div>
                    <x-lucide-trending-up class="text-purple bg-purple-400 rounded-3xl w-16 p-4" />
                </div>
                <div class="mt-4">
                    <div class="h-2.5 w-full rounded-full bg-[#FFBE48]">
                        <div class="h-2.5 rounded-full bg-[#F9F871]" style="width: {{ min($stats['progress'], 100) }}%">
                        </div>
                    </div>
                    <p class="text-right text-sm text-slate-50 mt-1">Target:
                        {{ number_format($stats['target_kunjungan'], 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Statistik Ringkas -->
            <div class="grid grid-cols-2 gap-4">
                <!-- Total Box -->
                <div class="relative flex flex-col items-start rounded-xl overflow-hidden text-slate-50 p-4 shadow-md">
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-pink-500 to-pink-300"></div>
                    <!-- Content -->
                    <div class="relative z-10 flex items-center justify-between w-full">
                        <p class="font-bold text-base">Total Order</p>
                        <x-lucide-shopping-bag class="w-6 h-6" />
                    </div>
                    <p class="relative z-10 text-2xl font-bold mt-2">
                        {{ number_format($stats['total_box_terjual'], 0, ',', '.') }}
                    </p>
                    <p class="relative z-10 text-xs text-subtle-light mt-1">Hari ini</p>
                </div>

                <!-- Outlet Baru -->
                <div class="relative flex flex-col items-start rounded-xl overflow-hidden text-slate-50 p-4 shadow-md">
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-tr from-orange-400 to-orange-300"></div>
                    <!-- Content -->
                    <div class="relative z-10 flex items-center justify-between w-full">
                        <p class="font-bold text-base">Outlet Baru</p>
                        <x-lucide-store class="w-6 h-6" />
                    </div>
                    <p class="relative z-10 text-2xl font-bold mt-2">{{ $stats['outlet_baru'] }}</p>
                    <p class="relative z-10 text-xs text-subtle-light mt-1">Bulan ini</p>
                </div>
            </div>


            <!-- Aktivitas Terakhir -->
            <div class="rounded-xl bg-background-light p-4 shadow-md text-slate-600">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold">Aktivitas Terakhir</h3>
                    <a href="{{ route('visit') }}"
                        class="text-sm font-normal bg-purple-400/50 py-1 px-2 rounded-2xl ">Lihat
                        Semua</a>
                </div>
                <div class="space-y-4">
                    @forelse($stats['aktivitas_terakhir'] as $aktivitas)
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-purple-400/10 rounded-full flex items-center justify-center">
                                @if($aktivitas['type'] === 'kunjungan')
                                    <x-lucide-store class=" w-6 h-6" />
                                @else
                                    <x-lucide-warehouse class=" w-6 h-6" />
                                @endif
                            </div>
                            <div class="flex-1">
                                <p class="font-normal text-[12px]">{{ $aktivitas['title'] }}</p>
                                <p class="font-normal text-[8px] text-slate-500">{{ $aktivitas['time'] }}</p>
                            </div>
                            @if($aktivitas['amount'])
                                <p class="ml-auto font-semibold text-green-600">+
                                    {{ number_format($aktivitas['amount'], 0, ',', '.') }} box
                                </p>
                            @endif
                        </div>
                    @empty
                        <div class="py-4 text-center text-subtle-light">
                            Belum ada aktivitas.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</layout>