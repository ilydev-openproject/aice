<layout>
    <div class="bg-white rounded-2xl shadow-sm border p-6">
        <h2 class="text-xl font-bold mb-6">Laporan Kunjungan</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Toko</label>
                <select wire:model="outlet_id"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                    <option value="">-- Pilih Toko --</option>
                    @foreach($outlets as $outlet)
                        <option value="{{ $outlet->id }}">
                            {{ $outlet->nama_toko }} {{ $outlet->kode_toko ? '(' . $outlet->kode_toko . ')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Bulan</label>
                <input type="month" wire:model="tanggal"
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
            </div>
        </div>

        @if($report)
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-bold mb-4">Detail Kunjungan</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p><strong>No Urut:</strong> {{ $loop->index + 1 }}</p>
                        <p><strong>Nama Toko:</strong> {{ $report['outlet']->nama_toko }}</p>
                        <p><strong>Kode Toko:</strong> {{ $report['outlet']->kode_toko }}</p>
                        <p><strong>Kunjungan ke (dalam bulan ini):</strong> {{ $report['kunjungan_ke'] }}</p>
                        <p><strong>Total order:</strong> Rp {{ number_format($report['total_order'], 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p><strong>Total orderan bulan ini:</strong> {{ $report['total_order_bulan_ini'] }} box</p>
                        <p><strong>Jam buka - tutup toko:</strong>
                            {{ $report['outlet']->jam_buka ? \Carbon\Carbon::parse($report['outlet']->jam_buka)->format('H.i') : '-' }}
                            -
                            {{ $report['outlet']->jam_tutup ? \Carbon\Carbon::parse($report['outlet']->jam_tutup)->format('H.i') : '-' }}
                        </p>
                        <p><strong>No hp:</strong>
                            @if($report['outlet']->nomor_wa)
                                <a href="https://wa.me/{{ $report['outlet']->nomor_wa }}" target="_blank"
                                    class="text-green-600 underline">
                                    {{ $report['outlet']->nomor_wa }}
                                </a>
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-10 text-gray-500">
                Pilih toko untuk melihat laporan.
            </div>
        @endif
    </div>
</layout>