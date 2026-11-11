
@extends('layouts.customer')

@section('title', 'SI Gamer - Booking')

@section('content')
<div class="flex flex-col gap-6 max-w-6xl mx-auto">

    <div class="flex flex-col gap-2">
        <a href="{{ route('customer.dashboard') }}"
           class="inline-flex items-center gap-2 text-xs font-semibold text-[#8B7EA1] hover:text-white transition-colors w-fit">
            <i class="ph ph-arrow-left text-base"></i>
            <span>Kembali ke Katalog</span>
        </a>

        <h1 class="text-3xl font-extrabold text-white tracking-wide mt-1">
            Booking Jadwal
        </h1>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">

        <div class="lg:col-span-2 bg-[#130B29] border border-[#2D2442] rounded-2xl p-6 md:p-8 flex flex-col gap-6 shadow-xl">

            <div class="flex flex-col gap-2">
                <label for="select-unit" class="text-xs font-semibold text-white tracking-wide">
                    Pilih Unit
                </label>

                <div class="relative">
                    <select id="select-unit"
                            onchange="updateSummary()"
                            class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-xl px-4 py-3.5 text-sm appearance-none focus:outline-none focus:border-purple-500 cursor-pointer pr-10">

                        @foreach($devicesList as $dev)
                            <option value="{{ $dev['nama'] }}"
                                    data-harga="{{ $dev['harga'] }}"
                                    {{ $selectedUnit === $dev['nama'] ? 'selected' : '' }}>
                                {{ $dev['nama'] }} (Rp {{ number_format($dev['harga'], 0, ',', '.') }}/jam)
                            </option>
                        @endforeach

                    </select>

                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#8B7EA1]">
                        <i class="ph ph-caret-down text-base"></i>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="flex flex-col gap-2">
                    <label for="input-tanggal" class="text-xs font-semibold text-white tracking-wide">
                        Tanggal
                    </label>

                    <input type="date"
                           id="input-tanggal"
                           min="{{ date('Y-m-d') }}"
                           value="{{ date('Y-m-d') }}"
                           class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-purple-500 cursor-pointer">
                </div>

                <div class="flex flex-col gap-2">
                    <label for="input-jam" class="text-xs font-semibold text-white tracking-wide">
                        Jam Mulai
                    </label>

                    <input type="time"
                           id="input-jam"
                           value="13:00"
                           class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-xl px-4 py-3.5 text-sm focus:outline-none focus:border-purple-500 cursor-pointer">
                </div>

            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                <div class="flex flex-col gap-2">
                    <label for="select-durasi" class="text-xs font-semibold text-white tracking-wide">
                        Durasi
                    </label>

                    <div class="relative">
                        <select id="select-durasi"
                                onchange="updateSummary()"
                                class="w-full bg-[#161124] border border-[#2D2442] text-white rounded-xl px-4 py-3.5 text-sm appearance-none focus:outline-none focus:border-purple-500 cursor-pointer pr-10">
                            <option value="1">1 Jam</option>
                            <option value="2">2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                            <option value="5">5 Jam</option>
                        </select>

                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-[#8B7EA1]">
                            <i class="ph ph-caret-down text-base"></i>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <label class="text-xs font-semibold text-white tracking-wide">
                        Status Membership
                    </label>

                    <div class="w-full bg-[#161124] border border-[#2D2442] rounded-xl px-4 py-3.5 text-sm flex items-center gap-2">
                        @if($isMember)
                            <i class="ph-fill ph-crown text-purple-400"></i>
                            <span class="text-emerald-400 font-semibold">Member Aktif</span>
                        @else
                            <i class="ph ph-user text-gray-500"></i>
                            <span class="text-gray-400">Non-Member</span>
                        @endif
                    </div>
                </div>

            </div>

            @if($isMember)
                <div class="flex items-center gap-3 bg-purple-950/30 border border-purple-500/30 text-purple-300 rounded-xl p-4 text-xs font-semibold mt-2">
                    <i class="ph-fill ph-tag text-lg shrink-0"></i>
                    <span>Diskon membership 5% otomatis diterapkan pada booking ini.</span>
                </div>
            @else
                <div class="flex items-center gap-3 bg-emerald-950/30 border border-emerald-500/30 text-emerald-400 rounded-xl p-4 text-xs font-semibold mt-2">
                    <i class="ph-fill ph-check-circle text-lg shrink-0"></i>
                    <span>Unit tersedia pada jadwal yang dipilih.</span>
                </div>
            @endif

        </div>

        <div class="bg-[#130B29] border border-[#2D2442] rounded-2xl p-6 md:p-8 flex flex-col justify-between shadow-xl">

            <div>
                <h3 class="text-xl font-bold text-white tracking-wide mb-6">
                    Ringkasan Pesanan
                </h3>

                <div class="flex flex-col gap-4 text-sm">

                    <div class="flex justify-between items-center gap-4">
                        <span class="text-[#8B7EA1]">Unit</span>
                        <span id="summary-unit" class="font-bold text-white text-right">
                            {{ $selectedUnit }}
                        </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[#8B7EA1]">Durasi</span>
                        <span id="summary-durasi" class="font-bold text-white">1 Jam</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[#8B7EA1]">Tarif</span>
                        <span id="summary-tarif" class="font-bold text-white">Rp 25.000 / jam</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[#8B7EA1]">Subtotal</span>
                        <span id="summary-subtotal" class="font-bold text-white">Rp 25.000</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-[#8B7EA1]">Diskon Member (5%)</span>
                        <span id="summary-diskon" class="font-bold text-emerald-400">
                            Rp 0
                        </span>
                    </div>

                </div>

                <div class="border-t border-[#2D2442] my-6"></div>

                <div class="flex justify-between items-baseline gap-3 mb-6">
                    <span class="text-base font-bold text-white">Total</span>
                    <span id="summary-total" class="text-2xl font-black text-purple-400">
                        Rp 25.000
                    </span>
                </div>
            </div>

            <div>
                <a href="{{ route('customer.riwayat') }}"
                   class="w-full py-3.5 bg-purple-600 hover:bg-purple-500 text-white font-bold rounded-xl transition-colors text-sm shadow-lg shadow-purple-600/25 flex items-center justify-center gap-2 cursor-pointer text-center">
                    <span>Bayar dengan QRIS</span>
                </a>

                <p class="text-xs text-[#8B7EA1] text-center mt-3 font-medium">
                    Diskon membership dihitung otomatis berdasarkan status akun.
                </p>
            </div>

        </div>

    </div>
</div>

<script>
    const isMember = @json($isMember);
    const discountRate = isMember ? 0.05 : 0;

    function formatRupiah(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }

    function updateSummary() {
        const unitSelect = document.getElementById('select-unit');
        const durasiSelect = document.getElementById('select-durasi');

        const selectedOption = unitSelect.options[unitSelect.selectedIndex];
        const unitName = selectedOption.value;
        const hargaPerJam = parseInt(selectedOption.dataset.harga) || 0;
        const durasi = parseInt(durasiSelect.value) || 1;

        const subtotal = hargaPerJam * durasi;
        const diskon = Math.round(subtotal * discountRate);
        const total = subtotal - diskon;

        document.getElementById('summary-unit').textContent = unitName;
        document.getElementById('summary-durasi').textContent = durasi + ' Jam';
        document.getElementById('summary-tarif').textContent =
            formatRupiah(hargaPerJam) + ' / jam';

        document.getElementById('summary-subtotal').textContent =
            formatRupiah(subtotal);

        document.getElementById('summary-diskon').textContent =
            formatRupiah(diskon);

        document.getElementById('summary-total').textContent =
            formatRupiah(total);
    }

    document.addEventListener('DOMContentLoaded', updateSummary);
</script>
@endsection
