@extends('layouts.staff')

@section('title', 'Status Perangkat - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Status perangkat</h2>
    <p class="text-xs text-slate-400 mt-1">Ketersediaan seluruh perangkat rental saat ini.</p>
</div>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <div class="flex flex-wrap gap-3 mb-5 text-[11px]">
        <span class="flex items-center text-slate-300"><span class="w-2 h-2 rounded-full bg-emerald-400 mr-2"></span>Tersedia</span>
        <span class="flex items-center text-slate-300"><span class="w-2 h-2 rounded-full bg-purple-400 mr-2"></span>Sedang bermain</span>
        <span class="flex items-center text-slate-300"><span class="w-2 h-2 rounded-full bg-amber-400 mr-2"></span>Perbaikan</span>
        <span class="flex items-center text-slate-300"><span class="w-2 h-2 rounded-full bg-slate-500 mr-2"></span>Offline</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[540px] text-left text-xs">
            <thead>
                <tr class="text-slate-400 border-b border-slate-800/80">
                    <th class="pb-3 font-medium">Perangkat</th>
                    <th class="pb-3 font-medium">Tipe</th>
                    <th class="pb-3 font-medium">Harga / jam</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($devices as $device)
                    @php
                        [$statusLabel, $statusClass] = match($device->status) {
                            'tersedia' => ['Tersedia', 'bg-emerald-500/10 text-emerald-400'],
                            'bermain' => ['Sedang bermain', 'bg-purple-500/10 text-purple-400'],
                            'perbaikan' => ['Perbaikan', 'bg-amber-500/10 text-amber-400'],
                            default => [ucfirst($device->status), 'bg-slate-500/10 text-slate-400'],
                        };
                    @endphp
                    <tr>
                        <td class="py-3 font-semibold text-white">{{ $device->nama_device }}</td>
                        <td class="py-3 text-slate-400">{{ $device->tipe }}</td>
                        <td class="py-3 text-slate-300">Rp{{ number_format($device->harga_per_jam, 0, ',', '.') }}</td>
                        <td class="py-3"><span class="inline-flex px-2 py-1 rounded text-[10px] font-medium {{ $statusClass }}">{{ $statusLabel }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada perangkat terdaftar.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
