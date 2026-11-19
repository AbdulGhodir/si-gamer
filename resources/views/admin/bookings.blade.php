@extends('layouts.admin')

@section('content')
@php
    $totalBooking = count($bookings);
    $dikonfirmasiCount = collect($bookings)->where('status', 'Dikonfirmasi')->count();
    $menungguCount = collect($bookings)->where('status', '!=', 'Dikonfirmasi')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Daftar Booking</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola jadwal pemesanan perangkat rental.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahBooking" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Booking
        </button>
    </div>
</header>

<div class="p-6 space-y-6">
    @if(session('success'))
        <div class="bg-emerald-950/60 border border-emerald-700/60 text-emerald-300 px-4 py-3 rounded-xl text-sm">
            {{ session('success') }}
        </div>
    @endif

    <!-- Card Ringkasan Booking -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Booking</p>
            <h2 id="cardTotalBooking" class="text-2xl font-bold text-white mt-1">{{ $totalBooking }}</h2>
            <p class="text-xs text-gray-400 mt-2">Jadwal terdaftar</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Dikonfirmasi</p>
            <h2 id="cardBookingDikonfirmasi" class="text-2xl font-bold text-emerald-400 mt-1">{{ $dikonfirmasiCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Slot perangkat siap</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Menunggu</p>
            <h2 id="cardBookingMenunggu" class="text-2xl font-bold text-amber-400 mt-1">{{ $menungguCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Perlu konfirmasi jadwal</p>
        </div>
    </div>

    <!-- Tabel Jadwal Booking -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Jadwal Booking Aktif</h3>
            <span class="text-xs text-gray-400">Klik badge status untuk mengubah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="bookingsTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">ID Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="bookingsTableBody">
                    @foreach($bookings as $b)
                    @php $isConfirmed = ($b['status'] ?? '') === 'Dikonfirmasi'; @endphp
                    <tr class="booking-row hover:bg-white/[0.02] transition" data-status="{{ $b['status'] }}">
                        <td class="p-4 font-medium text-white">{{ $b['id'] }}</td>
                        <td class="p-4 text-gray-300">{{ $b['pelanggan'] }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $b['jadwal'] }}</td>
                        <td class="p-4 text-purple-400">{{ $b['durasi'] }}</td>
                        <td class="p-4">
                            <button type="button" class="btn-toggle-booking-status px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $isConfirmed ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}" title="Klik untuk ubah status">
                                {{ $b['status'] }}
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Booking -->
<div id="modalTambahBooking" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 class="font-semibold text-white text-base">Tambah Booking Baru</h3>
            <button type="button" id="btnCloseTambahBooking" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formTambahBooking" action="#" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">ID Booking</label>
                    <input type="text" name="id" id="inputIdBooking" required placeholder="Contoh: BOK-03"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white uppercase focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Durasi</label>
                    <input type="text" name="durasi" id="inputDurasiBooking" required placeholder="Contoh: 2 Jam"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Pelanggan</label>
                <input type="text" name="pelanggan" id="inputPelangganBooking" required placeholder="Contoh: Sinta Dewi"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Jadwal Main</label>
                    <input type="text" name="jadwal" id="inputJadwalBooking" required placeholder="Contoh: 26/10/2026 21:00"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status</label>
                    <select name="status" id="inputStatusBooking"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Dikonfirmasi">Dikonfirmasi</option>
                        <option value="Menunggu">Menunggu</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalTambahBooking" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
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
    const modal = document.getElementById('modalTambahBooking');
    const btnOpen = document.getElementById('btnOpenTambahBooking');
    const btnClose = document.getElementById('btnCloseTambahBooking');
    const btnCancel = document.getElementById('btnBatalTambahBooking');
    const form = document.getElementById('formTambahBooking');
    const tbody = document.getElementById('bookingsTableBody');

    function getStatusBadgeClass(status) {
        return status === 'Dikonfirmasi'
            ? 'btn-toggle-booking-status px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition bg-emerald-900/50 text-emerald-300'
            : 'btn-toggle-booking-status px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition bg-amber-900/50 text-amber-300';
    }

    function updateSummaryCards() {
        const rows = document.querySelectorAll('#bookingsTableBody .booking-row');
        let total = rows.length;
        let dikonfirmasi = 0;
        let menunggu = 0;

        rows.forEach((row) => {
            if (row.dataset.status === 'Dikonfirmasi') {
                dikonfirmasi++;
            } else {
                menunggu++;
            }
        });

        document.getElementById('cardTotalBooking').textContent = total;
        document.getElementById('cardBookingDikonfirmasi').textContent = dikonfirmasi;
        document.getElementById('cardBookingMenunggu').textContent = menunggu;
    }

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputIdBooking').focus();
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

    tbody.addEventListener('click', function (e) {
        const btn = e.target.closest('.btn-toggle-booking-status');
        if (!btn) return;

        const row = btn.closest('.booking-row');
        const currentStatus = row.dataset.status;
        const nextStatus = currentStatus === 'Dikonfirmasi' ? 'Menunggu' : 'Dikonfirmasi';

        row.dataset.status = nextStatus;
        btn.className = getStatusBadgeClass(nextStatus);
        btn.textContent = nextStatus;
        updateSummaryCards();
    });

    form.addEventListener('submit', function (e) {
        if (form.getAttribute('action') === '#') {
            e.preventDefault();
            const id = document.getElementById('inputIdBooking').value.trim().toUpperCase();
            const pelanggan = document.getElementById('inputPelangganBooking').value.trim();
            const jadwal = document.getElementById('inputJadwalBooking').value.trim();
            const durasi = document.getElementById('inputDurasiBooking').value.trim();
            const status = document.getElementById('inputStatusBooking').value;

            const tr = document.createElement('tr');
            tr.className = 'booking-row hover:bg-white/[0.02] transition';
            tr.dataset.status = status;
            tr.innerHTML = `
                <td class="p-4 font-medium text-white"></td>
                <td class="p-4 text-gray-300"></td>
                <td class="p-4 text-gray-400 text-xs"></td>
                <td class="p-4 text-purple-400"></td>
                <td class="p-4"><button type="button" class="${getStatusBadgeClass(status)}" title="Klik untuk ubah status"></button></td>
            `;

            tr.children[0].textContent = id;
            tr.children[1].textContent = pelanggan;
            tr.children[2].textContent = jadwal;
            tr.children[3].textContent = durasi;
            tr.children[4].firstElementChild.textContent = status;

            tbody.prepend(tr);
            updateSummaryCards();
            closeModal();
        }
    });
});
</script>
@endsection