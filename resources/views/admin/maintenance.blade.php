@extends('layouts.admin')

@section('content')
@php
    $maintenanceList = collect($maintenances ?? []);
    $deviceList = collect($devices ?? []);
    $totalTiket = $maintenanceList->count();
    $dalamPenangananCount = $maintenanceList->where('status', '!=', 'Selesai')->count();
    $selesaiCount = $maintenanceList->where('status', 'Selesai')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Maintenance Perangkat</h1>
        <p class="text-xs text-gray-400 mt-0.5">Monitor perbaikan unit rental yang mengalami kendala.</p>
    </div>
    <div>
        <button type="button" id="btnOpenTambahMaintenance" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-xl text-sm font-medium transition shadow-lg shadow-purple-900/30 flex items-center gap-2 cursor-pointer">
            <span>➕</span> Tambah Tiket
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

    <!-- Card Ringkasan Maintenance -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Total Tiket Maintenance</p>
            <h2 class="text-2xl font-bold text-white mt-1">{{ $totalTiket }}</h2>
            <p class="text-xs text-gray-400 mt-2">Tercatat pada sistem</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Dalam Penanganan</p>
            <h2 class="text-2xl font-bold text-amber-400 mt-1">{{ $dalamPenangananCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Sedang diperbaiki</p>
        </div>
        <div class="bg-[#141221] border border-gray-800/80 p-5 rounded-2xl shadow-sm">
            <p class="text-xs text-gray-400">Selesai</p>
            <h2 class="text-2xl font-bold text-emerald-400 mt-1">{{ $selesaiCount }}</h2>
            <p class="text-xs text-gray-400 mt-2">Siap digunakan kembali</p>
        </div>
    </div>

    <!-- Tabel Tiket Perbaikan -->
    <div class="bg-[#141221] border border-gray-800/80 rounded-2xl overflow-hidden shadow-sm">
        <div class="p-4 border-b border-gray-800 flex justify-between items-center">
            <h3 class="font-semibold text-white">Tiket Perbaikan</h3>
            <span class="text-xs text-gray-400">Klik badge status untuk mengubah</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse" id="maintenanceTable">
                <thead>
                    <tr class="border-b border-gray-800 text-xs text-gray-400 bg-[#110f1c]">
                        <th class="p-4">Tiket</th>
                        <th class="p-4">Unit Perangkat</th>
                        <th class="p-4">Masalah / Keterangan</th>
                        <th class="p-4">Waktu Mulai</th>
                        <th class="p-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-800/60 text-sm" id="maintenanceTableBody">
                    @forelse($maintenanceList as $m)
                    @php
                        $modelId = $m['model_id'] ?? null;
                        $statusMaintenance = $m['status'] ?? 'Dalam Penanganan';
                        $isSelesai = $statusMaintenance === 'Selesai';
                    @endphp
                    <tr class="maintenance-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $m['ticket'] ?? '-' }}</td>
                        <td class="p-4 text-purple-400">{{ $m['unit'] ?? '-' }}</td>
                        <td class="p-4 text-gray-300">{{ $m['masalah'] ?? '-' }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $m['waktu'] ?? '-' }}</td>
                        <td class="p-4">
                            @if($modelId)
                                <form action="{{ route('admin.maintenance.toggle', $modelId) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $isSelesai ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}" title="Klik untuk ubah status">
                                        {{ $statusMaintenance }}
                                    </button>
                                </form>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ $isSelesai ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}">
                                    {{ $statusMaintenance }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-6 text-center text-xs text-gray-500">
                            Belum ada tiket maintenance di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Tiket Maintenance -->
<div id="modalTambahMaintenance" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/70 backdrop-blur-xs p-4">
    <div class="bg-[#141221] border border-gray-800 rounded-2xl w-full max-w-md p-6 shadow-xl">
        <div class="flex justify-between items-center border-b border-gray-800 pb-3 mb-4">
            <h3 class="font-semibold text-white text-base">Tambah Tiket Maintenance</h3>
            <button type="button" id="btnCloseTambahMaintenance" class="text-gray-400 hover:text-white text-lg leading-none cursor-pointer">&times;</button>
        </div>

        <form id="formTambahMaintenance" action="{{ route('admin.maintenance.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs text-gray-400 mb-1">Unit Perangkat</label>
                <select name="id_device" id="inputDeviceMaintenance" required
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                    @foreach($deviceList as $dev)
                        <option value="{{ $dev->id }}">{{ $dev->nama_device }} · {{ $dev->tipe }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs text-gray-400 mb-1">Masalah / Keterangan</label>
                <input type="text" name="keterangan" id="inputKeteranganMaintenance" required placeholder="Contoh: DualSense drift / Port HDMI longgar"
                    class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs text-gray-400 mb-1">Waktu Mulai</label>
                    <input type="datetime-local" name="waktu_mulai" id="inputWaktuMulaiMaintenance"
                        value="{{ now()->format('Y-m-d\TH:i') }}"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                </div>

                <div>
                    <label class="block text-xs text-gray-400 mb-1">Status</label>
                    <select name="status" id="inputStatusMaintenance"
                        class="w-full bg-[#0b0a12] border border-gray-800 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:border-purple-600">
                        <option value="Dalam Penanganan">Dalam Penanganan</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-3 border-t border-gray-800">
                <button type="button" id="btnBatalTambahMaintenance" class="px-4 py-2 rounded-xl text-xs text-gray-300 bg-gray-800 hover:bg-gray-700 transition cursor-pointer">
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
    const modal = document.getElementById('modalTambahMaintenance');
    const btnOpen = document.getElementById('btnOpenTambahMaintenance');
    const btnClose = document.getElementById('btnCloseTambahMaintenance');
    const btnCancel = document.getElementById('btnBatalTambahMaintenance');
    const form = document.getElementById('formTambahMaintenance');

    function openModal() {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.getElementById('inputKeteranganMaintenance')?.focus();
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