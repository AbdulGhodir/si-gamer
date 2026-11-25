@extends('layouts.staff')

@section('title', 'Maintenance - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Maintenance</h2>
    <p class="text-xs text-slate-400 mt-1">Laporkan kerusakan perangkat dan lihat riwayat tiket.</p>
</div>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5 mb-5">
    <h3 class="text-sm font-bold text-white mb-4">Laporkan kerusakan</h3>
    <form action="{{ route('staff.maintenance.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-3">
        @csrf
        <label class="text-xs text-slate-400">
            Perangkat
            <select name="id_device" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                <option value="">Pilih perangkat</option>
                @foreach($devices as $device)
                    <option value="{{ $device->id }}" {{ old('id_device') == $device->id ? 'selected' : '' }}>{{ $device->nama_device }} ({{ ucfirst($device->status) }})</option>
                @endforeach
            </select>
        </label>
        <label class="text-xs text-slate-400 md:col-span-2">
            Keterangan kerusakan
            <textarea name="keterangan" rows="2" required maxlength="1000" placeholder="Jelaskan masalah pada perangkat..." class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-600">{{ old('keterangan') }}</textarea>
        </label>
        <div class="md:col-span-3 flex flex-wrap items-center justify-between gap-3">
            <p class="text-[11px] text-slate-500">
                @if($devices->isEmpty()) Tidak ada perangkat yang dapat dilaporkan saat ini. @else Tiket akan diteruskan ke admin untuk ditangani. @endif
            </p>
            <button type="submit" @if($devices->isEmpty()) disabled @endif
                    class="bg-purple-600 hover:bg-purple-500 disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition"
                    onclick="return confirm('Kirim laporan maintenance ini ke admin?')">
                Kirim laporan
            </button>
        </div>
    </form>
</section>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-white">Riwayat maintenance</h3>
        <span class="text-xs text-slate-400">{{ $maintenances->count() }} tiket</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[600px] text-left text-xs">
            <thead>
                <tr class="text-slate-400 border-b border-slate-800/80">
                    <th class="pb-3 font-medium">Perangkat</th>
                    <th class="pb-3 font-medium">Keterangan</th>
                    <th class="pb-3 font-medium">Mulai</th>
                    <th class="pb-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($maintenances as $maintenance)
                    <tr>
                        <td class="py-3 font-semibold text-white">{{ $maintenance->device->nama_device ?? 'Perangkat dihapus' }}</td>
                        <td class="py-3 text-slate-300">{{ $maintenance->keterangan }}</td>
                        <td class="py-3 text-slate-400">{{ $maintenance->waktu_mulai ? \Carbon\Carbon::parse($maintenance->waktu_mulai)->format('d M Y, H.i') : '-' }}</td>
                        <td class="py-3">
                            <span class="inline-flex px-2 py-1 rounded text-[10px] font-medium {{ $maintenance->status === 'berlangsung' ? 'bg-amber-500/10 text-amber-400' : 'bg-emerald-500/10 text-emerald-400' }}">
                                {{ $maintenance->status === 'berlangsung' ? 'Dalam penanganan' : ucfirst($maintenance->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-500">Belum ada tiket maintenance.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
