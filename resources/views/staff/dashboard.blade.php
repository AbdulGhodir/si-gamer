@extends('layouts.staff')

@section('title', 'Dashboard Staff - GameHub')

@section('content')
<div class="mb-6">
    <h2 class="text-2xl font-bold text-white tracking-tight">Operasional hari ini</h2>
    <p class="text-xs text-slate-400 mt-1">Ringkasan aktivitas dan akses cepat ke fitur operasional.</p>
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

<section>
    <h3 class="text-sm font-bold text-white mb-3">Akses cepat</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3">
        <a href="{{ route('staff.bookings') }}" class="flex items-center gap-3 bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
            <i class="fa-solid fa-plus w-9 h-9 rounded-lg bg-purple-600/15 text-purple-400 flex items-center justify-center"></i>
            <span class="text-sm font-medium text-white">Tambah Booking</span>
            <i class="fa-solid fa-arrow-right ml-auto text-xs text-slate-500"></i>
        </a>
        <a href="{{ route('staff.booking.walkin') }}" class="flex items-center gap-3 bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
            <i class="fa-solid fa-user-plus w-9 h-9 rounded-lg bg-purple-600/15 text-purple-400 flex items-center justify-center"></i>
            <span class="text-sm font-medium text-white">Booking walk-in</span>
            <i class="fa-solid fa-arrow-right ml-auto text-xs text-slate-500"></i>
        </a>
        <a href="{{ route('staff.maintenance') }}" class="flex items-center gap-3 bg-[#12131a] border border-slate-800/80 hover:border-purple-500/50 rounded-xl p-4 transition">
            <i class="fa-solid fa-wrench w-9 h-9 rounded-lg bg-purple-600/15 text-purple-400 flex items-center justify-center"></i>
            <span class="text-sm font-medium text-white">Laporkan kerusakan</span>
            <i class="fa-solid fa-arrow-right ml-auto text-xs text-slate-500"></i>
        </a>
    </div>
</section>
@endsection
