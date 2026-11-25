@extends('layouts.admin')

@section('content')
@php
    $maintenanceList = collect($maintenances ?? []);
    $totalTiket = $maintenanceList->count();
    $dalamPenangananCount = $maintenanceList->where('status', '!=', 'Selesai')->count();
    $selesaiCount = $maintenanceList->where('status', 'Selesai')->count();
@endphp

<header class="p-6 pb-4 border-b border-gray-800/50">
    <div>
        <h1 class="text-2xl font-bold text-white tracking-tight">Maintenance Perangkat</h1>
        <p class="text-xs text-gray-400 mt-0.5">Pantau tiket perbaikan yang dilaporkan oleh staff.</p>
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
            <span class="text-xs text-gray-400">Klik status untuk mengubah setelah konfirmasi</span>
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
                        $maintenanceConfirm = $isSelesai
                            ? 'Buka kembali tiket maintenance ini?'
                            : 'Tandai tiket maintenance ini sebagai selesai?';
                    @endphp
                    <tr class="maintenance-row hover:bg-white/[0.02] transition">
                        <td class="p-4 font-medium text-white">{{ $m['ticket'] ?? '-' }}</td>
                        <td class="p-4 text-purple-400">{{ $m['unit'] ?? '-' }}</td>
                        <td class="p-4 text-gray-300">{{ $m['masalah'] ?? '-' }}</td>
                        <td class="p-4 text-gray-400 text-xs">{{ $m['waktu'] ?? '-' }}</td>
                        <td class="p-4">
                            @if($modelId)
                                <form action="{{ route('admin.maintenance.toggle', $modelId) }}" method="POST" class="inline"
                                      onsubmit="return confirm(@js($maintenanceConfirm))">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-2.5 py-1 rounded-full text-xs font-medium cursor-pointer transition {{ $isSelesai ? 'bg-emerald-900/50 text-emerald-300' : 'bg-amber-900/50 text-amber-300' }}" title="Klik untuk ubah status maintenance">
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
@endsection