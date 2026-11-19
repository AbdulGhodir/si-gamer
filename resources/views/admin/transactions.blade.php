@extends('layouts.admin')

@section('content')
@php
    $totalTransaksi = count($transactions);
    $lunasCount = collect($transactions)->where('status', 'Lunas')->count();
    $belumBayarCount = collect($transactions)->where('status', '!=', 'Lunas')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Kelola Transaksi</h1>
        <p class="text-xs text-gray-400 mt-0.5">Daftar transaksi pembayaran rental GameHub.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahTransaksi" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Transaksi
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 text-emerald-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Ringkasan Transaksi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Transaksi</p>
            <h2 id="cardTotalTransaksi" class="text-2xl font-bold text-white mt-1">{{ $totalTransaksi }}</h2>
            <p class="text-xs text-gray-400 mt-2">Tercatat pada daftar</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Transaksi Lunas</p>
            <h2 id="cardTransaksiLunas" class="text-2xl font-bold text-emerald-400 mt-1">{{ $lunasCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Pembayaran terkonfirmasi</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Belum Bayar</p>
            <h2 id="cardBelumBayar" class="text-2xl font-bold text-amber-400 mt-1">{{ $belumBayarCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Menunggu pembayaran</p>
        </div>
    </div>

    <!-- Tabel Transaksi -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Riwayat Transaksi Terbaru</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="transactionsTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Kode Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Perangkat</th>
                        <th class="p-4">Total</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="transactionsTableBody">
                    @foreach($transactions as $t)
                    <tr class="transaction-row hover:bg-white/[0.02] transition" data-status="{{ $t['status'] }}">
                        <td class="p-4 font-medium text-white">{{ $t['kode'] }}</td>
                        <td class="p-4 text-gray-300">{{ $t['pelanggan'] }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $t['perangkat'] }}</td>
                        <td class="p-4 text-purple-400 font-semibold">{{ $t['total'] }}</td>
                        <td class="p-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $t['status'] == 'Lunas' ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                {{ $t['status'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div id="modalTambahTransaksi" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 class="font-semibold text-white text-base">Tambah Transaksi Baru</h3>
            <button type="button" id="btnCloseTambahTransaksi" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        {{-- Jika sudah ada route POST, ganti action ke "{{ route('admin.transactions.store') }}" --}}
        <form id="formTambahTransaksi" action="#" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Kode Booking</label>
                <input type="text" name="kode" id="inputKode" required placeholder="Contoh: GH-181026-025"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Pelanggan</label>
                <input type="text" name="pelanggan" id="inputPelanggan" required placeholder="Contoh: Raka Pratama"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Perangkat</label>
                    <input type="text" name="perangkat" id="inputPerangkat" required placeholder="Contoh: PS5-01"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Total</label>
                    <input type="text" name="total" id="inputTotal" required placeholder="Contoh: Rp50.000"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Status Pembayaran</label>
                <select name="status" id="inputStatusTransaksi"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    <option value="Lunas">Lunas</option>
                    <option value="Belum bayar">Belum bayar</option>
                </select>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalTambahTransaksi" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-medium text-white bg-purple-600 hover:bg-purple-700 transition cursor-pointer">
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('modalTambahTransaksi');
    const btnOpen = document.getElementById('btnOpenTambahTransaksi');
    const btnClose = document.getElementById('btnCloseTambahTransaksi');
    const btnCancel = document.getElementById('btnBatalTambahTransaksi');
    const form = document.getElementById('formTambahTransaksi');

    function updateSummaryCards() {
        const rows = document.querySelectorAll('#transactionsTableBody .transaction-row');
        let total = rows.length;
        let lunas = 0;
        let belumBayar = 0;

        rows.forEach((row) => {
            if (row.dataset.status === 'Lunas') {
                lunas++;
            } else {
                belumBayar++;
            }
        });

        document.getElementById('cardTotalTransaksi').textContent = total;
        document.getElementById('cardTransaksiLunas').textContent = lunas;
        document.getElementById('cardBelumBayar').textContent = belumBayar;
    }

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputKode').focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form.reset();
    }

    btnOpen.addEventListener('click', openModal);
    btnClose.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const kode = document.getElementById('inputKode').value.trim();
            const pelanggan = document.getElementById('inputPelanggan').value.trim();
            const perangkat = document.getElementById('inputPerangkat').value.trim();
            const total = document.getElementById('inputTotal').value.trim();
            const status = document.getElementById('inputStatusTransaksi').value;

            const statusClass = status === 'Lunas'
                ? 'bg-emerald-900/50 text-emerald-300'
                : 'bg-amber-900/50 text-amber-300';

            const tr = document.createElement('tr');
            tr.className = 'transaction-row hover:bg-white/[0.02] transition';
            tr.dataset.status = status;
            tr.innerHTML = `
                <td class="p-4 font-medium text-white"></td>
                <td class="p-4 text-gray-300"></td>
                <td class="p-4 text-gray-400 text-xs"></td>
                <td class="p-4 text-purple-400 font-semibold"></td>
                <td class="p-4"><span class="px-2.5 py-1 rounded-full text-xs font-medium ${statusClass}"></span></td>
            `;

            tr.children[0].textContent = kode;
            tr.children[1].textContent = pelanggan;
            tr.children[2].textContent = perangkat;
            tr.children[3].textContent = total;
            tr.children[4].firstElementChild.textContent = status;

            document.getElementById('transactionsTableBody').prepend(tr);
            updateSummaryCards();
            closeModal();
        }
    });
});
</script>
@endsection