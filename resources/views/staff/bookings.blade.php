@extends('layouts.staff')

@section('title', 'Daftar Booking - GameHub')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
    <div>
        <h2 class="text-2xl font-bold text-white tracking-tight">Daftar booking</h2>
        <p class="text-xs text-slate-400 mt-1">Cari booking, lakukan check-in, atau buat booking terjadwal.</p>
    </div>
</div>

<details class="group bg-[#12131a] border border-slate-800/80 rounded-xl mb-5">
    <summary class="cursor-pointer list-none flex items-center justify-between p-4 text-sm font-semibold text-white">
        <span><i class="fa-solid fa-plus text-purple-400 mr-2"></i>Tambah booking terjadwal</span>
        <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition group-open:rotate-180"></i>
    </summary>
    <form action="{{ route('staff.booking.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3 px-4 pb-4">
        @csrf
        <label class="text-xs text-slate-400">
            Pelanggan
            <select name="id_user" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                <option value="">Pilih pelanggan</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}" {{ old('id_user') == $customer->id ? 'selected' : '' }}>{{ $customer->nama }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-xs text-slate-400">
            Perangkat
            <select name="id_device" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                <option value="">Pilih perangkat</option>
                @foreach($devicesBookable as $device)
                    <option value="{{ $device->id }}" {{ old('id_device') == $device->id ? 'selected' : '' }}>{{ $device->nama_device }} — Rp{{ number_format($device->harga_per_jam, 0, ',', '.') }}/jam</option>
                @endforeach
            </select>
        </label>
        <label class="text-xs text-slate-400">
            Waktu mulai
            <input type="datetime-local" name="waktu_mulai" value="{{ old('waktu_mulai') }}" min="{{ now()->format('Y-m-d\TH:i') }}" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
        </label>
        <div class="flex gap-3 items-end">
            <label class="text-xs text-slate-400 flex-1">
                Durasi
                <select name="durasi" required class="mt-1 w-full bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-white focus:outline-none focus:border-purple-600">
                    @for($hours = 1; $hours <= 12; $hours++)
                        <option value="{{ $hours }}" {{ old('durasi', 1) == $hours ? 'selected' : '' }}>{{ $hours }} jam</option>
                    @endfor
                </select>
            </label>
            <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white text-xs font-semibold px-4 py-2.5 rounded-lg transition">Simpan</button>
        </div>
        @if($customers->isEmpty() || $devicesBookable->isEmpty())
            <p class="md:col-span-2 xl:col-span-4 text-xs text-amber-400">
                @if($customers->isEmpty()) Belum ada pelanggan terdaftar. @endif
                @if($devicesBookable->isEmpty()) Tidak ada perangkat yang dapat dibooking. @endif
            </p>
        @endif
    </form>
</details>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <form method="GET" action="{{ route('staff.bookings') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
        <label class="sm:col-span-2 relative">
            <span class="sr-only">Cari booking</span>
            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-slate-500 text-xs"></i>
            <input type="search" name="search" value="{{ request('search') }}" placeholder="Cari kode booking atau nama pelanggan" class="w-full bg-[#181924] border border-slate-800 rounded-lg pl-8 pr-3 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-600">
        </label>
        <div class="flex gap-2">
            <select name="status" class="flex-1 bg-[#181924] border border-slate-800 rounded-lg px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-purple-600">
                <option value="">Semua status</option>
                <option value="menunggu" {{ request('status') === 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                <option value="bermain" {{ request('status') === 'bermain' ? 'selected' : '' }}>Bermain</option>
                <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                <option value="dibatalkan" {{ request('status') === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
            </select>
            <button type="submit" class="bg-[#232534] hover:bg-slate-700 text-white text-xs px-4 rounded-lg transition">Cari</button>
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full min-w-[760px] text-left text-xs">
            <thead>
                <tr class="text-slate-400 border-b border-slate-800/80">
                    <th class="pb-3 font-medium">Kode / pelanggan</th>
                    <th class="pb-3 font-medium">Perangkat</th>
                    <th class="pb-3 font-medium">Jadwal</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium text-right">Tindakan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/40">
                @forelse($daftarBooking as $booking)
                    <tr>
                        <td class="py-3">
                            <p class="font-semibold text-white">{{ $booking->kode_booking }}</p>
                            <p class="text-[11px] text-slate-400">{{ $booking->user->nama ?? 'Pelanggan' }}@if($booking->jenis_booking === 'langsung') &middot; <span class="italic text-purple-400">walk-in</span>@endif</p>
                        </td>
                        <td class="py-3 text-slate-300">{{ $booking->device->nama_device ?? '-' }}</td>
                        <td class="py-3 text-slate-400">
                            {{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y, H.i') }}–{{ \Carbon\Carbon::parse($booking->waktu_selesai)->format('H.i') }}
                        </td>
                        <td class="py-3">
                            @php
                                $statusClass = match($booking->status) {
                                    'menunggu' => 'bg-emerald-500/10 text-emerald-400',
                                    'bermain' => 'bg-purple-500/10 text-purple-400',
                                    'selesai' => 'bg-slate-500/10 text-slate-400',
                                    default => 'bg-rose-500/10 text-rose-400',
                                };
                            @endphp
                            <span class="inline-flex px-2 py-1 rounded text-[10px] font-medium {{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                        </td>
                        <td class="py-3 text-right">
                            <div class="inline-flex items-center justify-end gap-2">
                                @if($booking->status !== 'dibatalkan' && ($booking->payment->status_pembayaran ?? 'menunggu') !== 'berhasil')
                                    <form action="{{ route('staff.booking.confirm_payment', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Konfirmasi pembayaran tunai di tempat untuk booking ini?')">
                                        @csrf
                                        <button type="submit" class="bg-[#232534] hover:bg-emerald-600 text-white text-[11px] font-medium px-3 py-1.5 rounded transition">Terima bayar</button>
                                    </form>
                                @endif
                                @if($booking->status === 'menunggu')
                                    <form action="{{ route('staff.booking.checkin', $booking->id) }}" method="POST" class="inline" onsubmit="return confirm('Check-in booking ini dan mulai sesi?')">
                                        @csrf
                                        <button type="submit" class="bg-purple-600 hover:bg-purple-500 text-white text-[11px] font-medium px-3 py-1.5 rounded transition">Check-in</button>
                                    </form>
                                @elseif($booking->status === 'bermain')
                                    <a href="{{ route('staff.sessions') }}" class="text-purple-400 hover:text-purple-300">Lihat sesi</a>
                                @elseif(($booking->payment->status_pembayaran ?? 'menunggu') === 'berhasil' || $booking->status === 'dibatalkan')
                                    <span class="text-slate-500">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="py-8 text-center text-slate-500">Tidak ada data booking.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="flex justify-between items-center text-[11px] text-slate-400 pt-4 mt-2 border-t border-slate-800/80">
        <span>{{ $daftarBooking->count() }} dari {{ $daftarBooking->total() }} booking</span>
        {{ $daftarBooking->links() }}
    </div>
</section>
@endsection
