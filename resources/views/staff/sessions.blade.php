@extends('layouts.staff')

@section('title', 'Sesi Aktif - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Sesi aktif</h2>
    <p class="text-xs text-slate-400 mt-1">Pantau waktu bermain, tambah durasi, atau akhiri sesi.</p>
</div>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-white">Sedang bermain</h3>
        <span class="text-xs text-slate-400">{{ $sesiAktif->count() }} sesi</span>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead>
                <tr class="text-slate-400 border-b border-slate-800/80">
                    <th class="pb-3 font-medium">Pelanggan</th>
                    <th class="pb-3 font-medium">Perangkat</th>
                    <th class="pb-3 font-medium">Waktu</th>
                    <th class="pb-3 font-medium">Sisa waktu</th>
                    <th class="pb-3 font-medium text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($sesiAktif as $sesi)
                    @php
                        $waktuMulai = \Carbon\Carbon::parse($sesi->waktu_mulai);
                        $waktuSelesai = \Carbon\Carbon::parse($sesi->waktu_selesai);
                        $sisaMenit = max(0, now()->diffInMinutes($waktuSelesai, false));
                    @endphp
                    <tr>
                        <td class="py-4">
                            <p class="font-semibold text-white">{{ $sesi->user->nama ?? 'Pelanggan' }}</p>
                            <p class="text-[11px] text-slate-500">{{ $sesi->kode_booking }}</p>
                        </td>
                        <td class="py-4 text-slate-300">{{ $sesi->device->nama_device ?? '-' }}</td>
                        <td class="py-4 text-slate-400">{{ $waktuMulai->format('H.i') }}–{{ $waktuSelesai->format('H.i') }} WIB</td>
                        <td class="py-4 font-semibold text-white">{{ sprintf('%02d:%02d', floor($sisaMenit / 60), $sisaMenit % 60) }}</td>
                        <td class="py-4 text-right">
                            <div class="inline-flex gap-2">
                                <form action="{{ route('staff.booking.add_duration', $sesi->id) }}" method="POST" onsubmit="return confirm('Tambah durasi sesi ini 1 jam?')">
                                    @csrf
                                    <button type="submit" class="bg-[#232534] hover:bg-purple-600 text-white text-xs px-3 py-1.5 rounded transition">+1 jam</button>
                                </form>
                                <form action="{{ route('staff.booking.checkout', $sesi->id) }}" method="POST" onsubmit="return confirm('Check-out booking ini dan akhiri sesi?')">
                                    @csrf
                                    <button type="submit" class="bg-rose-600/80 hover:bg-rose-600 text-white text-xs px-3 py-1.5 rounded transition">Check-out</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-500">Belum ada sesi bermain yang aktif.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>
@endsection
