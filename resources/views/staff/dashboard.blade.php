@extends('layouts.staff')

@section('title', 'Dashboard Staff - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Operasional hari ini</h2>
    <p class="text-xs text-slate-400 mt-1">Ringkasan aktivitas dan jadwal booking mendatang.</p>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <a href="{{ route('staff.bookings') }}" class="bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Booking hari ini</span>
            <i class="fa-regular fa-calendar text-xs"></i>
        </div>
        <h3 class="text-2xl font-bold text-white">{{ $bookingHariIni }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">{{ $bookingAkanDatang }} akan datang</p>
    </a>

    <a href="{{ route('staff.sessions') }}" class="bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Sesi aktif</span>
            <i class="fa-regular fa-clock text-xs"></i>
        </div>
        <h3 class="text-2xl font-bold text-white">{{ $sesiAktifCount }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">dari {{ $totalPerangkat }} perangkat</p>
    </a>

    <a href="{{ route('staff.devices') }}" class="bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Perangkat siap</span>
            <i class="fa-regular fa-square-check text-xs"></i>
        </div>
        <h3 class="text-2xl font-bold text-white">{{ $perangkatSiap }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">siap digunakan</p>
    </a>

    <a href="{{ route('staff.maintenance') }}" class="bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
        <div class="flex justify-between items-start text-slate-400 mb-2">
            <span class="text-xs font-medium">Perlu maintenance</span>
            <i class="fa-solid fa-wrench text-xs"></i>
        </div>
        <h3 class="text-2xl font-bold text-white">{{ $perluMaintenance }}</h3>
        <p class="text-[11px] text-slate-400 mt-1">{{ $perangkatMaintenance->nama_device ?? 'Semua perangkat siap' }}</p>
    </a>
</div>

<section class="bg-[#12131a] border border-slate-800/80 rounded-xl p-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
        <div>
            <h3 class="text-sm font-bold text-white">Agenda booking terdekat</h3>
            <p class="text-[11px] text-slate-400 mt-1">Jadwal berikutnya yang perlu disiapkan.</p>
        </div>
        <a href="{{ route('staff.bookings') }}" class="text-xs text-purple-400 hover:text-purple-300 font-medium">Semua booking &rarr;</a>
    </div>

    <div class="divide-y divide-slate-800/60">
        @forelse($agendaBooking as $booking)
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 py-3 first:pt-0 last:pb-0">
                <div class="sm:w-32 shrink-0">
                    <p class="text-sm font-semibold text-white">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('H.i') }}</p>
                    <p class="text-[11px] text-slate-400">{{ \Carbon\Carbon::parse($booking->waktu_mulai)->format('d M Y') }}</p>
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-white truncate">{{ $booking->device->nama_device ?? 'Perangkat' }}</p>
                    <p class="text-[11px] text-slate-400 truncate">{{ $booking->user->nama ?? 'Pelanggan' }} &middot; {{ $booking->kode_booking }}</p>
                    <p class="text-[10px] text-slate-500">{{ $booking->durasi }} jam</p>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-500/10 px-2.5 py-1 text-[10px] font-medium text-emerald-400">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                    Menunggu
                </span>
            </div>
        @empty
            <div class="py-8 text-center">
                <i class="fa-regular fa-calendar-check text-lg text-slate-600 mb-2"></i>
                <p class="text-xs text-slate-400">Belum ada booking terjadwal selanjutnya.</p>
            </div>
        @endforelse
    </div>
</section>
@endsection
