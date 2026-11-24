@extends('layouts.admin')

@section('content')
@php
    $bookingList = collect($bookings ?? []);
    $customerList = collect($customers ?? []);
    $deviceList = collect($devices ?? []);
    $totalBooking = $bookingList->count();
    $dikonfirmasiCount = $bookingList->where('status', 'Dikonfirmasi')->count();
    $menungguCount = $bookingList->where('status', '!=', 'Dikonfirmasi')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Daftar Booking</h1>
        <p class="text-xs text-gray-400 mt-0.5">Kelola jadwal pemesanan perangkat, harga, dan status pembayaran.</p>
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

    @if($errors->any())
        <div class="bg-rose-950/60 border border-rose-700/60 text-rose-300 px-4 py-3 rounded-xl text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Card Ringkasan Booking -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Booking</p>
            <h2 class="text-2xl font-bold text-white mt-1">{{ $totalBooking }}</h2>
            <p class="text-xs text-gray-400 mt-2">Jadwal terdaftar</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Dikonfirmasi</p>
            <h2 class="text-2xl font-bold text-emerald-400 mt-1">{{ $dikonfirmasiCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Slot perangkat siap</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Menunggu</p>
            <h2 class="text-2xl font-bold text-amber-400 mt-1">{{ $menungguCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Perlu konfirmasi jadwal</p>
        </div>
    </div>

    <!-- Tabel Jadwal Booking -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Jadwal Booking Aktif</h3>
            <span class="text-xs text-gray-400">Klik badge untuk mengubah status booking atau bayar</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="bookingsTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">ID Booking</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Jadwal Main</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Total / Harga</th>
                        <th class="p-4">Pembayaran</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="bookingsTableBody">
                    @forelse($bookingList as $b)
                    @php
                        $modelId = $b['model_id'] ?? null;
                        $bookingStatus = $b['status'] ?? 'Menunggu';
                        $isConfirmed = $bookingStatus === 'Dikonfirmasi';
                        $paymentStatus = $b['status_bayar'] ?? 'Belum bayar';
                        $isPaid = $paymentStatus === 'Lunas';
                        $totalHarga = $b['total'] ?? 'Rp0';
                    @endphp
                    <tr class="booking-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $b['id'] ?? '-' }}</td>
                        <td class="p-4 text-gray-300">{{ $b['pelanggan'] ?? '-' }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $b['jadwal'] ?? '-' }}</td>
                        <td class="p-4 text-purple-400">{{ $b['durasi'] ?? '-' }}</td>
                        <td class="p-4 font-semibold text-white">{{ $totalHarga }}</td>
                        <td class="p-4">
                            @if($modelId)
                                <form action="{{ route('admin.bookings.toggle_payment', $modelId) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $isPaid ? 'bg-emerald-900/50 text-emerald-300' : 'bg-rose-900/50 text-rose-300' }}" title="Klik untuk ubah status bayar">
                                        {{ $paymentStatus }}
                                    </button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isPaid ? 'bg-emerald-900/50 text-emerald-300' : 'bg-rose-900/50 text-rose-300' }}">
                                    {{ $paymentStatus }}
                                </span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($modelId)
                                <form action="{{ route('admin.bookings.toggle_status', $modelId) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $isConfirmed ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}" title="Klik untuk ubah status">
                                        {{ $bookingStatus }}
                                    </button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isConfirmed ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                    {{ $bookingStatus }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-xs text-gray-500">
                            Belum ada data booking di database.
                        </td>
                    </tr>
                    @endforelse
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

        <form id="formTambahBooking" action="{{ route('admin.bookings.store') }}" method="POST" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">ID Booking (Opsional)</label>
                    <input type="text" name="kode_booking" id="inputIdBooking" placeholder="Otomatis jika kosong"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white uppercase focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Durasi (Jam)</label>
                    <input type="number" name="durasi" id="inputDurasiBooking" required min="1" max="24" value="2"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Pelanggan</label>
                <select name="id_user" id="inputPelangganBooking" required
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    @foreach($customerList as $c)
                        <option value="{{ $c->id }}">{{ $c->nama }} ({{ $c->email }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Unit Perangkat</label>
                <select name="id_device" id="inputDeviceBooking" required
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    @foreach($deviceList as $dev)
                        <option value="{{ $dev->id }}" data-harga="{{ $dev->harga_per_jam ?? 0 }}">
                            {{ $dev->nama_device }} · {{ $dev->tipe }} (Rp{{ number_format((int) ($dev->harga_per_jam ?? 0), 0, ',', '.') }}/jam)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Jadwal Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" id="inputJadwalBooking" required
                        value="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Total / Harga (Rp)</label>
                    <input type="number" name="total_harga" id="inputTotalBooking" required min="0" step="500" value="30000"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status Pembayaran</label>
                    <select name="status_bayar" id="inputStatusBayarBooking"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Lunas">Lunas</option>
                        <option value="Belum bayar">Belum bayar</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status Booking</label>
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

    const selectDevice = document.getElementById('inputDeviceBooking');
    const inputDurasi = document.getElementById('inputDurasiBooking');
    const inputTotal = document.getElementById('inputTotalBooking');

    function recalculateTotal() {
        if (!selectDevice || !inputDurasi || !inputTotal) return;
        const selectedOption = selectDevice.options[selectDevice.selectedIndex];
        const hargaPerJam = Number(selectedOption?.dataset?.harga || 0);
        const durasi = Math.max(1, Number(inputDurasi.value || 1));
        if (hargaPerJam > 0) {
            inputTotal.value = hargaPerJam * durasi;
        }
    }

    selectDevice?.addEventListener('change', recalculateTotal);
    inputDurasi?.addEventListener('input', recalculateTotal);

    function openModal() {
        recalculateTotal();
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputIdBooking')?.focus();
    }

    function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        form?.reset();
    }

    btnOpen?.addEventListener('click', openModal);
    btnClose?.addEventListener('click', closeModal);
    btnCancel?.addEventListener('click', closeModal);

    modal?.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });
});
</script>
@endsection